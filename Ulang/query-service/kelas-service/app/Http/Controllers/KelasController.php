<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        return response()->json(Kelas::all());
    }

    public function show($nim, $kodekelas, $semester)
    {
        $kelas = Kelas::where('nim', $nim)
                      ->where('kodekelas', $kodekelas)
                      ->where('semester', $semester)
                      ->first();

        if (!$kelas) {
            return response()->json(['message' => 'kelas tidak ditemukan'], 404);
        }

        return response()->json($kelas);
    }

    public function store(Request $request)
    {
        Kelas::create($request->all());

        return response()->json(['message' => 'Data kelas berhasil disinkronkan']);
    }

    public function update(Request $request, $nim, $kodekelas, $semester)
    {
        $kelas = Kelas::where('nim', $nim)
                      ->where('kodekelas', $kodekelas)
                      ->where('semester', $semester)
                      ->first();

        if ($kelas) {
            $kelas->update($request->all());
            return response()->json(['message' => 'Data kelas berhasil diperbarui']);
        }

        Kelas::create(array_merge($request->all(), [
            'nim' => $nim,
            'kodekelas' => $kodekelas,
            'semester' => $semester,
        ]));

        return response()->json(['message' => 'Data kelas tidak ditemukan, jadi ditambahkan']);
    }

    public function destroy($nim, $kodekelas, $semester)
    {
        $kelas = Kelas::where('nim', $nim)
                      ->where('kodekelas', $kodekelas)
                      ->where('semester', $semester)
                      ->firstOrFail();

        $kelas->delete();

        return response()->json(['message' => 'Data kelas berhasil dihapus']);
    }
}
