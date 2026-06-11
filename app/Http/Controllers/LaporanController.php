<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Inventaris;
use App\Models\JadwalPetugas;
use App\Models\Kegiatan;
use App\Models\Pengumuman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $mosqueId = $this->activeMosqueId();
        abort_unless($mosqueId, 404);

        $tanggalAwal = $request->query('tanggal_awal');
        $tanggalAkhir = $request->query('tanggal_akhir');

        $kegiatanQuery = $this->applyDateFilter(
            Kegiatan::where('mosque_id', $mosqueId),
            'tanggal_mulai',
            $tanggalAwal,
            $tanggalAkhir
        );
        $jadwalQuery = $this->applyDateFilter(
            JadwalPetugas::with(['kegiatan', 'user'])->where('mosque_id', $mosqueId),
            'tanggal',
            $tanggalAwal,
            $tanggalAkhir
        );
        $pengumumanQuery = $this->applyDateFilter(
            Pengumuman::with('kegiatan')->where('mosque_id', $mosqueId),
            'tanggal_mulai',
            $tanggalAwal,
            $tanggalAkhir
        );
        $inventarisQuery = $this->applyDateFilter(
            Inventaris::where('mosque_id', $mosqueId),
            'tanggal_perolehan',
            $tanggalAwal,
            $tanggalAkhir
        );
        $dokumenQuery = $this->applyDateFilter(
            Dokumen::where('mosque_id', $mosqueId),
            'tanggal_dokumen',
            $tanggalAwal,
            $tanggalAkhir
        );

        $summary = [
            'kegiatan_total' => (clone $kegiatanQuery)->count(),
            'kegiatan_terencana' => (clone $kegiatanQuery)->where('status', 'terencana')->count(),
            'kegiatan_berjalan' => (clone $kegiatanQuery)->where('status', 'berjalan')->count(),
            'kegiatan_selesai' => (clone $kegiatanQuery)->where('status', 'selesai')->count(),
            'kegiatan_batal' => (clone $kegiatanQuery)->where('status', 'batal')->count(),

            'jadwal_total' => (clone $jadwalQuery)->count(),
            'jadwal_terjadwal' => (clone $jadwalQuery)->where('status', 'terjadwal')->count(),
            'jadwal_hadir' => (clone $jadwalQuery)->where('status', 'hadir')->count(),
            'jadwal_berhalangan' => (clone $jadwalQuery)->where('status', 'berhalangan')->count(),
            'jadwal_selesai' => (clone $jadwalQuery)->where('status', 'selesai')->count(),
            'jadwal_batal' => (clone $jadwalQuery)->where('status', 'batal')->count(),

            'pengumuman_total' => (clone $pengumumanQuery)->count(),
            'pengumuman_draft' => (clone $pengumumanQuery)->where('status', 'draft')->count(),
            'pengumuman_terbit' => (clone $pengumumanQuery)->where('status', 'terbit')->count(),
            'pengumuman_arsip' => (clone $pengumumanQuery)->where('status', 'arsip')->count(),
            'pengumuman_dashboard' => (clone $pengumumanQuery)->where('tampil_di_dashboard', true)->count(),

            'inventaris_total' => (clone $inventarisQuery)->count(),
            'inventaris_jumlah' => (clone $inventarisQuery)->sum('jumlah'),
            'inventaris_nilai' => (clone $inventarisQuery)->sum('nilai_perolehan'),
            'inventaris_aktif' => (clone $inventarisQuery)->where('status', 'aktif')->count(),
            'inventaris_dipinjam' => (clone $inventarisQuery)->where('status', 'dipinjam')->count(),
            'inventaris_hilang' => (clone $inventarisQuery)->where('status', 'hilang')->count(),
            'inventaris_rusak_ringan' => (clone $inventarisQuery)->where('kondisi', 'rusak_ringan')->count(),
            'inventaris_rusak_berat' => (clone $inventarisQuery)->where('kondisi', 'rusak_berat')->count(),

            'dokumen_total' => (clone $dokumenQuery)->count(),
            'dokumen_aktif' => (clone $dokumenQuery)->where('status', 'aktif')->count(),
            'dokumen_arsip' => (clone $dokumenQuery)->where('status', 'arsip')->count(),
            'dokumen_kedaluwarsa' => (clone $dokumenQuery)->whereNotNull('tanggal_berakhir')->whereDate('tanggal_berakhir', '<', now()->toDateString())->count(),
            'dokumen_akan_kedaluwarsa' => (clone $dokumenQuery)->whereNotNull('tanggal_berakhir')->whereBetween('tanggal_berakhir', [now()->toDateString(), now()->addDays(30)->toDateString()])->count(),
        ];

        return view('admin.laporan.index', [
            'summary' => $summary,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'latestKegiatans' => (clone $kegiatanQuery)->latest('tanggal_mulai')->latest()->limit(10)->get(),
            'latestJadwalPetugas' => (clone $jadwalQuery)->orderByDesc('tanggal')->orderBy('waktu_mulai')->limit(10)->get(),
            'latestPengumumans' => (clone $pengumumanQuery)->latest()->limit(10)->get(),
            'latestInventaris' => (clone $inventarisQuery)->latest()->limit(10)->get(),
            'latestDokumens' => (clone $dokumenQuery)->latest()->limit(10)->get(),
        ]);
    }

    private function applyDateFilter(Builder $query, string $column, ?string $tanggalAwal, ?string $tanggalAkhir): Builder
    {
        if (! $tanggalAwal && ! $tanggalAkhir) {
            return $query;
        }

        return $query->where(function (Builder $dateQuery) use ($column, $tanggalAwal, $tanggalAkhir) {
            $dateQuery->whereNull($column)
                ->orWhere(function (Builder $boundedQuery) use ($column, $tanggalAwal, $tanggalAkhir) {
                    if ($tanggalAwal) {
                        $boundedQuery->whereDate($column, '>=', $tanggalAwal);
                    }
                    if ($tanggalAkhir) {
                        $boundedQuery->whereDate($column, '<=', $tanggalAkhir);
                    }
                });
        });
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }
}
