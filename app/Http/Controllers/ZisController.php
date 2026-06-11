<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\ZisCategory;
use App\Models\ZisDistribution;
use App\Models\ZisReceipt;

class ZisController extends Controller
{
    public function index()
    {
        $mosqueId = (int) session('active_mosque_id');
        CashAccount::ensureDefaultsForMosque($mosqueId);
        ZisCategory::ensureDefaultsForMosque($mosqueId);

        $totalReceipts = ZisReceipt::where('mosque_id', $mosqueId)->sum('amount');
        $totalDistributions = ZisDistribution::where('mosque_id', $mosqueId)->sum('amount');
        $remainingBalance = $totalReceipts - $totalDistributions;
        $totalZakat = $this->receiptTotalByType($mosqueId, ZisCategory::TYPE_ZAKAT);
        $totalInfak = $this->receiptTotalByType($mosqueId, ZisCategory::TYPE_INFAK);
        $totalSedekah = $this->receiptTotalByType($mosqueId, ZisCategory::TYPE_SEDEKAH);
        $categorySummaries = $this->categorySummaries($mosqueId);
        $accountBalances = $this->accountBalances($mosqueId);

        return view('admin.zis.dashboard', compact('accountBalances', 'totalReceipts', 'totalDistributions', 'remainingBalance', 'totalZakat', 'totalInfak', 'totalSedekah', 'categorySummaries'));
    }

    private function receiptTotalByType(int $mosqueId, string $type): float
    {
        return (float) ZisReceipt::where('mosque_id', $mosqueId)
            ->whereHas('category', fn ($query) => $query->where('type', $type))
            ->sum('amount');
    }

    private function categorySummaries(int $mosqueId)
    {
        return ZisCategory::withSum([
            'receipts as total_receipts' => fn ($query) => $query->where('mosque_id', $mosqueId),
        ], 'amount')
            ->withSum([
                'distributions as total_distributions' => fn ($query) => $query->where('mosque_id', $mosqueId),
            ], 'amount')
            ->where('mosque_id', $mosqueId)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function (ZisCategory $category) {
                $category->total_receipts = (float) ($category->total_receipts ?? 0);
                $category->total_distributions = (float) ($category->total_distributions ?? 0);
                $category->balance = $category->total_receipts - $category->total_distributions;

                return $category;
            })
            ->filter(fn (ZisCategory $category) => $category->is_active
                || $category->total_receipts > 0
                || $category->total_distributions > 0
                || $category->balance !== 0.0)
            ->values();
    }

    private function accountBalances(int $mosqueId)
    {
        return CashAccount::where('mosque_id', $mosqueId)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function (CashAccount $account) use ($mosqueId) {
                $account->zis_balance = $account->zisBalance();
                $account->transfer_balance = $account->transferBalance();
                $account->available_balance = $account->availableBalance();

                return $account;
            });
    }
}
