<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwalkuliah;

class JadwalkuliahController extends Controller
{
    public function index()
    {
        return response()->json(Jadwalkuliah::all());
    }

    public function show($kodekelas, $kodematkul)
    {
        $jadwalkuliah = Jadwalkuliah::where('kodekelas', $kodekelas)
                      ->where('kodematkul', $kodematkul)
                      ->first();

        if (!$jadwalkuliah) {
            return response()->json(['message' => 'jadwalkuliah tidak ditemukan'], 404);
        }

        return response()->json($jadwalkuliah);
    }

    public function store(Request $request)
    {
        Jadwalkuliah::create($request->all());
        return response()->json(['message' => 'jadwalkuliah berhasil disinkronkan']);
    }

    public function update(Request $request, $kodekelas, $kodematkul)
    {
        $jadwalkuliah = Jadwalkuliah::where('kodekelas', $kodekelas)
                      ->where('kodematkul', $kodematkul)
                      ->first();

        if ($jadwalkuliah) {
            $jadwalkuliah->update($request->all());
            return response()->json(['message' => 'Data jadwalkuliah berhasil diperbarui']);
        }

        // Jika belum ada, insert baru (untuk fallback via RabbitMQ)
        Jadwalkuliah::create(array_merge($request->all(), [
            'kodekelas' => $kodekelas,
            'kodematkul' => $kodematkul,
        ]));

        return response()->json(['message' => 'Data jadwalkuliah tidak ditemukan, jadi ditambahkan']);
    }

     public function destroy($kodekelas, $kodematkul)
    {
        $jadwalkuliah = Jadwalkuliah::where('kodekelas', $kodekelas)
                      ->where('kodematkul', $kodematkul)
                      ->firstOrFail();

        $jadwalkuliah->delete();

        return response()->json(['message' => 'Data jadwalkuliah berhasil dihapus']);
    }
}
