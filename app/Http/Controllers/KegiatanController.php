<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\DesignRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        $filters = $request->only(['q', 'status', 'jenis_kegiatan', 'bulan']);

        $calendarMonth = $request->filled('bulan')
            ? Carbon::parse($request->input('bulan'))->startOfMonth()
            : now()->startOfMonth();

        $baseQuery = Kegiatan::where('mosque_id', $mosqueId);

        $totalKegiatan = (clone $baseQuery)->count();
        $kegiatanHariIni = (clone $baseQuery)->whereDate('tanggal_mulai', today())->count();
        $kegiatanMingguIni = (clone $baseQuery)
            ->whereBetween('tanggal_mulai', [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::SUNDAY)])
            ->count();
        $kegiatanBulanIni = (clone $baseQuery)
            ->whereBetween('tanggal_mulai', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $kegiatanSelesai = (clone $baseQuery)->where('status', 'selesai')->count();
        $kegiatanBatal = (clone $baseQuery)->where('status', 'batal')->count();

        $agendaMendatang = Kegiatan::where('mosque_id', $mosqueId)
            ->withCount('jadwalPetugas')
            ->whereNotNull('tanggal_mulai')
            ->where('tanggal_mulai', '>=', now()->startOfDay())
            ->orderBy('tanggal_mulai')
            ->limit(10)
            ->get();

        $calendarEvents = Kegiatan::where('mosque_id', $mosqueId)
            ->withCount('jadwalPetugas')
            ->whereNotNull('tanggal_mulai')
            ->orderBy('tanggal_mulai')
            ->get()
            ->map(fn (Kegiatan $kegiatan) => [
                'title' => $kegiatan->nama_kegiatan,
                'start' => $kegiatan->tanggal_mulai?->toIso8601String(),
                'end' => $kegiatan->tanggal_selesai?->toIso8601String(),
                'url' => route('kegiatan.show', $kegiatan),
                'extendedProps' => [
                    'lokasi' => $kegiatan->lokasi,
                    'status' => $kegiatan->status,
                    'jenis_kegiatan' => $kegiatan->jenis_kegiatan,
                    'penanggung_jawab' => $kegiatan->penanggung_jawab,
                    'petugas_count' => $kegiatan->jadwal_petugas_count,
                ],
            ])
            ->values();

        $jenisKegiatanOptions = collect(Kegiatan::jenisOptions())->merge(Kegiatan::where('mosque_id', $mosqueId)
            ->whereNotNull('jenis_kegiatan')
            ->where('jenis_kegiatan', '<>', '')
            ->distinct()
            ->orderBy('jenis_kegiatan')
            ->pluck('jenis_kegiatan'))
            ->unique()
            ->values();

        $kegiatans = Kegiatan::where('mosque_id', $mosqueId)
            ->withCount('jadwalPetugas')
            ->when($filters['q'] ?? null, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('nama_kegiatan', 'like', "%{$keyword}%")
                        ->orWhere('jenis_kegiatan', 'like', "%{$keyword}%")
                        ->orWhere('lokasi', 'like', "%{$keyword}%")
                        ->orWhere('penanggung_jawab', 'like', "%{$keyword}%")
                        ->orWhere('narasumber', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['jenis_kegiatan'] ?? null, function ($query, $jenisKegiatan) {
                $query->where('jenis_kegiatan', $jenisKegiatan);
            })
            ->when($filters['bulan'] ?? null, function ($query, $bulan) {
                $month = Carbon::parse($bulan)->startOfMonth();
                $query->whereBetween('tanggal_mulai', [$month, $month->copy()->endOfMonth()]);
            })
            ->latest('tanggal_mulai')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.kegiatan.index', compact(
            'agendaMendatang',
            'calendarEvents',
            'calendarMonth',
            'filters',
            'jenisKegiatanOptions',
            'kegiatanBatal',
            'kegiatanBulanIni',
            'kegiatanHariIni',
            'kegiatanMingguIni',
            'kegiatanSelesai',
            'kegiatans',
            'totalKegiatan'
        ));
    }

    public function create()
    {
        return view('admin.kegiatan.create', [
            'jenisOptions' => Kegiatan::jenisOptions(),
            'kegiatan' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['status'] = $data['status'] ?? 'terencana';
        $data['status_publik'] = $data['status_publik'] ?? 'draft';
        $data['tampilkan_di_website'] = $request->boolean('tampilkan_di_website');
        $data['prompt_pakai_foto_narasumber'] = $request->boolean('prompt_pakai_foto_narasumber');
        $data['prompt_elemen_desain'] = $data['prompt_elemen_desain'] ?? [];

        if ($request->hasFile('poster_publik')) {
            $data['poster_publik'] = $request->file('poster_publik')->store('website/events', 'public');
        }

        Kegiatan::create($data);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil disimpan.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);
        $kegiatan->load(['jadwalPetugas' => function ($query) {
            $query->with('user')
                ->orderBy('tanggal')
                ->orderBy('waktu_mulai');
        }]);

        return view('admin.kegiatan.show', [
            'kegiatan' => $kegiatan,
            'existingDesignRequest' => $this->existingDesignRequest($kegiatan),
        ]);
    }

    public function edit(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        return view('admin.kegiatan.edit', [
            'jenisOptions' => Kegiatan::jenisOptions(),
            'kegiatan' => $kegiatan,
            'existingDesignRequest' => $this->existingDesignRequest($kegiatan),
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        $data = $this->validatedData($request, $kegiatan);
        $data['status'] = $data['status'] ?? 'terencana';
        $data['status_publik'] = $data['status_publik'] ?? 'draft';
        $data['tampilkan_di_website'] = $request->boolean('tampilkan_di_website');
        $data['prompt_pakai_foto_narasumber'] = $request->boolean('prompt_pakai_foto_narasumber');
        $data['prompt_elemen_desain'] = $data['prompt_elemen_desain'] ?? [];

        if ($request->hasFile('poster_publik')) {
            $this->deletePublicFile($kegiatan->poster_publik);
            $data['poster_publik'] = $request->file('poster_publik')->store('website/events', 'public');
        }

        $kegiatan->update($data);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        if ($kegiatan->jadwalPetugas()->exists()) {
            return back()->with('error', 'Kegiatan tidak bisa dihapus karena sudah memiliki jadwal petugas. Hapus jadwal petugas terlebih dahulu.');
        }

        $kegiatan->delete();

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Kegiatan $kegiatan = null): array
    {
        $jenisOptions = Kegiatan::jenisOptions();
        if ($kegiatan?->jenis_kegiatan && ! in_array($kegiatan->jenis_kegiatan, $jenisOptions, true)) {
            $jenisOptions[] = $kegiatan->jenis_kegiatan;
        }

        return $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => ['nullable', 'string', 'max:255', Rule::in($jenisOptions)],
            'tema_materi' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'narasumber' => 'nullable|string|max:255',
            'target_peserta' => 'nullable|string|max:255',
            'kontak_person' => 'nullable|string|max:255',
            'nomor_kontak' => 'nullable|string|max:255',
            'label_kontak' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::labelKontakOptions())],
            'status' => ['nullable', Rule::in(['terencana', 'berjalan', 'selesai', 'batal'])],
            'deskripsi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'tampilkan_di_website' => 'nullable|boolean',
            'status_publik' => ['nullable', Rule::in(['draft', 'tayang', 'arsip'])],
            'judul_publik' => 'nullable|string|max:255',
            'deskripsi_publik' => 'nullable|string',
            'poster_publik' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'prompt_nuansa_desain' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptNuansaOptions())],
            'prompt_warna_utama' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptWarnaOptions())],
            'prompt_gaya_desain' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptGayaOptions())],
            'prompt_catatan_khusus' => 'nullable|string',
            'prompt_instruksi_foto' => 'nullable|string',
            'prompt_pakai_foto_narasumber' => 'nullable|boolean',
            'prompt_posisi_foto_pemateri' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptPosisiFotoOptions())],
            'prompt_tujuan_flyer' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptTujuanFlyerOptions())],
            'prompt_model_layout' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptModelLayoutOptions())],
            'prompt_kepadatan_teks' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptKepadatanTeksOptions())],
            'prompt_target_audiens' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptTargetAudiensOptions())],
            'prompt_tingkat_keramaian' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptTingkatKeramaianOptions())],
            'prompt_fokus_utama' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptFokusUtamaOptions())],
            'prompt_elemen_desain' => 'nullable|array',
            'prompt_elemen_desain.*' => ['nullable', 'string', 'max:255', Rule::in(Kegiatan::promptElemenDesainOptions())],
            'prompt_catatan_tambahan' => 'nullable|string',
        ]);
    }

    private function authorizeKegiatan(Kegiatan $kegiatan): void
    {
        $mosqueId = $this->activeMosqueId();

        abort_unless($mosqueId && (int) $kegiatan->mosque_id === (int) $mosqueId, 404);
    }

    private function existingDesignRequest(Kegiatan $kegiatan): ?DesignRequest
    {
        return DesignRequest::where('mosque_id', $this->activeMosqueId())
            ->where('source_type', 'kegiatan')
            ->where('source_id', $kegiatan->id)
            ->latest()
            ->first();
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

}
