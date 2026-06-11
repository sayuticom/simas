@extends('layouts.admin')

@section('title', 'Modul Sedang Disiapkan - SIMAS')
@section('page_title', 'Modul Sedang Disiapkan')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="mb-6">
            <i class="fas fa-tools text-6xl text-gray-400"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Modul Sedang Disiapkan</h1>
        <p class="text-gray-600 text-lg mb-8">Fitur ini sedang dalam tahap pengembangan dan akan segera tersedia.</p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 max-w-md mx-auto mb-8">
            <p class="text-blue-800 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                Tim pengembang sedang bekerja keras untuk menyiapkan modul ini. Mohon ditunggu perkembangannya.
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
