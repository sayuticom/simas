<?php

namespace App\Http\Controllers;

use App\Models\MosquePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MosquePhotoController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $activeMosque = $user?->getActiveMosque();

        if (! $activeMosque) {
            return back()->withErrors(['active_mosque' => 'Masjid aktif tidak ditemukan.']);
        }

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png,gif,svg|max:5120',
        ]);

        $created = 0;
        foreach ($request->file('photos') as $file) {
            $path = $file->store('mosque_photos', 'public');

            MosquePhoto::create([
                'mosque_id' => $activeMosque->id,
                'path' => $path,
            ]);

            $created++;
        }

        return back()->with('success', "Berhasil mengunggah {$created} foto.");
    }

    public function destroy(MosquePhoto $photo)
    {
        $user = auth()->user();
        $activeMosque = $user?->getActiveMosque();

        if (! $activeMosque || $photo->mosque_id !== $activeMosque->id) {
            abort(403);
        }

        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function feature(MosquePhoto $photo)
    {
        $user = auth()->user();
        $activeMosque = $user?->getActiveMosque();

        if (! $activeMosque || $photo->mosque_id !== $activeMosque->id) {
            abort(403);
        }

        // Unset existing featured photos for this mosque
        MosquePhoto::where('mosque_id', $activeMosque->id)->update(['is_featured' => false]);

        // Set this photo as featured
        $photo->is_featured = true;
        $photo->save();

        return back()->with('success', 'Foto ditetapkan sebagai tampak depan.');
    }
}
