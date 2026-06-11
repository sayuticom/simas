@extends('layouts.admin')

@section('title', 'Edit Program Donasi - SIMAS')
@section('page_title', 'Edit Program Donasi')

@section('content')
<div class="rounded-lg bg-white p-6 shadow">
    <form action="{{ route('donation-programs.update', $program) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.donation_programs._form')

        @include('admin.design_requests._source_card', [
            'sourceType' => 'donasi',
            'sourceId' => $program->id,
            'existingDesignRequest' => $existingDesignRequest ?? null,
            'returnUrl' => route('donation-programs.edit', $program, false),
        ])

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('donation-programs.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
