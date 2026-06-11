<?php

namespace App\Http\Controllers;

use App\Models\ZisProgram;
use Illuminate\Http\Request;

class ZisProgramController extends Controller
{
    public function index()
    {
        $programs = ZisProgram::latest()->paginate(10);

        return view('admin.zis.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.zis.programs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_dana' => 'required|numeric|min:0',
            'status' => 'required|string|max:50',
        ]);

        ZisProgram::create($data);

        return redirect()->route('zis.programs.index')->with('success', 'Program ZIS berhasil disimpan.');
    }

    public function show(ZisProgram $program)
    {
        return view('admin.zis.programs.show', compact('program'));
    }

    public function edit(ZisProgram $program)
    {
        return view('admin.zis.programs.edit', compact('program'));
    }

    public function update(Request $request, ZisProgram $program)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target_dana' => 'required|numeric|min:0',
            'status' => 'required|string|max:50',
        ]);

        $program->update($data);

        return redirect()->route('zis.programs.index')->with('success', 'Program ZIS berhasil diperbarui.');
    }

    public function destroy(ZisProgram $program)
    {
        $program->delete();

        return redirect()->route('zis.programs.index')->with('success', 'Program ZIS berhasil dihapus.');
    }
}
