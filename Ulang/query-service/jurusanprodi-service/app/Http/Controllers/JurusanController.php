<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function index()
    {
        return response()->json(Jurusan::all());
    }

    public function show($kodejurusan)
    {
        $jurusan = Jurusan::where('kodejurusan', $kodejurusan)->first();

        if (!$jurusan) {
            return response()->json(['message' => 'Jurusan tidak ditemukan'], 404);
        }

        return response()->json($jurusan);
    }

    public function store(Request $request)
    {
        Jurusan::create($request->only(['kodejurusan', 'namajurusan']));
        return response()->json(['message' => 'Jurusan berhasil disinkronkan']);
    }

    public function update(Request $request, $kodejurusan)
    {
        $jurusan = Jurusan::where('kodejurusan', $kodejurusan)->firstOrFail();
        $jurusan->update($request->only(['kodejurusan', 'namajurusan']));
        return response()->json(['message' => 'Jurusan berhasil diperbarui']);
    }

    public function destroy($kodejurusan)
    {
        $jurusan = Jurusan::where('kodejurusan', $kodejurusan)->firstOrFail();
        $jurusan->delete();
        return response()->json(['message' => 'Jurusan berhasil dihapus']);
    }
}
