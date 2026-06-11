@extends('layouts.admin')

@section('title', 'QR Input Jamaah Mandiri - SIMAS')
@section('page_title', 'QR Input Jamaah Mandiri')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-center">
            <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">Input Jamaah Mandiri</p>
            <h2 class="mt-2 text-2xl font-bold text-gray-800">{{ $mosque->name }}</h2>
            <p class="mt-2 text-gray-600">Silakan scan QR ini agar jamaah dapat mengisi data secara mandiri.</p>
        </div>

        <div class="mt-8 flex justify-center">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->margin(2)->generate($registrationUrl) !!}
            </div>
        </div>

        <div class="mt-8">
            <label class="block text-sm font-semibold text-gray-700">URL Form Publik</label>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <input id="qr-url" type="text" value="{{ $registrationUrl }}" readonly class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" onclick="copyQrUrl()" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">
                    <i class="fas fa-copy mr-2"></i> Salin Link
                </button>
                <a href="{{ $registrationUrl }}" target="_blank" class="inline-flex items-center justify-center rounded-lg border border-indigo-600 px-5 py-3 font-semibold text-indigo-600 hover:bg-indigo-50">
                    <i class="fas fa-up-right-from-square mr-2"></i> Buka Form
                </a>
            </div>
            <p id="copy-message" class="mt-2 hidden text-sm text-green-600">Link berhasil disalin.</p>
        </div>
    </div>
</div>

<script>
    function copyQrUrl() {
        const input = document.getElementById('qr-url');
        const message = document.getElementById('copy-message');

        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(function () {
            message.classList.remove('hidden');
            setTimeout(function () {
                message.classList.add('hidden');
            }, 2500);
        });
    }
</script>
@endsection
