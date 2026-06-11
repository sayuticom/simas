@extends('layouts.admin')

@section('title', 'Program Donasi - SIMAS')
@section('page_title', 'Program Donasi')

@section('content')
@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-700',
        'published' => 'bg-green-100 text-green-700',
        'closed' => 'bg-amber-100 text-amber-800',
        'archived' => 'bg-blue-100 text-blue-700',
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Program Donasi</h2>
                <p class="mt-1 text-sm text-gray-600">Kelola etalase donasi publik. Tahap ini masih manual, tanpa payment gateway.</p>
            </div>
            <a href="{{ route('donation-programs.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Program
            </a>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Publik</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($programs as $program)
                        @php
                            $progress = $program->progressPercentage();
                        @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <div class="flex items-start gap-3">
                                    @if($program->featured_image)
                                        <img src="{{ asset('storage/' . $program->featured_image) }}" alt="{{ $program->title }}" class="h-14 w-20 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-14 w-20 items-center justify-center rounded-lg bg-emerald-950 text-xs font-bold text-amber-100">Donasi</div>
                                    @endif
                                    <div>
                                        <p class="font-semibold leading-snug text-gray-900">{{ $program->title }}</p>
                                        <p class="mt-1 text-xs font-semibold text-gray-600">{{ $program->category ?: 'Tanpa kategori' }} | {{ $program->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$program->status] ?? $statusClasses['draft'] }}">
                                    {{ $statuses[$program->status] ?? 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <p class="font-bold text-emerald-950">Rp {{ number_format((float) $program->collected_amount, 0, ',', '.') }}</p>
                                <p class="text-xs font-semibold text-gray-600">Target: {{ $program->target_amount ? 'Rp '.number_format((float) $program->target_amount, 0, ',', '.') : '-' }}</p>
                                <div class="mt-2 h-2 w-36 rounded-full bg-gray-100">
                                    <div class="h-2 rounded-full bg-emerald-700" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="mt-1 text-xs font-semibold text-gray-600">{{ $progress }}%</p>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex flex-col gap-2">
                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $program->show_on_public ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $program->show_on_public ? 'Tampil' : 'Disembunyikan' }}
                                    </span>
                                    @if($program->is_featured)
                                        <span class="inline-flex w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Unggulan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('donation-programs.show', $program) }}" class="inline-flex rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100">Lihat</a>
                                    <a href="{{ route('donation-programs.edit', $program) }}" class="inline-flex rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700 hover:bg-green-100">Edit</a>
                                    <form action="{{ route('donation-programs.destroy', $program) }}" method="POST" class="inline" onsubmit="return confirm('Hapus program donasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada program donasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $programs->links() }}
        </div>
    </div>
</div>
@endsection
