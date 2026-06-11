<?php

namespace App\Http\Controllers;

use App\Models\Jamaah;
use App\Models\JamaahCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JamaahController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $kategori = $request->query('kategori');

        $jamaahs = Jamaah::with('categories')
            ->when($search, fn ($query, $value) => $query->where(function ($sub) use ($value) {
                $sub->where('nama', 'like', "%{$value}%")
                    ->orWhere('no_hp', 'like', "%{$value}%")
                    ->orWhere('alamat', 'like', "%{$value}%");
            }))
            ->when($status, fn ($query, $value) => $query->where('status', $value))
            ->when($kategori, fn ($query, $value) => $query->whereHas(
                'categories',
                fn ($categoryQuery) => $categoryQuery->where('jamaah_categories.name', $value)
            ))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $statusOptions = Jamaah::STATUS_OPTIONS;
        $categories = $this->categories();

        return view('admin.jamaah.index', compact('jamaahs', 'statusOptions', 'categories'));
    }

    public function create(): View
    {
        $statusOptions = Jamaah::STATUS_OPTIONS;
        $categories = $this->categories();
        $genderOptions = ['Laki-laki', 'Perempuan'];
        $pekerjaanOptions = Jamaah::PEKERJAAN_OPTIONS;

        return view('admin.jamaah.create', compact('statusOptions', 'categories', 'genderOptions', 'pekerjaanOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:50',
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['required', 'integer', 'distinct', Rule::exists('jamaah_categories', 'id')],
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer|min:0|max:120',
            'pekerjaan' => ['nullable', 'string', 'max:255', Rule::in(Jamaah::PEKERJAAN_OPTIONS)],
            'pekerjaan_lainnya' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($request->input('pekerjaan') === Jamaah::PEKERJAAN_LAINNYA),
            ],
            'keahlian' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(array_keys(Jamaah::STATUS_OPTIONS))],
            'keterangan' => 'nullable|string',
        ]);

        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);
        $data['pekerjaan'] = $this->pekerjaanFinal($data);
        unset($data['pekerjaan_lainnya']);
        $data['kategori'] = JamaahCategory::whereKey($categoryIds[0])->value('name');

        DB::transaction(function () use ($data, $categoryIds): void {
            $jamaah = Jamaah::create($data);
            $jamaah->categories()->sync($categoryIds);
        });

        return redirect()->route('jamaah.index')->with('success', 'Data jamaah berhasil ditambahkan.');
    }

    public function show(Jamaah $jamaah): View
    {
        $jamaah->load('categories');

        return view('admin.jamaah.show', compact('jamaah'));
    }

    public function edit(Jamaah $jamaah): View
    {
        $statusOptions = Jamaah::STATUS_OPTIONS;
        $categories = $this->categories();
        $genderOptions = ['Laki-laki', 'Perempuan'];
        $pekerjaanOptions = Jamaah::PEKERJAAN_OPTIONS;
        $jamaah->load('categories');

        return view('admin.jamaah.edit', compact('jamaah', 'statusOptions', 'categories', 'genderOptions', 'pekerjaanOptions'));
    }

    public function update(Request $request, Jamaah $jamaah)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:50',
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['required', 'integer', 'distinct', Rule::exists('jamaah_categories', 'id')],
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer|min:0|max:120',
            'pekerjaan' => ['nullable', 'string', 'max:255', Rule::in(Jamaah::PEKERJAAN_OPTIONS)],
            'pekerjaan_lainnya' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($request->input('pekerjaan') === Jamaah::PEKERJAAN_LAINNYA),
            ],
            'keahlian' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(array_keys(Jamaah::STATUS_OPTIONS))],
            'keterangan' => 'nullable|string',
        ]);

        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);
        $data['pekerjaan'] = $this->pekerjaanFinal($data);
        unset($data['pekerjaan_lainnya']);
        $data['kategori'] = JamaahCategory::whereKey($categoryIds[0])->value('name');

        DB::transaction(function () use ($data, $categoryIds, $jamaah): void {
            $jamaah->update($data);
            $jamaah->categories()->sync($categoryIds);
        });

        return redirect()->route('jamaah.index')->with('success', 'Data jamaah berhasil diperbarui.');
    }

    public function destroy(Jamaah $jamaah)
    {
        $jamaah->delete();

        return redirect()->route('jamaah.index')->with('success', 'Data jamaah berhasil dihapus.');
    }

    private function categories(): Collection
    {
        return JamaahCategory::orderBy('label')->get();
    }

    private function pekerjaanFinal(array $data): ?string
    {
        if (($data['pekerjaan'] ?? null) === Jamaah::PEKERJAAN_LAINNYA) {
            return trim($data['pekerjaan_lainnya']);
        }

        return $data['pekerjaan'] ?? null;
    }
}
