<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodi;

class ProdiController extends Controller
{
    public function index()
    {
        $prodi = Prodi::all();
        return response()->json($prodi);
    }

    public function show($kodeprodi)
    {
        $prodi = Prodi::where('kodeprodi', $kodeprodi)->first();

        if (!$prodi) {
            return response()->json(['message' => 'Prodi tidak ditemukan'], 404);
        }

        return response()->json($prodi);
    }

    public function store(Request $request)
    {
        Prodi::create($request->all());
        return response()->json(['message' => 'Prodi berhasil disinkronkan']);
    }

    public function update(Request $request, $kodeprodi)
    {
        $prodi = Prodi::where('kodeprodi', $kodeprodi)->firstOrFail();
        $prodi->update($request->all());
        return response()->json(['message' => 'Prodi berhasil diperbarui']);
    }

    public function destroy($kodeprodi)
    {
        $prodi = Prodi::where('kodeprodi', $kodeprodi)->firstOrFail();
        $prodi->delete();
        return response()->json(['message' => 'Prodi berhasil dihapus']);
    }
}
