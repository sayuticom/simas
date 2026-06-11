@extends('layouts.admin')

@section('title', 'Tambah Penyaluran ZIS - SIMAS')
@section('page_title', 'Tambah Penyaluran ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Penyaluran ZIS belum bisa disimpan:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('zis.distributions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" novalidate>
        @csrf
        @include('admin.zis.distributions.form', ['distribution' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('zis.distributions.index') }}" class="rounded-lg border px-5 py-3 text-gray-700">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white">Simpan</button>
        </div>
    </form>
</div>
@endsection
