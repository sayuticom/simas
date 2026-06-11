@extends('layouts.admin')

@section('title', 'Edit Program ZIS')
@section('page_title', 'Edit Program ZIS')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <form action="{{ route('zis.programs.update', $program) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Program</label>
                    <input type="text" name="nama" value="{{ old('nama', $program->nama) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Target Dana (Rp)</label>
                    <input type="number" name="target_dana" value="{{ old('target_dana', $program->target_dana) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach(['Berjalan', 'Selesai', 'Draf'] as $status)
                            <option value="{{ $status }}" {{ old('status', $program->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">Perbarui</button>
                <a href="{{ route('zis.programs.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
@endsection
