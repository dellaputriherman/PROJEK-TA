<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DosenController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-dosen-service:8101/api/sync/dosen');

            if ($response->successful()) {
                $dosen = $response->json();
            } else {
                Log::error('[DosenController@index] Gagal mengambil data dosen: ' . $response->body());
                $dosen = [];
            }
        } catch (\Exception $e) {
            Log::error('[DosenController@index] Exception: ' . $e->getMessage());
            $dosen = [];
        }

        return view('dosen.index', compact('dosen'));
    }

    public function create()
    {
        try {
            $responseJurusan = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');

            if ($responseJurusan->successful()) {
                $jurusanData = $responseJurusan->json();
                $jurusan = collect($jurusanData)->pluck('namajurusan', 'kodejurusan')->toArray();
            } else {
                Log::error('[DosenController@create] Gagal mengambil data jurusan: ' . $responseJurusan->body());
                $jurusan = [];
            }
        } catch (\Exception $e) {
            Log::error('[DosenController@create] Exception: ' . $e->getMessage());
            $jurusan = [];
        }

        return view('dosen.create', compact('jurusan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'nama' => 'required',
            'kodejurusan' => 'required',
        ]);

        try {
            // Ambil data jurusan untuk mendapatkan namajurusan
            $responseJurusan = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');

            if ($responseJurusan->successful()) {
                $jurusanList = $responseJurusan->json();
                $selectedJurusan = collect($jurusanList)->firstWhere('kodejurusan', $request->kodejurusan);

                if (!$selectedJurusan) {
                    return back()->with('error', 'Kode jurusan tidak ditemukan.');
                }

                $payload = [
                    'nip' => $request->nip,
                    'nama' => $request->nama,
                    'kodejurusan' => $request->kodejurusan,
                    'namajurusan' => $selectedJurusan['namajurusan'],
                ];

                $response = Http::post('http://dosen-service:8001/api/dosen', $payload);

                if ($response->successful()) {
                    return redirect()->route('dosen.index')->with('success', 'Data berhasil disimpan.');
                } else {
                    Log::error('[DosenController@store] Gagal menyimpan dosen. Status: ' . $response->status() . ', Body: ' . $response->body());
                    return back()->with('error', 'Gagal menyimpan data dosen.');
                }
            } else {
                Log::error('[DosenController@store] Gagal mengambil data jurusan. Status: ' . $responseJurusan->status());
                return back()->with('error', 'Gagal mengambil data jurusan.');
            }
        } catch (\Exception $e) {
            Log::error('[DosenController@store] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data dosen.');
        }
    }
    public function edit($nip)
    {
        try {
            $responseDosen = Http::get("http://query-dosen-service:8101/api/sync/dosen/{$nip}");
            $responseJurusan = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');

            if ($responseDosen->successful() && $responseJurusan->successful()) {
                $dosen = $responseDosen->json();
                $jurusanList = $responseJurusan->json();
                $jurusan = collect($jurusanList)->pluck('namajurusan', 'kodejurusan')->toArray();

                return view('dosen.edit', compact('dosen', 'jurusan'));
            } else {
                return back()->with('error', 'Gagal mengambil data dosen atau jurusan.');
            }
        } catch (\Exception $e) {
            Log::error('[DosenController@edit] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

     public function update(Request $request, $nip)
    {
        $response = Http::put("http://dosen-service:8001/api/dosen/{$nip}", [
            'nip' => $request->nip,
            'nama' => $request->nama,
            'kodejurusan' => $request->kodejurusan,
        ]);

        if ($response->successful()) {
            return redirect()->route('dosen.index')->with('success', 'Data berhasil diperbarui.');
        } else {
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($nip)
    {
        try {
            $response = Http::delete("http://dosen-service:8001/api/dosen/{$nip}");

            if ($response->successful()) {
                return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus.');
            } else {
                return back()->with('error', 'Gagal menghapus data dosen.');
            }
        } catch (\Exception $e) {
            Log::error('[DosenController@destroy] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
