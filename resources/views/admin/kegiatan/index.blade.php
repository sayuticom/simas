@extends('layouts.admin')

@section('title', 'Kegiatan Masjid - SIMAS')
@section('page_title', 'Kegiatan Masjid')

@section('content')
@php
    $statusLabels = [
        'terencana' => 'Terencana',
        'berjalan' => 'Berjalan',
        'selesai' => 'Selesai',
        'batal' => 'Batal',
    ];
    $statusClasses = [
        'terencana' => 'bg-blue-100 text-blue-700 border-blue-200',
        'berjalan' => 'bg-amber-100 text-amber-700 border-amber-200',
        'selesai' => 'bg-green-100 text-green-700 border-green-200',
        'batal' => 'bg-red-100 text-red-700 border-red-200',
    ];
    $websiteStatusLabels = [
        'draft' => 'Draft Website',
        'tayang' => 'Tayang di Website',
        'arsip' => 'Arsip Website',
    ];
    $websiteStatusClasses = [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'tayang' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'arsip' => 'bg-blue-100 text-blue-800 border-blue-200',
        'hidden' => 'bg-gray-100 text-gray-700 border-gray-200',
    ];
    $summaryCards = [
        ['label' => 'Total Kegiatan', 'value' => $totalKegiatan, 'icon' => 'fa-calendar-check', 'class' => 'border-indigo-200 bg-indigo-50 text-indigo-700'],
        ['label' => 'Hari Ini', 'value' => $kegiatanHariIni, 'icon' => 'fa-calendar-day', 'class' => 'border-sky-200 bg-sky-50 text-sky-700'],
        ['label' => 'Minggu Ini', 'value' => $kegiatanMingguIni, 'icon' => 'fa-calendar-week', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700'],
        ['label' => 'Bulan Ini', 'value' => $kegiatanBulanIni, 'icon' => 'fa-calendar-days', 'class' => 'border-violet-200 bg-violet-50 text-violet-700'],
        ['label' => 'Selesai', 'value' => $kegiatanSelesai, 'icon' => 'fa-circle-check', 'class' => 'border-green-200 bg-green-50 text-green-700'],
        ['label' => 'Batal', 'value' => $kegiatanBatal, 'icon' => 'fa-circle-xmark', 'class' => 'border-red-200 bg-red-50 text-red-700'],
    ];
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
    #kegiatan-calendar .fc {
        font-size: 0.875rem;
    }
    #kegiatan-calendar .fc-toolbar-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
    }
    #kegiatan-calendar .fc-button {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
        box-shadow: none;
        text-transform: capitalize;
    }
    #kegiatan-calendar .fc-button-primary:not(:disabled).fc-button-active,
    #kegiatan-calendar .fc-button-primary:not(:disabled):active {
        border-color: #4f46e5;
        background: #4f46e5;
        color: #ffffff;
    }
    #kegiatan-calendar .fc-event {
        border: 0;
        border-radius: 0.375rem;
        padding: 0.125rem 0.25rem;
        min-height: 0;
    }
    #kegiatan-calendar .fc-event-main,
    #kegiatan-calendar .fc-event-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #kegiatan-calendar .fc-daygrid-event {
        padding: 2px 4px;
        font-size: 11px;
        line-height: 1.2;
    }
    #kegiatan-calendar .fc-daygrid-day-frame {
        min-height: 95px;
    }
    #kegiatan-calendar .fc-daygrid-day-events {
        margin-top: 4px;
    }
    #kegiatan-calendar .fc-daygrid-more-link {
        font-size: 11px;
        font-weight: 600;
        color: #4f46e5;
    }
    #kegiatan-calendar .fc-list-event-title,
    #kegiatan-calendar .fc-list-event-time {
        font-size: 0.8125rem;
    }
    @media (max-width: 640px) {
        #kegiatan-calendar .fc {
            font-size: 0.75rem;
        }
        #kegiatan-calendar .fc-toolbar {
            align-items: flex-start;
            flex-direction: column;
            gap: 0.75rem;
        }
        #kegiatan-calendar .fc-toolbar-title {
            font-size: 1rem;
        }
        #kegiatan-calendar .fc-button {
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kegiatan Masjid</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola agenda, jadwal, dan petugas kegiatan masjid.</p>
            </div>
            <a href="{{ route('kegiatan.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        @foreach($summaryCards as $card)
            <div class="rounded-lg border bg-white p-4 {{ $card['class'] }}">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold">{{ number_format($card['value'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-white/75 text-lg">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3 xl:items-start">
        <div class="rounded-lg bg-white p-5 shadow xl:col-span-2">
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Kalender Agenda</h3>
                    <p class="text-sm text-gray-500">Klik agenda untuk membuka detail kegiatan.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs">
                    @foreach($statusLabels as $value => $label)
                        <span class="inline-flex rounded-full border px-3 py-1 font-semibold {{ $statusClasses[$value] }}">{{ $label }}</span>
                    @endforeach
                </div>
            </div>
            <div id="kegiatan-calendar" class="min-h-[560px]"></div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow xl:col-span-1 xl:max-h-[720px] xl:overflow-y-auto">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900">Agenda Mendatang</h3>
                <p class="text-sm text-gray-500">Kegiatan terdekat dari hari ini.</p>
            </div>

            <div class="space-y-3">
                @forelse($agendaMendatang as $agenda)
                    <a href="{{ route('kegiatan.show', $agenda) }}" class="block rounded-lg border border-gray-200 p-3 hover:border-indigo-300 hover:bg-indigo-50">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold leading-snug text-gray-900">{{ $agenda->nama_kegiatan }}</p>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $agenda->tanggal_mulai?->format('d-m-Y H:i') ?? '-' }}
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClasses[$agenda->status] ?? $statusClasses['terencana'] }}">
                                {{ $statusLabels[$agenda->status] ?? 'Terencana' }}
                            </span>
                        </div>
                        <div class="mt-2 grid gap-1.5 text-sm text-gray-600">
                            <span><i class="fas fa-location-dot mr-2 text-gray-400"></i>{{ $agenda->lokasi ?: 'Lokasi belum diisi' }}</span>
                            <span><i class="fas fa-users mr-2 text-gray-400"></i>Petugas: {{ $agenda->jadwal_petugas_count }}</span>
                        </div>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                        Belum ada agenda mendatang.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Daftar Kegiatan</h3>
                <p class="text-sm text-gray-500">Gunakan filter untuk mencari kegiatan tertentu.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('kegiatan.index') }}" class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="q" class="mb-1 block text-sm font-medium text-gray-700">Cari</label>
                    <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama, lokasi, narasumber" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="jenis_kegiatan" class="mb-1 block text-sm font-medium text-gray-700">Jenis Kegiatan</label>
                    <select id="jenis_kegiatan" name="jenis_kegiatan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisKegiatanOptions as $jenis)
                            <option value="{{ $jenis }}" @selected(($filters['jenis_kegiatan'] ?? '') === $jenis)>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bulan" class="mb-1 block text-sm font-medium text-gray-700">Bulan</label>
                    <input type="month" id="bulan" name="bulan" value="{{ $filters['bulan'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('kegiatan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Kegiatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Mulai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Selesai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lokasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Petugas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kegiatans as $kegiatan)
                        @php
                            $websiteStatusLabel = $kegiatan->tampilkan_di_website
                                ? ($websiteStatusLabels[$kegiatan->status_publik] ?? 'Draft Website')
                                : 'Tidak Ditampilkan';
                            $websiteStatusClass = $kegiatan->tampilkan_di_website
                                ? ($websiteStatusClasses[$kegiatan->status_publik] ?? $websiteStatusClasses['draft'])
                                : $websiteStatusClasses['hidden'];
                        @endphp
                        <tr>
                            <td class="px-4 py-4 text-sm">
                                <div class="font-semibold text-gray-900">{{ $kegiatan->nama_kegiatan }}</div>
                                <div class="mt-1 text-xs text-gray-600">{{ $kegiatan->penanggung_jawab ?: 'Penanggung jawab belum diisi' }}</div>
                                <div class="mt-2">
                                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $websiteStatusClass }}">
                                        {{ $websiteStatusLabel }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $kegiatan->jenis_kegiatan ?: '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $kegiatan->tanggal_mulai?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $kegiatan->tanggal_selesai?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $kegiatan->lokasi ?: '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">Petugas: {{ $kegiatan->jadwal_petugas_count }}</td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClasses[$kegiatan->status] ?? $statusClasses['terencana'] }}">
                                    {{ $statusLabels[$kegiatan->status] ?? 'Terencana' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('kegiatan.show', $kegiatan) }}" class="inline-flex rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100">Lihat</a>
                                    <a href="{{ route('kegiatan.edit', $kegiatan) }}" class="inline-flex rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700 hover:bg-green-100">Edit</a>
                                    <form action="{{ route('kegiatan.destroy', $kegiatan) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kegiatan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada kegiatan yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $kegiatans->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarElement = document.getElementById('kegiatan-calendar');
        const events = @json($calendarEvents);
        const statusLabels = @json($statusLabels);
        const statusColors = {
            terencana: '#2563eb',
            berjalan: '#d97706',
            selesai: '#16a34a',
            batal: '#dc2626',
        };

        if (!calendarElement || typeof FullCalendar === 'undefined') {
            return;
        }

        const calendar = new FullCalendar.Calendar(calendarElement, {
            initialView: 'dayGridMonth',
            initialDate: '{{ $calendarMonth->format('Y-m-d') }}',
            locale: 'id',
            height: 'auto',
            dayMaxEvents: 3,
            eventDisplay: 'block',
            moreLinkClick: 'popover',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek',
            },
            buttonText: {
                today: 'Hari ini',
                month: 'Bulan',
                week: 'Minggu',
                list: 'List',
            },
            events: events.map(function (event) {
                const status = event.extendedProps.status || 'terencana';
                return {
                    ...event,
                    backgroundColor: statusColors[status] || '#4f46e5',
                    borderColor: statusColors[status] || '#4f46e5',
                };
            }),
            eventContent: function (info) {
                const wrapper = document.createElement('div');
                wrapper.className = 'truncate font-semibold';
                wrapper.textContent = info.event.title;
                return { domNodes: [wrapper] };
            },
            eventClick: function (info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            },
        });

        calendar.render();
    });
</script>
@endsection
