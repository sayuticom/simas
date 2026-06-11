<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $website->display_name)</title>
    @php
        $metaDescription = $website->slogan ?: ($website->deskripsi_singkat ?: 'Website publik masjid yang dikelola melalui SIMAS.');
        $metaImage = $website->banner ?: $website->logo;
        $metaImageUrl = $metaImage ? asset('storage/' . $metaImage) : null;
    @endphp
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:title" content="@yield('title', $website->display_name)">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    @if($metaImageUrl)
        <meta property="og:image" content="{{ $metaImageUrl }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen overflow-x-hidden bg-[#f7faf7] text-slate-950 antialiased">
    @php
        $socialLinks = [
            'Instagram' => $website->instagram_url,
            'TikTok' => $website->tiktok_url,
            'Facebook' => $website->facebook_url,
            'YouTube' => $website->youtube_url,
        ];
        $socialLinks = array_filter($socialLinks);
    @endphp

    <header class="sticky top-0 z-30 border-b border-emerald-900/10 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-4 lg:px-8">
            <div class="flex min-w-0 items-center justify-between gap-3">
                <a href="{{ route('public-website.home', ['subdomain' => $website->subdomain]) }}" class="flex min-w-0 items-center gap-3">
                    @if($website->logo)
                        <img src="{{ asset('storage/' . $website->logo) }}" alt="{{ $website->display_name }}" class="h-11 w-11 flex-shrink-0 rounded-xl border border-emerald-900/10 bg-white object-contain shadow-sm sm:h-12 sm:w-12">
                    @else
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-950 text-lg font-black text-amber-200 shadow-sm sm:h-12 sm:w-12 sm:text-xl">M</div>
                    @endif
                    <div class="min-w-0 max-w-[11.5rem] sm:max-w-none">
                        <p class="truncate text-base font-black text-emerald-950 sm:text-lg">{{ $website->display_name }}</p>
                        @if($website->slogan)
                            <p class="hidden truncate text-xs font-semibold text-slate-700 min-[390px]:block sm:text-sm">{{ $website->slogan }}</p>
                        @else
                            <p class="hidden truncate text-xs font-semibold text-slate-700 min-[390px]:block sm:text-sm">Pusat ibadah, ilmu, dan ukhuwah</p>
                        @endif
                    </div>
                </a>

                <button type="button" id="publicMenuToggle" class="inline-flex h-10 flex-shrink-0 items-center justify-center rounded-xl border border-emerald-900/10 bg-emerald-950 px-3 text-sm font-black text-white sm:hidden" aria-controls="publicNav" aria-expanded="false">
                    Menu
                </button>
            </div>

            <nav id="publicNav" class="hidden flex-col gap-1 rounded-2xl border border-emerald-900/10 bg-white p-2 text-sm font-bold text-slate-800 shadow-lg shadow-emerald-950/5 sm:flex sm:flex-row sm:flex-wrap sm:gap-2 sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
                <a href="{{ route('public-website.home', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Beranda</a>
                <a href="{{ route('public-website.profile', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Profil</a>
                <a href="{{ route('public-website.events', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Kegiatan</a>
                @if($website->show_public_donasi)
                    <a href="{{ route('public-website.donasi', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Donasi</a>
                @endif
                @if($website->show_public_pengumuman)
                    <a href="{{ route('public-website.pengumuman', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Pengumuman</a>
                @endif
                <a href="{{ route('public-website.berita', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Berita</a>
                <a href="{{ route('public-website.artikel', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Artikel</a>
                @if($website->show_public_informasi)
                    <a href="{{ route('public-website.informasi', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Informasi</a>
                @endif
                <a href="{{ route('public-website.contact', ['subdomain' => $website->subdomain]) }}" class="rounded-xl px-4 py-3 hover:bg-emerald-950 hover:text-white sm:rounded-full sm:py-2.5">Kontak</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-12 border-t border-emerald-900/10 bg-emerald-950 text-white sm:mt-16">
        <div class="mx-auto max-w-6xl px-4 py-7 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="text-sm font-semibold">
                    <p>&copy; {{ date('Y') }} {{ $website->display_name }}</p>
                    <p class="mt-1 text-emerald-100">Dikelola melalui SIMAS</p>
                </div>

                @if(! empty($socialLinks))
                    <div class="sm:text-right">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-200">Ikuti Kami</p>
                        <div class="mt-3 flex flex-wrap gap-2 sm:justify-end">
                            @foreach($socialLinks as $label => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/20 px-4 py-2 text-xs font-black text-white hover:bg-white hover:text-emerald-950">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('publicMenuToggle');
            const nav = document.getElementById('publicNav');

            toggle?.addEventListener('click', function () {
                const isHidden = nav.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(!isHidden));
            });
        });
    </script>
</body>
</html>
