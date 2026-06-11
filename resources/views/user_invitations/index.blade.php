@extends('layouts.admin')

@section('title', 'Undangan User - SIMAS')
@section('page_title', 'Undangan User')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Daftar Undangan User</h3>
            <p class="mt-1 text-sm text-gray-500">Buat link undangan dan kirimkan melalui WhatsApp.</p>
        </div>
        <a href="{{ route('user-invitations.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
            Buat Undangan User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Nomor WhatsApp</th>
                    <th class="px-4 py-3">Masjid</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Expired At</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invitations as $invitation)
                    @php
                        $registerUrl = url('/register/invitation/' . $invitation->token);
                        $message = "Assalamu'alaikum, Anda diundang untuk membuat akun SIMAS. Silakan daftar melalui link berikut:\n" . $registerUrl;
                        $whatsAppUrl = 'https://wa.me/' . $invitation->phone . '?text=' . rawurlencode($message);
                        $privilegedInvitation = $invitation->role?->name === \App\Models\Role::SUPERUSER;
                        $canManageInvitation = ! $privilegedInvitation || auth()->user()->isSuperSuperuser();
                        $statusClass = match ($invitation->status) {
                            'submitted' => 'bg-blue-50 text-blue-700',
                            'approved' => 'bg-green-50 text-green-700',
                            'expired' => 'bg-red-50 text-red-700',
                            'cancelled' => 'bg-gray-100 text-gray-600',
                            default => 'bg-yellow-50 text-yellow-700',
                        };
                        $statusLabel = match ($invitation->status) {
                            'draft' => 'Draft',
                            'submitted' => 'Submitted',
                            'approved' => 'Approved',
                            'expired' => 'Expired',
                            'cancelled' => 'Cancelled',
                            default => ucfirst($invitation->status),
                        };
                    @endphp
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $invitation->name ?? '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $invitation->phone }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $invitation->mosque?->name ?? 'Akses Global' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $invitation->role?->label ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $invitation->expires_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-2">
                                @if(! $canManageInvitation && in_array($invitation->status, ['draft', 'submitted']))
                                    <span class="inline-flex items-center px-3 py-2 font-semibold text-gray-400">Terproteksi</span>
                                @elseif($invitation->status === 'draft')
                                    <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-lg border border-green-200 px-3 py-2 font-semibold text-green-700 hover:bg-green-50">
                                        Kirim WhatsApp
                                    </a>
                                @elseif($invitation->status === 'submitted')
                                    <form action="{{ route('user-invitations.approve', $invitation) }}" method="POST" onsubmit="return confirm('Yakin menyetujui undangan ini?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 font-semibold text-white hover:bg-indigo-700">
                                            Approve
                                        </button>
                                    </form>
                                @endif

                                @if($canManageInvitation && in_array($invitation->status, ['draft', 'submitted']))
                                    <form action="{{ route('user-invitations.cancel', $invitation) }}" method="POST" onsubmit="return confirm('Yakin membatalkan undangan ini?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">
                                            Cancel
                                        </button>
                                    </form>
                                @elseif($invitation->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-2 font-semibold text-green-700">Sudah disetujui</span>
                                @elseif($invitation->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-2 font-semibold text-gray-500">Dibatalkan</span>
                                @elseif($invitation->status === 'expired')
                                    <span class="inline-flex items-center px-3 py-2 font-semibold text-red-600">Kedaluwarsa</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada undangan user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
