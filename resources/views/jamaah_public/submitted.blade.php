<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jamaah Terkirim - SIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="mx-auto flex min-h-screen max-w-lg items-center px-4 py-8">
        <div class="w-full rounded-2xl bg-white p-8 text-center shadow-lg">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                <svg class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="mt-5 text-2xl font-bold text-gray-800">Terima kasih</h1>
            <p class="mt-3 text-gray-600">Data jamaah berhasil dikirim ke {{ $mosque->name }}.</p>
            <p class="mt-2 text-sm text-gray-500">Petugas masjid akan memeriksa data yang masuk.</p>
        </div>
    </div>
</body>
</html>
