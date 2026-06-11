<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun SIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-10">
    <div class="mx-auto max-w-xl">
        <div class="mb-6 text-center">
            <p class="text-sm font-semibold uppercase tracking-[.22em] text-indigo-600">SIMAS</p>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Pendaftaran Akun SIMAS</h1>
            <p class="mt-2 text-sm text-gray-500">Silakan lengkapi data akun berdasarkan undangan yang Anda terima.</p>
        </div>

        <div class="mb-5 rounded-lg bg-white p-5 shadow">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-600">Informasi Undangan</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Nomor WhatsApp</span>
                    <span class="text-right font-semibold text-gray-800">{{ $invitation->phone }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Masjid Tujuan</span>
                    <span class="text-right font-semibold text-gray-800">{{ $invitation->mosque?->name ?? 'Akses Global' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Role</span>
                    <span class="text-right font-semibold text-gray-800">{{ $invitation->role?->label ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Berlaku Sampai</span>
                    <span class="text-right font-semibold text-gray-800">{{ $invitation->expires_at?->format('d/m/Y H:i') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            @if($errorMessage)
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Pendaftaran tidak dapat dilanjutkan.</p>
                    <p class="mt-1">{{ $errorMessage }}</p>
                </div>
            @else
                @if($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-semibold">Periksa kembali data pendaftaran.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('invitations.submit', ['token' => $invitation->token]) }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $invitation->name) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">Nomor WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $invitation->phone) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Nomor ini digunakan untuk komunikasi admin masjid.</p>
                    </div>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        Kirim Pendaftaran
                    </button>
                </form>
            @endif
        </div>
    </div>
</body>
</html>
