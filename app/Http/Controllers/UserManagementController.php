<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    private const ASSIGNABLE_ROLE_NAMES = [
        Role::ADMIN_MASJID,
        Role::KETUA_DKM,
        Role::BENDAHARA,
        Role::SEKRETARIS,
        Role::OPERATOR,
    ];

    public function index(Request $request): View
    {
        $this->authorizeSuperuser();

        $filters = $request->only(['q', 'role_id', 'mosque_id']);

        $users = User::with('roles')
            ->when($filters['q'] ?? null, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['role_id'] ?? null, function ($query, $roleId) {
                $query->whereHas('roles', fn ($query) => $query->where('roles.id', $roleId));
            })
            ->when($filters['mosque_id'] ?? null, function ($query, $mosqueId) {
                $query->whereHas('roles', fn ($query) => $query->where('role_user.mosque_id', $mosqueId));
            })
            ->orderBy('name')
            ->get();
        $mosqueNames = Mosque::pluck('name', 'id');
        $mosques = Mosque::orderBy('name')->get(['id', 'name']);
        $roles = Role::orderBy('label')->get(['id', 'label']);

        return view('users.index', compact('filters', 'mosqueNames', 'mosques', 'roles', 'users'));
    }

    public function create(): View
    {
        $this->authorizeSuperuser();

        $mosques = Mosque::orderBy('name')->get();
        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLE_NAMES)->orderBy('label')->get();
        $canAssignSuperuser = auth()->user()->isSuperSuperuser();

        return view('users.create', compact('mosques', 'roles', 'canAssignSuperuser'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeSuperuser();
        $this->authorizeSuperuserAssignment($request->boolean('is_superuser'));

        $validated = $this->validateUserRequest($request, true);
        $isSuperuser = $validated['is_superuser'];
        $accesses = $this->normalizedAccesses($validated);

        DB::transaction(function () use ($validated, $isSuperuser, $accesses): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $this->syncAssignments($user, $isSuperuser, $accesses);
        });

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $this->authorizeSuperuser();
        $this->authorizePrivilegedTarget($user);

        $user->load('roles');
        $mosques = Mosque::orderBy('name')->get();
        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLE_NAMES)->orderBy('label')->get();
        $isSuperuser = $user->isSuperuser();
        $isSuperSuperuser = $user->isSuperSuperuser();
        $canAssignSuperuser = auth()->user()->isSuperSuperuser();
        $assignedAccesses = $user->roles
            ->whereNotNull('pivot.mosque_id')
            ->map(fn (Role $role) => [
                'mosque_id' => $role->pivot->mosque_id,
                'role_id' => $role->id,
            ])
            ->values()
            ->all();

        return view('users.edit', compact('user', 'mosques', 'roles', 'isSuperuser', 'isSuperSuperuser', 'canAssignSuperuser', 'assignedAccesses'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeSuperuser();
        $this->authorizePrivilegedTarget($user);

        $isSuperSuperuser = $user->isSuperSuperuser();
        if ($isSuperSuperuser) {
            $request->merge(['is_superuser' => true]);
        } else {
            $this->authorizeSuperuserAssignment($request->boolean('is_superuser'));
        }

        if (! $request->boolean('reset_password')) {
            $request->request->remove('password');
            $request->request->remove('password_confirmation');
        }

        $validated = $this->validateUserRequest($request, false, $user);
        $isSuperuser = $validated['is_superuser'];
        $accesses = $this->normalizedAccesses($validated);

        DB::transaction(function () use ($validated, $isSuperuser, $isSuperSuperuser, $accesses, $user): void {
            $attributes = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (! empty($validated['password'])) {
                $attributes['password'] = Hash::make($validated['password']);
            }

            $user->update($attributes);
            $this->syncAssignments($user, $isSuperuser, $accesses, $isSuperSuperuser);

            if (! $isSuperuser && $user->active_mosque_id && ! $user->canSelectMosque($user->active_mosque_id)) {
                $user->clearActiveMosque();
            }
        });

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    private function validateUserRequest(Request $request, bool $passwordRequired, ?User $user = null): array
    {
        $request->merge(['is_superuser' => $request->boolean('is_superuser')]);
        $isSuperuser = $request->boolean('is_superuser');

        $passwordRules = $passwordRequired || $request->boolean('reset_password')
            ? ['required', 'string', 'min:8', 'confirmed']
            : ['nullable', 'string', 'min:8', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => $passwordRules,
            'reset_password' => ['nullable', 'boolean'],
            'is_superuser' => ['required', 'boolean'],
            'accesses' => $isSuperuser ? ['nullable', 'array'] : ['required', 'array', 'min:1'],
            'accesses.*.mosque_id' => ['required_with:accesses', 'integer', Rule::exists('mosques', 'id')],
            'accesses.*.role_id' => [
                'required_with:accesses',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->whereIn('name', self::ASSIGNABLE_ROLE_NAMES)),
            ],
        ]);
    }

    private function normalizedAccesses(array $validated)
    {
        $accesses = collect($validated['accesses'] ?? [])->map(fn (array $access) => [
            'mosque_id' => (int) $access['mosque_id'],
            'role_id' => (int) $access['role_id'],
        ]);

        if ($accesses->duplicates(fn (array $access) => $access['mosque_id'].'-'.$access['role_id'])->isNotEmpty()) {
            throw ValidationException::withMessages([
                'accesses' => 'Akses masjid dan role yang sama tidak boleh ditambahkan lebih dari sekali.',
            ]);
        }

        return $accesses;
    }

    private function syncAssignments(User $user, bool $isSuperuser, $accesses, bool $isSuperSuperuser = false): void
    {
        $user->roles()->detach();

        if ($isSuperuser) {
            $roleName = $isSuperSuperuser ? Role::SUPER_SUPERUSER : Role::SUPERUSER;
            $superuserRole = Role::where('name', $roleName)->firstOrFail();
            $user->roles()->attach($superuserRole->id, ['mosque_id' => null]);

            return;
        }

        foreach ($accesses as $access) {
            $user->roles()->attach($access['role_id'], ['mosque_id' => $access['mosque_id']]);
        }
    }

    private function authorizeSuperuser(): void
    {
        abort_unless(auth()->user()->isSuperuser(), 403);
    }

    private function authorizePrivilegedTarget(User $user): void
    {
        abort_if($user->isSuperuser() && ! auth()->user()->isSuperSuperuser(), 403);
    }

    private function authorizeSuperuserAssignment(bool $isSuperuser): void
    {
        if ($isSuperuser && ! auth()->user()->isSuperSuperuser()) {
            throw ValidationException::withMessages([
                'is_superuser' => 'Hanya super superuser yang dapat menetapkan role superuser.',
            ]);
        }
    }
}
