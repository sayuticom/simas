@extends('layouts.admin')

@section('title', 'Edit Penerimaan ZIS - SIMAS')
@section('page_title', 'Edit Penerimaan ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('zis.receipts.update', $receipt) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.zis.receipts.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('zis.receipts.index') }}" class="rounded-lg border px-5 py-3 text-gray-700">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white">Simpan</button>
        </div>
    </form>
</div>
@endsection
