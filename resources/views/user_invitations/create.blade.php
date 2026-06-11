@extends('layouts.admin')

@section('title', 'Buat Undangan User - SIMAS')
@section('page_title', 'Buat Undangan User')

@section('content')
<div class="max-w-3xl bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800">Undangan Baru</h3>
        <p class="mt-1 text-sm text-gray-500">Link undangan dapat dikirim ke calon user melalui WhatsApp setelah tersimpan.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Periksa kembali data undangan.</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user-invitations.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama Calon User <span class="font-normal text-gray-400">(opsional)</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </div>
            <div>
                <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">Nomor WhatsApp</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08123456789" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <p class="mt-1 text-xs text-gray-500">Format 08, +62, atau 62 akan dinormalisasi menjadi 62.</p>
            </div>
        </div>

        @if($canInviteSuperuser)
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <label class="flex items-start gap-3">
                    <input type="checkbox" name="is_superuser" id="is_superuser" value="1" {{ old('is_superuser') ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">Undangan Superuser</span>
                        <span class="block text-xs text-gray-500">Gunakan akses global tanpa memilih masjid. Hanya super superuser yang dapat membuat pilihan ini.</span>
                    </span>
                </label>
            </div>
        @endif

        <div id="mosque-access-fields" class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="mosque_id" class="mb-2 block text-sm font-semibold text-gray-700">Masjid</label>
                <select name="mosque_id" id="mosque_id" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="">Pilih masjid</option>
                    @foreach($mosques as $mosque)
                        <option value="{{ $mosque->id }}" {{ (string) old('mosque_id') === (string) $mosque->id ? 'selected' : '' }}>{{ $mosque->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="role_id" class="mb-2 block text-sm font-semibold text-gray-700">Role</label>
                <select name="role_id" id="role_id" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="">Pilih role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ (string) old('role_id') === (string) $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="expires_in_days" class="mb-2 block text-sm font-semibold text-gray-700">Masa Berlaku Link (hari)</label>
            <input type="number" name="expires_in_days" id="expires_in_days" value="{{ old('expires_in_days', 3) }}" min="1" max="30" class="w-full max-w-xs rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('user-invitations.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                Simpan Undangan
            </button>
        </div>
    </form>
</div>

<script>
    (function () {
        const superuserInput = document.getElementById('is_superuser');
        const mosqueInput = document.getElementById('mosque_id');
        const roleInput = document.getElementById('role_id');

        function toggleMosqueFields() {
            const disabled = superuserInput ? superuserInput.checked : false;
            mosqueInput.disabled = disabled;
            roleInput.disabled = disabled;
            mosqueInput.required = ! disabled;
            roleInput.required = ! disabled;
        }

        if (superuserInput) {
            superuserInput.addEventListener('change', toggleMosqueFields);
        }
        toggleMosqueFields();
    }());
</script>
@endsection
