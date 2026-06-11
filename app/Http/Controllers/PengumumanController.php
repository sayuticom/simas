<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::with(['kegiatan', 'pembuat'])
            ->latest()
            ->paginate(10);

        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function create()
    {
        return view('admin.pengumuman.create', [
            'pengumuman' => null,
            'kegiatans' => $this->kegiatanOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['mosque_id'] = $this->activeMosqueId();
        $data['dibuat_oleh'] = auth()->id();
        $data['status'] = $data['status'] ?? 'draft';
        $data['tampil_di_dashboard'] = $request->boolean('tampil_di_dashboard');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['judul'], $data['mosque_id']);
        $data['published_at'] = $this->publishedAtValue($data['status'], $data['published_at'] ?? null);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('website/pengumuman', 'public');
        }

        Pengumuman::create($data);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function show(Pengumuman $pengumuman)
    {
        $this->authorizePengumuman($pengumuman);
        $pengumuman->load(['kegiatan', 'pembuat']);

        return view('admin.pengumuman.show', compact('pengumuman'));
    }

    public function edit(Pengumuman $pengumuman)
    {
        $this->authorizePengumuman($pengumuman);

        return view('admin.pengumuman.edit', [
            'pengumuman' => $pengumuman,
            'kegiatans' => $this->kegiatanOptions(),
        ]);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $this->authorizePengumuman($pengumuman);

        $data = $this->validatedData($request);
        $data['status'] = $data['status'] ?? 'draft';
        $data['tampil_di_dashboard'] = $request->boolean('tampil_di_dashboard');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['judul'], $pengumuman->mosque_id, $pengumuman->id);
        $data['published_at'] = $this->publishedAtValue($data['status'], $data['published_at'] ?? null, $pengumuman->published_at);

        if ($request->hasFile('featured_image')) {
            $newImage = $request->file('featured_image')->store('website/pengumuman', 'public');
            $oldImage = $pengumuman->featured_image;
            $data['featured_image'] = $newImage;
        }

        $pengumuman->update($data);

        if (isset($oldImage)) {
            $this->deletePublicFile($oldImage);
        }

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $this->authorizePengumuman($pengumuman);

        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $mosqueId = $this->activeMosqueId();

        return $request->validate([
            'kegiatan_id' => [
                'nullable',
                Rule::exists('kegiatans', 'id')->where('mosque_id', $mosqueId),
            ],
            'judul' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'isi' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'target_audiens' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['draft', 'terbit', 'arsip'])],
            'published_at' => 'nullable|date',
            'tampil_di_dashboard' => 'nullable|boolean',
        ]);
    }

    private function kegiatanOptions()
    {
        return Kegiatan::where('mosque_id', $this->activeMosqueId())
            ->orderByDesc('tanggal_mulai')
            ->orderBy('nama_kegiatan')
            ->get();
    }

    private function authorizePengumuman(Pengumuman $pengumuman): void
    {
        $mosqueId = $this->activeMosqueId();

        abort_unless($mosqueId && (int) $pengumuman->mosque_id === (int) $mosqueId, 404);
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function uniqueSlug(?string $slug, string $title, int $mosqueId, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug ?: $title) ?: 'pengumuman';
        $candidate = $baseSlug;
        $counter = 2;

        while (Pengumuman::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '<>', $ignoreId))
            ->exists()) {
            $candidate = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    private function publishedAtValue(string $status, ?string $publishedAt, $currentPublishedAt = null)
    {
        if ($publishedAt) {
            return $publishedAt;
        }

        if ($status === 'terbit') {
            return $currentPublishedAt ?: now();
        }

        return null;
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
