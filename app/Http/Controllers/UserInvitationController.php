<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserInvitationController extends Controller
{
    private const ASSIGNABLE_ROLE_NAMES = [
        Role::ADMIN_MASJID,
        Role::KETUA_DKM,
        Role::BENDAHARA,
        Role::SEKRETARIS,
        Role::OPERATOR,
    ];

    public function index(): View
    {
        $this->authorizeSuperuser();

        UserInvitation::whereIn('status', [UserInvitation::STATUS_DRAFT, UserInvitation::STATUS_SUBMITTED])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => UserInvitation::STATUS_EXPIRED]);

        $invitations = UserInvitation::with(['mosque', 'role', 'invitedBy'])
            ->latest()
            ->get();

        return view('user_invitations.index', compact('invitations'));
    }

    public function create(): View
    {
        $this->authorizeSuperuser();

        $mosques = Mosque::orderBy('name')->get();
        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLE_NAMES)
            ->orderBy('label')
            ->get();
        $canInviteSuperuser = auth()->user()->isSuperSuperuser();

        return view('user_invitations.create', compact('mosques', 'roles', 'canInviteSuperuser'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeSuperuser();

        $request->merge(['is_superuser' => $request->boolean('is_superuser')]);
        $isSuperuser = $request->boolean('is_superuser');
        $this->authorizeSuperuserInvitation($isSuperuser);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'name' => ['nullable', 'string', 'max:255'],
            'is_superuser' => ['required', 'boolean'],
            'mosque_id' => $isSuperuser
                ? ['nullable']
                : ['required', 'integer', Rule::exists('mosques', 'id')],
            'role_id' => $isSuperuser
                ? ['nullable']
                : [
                    'required',
                    'integer',
                    Rule::exists('roles', 'id')->where(
                        fn ($query) => $query->whereIn('name', self::ASSIGNABLE_ROLE_NAMES)
                    ),
                ],
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $role = $isSuperuser
            ? Role::where('name', Role::SUPERUSER)->firstOrFail()
            : Role::findOrFail($validated['role_id']);

        UserInvitation::create([
            'token' => $this->newToken(),
            'phone' => $this->normalizePhone($validated['phone']),
            'name' => $validated['name'] ?? null,
            'mosque_id' => $isSuperuser ? null : $validated['mosque_id'],
            'role_id' => $role->id,
            'invited_by' => auth()->id(),
            'status' => UserInvitation::STATUS_DRAFT,
            'expires_at' => now()->addDays((int) ($validated['expires_in_days'] ?? 3)),
        ]);

        return redirect()
            ->route('user-invitations.index')
            ->with('success', 'Undangan user berhasil dibuat.');
    }

    public function approve(UserInvitation $invitation): RedirectResponse
    {
        $this->authorizeSuperuser();

        $result = DB::transaction(function () use ($invitation): array {
            $currentInvitation = UserInvitation::whereKey($invitation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->expireIfNeeded($currentInvitation)) {
                return ['error', 'Undangan telah kedaluwarsa dan tidak dapat disetujui.'];
            }

            if ($currentInvitation->status !== UserInvitation::STATUS_SUBMITTED) {
                return ['error', 'Hanya undangan berstatus submitted yang dapat disetujui.'];
            }

            if (! $currentInvitation->name || ! $currentInvitation->email || ! $currentInvitation->password_hash) {
                return ['error', 'Data pendaftaran undangan belum lengkap.'];
            }

            if (User::where('email', $currentInvitation->email)->exists()) {
                return ['error', 'Email sudah digunakan oleh user lain.'];
            }

            $role = Role::find($currentInvitation->role_id);

            if (! $role) {
                return ['error', 'Role pada undangan tidak ditemukan.'];
            }

            if ($role->name === Role::SUPERUSER) {
                if (! auth()->user()->isSuperSuperuser()) {
                    return ['error', 'Hanya super superuser yang dapat menyetujui undangan superuser.'];
                }

                if ($currentInvitation->mosque_id !== null) {
                    return ['error', 'Undangan superuser tidak boleh memiliki masjid.'];
                }

                $mosqueId = null;
                $activeMosqueId = null;
            } else {
                if (! in_array($role->name, self::ASSIGNABLE_ROLE_NAMES, true)) {
                    return ['error', 'Role pada undangan tidak diizinkan.'];
                }

                if (! $currentInvitation->mosque_id || ! Mosque::whereKey($currentInvitation->mosque_id)->exists()) {
                    return ['error', 'Masjid pada undangan tidak ditemukan.'];
                }

                $mosqueId = $currentInvitation->mosque_id;
                $activeMosqueId = $mosqueId;
            }

            $user = User::create([
                'name' => $currentInvitation->name,
                'email' => $currentInvitation->email,
                'phone' => $currentInvitation->phone,
                'password' => $currentInvitation->password_hash,
                'active_mosque_id' => $activeMosqueId,
            ]);

            $user->roles()->attach($role->id, ['mosque_id' => $mosqueId]);

            $currentInvitation->update([
                'status' => UserInvitation::STATUS_APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return ['success', 'Undangan berhasil disetujui dan user baru telah aktif.'];
        });

        return redirect()->route('user-invitations.index')->with($result[0], $result[1]);
    }

    public function cancel(UserInvitation $invitation): RedirectResponse
    {
        $this->authorizeSuperuser();

        $result = DB::transaction(function () use ($invitation): array {
            $currentInvitation = UserInvitation::whereKey($invitation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->expireIfNeeded($currentInvitation)) {
                return ['error', 'Undangan telah kedaluwarsa dan tidak dapat dibatalkan.'];
            }

            if (! in_array($currentInvitation->status, [UserInvitation::STATUS_DRAFT, UserInvitation::STATUS_SUBMITTED], true)) {
                return ['error', 'Hanya undangan draft atau submitted yang dapat dibatalkan.'];
            }

            if ($currentInvitation->role?->name === Role::SUPERUSER && ! auth()->user()->isSuperSuperuser()) {
                return ['error', 'Hanya super superuser yang dapat membatalkan undangan superuser.'];
            }

            $currentInvitation->update(['status' => UserInvitation::STATUS_CANCELLED]);

            return ['success', 'Undangan user berhasil dibatalkan.'];
        });

        return redirect()->route('user-invitations.index')->with($result[0], $result[1]);
    }

    public function showRegisterForm(string $token): View
    {
        $invitation = UserInvitation::with(['mosque', 'role'])
            ->where('token', $token)
            ->firstOrFail();

        if ($this->expireIfNeeded($invitation)) {
            return view('user_invitations.register', [
                'invitation' => $invitation,
                'errorMessage' => 'Link undangan telah kedaluwarsa.',
            ]);
        }

        if ($invitation->status === UserInvitation::STATUS_SUBMITTED) {
            return view('user_invitations.submitted', compact('invitation'));
        }

        if ($invitation->status !== UserInvitation::STATUS_DRAFT) {
            return view('user_invitations.register', [
                'invitation' => $invitation,
                'errorMessage' => 'Link undangan sudah tidak berlaku.',
            ]);
        }

        return view('user_invitations.register', [
            'invitation' => $invitation,
            'errorMessage' => null,
        ]);
    }

    public function submitRegisterForm(Request $request, string $token): RedirectResponse
    {
        $invitation = UserInvitation::where('token', $token)->firstOrFail();

        if ($this->expireIfNeeded($invitation) || $invitation->status !== UserInvitation::STATUS_DRAFT) {
            return redirect()->route('invitations.register', ['token' => $token]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('user_invitations', 'email')->where(
                    fn ($query) => $query->where('status', UserInvitation::STATUS_SUBMITTED)
                ),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Email sudah digunakan atau sedang menunggu persetujuan.',
        ]);

        $submissionAccepted = DB::transaction(function () use ($invitation, $validated): bool {
            $currentInvitation = UserInvitation::whereKey($invitation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->expireIfNeeded($currentInvitation)) {
                return false;
            }

            if ($currentInvitation->status !== UserInvitation::STATUS_DRAFT) {
                return false;
            }

            $currentInvitation->update([
                'name' => $validated['name'],
                'phone' => $this->normalizePhone($validated['phone']),
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'status' => UserInvitation::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);

            return true;
        });

        if (! $submissionAccepted) {
            return redirect()->route('invitations.register', ['token' => $token]);
        }

        return redirect()->route('invitations.register', ['token' => $token]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        if (! preg_match('/^62[0-9]{8,13}$/', $phone)) {
            throw ValidationException::withMessages([
                'phone' => 'Nomor WhatsApp harus menggunakan format Indonesia yang valid.',
            ]);
        }

        return $phone;
    }

    private function newToken(): string
    {
        do {
            $token = Str::random(64);
        } while (UserInvitation::where('token', $token)->exists());

        return $token;
    }

    private function expireIfNeeded(UserInvitation $invitation): bool
    {
        if (! in_array($invitation->status, [UserInvitation::STATUS_DRAFT, UserInvitation::STATUS_SUBMITTED], true)
            || ! $invitation->expires_at?->isPast()) {
            return false;
        }

        $invitation->update(['status' => UserInvitation::STATUS_EXPIRED]);

        return true;
    }

    private function authorizeSuperuser(): void
    {
        abort_unless(auth()->user()->isSuperuser(), 403);
    }

    private function authorizeSuperuserInvitation(bool $isSuperuser): void
    {
        if ($isSuperuser && ! auth()->user()->isSuperSuperuser()) {
            throw ValidationException::withMessages([
                'is_superuser' => 'Hanya super superuser yang dapat membuat undangan superuser.',
            ]);
        }
    }
}
