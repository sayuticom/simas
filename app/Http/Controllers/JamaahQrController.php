<?php

namespace App\Http\Controllers;

use App\Models\Jamaah;
use App\Models\JamaahCategory;
use App\Models\Mosque;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JamaahQrController extends Controller
{
    public function showQr(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $mosque = $user->activeMosque;

        if (! $mosque) {
            $availableMosques = $user->selectableMosques();

            if ($availableMosques->count() === 1) {
                $mosque = $availableMosques->first();
                $user->setActiveMosque($mosque->id);
                $request->session()->put('active_mosque_id', $mosque->id);
            }
        }

        if (! $mosque) {
            return redirect()
                ->route('dashboard', ['choose_mosque' => 1])
                ->with('error', 'Pilih atau buat masjid terlebih dahulu untuk membuat QR input jamaah.');
        }

        $token = $mosque->ensureQrToken();
        $registrationUrl = route('jamaah.public.create', $token);

        return view('admin.jamaah.qr', compact('mosque', 'registrationUrl'));
    }

    public function showPublicForm(string $token): View
    {
        $mosque = $this->mosqueFromToken($token);
        $pekerjaanOptions = Jamaah::PEKERJAAN_OPTIONS;
        $genderOptions = ['Laki-laki', 'Perempuan'];
        $categories = JamaahCategory::orderBy('label')->get();
        $defaultCategory = JamaahCategory::where('name', 'jamaah_aktif')->first()
            ?? JamaahCategory::where('name', 'jamaah_tetap')->first();

        return view('jamaah_public.create', compact('mosque', 'pekerjaanOptions', 'genderOptions', 'categories', 'defaultCategory'));
    }

    public function storePublicForm(Request $request, string $token): View
    {
        $mosque = $this->mosqueFromToken($token);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer|min:0|max:120',
            'pekerjaan' => ['nullable', 'string', 'max:255', Rule::in(Jamaah::PEKERJAAN_OPTIONS)],
            'pekerjaan_lainnya' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($request->input('pekerjaan') === Jamaah::PEKERJAAN_LAINNYA),
            ],
            'category_id' => ['nullable', 'integer', Rule::exists('jamaah_categories', 'id')],
            'keterangan' => 'nullable|string',
        ]);

        $category = $this->categoryFromRequest($data['category_id'] ?? null);
        unset($data['category_id']);

        $data['pekerjaan'] = $this->pekerjaanFinal($data);
        unset($data['pekerjaan_lainnya']);
        $data['kategori'] = $category?->name ?? 'jamaah_aktif';
        $data['status'] = Jamaah::STATUS_PENDING;

        DB::transaction(function () use ($data, $category, $mosque): void {
            $jamaah = new Jamaah($data);
            $jamaah->mosque_id = $mosque->id;
            $jamaah->save();

            if ($category) {
                $jamaah->categories()->sync([$category->id]);
            }
        });

        return view('jamaah_public.submitted', compact('mosque'));
    }

    private function mosqueFromToken(string $token): Mosque
    {
        return Mosque::where('qr_token', $token)->firstOrFail();
    }

    private function categoryFromRequest(?int $categoryId): ?JamaahCategory
    {
        if ($categoryId) {
            return JamaahCategory::find($categoryId);
        }

        return JamaahCategory::where('name', 'jamaah_aktif')->first()
            ?? JamaahCategory::where('name', 'jamaah_tetap')->first();
    }

    private function pekerjaanFinal(array $data): ?string
    {
        if (($data['pekerjaan'] ?? null) === Jamaah::PEKERJAAN_LAINNYA) {
            return trim($data['pekerjaan_lainnya']);
        }

        return $data['pekerjaan'] ?? null;
    }
}
