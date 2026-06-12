<?php

namespace App\Http\Controllers;

use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransactionCategoryController extends Controller
{
    public function index(): View
    {
        // Only superuser may manage categories
        abort_unless(auth()->user()->isSuperuser(), 403);

        $mosqueId = $this->activeMosqueId();
        // Ensure default categories exist across all mosques
        TransactionCategory::ensureDefaultsForAllMosques();

        $categories = TransactionCategory::where('mosque_id', $mosqueId)
            ->where('type', TransactionCategory::TYPE_KELUAR)
            ->orderBy('name')
            ->get();

        $typeOptions = TransactionCategory::TYPE_OPTIONS;

        return view('admin.keuangan.categories.index', compact('categories', 'typeOptions'));
    }

    public function create(Request $request): View
    {
        abort_unless(auth()->user()->isSuperuser(), 403);

        $typeOptions = TransactionCategory::TYPE_OPTIONS;
        $returnTo = $request->query('return_to');
        $transactionId = $request->query('transaction_id');

        return view('admin.keuangan.categories.create', compact('typeOptions', 'returnTo', 'transactionId'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isSuperuser(), 403);

        $mosqueId = $this->activeMosqueId();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('transaction_categories', 'name')->where(fn ($query) => $query->where('mosque_id', $mosqueId)),
            ],
            'type' => ['required', Rule::in(array_keys(TransactionCategory::TYPE_OPTIONS))],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'return_to' => 'nullable|in:transaction_create,transaction_edit',
            'transaction_id' => 'nullable|required_if:return_to,transaction_edit|integer|exists:transactions,id',
        ]);

        $isReturningToTransactionForm = in_array($data['return_to'] ?? null, ['transaction_create', 'transaction_edit'], true);

        if ($isReturningToTransactionForm && $data['type'] !== TransactionCategory::TYPE_KELUAR) {
            throw ValidationException::withMessages([
                'type' => 'Kategori untuk transaksi manual harus bertipe keluar.',
            ]);
        }

        $category = TransactionCategory::create([
            'mosque_id' => $mosqueId,
            'name' => $data['name'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'is_active' => $this->isAllowedActiveCategory($data['type'], $data['name'], $isReturningToTransactionForm || $request->boolean('is_active', true)),
        ]);

        if (($data['return_to'] ?? null) === 'transaction_create') {
            return redirect()
                ->route('keuangan.transaksi.create', ['category_id' => $category->id])
                ->with('success', 'Kategori keuangan berhasil ditambahkan. Silakan lanjutkan transaksi.');
        }

        if (($data['return_to'] ?? null) === 'transaction_edit') {
            $transactionId = (int) ($data['transaction_id'] ?? 0);

            return redirect()
                ->route('keuangan.transaksi.edit', [
                    'transaction' => $transactionId,
                    'category_id' => $category->id,
                ])
                ->with('success', 'Kategori keuangan berhasil ditambahkan. Silakan lanjutkan edit transaksi.');
        }

        return redirect()->route('keuangan.kategori.index')->with('success', 'Kategori keuangan berhasil ditambahkan.');
    }

    public function edit(TransactionCategory $category): View
    {
        abort_unless(auth()->user()->isSuperuser(), 403);
        $this->ensureOwnCategory($category);
        $typeOptions = TransactionCategory::TYPE_OPTIONS;

        return view('admin.keuangan.categories.edit', compact('category', 'typeOptions'));
    }

    public function update(Request $request, TransactionCategory $category)
    {
        abort_unless(auth()->user()->isSuperuser(), 403);
        $this->ensureOwnCategory($category);
        $mosqueId = $this->activeMosqueId();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('transaction_categories', 'name')
                    ->ignore($category->id)
                    ->where(fn ($query) => $query->where('mosque_id', $mosqueId)),
            ],
            'type' => ['required', Rule::in(array_keys(TransactionCategory::TYPE_OPTIONS))],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'is_active' => $this->isAllowedActiveCategory($data['type'], $data['name'], $request->boolean('is_active')),
        ]);

        return redirect()->route('keuangan.kategori.index')->with('success', 'Kategori keuangan berhasil diperbarui.');
    }

    public function destroy(TransactionCategory $category)
    {
        abort_unless(auth()->user()->isSuperuser(), 403);
        $this->ensureOwnCategory($category);

        if ($category->transactions()->exists()) {
            $category->update(['is_active' => false]);

            return redirect()->route('keuangan.kategori.index')->with('success', 'Kategori sudah dipakai transaksi, sehingga dinonaktifkan.');
        }

        $category->delete();

        return redirect()->route('keuangan.kategori.index')->with('success', 'Kategori keuangan berhasil dihapus.');
    }

    private function activeMosqueId(): int
    {
        return (int) session('active_mosque_id');
    }

    private function ensureOwnCategory(TransactionCategory $category): void
    {
        abort_unless((int) $category->mosque_id === $this->activeMosqueId(), 404);
    }

    private function isAllowedActiveCategory(string $type, string $name, bool $requestedActive): bool
    {
        if ($type === TransactionCategory::TYPE_MASUK && $name !== 'Transfer dari ZIS') {
            return false;
        }

        return $requestedActive;
    }
}
