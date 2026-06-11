<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteSettingController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $activeMosque = $user?->getActiveMosque();
        $availableMosques = $user?->selectableMosques() ?? collect();
        $websiteSetting = null;
        $canUpdate = false;

        if ($activeMosque) {
            $websiteSetting = WebsiteSetting::firstOrNew(
                ['mosque_id' => $activeMosque->id],
                [
                    'nama_website' => $activeMosque->name,
                    'subdomain' => '',
                    'alamat_publik' => $activeMosque->address,
                    'no_whatsapp_publik' => $activeMosque->phone,
                    'status_website' => WebsiteSetting::STATUS_DRAFT,
                    'show_public_pengumuman' => true,
                    'show_public_informasi' => true,
                    'show_public_donasi' => true,
                ]
            );
            $canUpdate = $this->canUpdateWebsite($activeMosque->id);
        }

        return view('admin.website_settings.edit', [
            'activeMosque' => $activeMosque,
            'availableMosques' => $availableMosques,
            'baseDomain' => config('simas.base_domain', 'masjidkeren.my.id'),
            'canUpdate' => $canUpdate,
            'reservedSubdomains' => WebsiteSetting::RESERVED_SUBDOMAINS,
            'statuses' => WebsiteSetting::statuses(),
            'websiteSetting' => $websiteSetting,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $activeMosque = $user?->getActiveMosque();

        if (! $activeMosque) {
            return redirect()
                ->route('website-settings.edit')
                ->withErrors(['active_mosque' => 'Pilih masjid aktif terlebih dahulu sebelum mengatur website.']);
        }

        abort_unless($this->canUpdateWebsite($activeMosque->id), 403);

        $websiteSetting = WebsiteSetting::firstOrNew(['mosque_id' => $activeMosque->id]);
        $request->merge([
            'subdomain' => strtolower((string) $request->input('subdomain')),
        ]);

        $validated = $request->validate([
            'nama_website' => ['nullable', 'string', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'regex:/^(?!-)[a-z0-9-]+(?<!-)$/',
                Rule::notIn(WebsiteSetting::RESERVED_SUBDOMAINS),
                Rule::unique('website_settings', 'subdomain')->ignore($websiteSetting->id),
            ],
            'slogan' => ['nullable', 'string', 'max:255'],
            'deskripsi_singkat' => ['nullable', 'string'],
            'alamat_publik' => ['nullable', 'string'],
            'no_whatsapp_publik' => ['nullable', 'string', 'max:50'],
            'email_publik' => ['nullable', 'email', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'status_website' => ['required', Rule::in(WebsiteSetting::statuses())],
            'show_public_pengumuman' => ['nullable', 'boolean'],
            'show_public_informasi' => ['nullable', 'boolean'],
            'show_public_donasi' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'subdomain.regex' => 'Subdomain hanya boleh berisi huruf kecil, angka, dan strip, serta tidak boleh diawali atau diakhiri strip.',
            'subdomain.not_in' => 'Subdomain tersebut dicadangkan untuk sistem.',
        ]);

        $validated['mosque_id'] = $activeMosque->id;
        $validated['show_public_pengumuman'] = $request->boolean('show_public_pengumuman');
        $validated['show_public_informasi'] = $request->boolean('show_public_informasi');
        $validated['show_public_donasi'] = $request->boolean('show_public_donasi');
        $validated['updated_by'] = $user->id;

        if (! $websiteSetting->exists) {
            $validated['created_by'] = $user->id;
        }

        if ($request->hasFile('logo')) {
            $this->deletePublicFile($websiteSetting->logo);
            $validated['logo'] = $request->file('logo')->store('website/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $this->deletePublicFile($websiteSetting->banner);
            $validated['banner'] = $request->file('banner')->store('website/banners', 'public');
        }

        $websiteSetting->fill($validated)->save();

        return redirect()
            ->route('website-settings.edit')
            ->with('success', 'Pengaturan website masjid berhasil disimpan.');
    }

    private function canUpdateWebsite(int $mosqueId): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isSuperuser()) {
            return true;
        }

        return $user->hasRoleInMosque(Role::ADMIN_MASJID, $mosqueId);
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
