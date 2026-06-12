@extends('layouts.admin')

@section('title', 'Tambah Penerimaan ZIS - SIMAS')
@section('page_title', 'Tambah Penerimaan ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('zis.receipts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" data-prevent-double-submit>
        @csrf
        @if(!empty($formToken))
            <input type="hidden" name="form_token" value="{{ $formToken }}">
        @endif
        @include('admin.zis.receipts.form', ['receipt' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('zis.receipts.index') }}" class="rounded-lg border px-5 py-3 text-gray-700">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white">Simpan</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form[data-prevent-double-submit]').forEach(function (form) {
                form.addEventListener('submit', function () {
                    var btn = form.querySelector('button[type=submit]');
                    if (btn) {
                        btn.disabled = true;
                        btn.dataset.orig = btn.innerHTML;
                        btn.innerHTML = 'Menyimpan...';
                    }
                });
            });
        });
    </script>
</div>
@endsection
