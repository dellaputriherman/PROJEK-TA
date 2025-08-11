<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matakuliah;

class MatakuliahController extends Controller
{
    public function index()
    {
        $matakuliah = Matakuliah::all();
        return response()->json($matakuliah);
    }

    public function show($kodematkul)
    {
        $matakuliah = Matakuliah::where('kodematkul', $kodematkul)->first();

        if (!$matakuliah) {
            return response()->json(['message' => 'Matakuliah tidak ditemukan'], 404);
        }

        return response()->json($matakuliah);
    }

    public function store(Request $request)
    {
        Matakuliah::create($request->all());
        return response()->json(['message' => 'Matakuliah berhasil disinkronkan']);
    }

    public function update(Request $request, $kodematkul)
    {
        $matakuliah = Matakuliah::where('kodematkul', $kodematkul)->firstOrFail();
        $matakuliah->update($request->all());
        return response()->json(['message' => 'Matakuliah berhasil diperbarui']);
    }

    public function destroy($kodematkul)
    {
        $matakuliah = Matakuliah::where('kodematkul', $kodematkul)->firstOrFail();
        $matakuliah->delete();
        return response()->json(['message' => 'Matakuliah berhasil dihapus']);
    }
}
