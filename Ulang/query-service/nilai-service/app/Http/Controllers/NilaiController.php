<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nilai;

class NilaiController extends Controller
{
    public function index()
    {
        return response()->json(Nilai::all());
    }

    public function show($nim, $kodematkul)
    {
        $nilai = Nilai::where('nim', $nim)
                      ->where('kodematkul', $kodematkul)
                      ->first();

        if (!$nilai) {
            return response()->json(['message' => 'Nilai tidak ditemukan'], 404);
        }

        return response()->json($nilai);
    }

    public function store(Request $request)
    {
        Nilai::create($request->all());

        return response()->json(['message' => 'Data nilai berhasil disinkronkan']);
    }

    public function update(Request $request, $nim, $kodematkul)
    {
        $nilai = Nilai::where('nim', $nim)
                      ->where('kodematkul', $kodematkul)
                      ->first();

        if ($nilai) {
            $nilai->update($request->all());
            return response()->json(['message' => 'Data nilai berhasil diperbarui']);
        }

        Nilai::create(array_merge($request->all(), [
            'nim' => $nim,
            'kodematkul' => $kodematkul,
        ]));

        return response()->json(['message' => 'Data nilai tidak ditemukan, jadi ditambahkan']);
    }

    public function destroy($nim, $kodematkul)
    {
        $nilai = Nilai::where('nim', $nim)
                      ->where('kodematkul', $kodematkul)
                      ->firstOrFail();

        $nilai->delete();

        return response()->json(['message' => 'Data nilai berhasil dihapus']);
    }
}
