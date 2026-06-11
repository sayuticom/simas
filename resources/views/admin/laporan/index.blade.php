@extends('layouts.admin')

@section('title', 'Laporan Umum - SIMAS')
@section('page_title', 'Laporan Umum')

@section('content')
@php
    $statusLabels = [
        'terencana' => 'Terencana', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'batal' => 'Batal',
        'terjadwal' => 'Terjadwal', 'hadir' => 'Hadir', 'berhalangan' => 'Berhalangan',
        'draft' => 'Draft', 'terbit' => 'Terbit', 'arsip' => 'Arsip',
        'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'dipinjam' => 'Dipinjam', 'hilang' => 'Hilang', 'dihapus' => 'Dihapus',
        'kedaluwarsa' => 'Kedaluwarsa',
    ];
    $badge = [
        'terencana' => 'bg-blue-100 text-blue-700', 'berjalan' => 'bg-amber-100 text-amber-700', 'selesai' => 'bg-green-100 text-green-700', 'batal' => 'bg-red-100 text-red-700',
        'terjadwal' => 'bg-blue-100 text-blue-700', 'hadir' => 'bg-green-100 text-green-700', 'berhalangan' => 'bg-amber-100 text-amber-700',
        'draft' => 'bg-gray-100 text-gray-700', 'terbit' => 'bg-green-100 text-green-700', 'arsip' => 'bg-blue-100 text-blue-700',
        'aktif' => 'bg-green-100 text-green-700', 'nonaktif' => 'bg-gray-100 text-gray-700', 'dipinjam' => 'bg-blue-100 text-blue-700', 'hilang' => 'bg-red-100 text-red-700', 'dihapus' => 'bg-slate-100 text-slate-700',
        'kedaluwarsa' => 'bg-red-100 text-red-700',
    ];
    $cards = [
        ['title' => 'Kegiatan', 'main' => $summary['kegiatan_total'], 'note' => 'Terencana: '.$summary['kegiatan_terencana'].' | Berjalan: '.$summary['kegiatan_berjalan'].' | Selesai: '.$summary['kegiatan_selesai'].' | Batal: '.$summary['kegiatan_batal']],
        ['title' => 'Jadwal Petugas', 'main' => $summary['jadwal_total'], 'note' => 'Terjadwal: '.$summary['jadwal_terjadwal'].' | Hadir: '.$summary['jadwal_hadir'].' | Berhalangan: '.$summary['jadwal_berhalangan'].' | Selesai: '.$summary['jadwal_selesai'].' | Batal: '.$summary['jadwal_batal']],
        ['title' => 'Pengumuman', 'main' => $summary['pengumuman_total'], 'note' => 'Draft: '.$summary['pengumuman_draft'].' | Terbit: '.$summary['pengumuman_terbit'].' | Arsip: '.$summary['pengumuman_arsip'].' | Dashboard: '.$summary['pengumuman_dashboard']],
        ['title' => 'Inventaris', 'main' => $summary['inventaris_total'], 'note' => 'Jumlah barang: '.$summary['inventaris_jumlah'].' | Aktif: '.$summary['inventaris_aktif'].' | Dipinjam: '.$summary['inventaris_dipinjam'].' | Hilang: '.$summary['inventaris_hilang'].' | Rusak ringan: '.$summary['inventaris_rusak_ringan'].' | Rusak berat: '.$summary['inventaris_rusak_berat'], 'amount' => $summary['inventaris_nilai']],
        ['title' => 'Dokumen Umum', 'main' => $summary['dokumen_total'], 'note' => 'Aktif: '.$summary['dokumen_aktif'].' | Arsip: '.$summary['dokumen_arsip'].' | Kedaluwarsa: '.$summary['dokumen_kedaluwarsa'].' | Akan kedaluwarsa: '.$summary['dokumen_akan_kedaluwarsa']],
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Ringkasan Laporan Umum</h2>
                <p class="text-sm text-gray-500">Semua data dibatasi pada masjid aktif. Filter tanggal berlaku pada field tanggal utama tiap modul.</p>
            </div>
            <form action="{{ route('laporan.index') }}" method="GET" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto]">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Tanggal awal</label>
                    <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Tanggal akhir</label>
                    <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="self-end rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">Filter</button>
                <a href="{{ route('laporan.index') }}" class="self-end rounded-lg border border-gray-300 px-5 py-2 text-center text-gray-700 hover:bg-gray-50">Reset</a>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($cards as $card)
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $card['title'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $card['note'] }}</p>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ number_format($card['main']) }} data</span>
                </div>
                @if(isset($card['amount']))
                    <p class="mt-4 text-2xl font-bold text-gray-900">Rp {{ number_format($card['amount'], 0, ',', '.') }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Kegiatan Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Tanggal</th><th class="px-4 py-3 text-left">Kegiatan</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestKegiatans as $kegiatan)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $kegiatan->tanggal_mulai?->format('d-m-Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $kegiatan->nama_kegiatan }}</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$kegiatan->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabels[$kegiatan->status] ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada kegiatan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Jadwal Petugas Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Tanggal</th><th class="px-4 py-3 text-left">Tugas</th><th class="px-4 py-3 text-left">Petugas</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestJadwalPetugas as $jadwal)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $jadwal->tanggal?->format('d-m-Y') ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $jadwal->jenis_tugas }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $jadwal->nama_petugas_label }}</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$jadwal->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabels[$jadwal->status] ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-3 text-gray-500">Belum ada jadwal petugas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Pengumuman Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Tanggal</th><th class="px-4 py-3 text-left">Judul</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestPengumumans as $pengumuman)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $pengumuman->tanggal_mulai?->format('d-m-Y') ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $pengumuman->judul }}</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$pengumuman->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabels[$pengumuman->status] ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada pengumuman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Inventaris Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Barang</th><th class="px-4 py-3 text-left">Jumlah</th><th class="px-4 py-3 text-right">Nilai</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestInventaris as $item)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($item->jumlah) }} {{ $item->satuan }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format((float) $item->nilai_perolehan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$item->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabels[$item->status] ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-3 text-gray-500">Belum ada inventaris.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 xl:col-span-2">
            <h3 class="text-base font-bold text-gray-800">10 Dokumen Umum Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Judul</th><th class="px-4 py-3 text-left">Jenis</th><th class="px-4 py-3 text-left">Tanggal</th><th class="px-4 py-3 text-left">Masa Berlaku</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestDokumens as $dokumen)
                            @php $isExpired = $dokumen->tanggal_berakhir && $dokumen->tanggal_berakhir->isPast(); @endphp
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $dokumen->judul }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $dokumen->jenis_dokumen ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $dokumen->tanggal_dokumen?->format('d-m-Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    @if(! $dokumen->tanggal_berakhir)
                                        Tidak ada masa berlaku
                                    @elseif($isExpired)
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Kedaluwarsa</span>
                                    @else
                                        {{ $dokumen->tanggal_berakhir->format('d-m-Y') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badge[$dokumen->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabels[$dokumen->status] ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-3 text-gray-500">Belum ada dokumen umum.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
