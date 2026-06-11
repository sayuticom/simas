@extends('layouts.admin')

@section('title', 'Program ZIS')
@section('page_title', 'Program ZIS')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Program ZIS</h2>
        <a href="{{ route('zis.programs.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Tambah Program</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Nama Program</th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Target Dana</th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Status</th>
                    <th class="px-6 py-3 text-sm font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($programs as $program)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $program->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Rp {{ number_format($program->target_dana, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $program->status }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 space-x-2">
                            <a href="{{ route('zis.programs.show', $program) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                            <a href="{{ route('zis.programs.edit', $program) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            <form action="{{ route('zis.programs.destroy', $program) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus program ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada program ZIS.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $programs->links() }}</div>
@endsection
