<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Terkirim - SIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-16">
    <div class="mx-auto max-w-lg rounded-lg bg-white p-8 text-center shadow">
        <p class="text-sm font-semibold uppercase tracking-[.22em] text-indigo-600">SIMAS</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-900">Pendaftaran Berhasil Dikirim</h1>
        <p class="mt-3 text-sm leading-6 text-gray-600">
            Data pendaftaran Anda telah diterima dan sedang menunggu persetujuan superuser.
            Akun belum dapat digunakan untuk login sebelum disetujui.
        </p>
        <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4 text-left text-sm">
            <p class="text-gray-500">Nama</p>
            <p class="font-semibold text-gray-800">{{ $invitation->name ?? '-' }}</p>
            <p class="mt-3 text-gray-500">Email</p>
            <p class="font-semibold text-gray-800">{{ $invitation->email ?? '-' }}</p>
        </div>
    </div>
</body>
</html>
