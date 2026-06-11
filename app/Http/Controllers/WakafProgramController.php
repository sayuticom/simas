<?php

namespace App\Http\Controllers;

use App\Models\WakafProgram;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WakafProgramController extends Controller
{
    public function index()
    {
        $programs = WakafProgram::latest()->paginate(10);

        return view('admin.wakaf.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.wakaf.programs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_dana' => 'nullable|numeric|min:0',
            'tujuan' => 'nullable|string',
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif', 'selesai'])],
        ]);

        $data['target_dana'] = $data['target_dana'] ?? 0;
        $data['status'] = $data['status'] ?? 'aktif';

        WakafProgram::create($data);

        return redirect()->route('wakaf.programs.index')->with('success', 'Program Wakaf berhasil disimpan.');
    }

    public function show(WakafProgram $program)
    {
        return view('admin.wakaf.programs.show', compact('program'));
    }

    public function edit(WakafProgram $program)
    {
        return view('admin.wakaf.programs.edit', compact('program'));
    }

    public function update(Request $request, WakafProgram $program)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_dana' => 'nullable|numeric|min:0',
            'tujuan' => 'nullable|string',
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif', 'selesai'])],
        ]);

        $data['target_dana'] = $data['target_dana'] ?? 0;
        $data['status'] = $data['status'] ?? 'aktif';

        $program->update($data);

        return redirect()->route('wakaf.programs.index')->with('success', 'Program Wakaf berhasil diperbarui.');
    }

    public function destroy(WakafProgram $program)
    {
        if ($program->wakafCashes()->exists()) {
            return redirect()
                ->route('wakaf.programs.index')
                ->with('error', 'Program Wakaf tidak dapat dihapus karena sudah digunakan pada transaksi wakaf tunai.');
        }

        $program->delete();

        return redirect()->route('wakaf.programs.index')->with('success', 'Program Wakaf berhasil dihapus.');
    }
}
