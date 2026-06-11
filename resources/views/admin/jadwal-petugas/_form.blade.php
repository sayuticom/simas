@php
    $statusValue = old('status', $jadwalPetugas?->status ?? 'terjadwal');
    $selectedKegiatan = $selectedKegiatan ?? null;
    $kegiatanValue = old('kegiatan_id', $jadwalPetugas?->kegiatan_id ?? $selectedKegiatan?->id);
    $tanggalValue = old('tanggal', $jadwalPetugas?->tanggal?->format('Y-m-d') ?? $selectedKegiatan?->tanggal_mulai?->format('Y-m-d'));
    $waktuMulaiValue = old('waktu_mulai', $jadwalPetugas?->waktu_mulai ? substr($jadwalPetugas->waktu_mulai, 0, 5) : $selectedKegiatan?->tanggal_mulai?->format('H:i'));
    $waktuSelesaiValue = old('waktu_selesai', $jadwalPetugas?->waktu_selesai ? substr($jadwalPetugas->waktu_selesai, 0, 5) : $selectedKegiatan?->tanggal_selesai?->format('H:i'));
    $lokasiValue = old('lokasi', $jadwalPetugas?->lokasi ?? $selectedKegiatan?->lokasi);
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Kegiatan</label>
        <select name="kegiatan_id" id="kegiatan_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Tidak terkait kegiatan</option>
            @foreach($kegiatans as $kegiatan)
                <option
                    value="{{ $kegiatan->id }}"
                    data-tanggal="{{ $kegiatan->tanggal_mulai?->format('Y-m-d') }}"
                    data-waktu-mulai="{{ $kegiatan->tanggal_mulai?->format('H:i') }}"
                    data-waktu-selesai="{{ $kegiatan->tanggal_selesai?->format('H:i') }}"
                    data-lokasi="{{ $kegiatan->lokasi }}"
                    {{ (int) $kegiatanValue === $kegiatan->id ? 'selected' : '' }}
                >
                    {{ $kegiatan->nama_kegiatan }}
                </option>
            @endforeach
        </select>
        @error('kegiatan_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Tugas <span class="text-red-600">*</span></label>
        <input type="text" name="jenis_tugas" value="{{ old('jenis_tugas', $jadwalPetugas?->jenis_tugas) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Imam, Khatib, Muadzin, Bilal, Keamanan" required>
        @error('jenis_tugas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Petugas dari User</label>
        <select name="user_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih user petugas</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ (int) old('user_id', $jadwalPetugas?->user_id) === $user->id ? 'selected' : '' }}>
                    {{ $user->name }} - {{ $user->email }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Pilih user terdaftar, atau isi nama manual di bawah.</p>
        @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Petugas Manual</label>
        <input type="text" name="nama_petugas" value="{{ old('nama_petugas', $jadwalPetugas?->nama_petugas) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs text-gray-500">Wajib diisi jika tidak memilih user petugas.</p>
        @error('nama_petugas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal <span class="text-red-600">*</span></label>
        <input type="date" name="tanggal" id="tanggal" value="{{ $tanggalValue }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('tanggal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="terjadwal" {{ $statusValue === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
            <option value="hadir" {{ $statusValue === 'hadir' ? 'selected' : '' }}>Hadir</option>
            <option value="berhalangan" {{ $statusValue === 'berhalangan' ? 'selected' : '' }}>Berhalangan</option>
            <option value="selesai" {{ $statusValue === 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="batal" {{ $statusValue === 'batal' ? 'selected' : '' }}>Batal</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Waktu Mulai</label>
        <input type="time" name="waktu_mulai" id="waktu_mulai" value="{{ $waktuMulaiValue }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('waktu_mulai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Waktu Selesai</label>
        <input type="time" name="waktu_selesai" id="waktu_selesai" value="{{ $waktuSelesaiValue }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('waktu_selesai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
        <input type="text" name="lokasi" id="lokasi" value="{{ $lokasiValue }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('lokasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $jadwalPetugas?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kegiatanSelect = document.getElementById('kegiatan_id');
        const tanggalInput = document.getElementById('tanggal');
        const waktuMulaiInput = document.getElementById('waktu_mulai');
        const waktuSelesaiInput = document.getElementById('waktu_selesai');
        const lokasiInput = document.getElementById('lokasi');

        if (!kegiatanSelect) {
            return;
        }

        kegiatanSelect.addEventListener('change', function () {
            const option = kegiatanSelect.options[kegiatanSelect.selectedIndex];

            if (!option || !option.value) {
                return;
            }

            tanggalInput.value = option.dataset.tanggal || tanggalInput.value;
            waktuMulaiInput.value = option.dataset.waktuMulai || waktuMulaiInput.value;
            waktuSelesaiInput.value = option.dataset.waktuSelesai || waktuSelesaiInput.value;
            lokasiInput.value = option.dataset.lokasi || lokasiInput.value;
        });
    });
</script>
