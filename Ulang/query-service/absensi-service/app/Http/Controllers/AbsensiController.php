<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;

class AbsensiController extends Controller
{
     public function index()
    {
        $data = Absensi::all();

        return response()->json($data);
    }

    public function show($nim, $kodematkul, $tanggal)
    {
        $absensi = Absensi::where('nim', $nim)
            ->where('kodematkul', $kodematkul)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            return response()->json(['message' => 'Absensi tidak ditemukan'], 404);
        }

        return response()->json($absensi);
    }

    public function store(Request $request)
    {
        $exists = Absensi::where('nim', $request->nim)
            ->where('kodematkul', $request->kodematkul)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Data absensi sudah ada'], 409);
        }

        Absensi::create($request->all());
        return response()->json(['message' => 'Data absensi berhasil disimpan']);
    }

     public function update(Request $request, $nim, $kodematkul, $tanggal)
    {
        $absensi = Absensi::where('nim', $nim)
                      ->where('kodematkul', $kodematkul)
                      ->where('tanggal', $tanggal)
                      ->first();

        if ($absensi) {
            $absensi->update($request->all());
            return response()->json(['message' => 'Data nilai berhasil diperbarui']);
        }

        Absensi::create(array_merge($request->all(), [
            'nim' => $nim,
            'kodematkul' => $kodematkul,
            'tanggal' => $tanggal,
        ]));

        return response()->json(['message' => 'Data absensi tidak ditemukan, jadi ditambahkan']);
    }


    public function destroy($nim, $kodematkul, $tanggal)
    {
        $absensi = Absensi::where('nim', $nim)
            ->where('kodematkul', $kodematkul)
            ->where('tanggal', $tanggal)
            ->firstOrFail();

        $absensi->delete();
        return response()->json(['message' => 'Data absensi berhasil dihapus']);
    }
}
