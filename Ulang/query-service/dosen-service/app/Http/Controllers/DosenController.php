<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;

class DosenController extends Controller
{
    public function index()
    {
        return response()->json(Dosen::all());
    }

    public function show($nip)
    {
        $dosen = Dosen::where('nip', $nip)->first();

        if (!$dosen) {
            return response()->json(['message' => 'Dosen tidak ditemukan'], 404);
        }

        return response()->json($dosen);
    }

    public function store(Request $request)
    {
        Dosen::create($request->all());
        return response()->json(['message' => 'Dosen berhasil disinkronkan']);
    }

    public function update(Request $request, $nip)
    {
        $dosen = Dosen::where('nip', $nip)->firstOrFail();
        $dosen->update($request->all());
        return response()->json(['message' => 'Dosen berhasil diperbarui']);
    }

    public function destroy($nip)
    {
        $dosen = Dosen::where('nip', $nip)->firstOrFail();
        $dosen->delete();
        return response()->json(['message' => 'Dosen berhasil dihapus']);
    }
}
