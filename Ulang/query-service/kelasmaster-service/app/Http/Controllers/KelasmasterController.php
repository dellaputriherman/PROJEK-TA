<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelasmaster;

class KelasmasterController extends Controller
{
    public function index()
    {
        return response()->json(Kelasmaster::all());
    }

    public function show($kodekelas)
    {
        $kelas = Kelasmaster::where('kodekelas', $kodekelas)->first();

        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        return response()->json($kelas);
    }

    public function store(Request $request)
    {
        Kelasmaster::create($request->all());
        return response()->json(['message' => 'Kelas berhasil disinkronkan']);
    }

    public function update(Request $request, $kodekelas)
    {
        $kelas = Kelasmaster::where('kodekelas', $kodekelas)->firstOrFail();
        $kelas->update($request->all());

        return response()->json(['message' => 'Kelas berhasil diperbarui']);
    }

    public function destroy($kodekelas)
    {
        $kelas = Kelasmaster::where('kodekelas', $kodekelas)->firstOrFail();
        $kelas->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
