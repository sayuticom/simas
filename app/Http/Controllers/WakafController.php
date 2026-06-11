<?php

namespace App\Http\Controllers;

use App\Models\Nazhir;
use App\Models\WakafAsset;
use App\Models\WakafAssetMaintenance;
use App\Models\WakafCash;
use App\Models\WakafManagementResult;
use App\Models\WakafNonCash;
use App\Models\WakafProgram;
use App\Models\WakafProductiveAsset;
use App\Models\WakafDocument;
use App\Models\Wakif;
use Illuminate\Http\Request;

class WakafController extends Controller
{
    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('dashboard')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $totalWakif = Wakif::where('mosque_id', $mosqueId)->count();
        $totalNazhir = Nazhir::where('mosque_id', $mosqueId)->count();
        $totalPrograms = WakafProgram::where('mosque_id', $mosqueId)->count();
        $totalAssets = WakafAsset::where('mosque_id', $mosqueId)->count();
        $totalCash = WakafCash::where('mosque_id', $mosqueId)->sum('nominal');
        $totalNonCash = WakafNonCash::where('mosque_id', $mosqueId)->sum('nilai_estimasi');
        $totalResults = WakafManagementResult::where('mosque_id', $mosqueId)->sum('nominal');

        return view('admin.wakaf.dashboard', compact(
            'totalWakif',
            'totalNazhir',
            'totalPrograms',
            'totalAssets',
            'totalCash',
            'totalNonCash',
            'totalResults'
        ));
    }

    public function report(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $from = $request->query('from');
        $to = $request->query('to');

        $cashQuery = WakafCash::with(['wakif', 'nazhir', 'program'])
            ->where('mosque_id', $mosqueId)
            ->when($from, fn ($query) => $query->whereDate('tanggal_terima', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('tanggal_terima', '<=', $to));

        $nonCashQuery = WakafNonCash::with(['wakif', 'nazhir'])
            ->where('mosque_id', $mosqueId)
            ->when($from, fn ($query) => $query->whereDate('tanggal_terima', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('tanggal_terima', '<=', $to));

        $resultsQuery = WakafManagementResult::with('productiveAsset.wakafAsset')
            ->where('mosque_id', $mosqueId)
            ->when($from, fn ($query) => $query->whereDate('tanggal_penerimaan', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('tanggal_penerimaan', '<=', $to));

        $maintenanceQuery = WakafAssetMaintenance::with('wakafAsset')
            ->where('mosque_id', $mosqueId)
            ->when($from, fn ($query) => $query->whereDate('tanggal_pengeluaran', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('tanggal_pengeluaran', '<=', $to));

        $assetsQuery = WakafAsset::with('nazhir')->where('mosque_id', $mosqueId);
        $productiveAssetsQuery = WakafProductiveAsset::with('wakafAsset')->where('mosque_id', $mosqueId);
        $documentsQuery = WakafDocument::with('wakafAsset')->where('mosque_id', $mosqueId);

        $summary = [
            'totalWakif' => Wakif::where('mosque_id', $mosqueId)->count(),
            'totalNazhir' => Nazhir::where('mosque_id', $mosqueId)->count(),
            'totalPrograms' => WakafProgram::where('mosque_id', $mosqueId)->count(),
            'totalWakafCash' => (clone $cashQuery)->count(),
            'totalCashNominal' => (clone $cashQuery)->sum('nominal'),
            'totalWakafNonCash' => (clone $nonCashQuery)->count(),
            'totalNonCashValue' => (clone $nonCashQuery)->sum('nilai_estimasi'),
            'totalAssets' => (clone $assetsQuery)->count(),
            'totalAssetValue' => (clone $assetsQuery)->sum('nilai_estimasi'),
            'totalProductiveAssets' => (clone $productiveAssetsQuery)->count(),
            'totalProductiveTarget' => (clone $productiveAssetsQuery)->sum('target_pendapatan'),
            'totalManagementResults' => (clone $resultsQuery)->count(),
            'totalManagementNominal' => (clone $resultsQuery)->sum('nominal'),
            'totalMaintenances' => (clone $maintenanceQuery)->count(),
            'totalMaintenanceNominal' => (clone $maintenanceQuery)->sum('nominal'),
            'totalDocuments' => (clone $documentsQuery)->count(),
            'activeDocuments' => (clone $documentsQuery)
                ->where(function ($query) {
                    $query->whereNull('tanggal_berakhir')
                        ->orWhereDate('tanggal_berakhir', '>=', now()->toDateString());
                })
                ->count(),
            'expiredDocuments' => (clone $documentsQuery)
                ->whereNotNull('tanggal_berakhir')
                ->whereDate('tanggal_berakhir', '<', now()->toDateString())
                ->count(),
        ];

        $latestCash = (clone $cashQuery)->orderByDesc('tanggal_terima')->limit(10)->get();
        $latestNonCash = (clone $nonCashQuery)->orderByDesc('tanggal_terima')->limit(10)->get();
        $latestAssets = (clone $assetsQuery)->latest()->limit(10)->get();
        $latestResults = (clone $resultsQuery)->orderByDesc('tanggal_penerimaan')->limit(10)->get();
        $latestMaintenances = (clone $maintenanceQuery)->orderByDesc('tanggal_pengeluaran')->limit(10)->get();
        $expiringDocuments = (clone $documentsQuery)
            ->whereNotNull('tanggal_berakhir')
            ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
            ->whereDate('tanggal_berakhir', '<=', now()->addDays(30)->toDateString())
            ->orderBy('tanggal_berakhir')
            ->get();
        $expiredDocuments = (clone $documentsQuery)
            ->whereNotNull('tanggal_berakhir')
            ->whereDate('tanggal_berakhir', '<', now()->toDateString())
            ->orderByDesc('tanggal_berakhir')
            ->get();

        return view('admin.wakaf.report', compact(
            'from',
            'to',
            'summary',
            'latestCash',
            'latestNonCash',
            'latestAssets',
            'latestResults',
            'latestMaintenances',
            'expiringDocuments',
            'expiredDocuments'
        ));
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }
}
