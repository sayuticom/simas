@extends('layouts.admin')

@section('title', 'Penyaluran ZIS - SIMAS')
@section('page_title', 'Penyaluran ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Penyaluran ZIS</h2>
    <p class="text-sm text-gray-500">Daftar penyaluran ZIS berdasarkan kategori dana.</p>
        </div>
        <a href="{{ route('zis.distributions.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Tambah Penyaluran</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Penerima</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tujuan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sumber Penerimaan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($distributions as $distribution)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->distribution_date?->format('d-m-Y') ?? $distribution->tanggal?->format('d-m-Y') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->recipient_name ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $distribution->distribution_target === 'kas_operasional' ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700' }}">
                                {{ $distribution->distribution_target === 'kas_operasional' ? 'Kas Operasional' : 'Penerima Manfaat' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            @if($distribution->receipt)
                                {{ $distribution->receipt->donor_name ?: '-' }}
                            @else
                                <span class="text-gray-500">Saldo Kategori</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->category?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">Rp {{ number_format($distribution->amount ?? $distribution->nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('zis.distributions.show', $distribution) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                            <a href="{{ route('zis.distributions.edit', $distribution) }}" class="text-green-600 hover:text-green-900">Edit</a>
                            <form action="{{ route('zis.distributions.destroy', $distribution) }}" method="POST" class="inline" onsubmit="return confirm('Hapus penyaluran ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada penyaluran ZIS.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $distributions->links() }}</div>
</div>
@endsection
