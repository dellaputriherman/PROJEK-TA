<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;

class MahasiswaController extends Controller
{

    public function index()
    {
        return response()->json(Mahasiswa::all());
    }

    public function show($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json(['message' => 'Mahasiswa tidak ditemukan'], 404);
        }

        return response()->json($mahasiswa);
    }

    public function store(Request $request)
    {
        Mahasiswa::create($request->all());
        return response()->json(['message' => 'Mahasiswa berhasil disinkronkan']);
    }

    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
        $mahasiswa->update($request->all());
        return response()->json(['message' => 'Mahasiswa berhasil diperbarui']);
    }

    public function destroy($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
        $mahasiswa->delete();
        return response()->json(['message' => 'Mahasiswa berhasil dihapus']);
    }
}
