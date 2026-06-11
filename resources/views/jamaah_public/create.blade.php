<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Jamaah - SIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="mx-auto flex min-h-screen max-w-2xl items-center px-4 py-8">
        <div class="w-full rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-6 text-center">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">SIMAS</p>
                <h1 class="mt-2 text-2xl font-bold text-gray-800">Form Data Jamaah</h1>
                <p class="mt-2 text-sm text-gray-600">{{ $mosque->name }}</p>
            </div>

            <form action="{{ route('jamaah.public.store', $mosque->qr_token) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih jenis kelamin</option>
                        @foreach($genderOptions as $option)
                            <option value="{{ $option }}" {{ old('jenis_kelamin') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('jenis_kelamin')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('tanggal_lahir')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Umur</label>
                        <input type="number" name="umur" min="0" max="120" value="{{ old('umur') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Opsional">
                        @error('umur')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nomor HP/WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('no_hp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Alamat</label>
                    <textarea name="alamat" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('alamat') }}</textarea>
                    @error('alamat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Pekerjaan</label>
                    <select id="pekerjaan" name="pekerjaan" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih pekerjaan</option>
                        @foreach($pekerjaanOptions as $option)
                            <option value="{{ $option }}" {{ old('pekerjaan') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('pekerjaan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <div id="pekerjaan-lainnya-container" class="mt-4 hidden">
                        <label class="block text-sm font-semibold text-gray-700">Pekerjaan Lainnya</label>
                        <input id="pekerjaan-lainnya" type="text" name="pekerjaan_lainnya" value="{{ old('pekerjaan_lainnya') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tulis pekerjaan">
                        @error('pekerjaan_lainnya')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                @if($categories->count() > 0)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Kategori Jamaah</label>
                        <select name="category_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('category_id', $defaultCategory?->id) === (string) $category->id ? 'selected' : '' }}>{{ $category->label }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Catatan</label>
                    <textarea name="keterangan" rows="2" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Opsional">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">
                    Kirim Data Jamaah
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pekerjaan = document.getElementById('pekerjaan');
            const container = document.getElementById('pekerjaan-lainnya-container');
            const lainnya = document.getElementById('pekerjaan-lainnya');

            function togglePekerjaanLainnya() {
                const tampil = pekerjaan.value === @json(\App\Models\Jamaah::PEKERJAAN_LAINNYA);
                container.classList.toggle('hidden', !tampil);
                lainnya.disabled = !tampil;
            }

            pekerjaan.addEventListener('change', togglePekerjaanLainnya);
            togglePekerjaanLainnya();
        });
    </script>
</body>
</html>
