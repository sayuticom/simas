<?php

namespace App\Http\Controllers;

use App\Models\ZisCategory;
use App\Models\ZisDistribution;
use App\Models\ZisReceipt;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ZisReportController extends Controller
{
    public function index(Request $request): View
    {
        $mosqueId = $this->activeMosqueId();
        ZisCategory::ensureDefaultsForMosque($mosqueId);

        $filters = $this->filters($request, $mosqueId);
        $categories = ZisCategory::where('mosque_id', $mosqueId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $receiptQuery = $this->filteredReceipts($mosqueId, $filters);
        $distributionQuery = $this->filteredDistributions($mosqueId, $filters);

        $totalReceipts = (float) (clone $receiptQuery)->sum('amount');
        $totalDistributions = (float) (clone $distributionQuery)->sum('amount');
        $remainingBalance = $totalReceipts - $totalDistributions;

        $totalZakat = $this->receiptTotalByType($mosqueId, $filters, ZisCategory::TYPE_ZAKAT);
        $totalInfak = $this->receiptTotalByType($mosqueId, $filters, ZisCategory::TYPE_INFAK);
        $totalSedekah = $this->receiptTotalByType($mosqueId, $filters, ZisCategory::TYPE_SEDEKAH);

        $receipts = collect();
        $distributions = collect();

        if ($filters['report_type'] === 'detail') {
            $receipts = (clone $receiptQuery)
                ->with(['cashAccount', 'category'])
                ->latest('receipt_date')
                ->get();

            $distributions = (clone $distributionQuery)
                ->with('category')
                ->latest('distribution_date')
                ->get();
        }

        return view('admin.zis.reports.index', [
            'categories' => $categories,
            'distributions' => $distributions,
            'filters' => $filters,
            'receipts' => $receipts,
            'remainingBalance' => $remainingBalance,
            'totalDistributions' => $totalDistributions,
            'totalInfak' => $totalInfak,
            'totalReceipts' => $totalReceipts,
            'totalSedekah' => $totalSedekah,
            'totalZakat' => $totalZakat,
            'typeOptions' => ZisCategory::TYPE_OPTIONS,
        ]);
    }

    private function filters(Request $request, int $mosqueId): array
    {
        $monthStart = CarbonImmutable::now()->startOfMonth();
        $monthEnd = CarbonImmutable::now()->endOfMonth();

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'type' => ['nullable', Rule::in(array_keys(ZisCategory::TYPE_OPTIONS))],
            'category_id' => [
                'nullable',
                Rule::exists('zis_categories', 'id')->where('mosque_id', $mosqueId),
            ],
            'report_type' => ['nullable', Rule::in(['ringkasan', 'detail'])],
        ]);

        return [
            'start_date' => $validated['start_date'] ?? $monthStart->toDateString(),
            'end_date' => $validated['end_date'] ?? $monthEnd->toDateString(),
            'type' => $validated['type'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'report_type' => $validated['report_type'] ?? 'ringkasan',
        ];
    }

    private function filteredReceipts(int $mosqueId, array $filters)
    {
        return ZisReceipt::query()
            ->where('mosque_id', $mosqueId)
            ->whereBetween('receipt_date', [$filters['start_date'], $filters['end_date']])
            ->when($filters['category_id'], fn ($query, $categoryId) => $query->where('zis_category_id', $categoryId))
            ->when($filters['type'], fn ($query, $type) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('type', $type)));
    }

    private function filteredDistributions(int $mosqueId, array $filters)
    {
        return ZisDistribution::query()
            ->where('mosque_id', $mosqueId)
            ->whereBetween('distribution_date', [$filters['start_date'], $filters['end_date']])
            ->when($filters['category_id'], fn ($query, $categoryId) => $query->where('zis_category_id', $categoryId))
            ->when($filters['type'], fn ($query, $type) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('type', $type)));
    }

    private function receiptTotalByType(int $mosqueId, array $filters, string $type): float
    {
        return (float) ZisReceipt::query()
            ->where('mosque_id', $mosqueId)
            ->whereBetween('receipt_date', [$filters['start_date'], $filters['end_date']])
            ->when($filters['category_id'], fn ($query, $categoryId) => $query->where('zis_category_id', $categoryId))
            ->whereHas('category', fn ($query) => $query->where('type', $type))
            ->sum('amount');
    }

    private function activeMosqueId(): int
    {
        return (int) session('active_mosque_id');
    }
}
