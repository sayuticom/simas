<?php

namespace App\Http\Controllers;

use App\Models\ZisCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ZisCategoryController extends Controller
{
    public function index(): View
    {
        $mosqueId = $this->activeMosqueId();
        ZisCategory::ensureDefaultsForMosque($mosqueId);

        $categories = ZisCategory::withCount(['receipts', 'distributions'])
            ->where('mosque_id', $mosqueId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
        $typeOptions = ZisCategory::TYPE_OPTIONS;
        $usageOptions = ZisCategory::USAGE_OPTIONS;

        return view('admin.zis.categories.index', compact('categories', 'typeOptions', 'usageOptions'));
    }

    public function create(): View
    {
        $typeOptions = ZisCategory::TYPE_OPTIONS;
        $usageOptions = ZisCategory::USAGE_OPTIONS;

        return view('admin.zis.categories.create', compact('typeOptions', 'usageOptions'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zis_categories', 'name')->where(fn ($query) => $query->where('mosque_id', $mosqueId)),
            ],
            'type' => ['required', Rule::in(array_keys(ZisCategory::TYPE_OPTIONS))],
            'usage_type' => ['nullable', Rule::in(array_keys(ZisCategory::USAGE_OPTIONS))],
            'allow_operational_transfer' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $allowOperationalTransfer = $this->allowOperationalTransfer($data['type'], $request->boolean('allow_operational_transfer'));

        ZisCategory::create([
            'mosque_id' => $mosqueId,
            'name' => $data['name'],
            'type' => $data['type'],
            'usage_type' => $data['usage_type'] ?? $this->defaultUsageType($data['type']),
            'allow_operational_transfer' => $allowOperationalTransfer,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('zis.categories.index')->with('success', 'Kategori ZIS berhasil ditambahkan.');
    }

    public function edit(ZisCategory $category): View
    {
        $this->ensureOwnCategory($category);
        $typeOptions = ZisCategory::TYPE_OPTIONS;
        $usageOptions = ZisCategory::USAGE_OPTIONS;

        return view('admin.zis.categories.edit', compact('category', 'typeOptions', 'usageOptions'));
    }

    public function update(Request $request, ZisCategory $category)
    {
        $this->ensureOwnCategory($category);
        $mosqueId = $this->activeMosqueId();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zis_categories', 'name')
                    ->ignore($category->id)
                    ->where(fn ($query) => $query->where('mosque_id', $mosqueId)),
            ],
            'type' => ['required', Rule::in(array_keys(ZisCategory::TYPE_OPTIONS))],
            'usage_type' => ['nullable', Rule::in(array_keys(ZisCategory::USAGE_OPTIONS))],
            'allow_operational_transfer' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $allowOperationalTransfer = $this->allowOperationalTransfer($data['type'], $request->boolean('allow_operational_transfer'));

        $category->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'usage_type' => $data['usage_type'] ?? $this->defaultUsageType($data['type']),
            'allow_operational_transfer' => $allowOperationalTransfer,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('zis.categories.index')->with('success', 'Kategori ZIS berhasil diperbarui.');
    }

    public function destroy(ZisCategory $category)
    {
        $this->ensureOwnCategory($category);

        $usedInReceipts = $category->receipts()->exists();
        $usedInDistributions = $category->distributions()->exists();

        if ($usedInReceipts || $usedInDistributions) {
            $category->update(['is_active' => false]);

            return redirect()
                ->route('zis.categories.index')
                ->with('success', 'Kategori sudah memiliki transaksi, sehingga tidak dihapus permanen dan hanya dinonaktifkan.');
        }

        $category->delete();

        return redirect()->route('zis.categories.index')->with('success', 'Kategori ZIS berhasil dihapus.');
    }

    private function activeMosqueId(): int
    {
        return (int) session('active_mosque_id');
    }

    private function ensureOwnCategory(ZisCategory $category): void
    {
        abort_unless((int) $category->mosque_id === $this->activeMosqueId(), 404);
    }

    private function allowOperationalTransfer(string $type, bool $requested): bool
    {
        if (in_array($type, [ZisCategory::TYPE_ZAKAT, ZisCategory::TYPE_WAKAF], true)) {
            return false;
        }

        return $requested;
    }

    private function defaultUsageType(string $type): string
    {
        return match ($type) {
            ZisCategory::TYPE_ZAKAT => ZisCategory::USAGE_KHUSUS_MUSTAHIK,
            ZisCategory::TYPE_WAKAF => ZisCategory::USAGE_WAKAF,
            ZisCategory::TYPE_INFAK,
            ZisCategory::TYPE_SEDEKAH,
            ZisCategory::TYPE_DONASI,
            ZisCategory::TYPE_PENDAPATAN_LAYANAN => ZisCategory::USAGE_BEBAS_OPERASIONAL,
            default => ZisCategory::USAGE_KHUSUS_PROGRAM,
        };
    }
}
