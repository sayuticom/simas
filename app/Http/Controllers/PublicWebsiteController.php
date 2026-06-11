<?php

namespace App\Http\Controllers;

use App\Models\WebsiteSetting;
use App\Models\DonationProgram;
use App\Models\Kegiatan;
use App\Models\Pengumuman;
use App\Models\WebsitePost;
use Illuminate\View\View;

class PublicWebsiteController extends Controller
{
    public function home(string $subdomain): View
    {
        $website = $this->activeWebsite($subdomain);
        $latestEvents = $this->publicEventsQuery($website)
            ->limit(3)
            ->get();

        return view('public_website.home', compact('latestEvents', 'website'));
    }

    public function profile(string $subdomain): View
    {
        $website = $this->activeWebsite($subdomain);

        return view('public_website.profile', compact('website'));
    }

    public function contact(string $subdomain): View
    {
        $website = $this->activeWebsite($subdomain);

        return view('public_website.contact', compact('website'));
    }

    public function events(string $subdomain): View
    {
        $website = $this->activeWebsite($subdomain);
        $events = $this->publicEventsQuery($website)
            ->paginate(9);

        return view('public_website.events', compact('events', 'website'));
    }

    public function donasi(string $subdomain): View
    {
        $website = $this->activeWebsite($subdomain);
        $this->abortIfPublicDonasiHidden($website);
        $programs = $this->publicDonationProgramsQuery($website)->paginate(9);

        return view('public_website.donasi.index', compact('programs', 'website'));
    }

    public function donasiShow(string $subdomain, string $slug): View
    {
        $website = $this->activeWebsite($subdomain);
        $this->abortIfPublicDonasiHidden($website);
        $program = $this->publicDonationProgramsQuery($website)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public_website.donasi.show', compact('program', 'website'));
    }

    public function pengumuman(string $subdomain): View
    {
        $website = $this->activeWebsite($subdomain);
        $this->abortIfPublicPengumumanHidden($website);
        $pengumumans = $this->publicPengumumanQuery($website)
            ->paginate(9);

        return view('public_website.pengumuman.index', compact('pengumumans', 'website'));
    }

    public function pengumumanShow(string $subdomain, string $slug): View
    {
        $website = $this->activeWebsite($subdomain);
        $this->abortIfPublicPengumumanHidden($website);
        $pengumuman = $this->publicPengumumanQuery($website)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public_website.pengumuman.show', compact('pengumuman', 'website'));
    }

    public function berita(string $subdomain): View
    {
        return $this->postsByType($subdomain, WebsitePost::TYPE_BERITA);
    }

    public function beritaShow(string $subdomain, string $slug): View
    {
        return $this->postShowByType($subdomain, WebsitePost::TYPE_BERITA, $slug);
    }

    public function artikel(string $subdomain): View
    {
        return $this->postsByType($subdomain, WebsitePost::TYPE_ARTIKEL);
    }

    public function artikelShow(string $subdomain, string $slug): View
    {
        return $this->postShowByType($subdomain, WebsitePost::TYPE_ARTIKEL, $slug);
    }

    public function informasi(string $subdomain): View
    {
        $website = $this->activeWebsite($subdomain);
        $this->abortIfPublicInformasiHidden($website);

        return $this->postsByType($subdomain, WebsitePost::TYPE_INFORMASI, $website);
    }

    public function informasiShow(string $subdomain, string $slug): View
    {
        $website = $this->activeWebsite($subdomain);
        $this->abortIfPublicInformasiHidden($website);

        return $this->postShowByType($subdomain, WebsitePost::TYPE_INFORMASI, $slug, $website);
    }

    private function activeWebsite(string $subdomain): WebsiteSetting
    {
        return WebsiteSetting::with('mosque')
            ->where('subdomain', $subdomain)
            ->where('status_website', WebsiteSetting::STATUS_AKTIF)
            ->firstOrFail();
    }

    private function publicEventsQuery(WebsiteSetting $website)
    {
        return Kegiatan::withoutGlobalScope('mosque')
            ->where('mosque_id', $website->mosque_id)
            ->where('tampilkan_di_website', true)
            ->where('status_publik', 'tayang')
            ->where('status', '<>', 'batal')
            ->orderByRaw('tanggal_mulai is null')
            ->orderByRaw('case when tanggal_mulai >= now() then 0 else 1 end')
            ->orderBy('tanggal_mulai')
            ->latest();
    }

    private function publicPengumumanQuery(WebsiteSetting $website)
    {
        return Pengumuman::withoutGlobalScope('mosque')
            ->where('mosque_id', $website->mosque_id)
            ->where('status', 'terbit')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');
    }

    private function publicDonationProgramsQuery(WebsiteSetting $website)
    {
        return DonationProgram::withoutGlobalScope('mosque')
            ->where('mosque_id', $website->mosque_id)
            ->published()
            ->visiblePublic()
            ->latest();
    }

    private function postsByType(string $subdomain, string $type, ?WebsiteSetting $website = null): View
    {
        $website ??= $this->activeWebsite($subdomain);
        $posts = $this->publicPostsQuery($website, $type)->paginate(9);
        $title = WebsitePost::typeOptions()[$type] ?? ucfirst($type);

        return view('public_website.posts.index', compact('posts', 'website', 'type', 'title'));
    }

    private function postShowByType(string $subdomain, string $type, string $slug, ?WebsiteSetting $website = null): View
    {
        $website ??= $this->activeWebsite($subdomain);
        $post = $this->publicPostsQuery($website, $type)
            ->where('slug', $slug)
            ->firstOrFail();
        $title = WebsitePost::typeOptions()[$type] ?? ucfirst($type);

        return view('public_website.posts.show', compact('post', 'website', 'type', 'title'));
    }

    private function publicPostsQuery(WebsiteSetting $website, string $type)
    {
        return WebsitePost::withoutGlobalScope('mosque')
            ->where('mosque_id', $website->mosque_id)
            ->where('type', $type)
            ->where('status', WebsitePost::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');
    }

    private function abortIfPublicPengumumanHidden(WebsiteSetting $website): void
    {
        abort_unless($website->show_public_pengumuman, 404);
    }

    private function abortIfPublicInformasiHidden(WebsiteSetting $website): void
    {
        abort_unless($website->show_public_informasi, 404);
    }

    private function abortIfPublicDonasiHidden(WebsiteSetting $website): void
    {
        abort_unless($website->show_public_donasi, 404);
    }
}
