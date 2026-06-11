@extends('layouts.admin')

@section('title', 'Detail Program ZIS')
@section('page_title', 'Detail Program ZIS')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Nama Program</h3>
                <p class="mt-2 text-gray-700">{{ $program->nama }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Deskripsi</h3>
                <p class="mt-2 text-gray-700">{{ $program->deskripsi ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Target Dana</h3>
                <p class="mt-2 text-gray-700">Rp {{ number_format($program->target_dana, 0, ',', '.') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Status</h3>
                <p class="mt-2 text-gray-700">{{ $program->status }}</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('zis.programs.index') }}" class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200">Kembali</a>
        </div>
    </div>
@endsection
