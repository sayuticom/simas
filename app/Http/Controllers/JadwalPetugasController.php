<?php

namespace App\Http\Controllers;

use App\Models\JadwalPetugas;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JadwalPetugasController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['tanggal_awal', 'tanggal_akhir', 'status', 'kegiatan_id', 'q']);
        [$kegiatans] = $this->formOptions();

        $jadwalPetugas = JadwalPetugas::with(['kegiatan', 'user'])
            ->when($filters['tanggal_awal'] ?? null, function ($query, $tanggalAwal) {
                $query->whereDate('tanggal', '>=', $tanggalAwal);
            })
            ->when($filters['tanggal_akhir'] ?? null, function ($query, $tanggalAkhir) {
                $query->whereDate('tanggal', '<=', $tanggalAkhir);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['kegiatan_id'] ?? null, function ($query, $kegiatanId) {
                $query->where('kegiatan_id', $kegiatanId);
            })
            ->when($filters['q'] ?? null, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('nama_petugas', 'like', "%{$keyword}%")
                        ->orWhere('jenis_tugas', 'like', "%{$keyword}%")
                        ->orWhere('lokasi', 'like', "%{$keyword}%")
                        ->orWhereHas('user', function ($query) use ($keyword) {
                            $query->where('name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->orderByDesc('tanggal')
            ->orderBy('waktu_mulai')
            ->paginate(10)
            ->withQueryString();

        return view('admin.jadwal-petugas.index', compact('filters', 'jadwalPetugas', 'kegiatans'));
    }

    public function create(Request $request)
    {
        [$kegiatans, $users] = $this->formOptions();
        $selectedKegiatan = null;

        if ($request->filled('kegiatan_id')) {
            $selectedKegiatan = Kegiatan::where('mosque_id', $this->activeMosqueId())
                ->findOrFail($request->integer('kegiatan_id'));
        }

        return view('admin.jadwal-petugas.create', [
            'jadwalPetugas' => null,
            'kegiatans' => $kegiatans,
            'selectedKegiatan' => $selectedKegiatan,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['status'] = $data['status'] ?? 'terjadwal';

        JadwalPetugas::create($data);

        return redirect()->route('jadwal-petugas.index')->with('success', 'Jadwal Petugas berhasil disimpan.');
    }

    public function show(JadwalPetugas $jadwalPetugas)
    {
        $this->authorizeJadwal($jadwalPetugas);
        $jadwalPetugas->load(['kegiatan', 'user']);

        return view('admin.jadwal-petugas.show', ['jadwalPetugas' => $jadwalPetugas]);
    }

    public function edit(JadwalPetugas $jadwalPetugas)
    {
        $this->authorizeJadwal($jadwalPetugas);
        [$kegiatans, $users] = $this->formOptions();

        return view('admin.jadwal-petugas.edit', [
            'jadwalPetugas' => $jadwalPetugas,
            'kegiatans' => $kegiatans,
            'selectedKegiatan' => null,
            'users' => $users,
        ]);
    }

    public function update(Request $request, JadwalPetugas $jadwalPetugas)
    {
        $this->authorizeJadwal($jadwalPetugas);

        $data = $this->validatedData($request);
        $data['status'] = $data['status'] ?? 'terjadwal';

        $jadwalPetugas->update($data);

        return redirect()->route('jadwal-petugas.index')->with('success', 'Jadwal Petugas berhasil diperbarui.');
    }

    public function destroy(JadwalPetugas $jadwalPetugas)
    {
        $this->authorizeJadwal($jadwalPetugas);

        $jadwalPetugas->delete();

        return redirect()->route('jadwal-petugas.index')->with('success', 'Jadwal Petugas berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $mosqueId = $this->activeMosqueId();

        return $request->validate([
            'kegiatan_id' => [
                'nullable',
                Rule::exists('kegiatans', 'id')->where('mosque_id', $mosqueId),
            ],
            'user_id' => [
                'nullable',
                'required_without:nama_petugas',
                Rule::exists('users', 'id')->where(function ($query) use ($mosqueId) {
                    $query->where('active_mosque_id', $mosqueId)
                        ->orWhereIn('id', DB::table('role_user')
                            ->select('user_id')
                            ->where('mosque_id', $mosqueId));
                }),
            ],
            'nama_petugas' => 'nullable|required_without:user_id|string|max:255',
            'jenis_tugas' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'lokasi' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['terjadwal', 'hadir', 'berhalangan', 'selesai', 'batal'])],
            'keterangan' => 'nullable|string',
        ]);
    }

    private function formOptions(): array
    {
        $mosqueId = $this->activeMosqueId();

        $kegiatans = Kegiatan::where('mosque_id', $mosqueId)
            ->orderByDesc('tanggal_mulai')
            ->orderBy('nama_kegiatan')
            ->get();

        $users = User::where('active_mosque_id', $mosqueId)
            ->orWhereHas('roles', function ($query) use ($mosqueId) {
                $query->where('role_user.mosque_id', $mosqueId);
            })
            ->orderBy('name')
            ->get()
            ->unique('id')
            ->values();

        return [$kegiatans, $users];
    }

    private function authorizeJadwal(JadwalPetugas $jadwalPetugas): void
    {
        $mosqueId = $this->activeMosqueId();

        abort_unless($mosqueId && (int) $jadwalPetugas->mosque_id === (int) $mosqueId, 404);
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }
}
