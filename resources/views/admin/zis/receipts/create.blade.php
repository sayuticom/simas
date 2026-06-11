@extends('layouts.admin')

@section('title', 'Tambah Penerimaan ZIS - SIMAS')
@section('page_title', 'Tambah Penerimaan ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('zis.receipts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('admin.zis.receipts.form', ['receipt' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('zis.receipts.index') }}" class="rounded-lg border px-5 py-3 text-gray-700">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white">Simpan</button>
        </div>
    </form>
</div>
@endsection
