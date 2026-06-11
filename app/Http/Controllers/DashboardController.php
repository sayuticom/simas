<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\Jamaah;
use App\Models\Mosque;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\WebsiteSetting;
use App\Models\ZisDistribution;
use App\Models\ZisReceipt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display dashboard page
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $availableMosques = $user?->selectableMosques() ?? collect();
        $activeMosque = $user ? $user->activeMosque()->with(['profile', 'users.roles'])->first() : null;
        $totalJamaah = Jamaah::withoutGlobalScope('mosque')->count();

        if ($user && ! $user->isSuperuser() && ! $activeMosque && $availableMosques->count() === 1) {
            $user->setActiveMosque($availableMosques->first()->id);
            $request->session()->put('active_mosque_id', $availableMosques->first()->id);
            $activeMosque = $user->activeMosque()->with(['profile', 'users.roles'])->first();
        }

        $financialSummary = $this->financialSummaryFor($activeMosque?->id);
        $websiteSetting = $activeMosque
            ? WebsiteSetting::withoutGlobalScopes()
                ->where('mosque_id', $activeMosque->id)
                ->first()
            : null;
        $publicWebsiteUrl = $websiteSetting?->subdomain
            ? $websiteSetting->publicUrl('http')
            : null;

        $pengurus = $activeMosque
            ? $this->pengurusFor($activeMosque)
            : $this->emptyPengurus();

        $showMosqueList = $user && (
            ($user->isSuperuser() && ! $activeMosque)
            || (! $user->isSuperuser() && $availableMosques->count() > 1 && (! $activeMosque || $request->boolean('choose_mosque')))
        );

        if ($showMosqueList) {
            $mosques = $user->isSuperuser()
                ? Mosque::with(['profile', 'users.roles'])->get()
                : Mosque::with(['profile', 'users.roles'])->whereIn('id', $availableMosques->pluck('id'))->get();
            $mosquePengurus = $mosques->mapWithKeys(
                fn (Mosque $mosque) => [$mosque->id => $this->pengurusFor($mosque)]
            );
            $totalManagers = $mosques->flatMap->users->unique('id')->count();
            $totalContacts = $mosques->filter(fn ($mosque) => $mosque->address || $mosque->phone)->count();

            return view('admin.dashboard', compact('mosques', 'mosquePengurus', 'totalManagers', 'totalContacts', 'activeMosque', 'pengurus', 'totalJamaah', 'financialSummary', 'websiteSetting', 'publicWebsiteUrl'));
        }

        return view('admin.dashboard', compact('activeMosque', 'pengurus', 'availableMosques', 'totalJamaah', 'financialSummary', 'websiteSetting', 'publicWebsiteUrl'));
    }

    private function emptyPengurus(): array
    {
        return [
            Role::KETUA_DKM => '-',
            Role::BENDAHARA => '-',
            Role::SEKRETARIS => '-',
        ];
    }

    private function pengurusFor(Mosque $mosque): array
    {
        $pengurus = $this->emptyPengurus();

        if ($mosque->profile) {
            $pengurus[Role::KETUA_DKM] = $mosque->profile->nama_ketua_dkm ?: '-';
            $pengurus[Role::BENDAHARA] = $mosque->profile->nama_bendahara ?: '-';
            $pengurus[Role::SEKRETARIS] = $mosque->profile->nama_sekretaris ?: '-';
        }

        if (! in_array('-', $pengurus, true)) {
            return $pengurus;
        }

        foreach ($mosque->users as $userItem) {
            foreach ($userItem->roles as $role) {
                if ((int) $role->pivot->mosque_id !== (int) $mosque->id) {
                    continue;
                }

                if (array_key_exists($role->name, $pengurus) && $pengurus[$role->name] === '-') {
                    $pengurus[$role->name] = $userItem->name;
                }
            }
        }

        return $pengurus;
    }

    private function financialSummaryFor(?int $mosqueId): array
    {
        if (! $mosqueId) {
            return [
                'totalMasuk' => 0,
                'totalKeluar' => 0,
                'saldoKeuangan' => 0,
                'saldoZis' => 0,
                'totalDanaTerkelola' => 0,
                'positionByType' => [
                    CashAccount::TYPE_TUNAI => 0,
                    CashAccount::TYPE_BANK => 0,
                    CashAccount::TYPE_QRIS => 0,
                ],
            ];
        }

        CashAccount::ensureDefaultsForMosque($mosqueId);

        $totalMasuk = Transaction::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->where('type', 'masuk')
            ->sum('amount');

        $totalKeluar = Transaction::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->where('type', 'keluar')
            ->sum('amount');

        $totalPenerimaanZis = ZisReceipt::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->sum('amount');

        $totalPenyaluranZis = ZisDistribution::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->sum('amount');

        $saldoKeuangan = $totalMasuk - $totalKeluar;
        $saldoZis = $totalPenerimaanZis - $totalPenyaluranZis;
        $positionByType = $this->cashPositionByType($mosqueId);

        return [
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'saldoKeuangan' => $saldoKeuangan,
            'saldoZis' => $saldoZis,
            'totalDanaTerkelola' => $saldoKeuangan + $saldoZis,
            'positionByType' => $positionByType,
        ];
    }

    private function cashPositionByType(int $mosqueId): array
    {
        $positions = [
            CashAccount::TYPE_TUNAI => 0,
            CashAccount::TYPE_BANK => 0,
            CashAccount::TYPE_QRIS => 0,
        ];

        CashAccount::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->get()
            ->each(function (CashAccount $account) use (&$positions, $mosqueId) {
                $operationalMasuk = Transaction::withoutGlobalScope('mosque')
                    ->where('mosque_id', $mosqueId)
                    ->where('cash_account_id', $account->id)
                    ->where('type', 'masuk')
                    ->sum('amount');
                $operationalKeluar = Transaction::withoutGlobalScope('mosque')
                    ->where('mosque_id', $mosqueId)
                    ->where('cash_account_id', $account->id)
                    ->where('type', 'keluar')
                    ->sum('amount');
                $zisMasuk = ZisReceipt::withoutGlobalScope('mosque')
                    ->where('mosque_id', $mosqueId)
                    ->where('cash_account_id', $account->id)
                    ->sum('amount');
                $zisKeluar = ZisDistribution::withoutGlobalScope('mosque')
                    ->where('mosque_id', $mosqueId)
                    ->where('cash_account_id', $account->id)
                    ->sum('amount');

                $transferMasuk = $account->incomingTransfers()->sum('amount');
                $transferKeluar = $account->outgoingTransfers()->sum('amount');

                if (array_key_exists($account->type, $positions)) {
                    $positions[$account->type] += ($operationalMasuk - $operationalKeluar) + ($zisMasuk - $zisKeluar) + ($transferMasuk - $transferKeluar);
                }
            });

        return $positions;
    }
}
