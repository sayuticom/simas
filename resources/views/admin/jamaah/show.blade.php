@extends('layouts.admin')

@section('title', 'Detail Jamaah - SIMAS')
@section('page_title', 'Detail Jamaah')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $jamaah->nama }}</h2>
            <p class="text-sm text-gray-500">Detail lengkap data jamaah.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('jamaah.edit', $jamaah) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700 transition"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('jamaah.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Nama</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->nama }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Jenis Kelamin</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->jenis_kelamin }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Kategori</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse($jamaah->categories as $category)
                        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">{{ $category->label }}</span>
                    @empty
                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-600">
                            {{ \App\Models\Jamaah::CATEGORY_OPTIONS[$jamaah->kategori] ?? $jamaah->kategori }}
                        </span>
                    @endforelse
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Status Verifikasi</p>
                <p class="text-lg font-semibold text-gray-800">{{ \App\Models\Jamaah::STATUS_OPTIONS[$jamaah->status] ?? ucfirst($jamaah->status) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">No. WhatsApp/Telepon</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->no_hp ?: '-' }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Tanggal Lahir</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->tanggal_lahir?->format('d-m-Y') ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Umur</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->umur_tampilan !== null ? $jamaah->umur_tampilan . ' tahun' : '-' }}</p>
                @if($jamaah->tanggal_lahir)
                    <p class="text-xs text-gray-500">Dihitung dari tanggal lahir.</p>
                @endif
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Pekerjaan</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->pekerjaan ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Keahlian</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->keahlian ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <p class="text-sm text-gray-500">Alamat</p>
                <p class="text-lg font-semibold text-gray-800">{{ $jamaah->alamat ?: '-' }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $jamaah->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
