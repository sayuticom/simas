<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\DesignRequest;
use App\Models\DonationProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DonationProgramController extends Controller
{
    public function index(): View
    {
        $programs = DonationProgram::with('cashAccount')
            ->latest()
            ->paginate(10);

        return view('admin.donation_programs.index', [
            'programs' => $programs,
            'statuses' => DonationProgram::statuses(),
            'paymentModes' => DonationProgram::paymentModes(),
        ]);
    }

    public function create(): View
    {
        return view('admin.donation_programs.create', [
            'program' => null,
            'cashAccounts' => $this->cashAccounts(),
            'statuses' => DonationProgram::statuses(),
            'paymentModes' => DonationProgram::paymentModes(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $mosqueId = $this->activeMosqueId();

        $data['mosque_id'] = $mosqueId;
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $mosqueId);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['show_on_public'] = $request->boolean('show_on_public');
        $data['collected_amount'] = $data['collected_amount'] ?? 0;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('website/donasi/programs', 'public');
        }

        if ($request->hasFile('qris_image')) {
            $data['qris_image'] = $request->file('qris_image')->store('website/donasi/qris', 'public');
        }

        DonationProgram::create($data);

        return redirect()->route('donation-programs.index')->with('success', 'Program Donasi berhasil disimpan.');
    }

    public function show(DonationProgram $donationProgram): View
    {
        $this->authorizeProgram($donationProgram);
        $donationProgram->load(['cashAccount', 'creator', 'updater']);

        return view('admin.donation_programs.show', [
            'program' => $donationProgram,
            'existingDesignRequest' => $this->existingDesignRequest($donationProgram),
            'statuses' => DonationProgram::statuses(),
            'paymentModes' => DonationProgram::paymentModes(),
        ]);
    }

    public function edit(DonationProgram $donationProgram): View
    {
        $this->authorizeProgram($donationProgram);

        return view('admin.donation_programs.edit', [
            'program' => $donationProgram,
            'existingDesignRequest' => $this->existingDesignRequest($donationProgram),
            'cashAccounts' => $this->cashAccounts($donationProgram->cash_account_id),
            'statuses' => DonationProgram::statuses(),
            'paymentModes' => DonationProgram::paymentModes(),
        ]);
    }

    public function update(Request $request, DonationProgram $donationProgram)
    {
        $this->authorizeProgram($donationProgram);

        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $donationProgram->mosque_id, $donationProgram->id);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['show_on_public'] = $request->boolean('show_on_public');
        $data['collected_amount'] = $data['collected_amount'] ?? 0;
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $newFeaturedImage = $request->file('featured_image')->store('website/donasi/programs', 'public');
            $oldFeaturedImage = $donationProgram->featured_image;
            $data['featured_image'] = $newFeaturedImage;
        }

        if ($request->hasFile('qris_image')) {
            $newQrisImage = $request->file('qris_image')->store('website/donasi/qris', 'public');
            $oldQrisImage = $donationProgram->qris_image;
            $data['qris_image'] = $newQrisImage;
        }

        $donationProgram->update($data);

        if (isset($oldFeaturedImage)) {
            $this->deletePublicFile($oldFeaturedImage);
        }

        if (isset($oldQrisImage)) {
            $this->deletePublicFile($oldQrisImage);
        }

        return redirect()->route('donation-programs.index')->with('success', 'Program Donasi berhasil diperbarui.');
    }

    public function destroy(DonationProgram $donationProgram)
    {
        $this->authorizeProgram($donationProgram);
        $donationProgram->delete();

        return redirect()->route('donation-programs.index')->with('success', 'Program Donasi berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $mosqueId = $this->activeMosqueId();

        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'description' => 'required|string',
            'category' => 'nullable|string|max:100',
            'target_amount' => 'nullable|numeric|min:0',
            'collected_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:150',
            'whatsapp_number' => 'nullable|string|max:30',
            'status' => ['required', Rule::in(array_keys(DonationProgram::statuses()))],
            'is_featured' => 'nullable|boolean',
            'show_on_public' => 'nullable|boolean',
            'payment_mode' => ['required', Rule::in(array_keys(DonationProgram::paymentModes()))],
            'cash_account_id' => [
                'nullable',
                Rule::exists('cash_accounts', 'id')->where('mosque_id', $mosqueId),
            ],
        ]);
    }

    private function activeMosqueId(): int
    {
        return (int) (session('active_mosque_id') ?: auth()->user()?->active_mosque_id);
    }

    private function cashAccounts(?int $includeId = null)
    {
        CashAccount::ensureDefaultsForMosque($this->activeMosqueId());

        return CashAccount::where('mosque_id', $this->activeMosqueId())
            ->where(function ($query) use ($includeId) {
                $query->where('is_active', true)
                    ->when($includeId, fn ($q) => $q->orWhere('id', $includeId));
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    private function authorizeProgram(DonationProgram $program): void
    {
        abort_unless((int) $program->mosque_id === $this->activeMosqueId(), 404);
    }

    private function existingDesignRequest(DonationProgram $program): ?DesignRequest
    {
        return DesignRequest::where('mosque_id', $this->activeMosqueId())
            ->where('source_type', 'donasi')
            ->where('source_id', $program->id)
            ->latest()
            ->first();
    }

    private function uniqueSlug(?string $slug, string $title, int $mosqueId, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug ?: $title) ?: 'program-donasi';
        $candidate = $baseSlug;
        $counter = 2;

        while (DonationProgram::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '<>', $ignoreId))
            ->exists()) {
            $candidate = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
