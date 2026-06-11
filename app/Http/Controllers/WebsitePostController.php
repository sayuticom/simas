<?php

namespace App\Http\Controllers;

use App\Models\WebsitePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebsitePostController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['type', 'status', 'q']);

        $posts = WebsitePost::with(['creator', 'updater'])
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('title', 'like', '%'.$q.'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.website_posts.index', [
            'posts' => $posts,
            'filters' => $filters,
            'typeOptions' => WebsitePost::typeOptions(),
            'statusOptions' => WebsitePost::statusOptions(),
        ]);
    }

    public function create()
    {
        return view('admin.website_posts.create', [
            'post' => null,
            'typeOptions' => WebsitePost::typeOptions(),
            'statusOptions' => WebsitePost::statusOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['mosque_id'] = $this->activeMosqueId();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $data['mosque_id'], $data['type']);
        $data['published_at'] = $this->publishedAtValue($data['status'], $data['published_at'] ?? null);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('website/posts', 'public');
        }

        WebsitePost::create($data);

        return redirect()->route('website-posts.index')->with('success', 'Konten website berhasil disimpan.');
    }

    public function show(WebsitePost $websitePost)
    {
        $this->authorizeWebsitePost($websitePost);
        $websitePost->load(['creator', 'updater']);

        return view('admin.website_posts.show', [
            'post' => $websitePost,
            'typeOptions' => WebsitePost::typeOptions(),
            'statusOptions' => WebsitePost::statusOptions(),
        ]);
    }

    public function edit(WebsitePost $websitePost)
    {
        $this->authorizeWebsitePost($websitePost);

        return view('admin.website_posts.edit', [
            'post' => $websitePost,
            'typeOptions' => WebsitePost::typeOptions(),
            'statusOptions' => WebsitePost::statusOptions(),
        ]);
    }

    public function update(Request $request, WebsitePost $websitePost)
    {
        $this->authorizeWebsitePost($websitePost);

        $data = $this->validatedData($request);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $websitePost->mosque_id, $data['type'], $websitePost->id);
        $data['published_at'] = $this->publishedAtValue($data['status'], $data['published_at'] ?? null, $websitePost->published_at);
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $newImage = $request->file('featured_image')->store('website/posts', 'public');
            $oldImage = $websitePost->featured_image;
            $data['featured_image'] = $newImage;
        }

        $websitePost->update($data);

        if (isset($oldImage)) {
            $this->deletePublicFile($oldImage);
        }

        return redirect()->route('website-posts.index')->with('success', 'Konten website berhasil diperbarui.');
    }

    public function destroy(WebsitePost $websitePost)
    {
        $this->authorizeWebsitePost($websitePost);
        $websitePost->delete();

        return redirect()->route('website-posts.index')->with('success', 'Konten website berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(array_keys(WebsitePost::typeOptions()))],
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => ['required', Rule::in(array_keys(WebsitePost::statusOptions()))],
            'published_at' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
        ]);
    }

    private function authorizeWebsitePost(WebsitePost $post): void
    {
        $mosqueId = $this->activeMosqueId();

        abort_unless($mosqueId && (int) $post->mosque_id === (int) $mosqueId, 404);
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function uniqueSlug(?string $slug, string $title, int $mosqueId, string $type, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug ?: $title) ?: 'konten';
        $candidate = $baseSlug;
        $counter = 2;

        while (WebsitePost::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->where('type', $type)
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

        if ($status === WebsitePost::STATUS_PUBLISHED) {
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
