<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NilaiController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-nilai-service:8105/api/sync/nilai');

            if ($response->successful()) {
                $data = $response->json();
                $nilai = $data['data'] ?? $data;
            } else {
                Log::error('[NilaiController@index] Gagal mengambil data nilai: ' . $response->body());
                $nilai = [];
            }
        } catch (\Exception $e) {
            Log::error('[NilaiController@index] Exception: ' . $e->getMessage());
            $nilai = [];
        }

        return view('nilai.index', compact('nilai'));
    }

    public function create()
    {
        try {
            $responseMatkul = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');

            if ($responseMatkul->successful()) {
                $matkulData = $responseMatkul->json();
                $matakuliah = collect($matkulData)->pluck('namamatkul', 'kodematkul')->toArray();
            } else {
                Log::error('[NilaiController@create] Gagal mengambil data matakuliah: ' . $responseMatkul->body());
                $matakuliah = [];
            }
        } catch (\Exception $e) {
            Log::error('[NilaiController@create] Exception: ' . $e->getMessage());
            $matakuliah = [];
        }

        return view('nilai.create', compact('matakuliah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'kodematkul' => 'required',
            'nilaiangka' => 'required|numeric',
        ]);

        try {
            $responseMatkul = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');

            if ($responseMatkul->successful()) {
                $matkulList = $responseMatkul->json();
                $selectedMatkul = collect($matkulList)->firstWhere('kodematkul', $request->kodematkul);

                if (!$selectedMatkul) {
                    return back()->with('error', 'Kode matakuliah tidak ditemukan.');
                }

                $payload = [
                    'nim' => $request->nim,
                    'kodematkul' => $request->kodematkul,
                    'namamatkul' => $selectedMatkul['namamatkul'] ?? null,
                    'nilaiangka' => $request->nilaiangka,

                ];

                $response = Http::post('http://nilai-service:8005/api/nilai', $payload);

                if ($response->successful()) {
                    return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil disimpan.');
                } else {
                    Log::error('[NilaiController@store] Gagal menyimpan nilai. Status: ' . $response->status() . ', Body: ' . $response->body());
                    return back()->with('error', 'Gagal menyimpan data nilai.');
                }
            } else {
                Log::error('[NilaiController@store] Gagal mengambil data matakuliah. Status: ' . $responseMatkul->status());
                return back()->with('error', 'Gagal mengambil data matakuliah.');
            }
        } catch (\Exception $e) {
            Log::error('[NilaiController@store] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data nilai.');
        }
    }

   public function edit($nim, $kodematkul)
    {
        try {
            $response = Http::get("http://query-absensi-service:8100/api/sync/absensi/{$nim}/{$kodematkul}");

            if (!$response->successful()) {
                return redirect()->route('absensi.index')->with('error', 'Data absensi tidak ditemukan.');
            }

            $absensi = $response->json();

            $matkulResponse = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            $matakuliah = $matkulResponse->successful()
                ? collect($matkulResponse->json())->pluck('namamatkul', 'kodematkul')->toArray()
                : [];

            $kelasResponse = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');
            $kelasmaster = $kelasResponse->successful()
                ? collect($kelasResponse->json())->pluck('kodekelas')->toArray()
                : [];

            return view('absensi.edit', compact('absensi', 'matakuliah', 'kelasmaster'));
        } catch (\Exception $e) {
            Log::error('[AbsensiController@edit] Exception: ' . $e->getMessage());
            return redirect()->route('absensi.index')->with('error', 'Terjadi kesalahan saat mengambil data absensi.');
        }
    }


   public function update(Request $request, $nim, $kodematkul)
    {
        $request->validate([
            'nilaiangka' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $payload = [
                'nilaiangka' => $request->nilaiangka,
            ];

            $response = Http::put("http://nilai-service:8005/api/nilai/{$nim}/{$kodematkul}", $payload);

            if ($response->successful()) {
                return redirect()->route('nilai.index')->with('success', 'Nilai berhasil diperbarui.');
            } else {
                Log::error('[NilaiController@update] Gagal update. Body: ' . $response->body());
                return back()->with('error', 'Gagal memperbarui nilai.');
            }
        } catch (\Exception $e) {
            Log::error('[NilaiController@update] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat update.');
        }
    }

    public function destroy($nim, $kodematkul)
    {
        try {
            $response = Http::delete("http://nilai-service:8005/api/nilai/{$nim}/{$kodematkul}");

            if ($response->successful()) {
                return redirect()->route('nilai.index')->with('success', 'Nilai berhasil dihapus.');
            } else {
                Log::error('[NilaiController@destroy] Gagal hapus. Body: ' . $response->body());
                return back()->with('error', 'Gagal menghapus nilai.');
            }
        } catch (\Exception $e) {
            Log::error('[NilaiController@destroy] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus.');
        }
    }

}
