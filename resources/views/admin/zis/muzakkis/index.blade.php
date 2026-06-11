@extends('layouts.admin')

@section('title', 'Data Muzakki')
@section('page_title', 'Data Muzakki')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Muzakki</h2>
        <a href="{{ route('zis.muzakkis.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Tambah Muzakki</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">No HP</th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Alamat</th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Anggota</th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($muzakkis as $muzakki)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $muzakki->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $muzakki->no_hp }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ Str::limit($muzakki->alamat, 40) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $muzakki->jumlah_anggota_keluarga ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 space-x-2">
                            <a href="{{ route('zis.muzakkis.show', $muzakki) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                            <a href="{{ route('zis.muzakkis.edit', $muzakki) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            <form action="{{ route('zis.muzakkis.destroy', $muzakki) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data muzakki ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data muzakki.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $muzakkis->links() }}</div>
@endsection
