@extends('layouts.admin')

@section('title', 'Dashboard Wakaf')
@section('page_title', 'Modul Wakaf')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500">Total Wakif</h3>
                <p class="mt-4 text-3xl font-bold text-indigo-600">{{ number_format($totalWakif) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500">Total Nazhir</h3>
                <p class="mt-4 text-3xl font-bold text-indigo-600">{{ number_format($totalNazhir) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500">Program Wakaf</h3>
                <p class="mt-4 text-3xl font-bold text-indigo-600">{{ number_format($totalPrograms) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500">Aset Wakaf</h3>
                <p class="mt-4 text-3xl font-bold text-indigo-600">{{ number_format($totalAssets) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500">Total Wakaf Tunai</h3>
                <p class="mt-4 text-3xl font-bold text-green-600">Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500">Total Wakaf Non-Tunai</h3>
                <p class="mt-4 text-3xl font-bold text-yellow-600">Rp {{ number_format($totalNonCash, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500">Total Hasil Kelola</h3>
                <p class="mt-4 text-3xl font-bold text-green-700">Rp {{ number_format($totalResults, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('wakaf.wakifs.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Wakif</h4>
                <p class="mt-2 text-sm text-gray-600">Kelola data pemberi wakaf.</p>
            </a>
            <a href="{{ route('wakaf.nazhirs.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Nazhir</h4>
                <p class="mt-2 text-sm text-gray-600">Kelola data pengelola wakaf.</p>
            </a>
            <a href="{{ route('wakaf.programs.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Program Wakaf</h4>
                <p class="mt-2 text-sm text-gray-600">Kelola program dan tujuan wakaf.</p>
            </a>
            <a href="{{ route('wakaf.cash.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Wakaf Tunai</h4>
                <p class="mt-2 text-sm text-gray-600">Catat wakaf tunai dan bukti transfer.</p>
            </a>
            <a href="{{ route('wakaf.non-cash.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Wakaf Non-Tunai</h4>
                <p class="mt-2 text-sm text-gray-600">Kelola aset wakaf non-tunai.</p>
            </a>
            <a href="{{ route('wakaf.assets.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Aset Wakaf</h4>
                <p class="mt-2 text-sm text-gray-600">Lihat dan kelola aset wakaf.</p>
            </a>
            <a href="{{ route('wakaf.productive-assets.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Aset Produktif</h4>
                <p class="mt-2 text-sm text-gray-600">Atur aset wakaf produktif.</p>
            </a>
            <a href="{{ route('wakaf.management-results.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Hasil Kelola</h4>
                <p class="mt-2 text-sm text-gray-600">Catat hasil pengelolaan wakaf.</p>
            </a>
            <a href="{{ route('wakaf.asset-maintenances.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Perawatan Aset</h4>
                <p class="mt-2 text-sm text-gray-600">Kelola biaya perawatan aset wakaf.</p>
            </a>
            <a href="{{ route('wakaf.documents.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Dokumen Wakaf</h4>
                <p class="mt-2 text-sm text-gray-600">Simpan dokumen wakaf penting.</p>
            </a>
            <a href="{{ route('wakaf.report') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                <h4 class="font-semibold text-gray-800">Laporan Wakaf</h4>
                <p class="mt-2 text-sm text-gray-600">Lihat ringkasan laporan wakaf.</p>
            </a>
        </div>
    </div>
@endsection
