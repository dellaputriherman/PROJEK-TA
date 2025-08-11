<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatakuliahController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            if ($response->successful()) {
                $matakuliah = $response->json();
            } else {
                Log::error('Gagal mengambil data matakuliah: ' . $response->body());
                $matakuliah = [];
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil matakuliah: ' . $e->getMessage());
            $matakuliah = [];
        }

        return view('matakuliah.index', compact('matakuliah'));
    }

    public function create()
    {
        try {
            $responseProdi = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');

            if ($responseProdi->successful()) {
                $prodiData = $responseProdi->json();
                $prodi = collect($prodiData)->pluck('namaprodi', 'kodeprodi')->toArray();
            } else {
                Log::error('Gagal mengambil data prodi: ' . $responseProdi->body());
                $prodi = [];
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil prodi: ' . $e->getMessage());
            $prodi = [];
        }

        return view('matakuliah.create', compact('prodi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodematkul' => 'required|string|max:10',
            'namamatkul' => 'required|string|max:100',
            'semester' => 'required|integer|min:1',
            'sks' => 'required|integer|min:1',
            'jam' => 'required|integer|min:1',
            'kodeprodi' => 'required|string',
        ]);

        try {
            $responseProdi = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');

            if ($responseProdi->successful()) {
                $prodiList = $responseProdi->json();
                $selectedProdi = collect($prodiList)->firstWhere('kodeprodi', $request->kodeprodi);

                if (!$selectedProdi) {
                    return back()->with('error', 'Kode prodi tidak ditemukan.');
                }

                $payload = [
                    'kodematkul' => $request->kodematkul,
                    'namamatkul' => $request->namamatkul,
                    'semester' => $request->semester,
                    'sks' => $request->sks,
                    'jam' => $request->jam,
                    'kodeprodi' => $request->kodeprodi,
                    'namaprodi' => $selectedProdi['namaprodi'],
                ];

                $response = Http::post('http://matakuliah-service:8004/api/matakuliah', $payload);

                if ($response->successful()) {
                    return redirect()->route('matakuliah.index')->with('success', 'Data matakuliah berhasil disimpan.');
                } else {
                    Log::error('[MatakuliahController@store] Gagal menyimpan matakuliah. Status: ' . $response->status() . ', Body: ' . $response->body());
                    return back()->with('error', 'Gagal menyimpan data matakuliah.');
                }
            } else {
                Log::error('[MatakuliahController@store] Gagal mengambil data prodi. Status: ' . $responseProdi->status());
                return back()->with('error', 'Gagal mengambil data prodi.');
            }
        } catch (\Exception $e) {
            Log::error('[MatakuliahController@store] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data matakuliah.');
        }
    }

    public function edit($kodematkul)
    {
        try {
            $response = Http::get("http://query-matakuliah-service:8104/api/sync/matakuliah/{$kodematkul}");
            $responseProdi = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');

            if ($response->successful() && $responseProdi->successful()) {
                $matakuliah = $response->json();
                $prodiData = $responseProdi->json();
                $prodi = collect($prodiData)->pluck('namaprodi', 'kodeprodi')->toArray();

                return view('matakuliah.edit', compact('matakuliah', 'prodi'));
            } else {
                return back()->with('error', 'Gagal mengambil data.');
            }
        } catch (\Exception $e) {
            Log::error('[MatakuliahController@edit] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

     public function update(Request $request, $kodematkul)
    {
        $response = Http::put("http://matakuliah-service:8004/api/matakuliah/{$kodematkul}", [
            'kodematkul' => $request->kodematkul,
            'namamatkul' => $request->namamatkul,
            'semester' => $request->semester,
            'sks' => $request->sks,
            'jam' => $request->jam,
            'kodeprodi' => $request->kodeprodi,

        ]);

        if ($response->successful()) {
            return redirect()->route('matakuliah.index')->with('success', 'Data berhasil diperbarui.');
        } else {
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }


    public function destroy($kodematkul)
    {
        try {
            $response = Http::delete("http://matakuliah-service:8004/api/matakuliah/{$kodematkul}");

            if ($response->successful()) {
                return redirect()->route('matakuliah.index')->with('success', 'Data berhasil dihapus.');
            } else {
                Log::error('[MatakuliahController@destroy] Gagal hapus. Body: ' . $response->body());
                return back()->with('error', 'Gagal menghapus data.');
            }
        } catch (\Exception $e) {
            Log::error('[MatakuliahController@destroy] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
