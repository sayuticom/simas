@extends('public_website.layout')

@section('title', 'Kegiatan - ' . $website->display_name)

@section('content')
@php
    $whatsappNumber = null;
    if ($website->no_whatsapp_publik) {
        $whatsappNumber = preg_replace('/\D+/', '', $website->no_whatsapp_publik);
        if (str_starts_with($whatsappNumber, '08')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }
    }
@endphp

<section class="bg-emerald-950 px-4 py-10 text-white sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <p class="text-xs font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200 sm:text-sm">Agenda Publik</p>
        <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">Kajian &amp; Kegiatan</h1>
        <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-emerald-50 sm:mt-4 sm:text-base">Daftar kajian dan kegiatan publik yang ditayangkan oleh pengurus {{ $website->display_name }}.</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($events as $event)
            @php
                $eventTitle = $event->nama_kegiatan;
                $eventDescription = $event->deskripsi_publik ?: $event->deskripsi;
                $eventUrl = request()->fullUrl() . '#kegiatan-' . $event->id;
                $whatsappShareUrl = 'https://wa.me/?text=' . rawurlencode($eventTitle . ' - ' . $eventUrl);
                $facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($eventUrl);
            @endphp
            <article id="kegiatan-{{ $event->id }}" class="flex h-full flex-col overflow-hidden rounded-2xl border border-emerald-900/10 bg-white shadow-lg shadow-emerald-950/5 sm:rounded-3xl">
                @if($event->poster_publik)
                    <img src="{{ asset('storage/' . $event->poster_publik) }}" alt="{{ $eventTitle }}" class="h-48 w-full object-cover sm:h-auto sm:aspect-square">
                @else
                    <div class="flex h-40 w-full items-center justify-center bg-emerald-950 p-5 text-center text-white sm:h-auto sm:aspect-square sm:p-6">
                        <span class="text-lg font-black leading-tight sm:text-xl">{{ $eventTitle }}</span>
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-5 sm:min-h-[28rem] sm:p-6">
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">{{ $event->jenis_kegiatan ?: 'Kegiatan' }}</p>
                    <h2 class="mt-2 text-xl font-black leading-tight text-emerald-950 sm:text-2xl">{{ $eventTitle }}</h2>
                    @if($event->tema_materi)
                        <p class="mt-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-black leading-6 text-emerald-950">Tema: {{ $event->tema_materi }}</p>
                    @endif

                    <dl class="mt-5 space-y-3 text-sm font-semibold text-slate-800">
                        @if($event->narasumber)
                            <div>
                                <dt class="font-black text-emerald-950">Pemateri</dt>
                                <dd class="mt-1">{{ $event->narasumber }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="font-black text-emerald-950">Waktu</dt>
                            <dd class="mt-1">
                                {{ $event->tanggal_mulai?->format('d M Y') ?? 'Tanggal menyusul' }}
                                @if($event->tanggal_mulai)
                                    <span class="block">{{ $event->tanggal_mulai->format('H:i') }}{{ $event->tanggal_selesai ? ' - ' . $event->tanggal_selesai->format('H:i') : '' }}</span>
                                @endif
                            </dd>
                        </div>
                        @if($event->lokasi)
                            <div>
                                <dt class="font-black text-emerald-950">Lokasi</dt>
                                <dd class="mt-1">{{ $event->lokasi }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if($eventDescription)
                        <p class="mt-5 text-sm font-medium leading-7 text-slate-700">{{ \Illuminate\Support\Str::limit($eventDescription, 170) }}</p>
                    @endif

                    <div class="mt-auto pt-6">
                        @if($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}" class="inline-flex w-full justify-center rounded-xl bg-emerald-950 px-4 py-3 text-sm font-black text-white hover:bg-emerald-800">
                                Hubungi Pengurus
                            </a>
                        @endif

                        <div class="mt-4 border-t border-slate-200 pt-4">
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-600">Bagikan</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ $whatsappShareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-black text-emerald-800 hover:bg-emerald-100">WhatsApp</a>
                                <a href="{{ $facebookShareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-lg border border-blue-200 bg-blue-50 px-3 py-2.5 text-xs font-black text-blue-800 hover:bg-blue-100">Facebook</a>
                                <button type="button" data-share-url="{{ $eventUrl }}" class="share-copy-button inline-flex rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-black text-slate-800 hover:bg-slate-100">Salin Link</button>
                            </div>
                            <p class="mt-3 text-xs font-semibold leading-5 text-slate-600">Untuk Instagram dan TikTok, salin link lalu bagikan manual.</p>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-emerald-900/20 bg-white p-6 text-center text-sm font-semibold text-slate-700 md:col-span-2 lg:col-span-3 sm:rounded-3xl sm:p-8">
                Belum ada kajian atau kegiatan yang ditayangkan.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $events->links() }}
    </div>
</section>

<script>
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.share-copy-button');
        if (!button) {
            return;
        }

        const shareUrl = button.getAttribute('data-share-url');
        const originalText = button.textContent;

        const markCopied = function () {
            button.textContent = 'Link Disalin';
            window.setTimeout(function () {
                button.textContent = originalText;
            }, 1600);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(shareUrl).then(markCopied).catch(function () {
                window.prompt('Salin link kegiatan:', shareUrl);
            });
            return;
        }

        window.prompt('Salin link kegiatan:', shareUrl);
    });
</script>
@endsection
