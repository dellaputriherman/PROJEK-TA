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
            $nilai = $response->successful() ? ($response->json()['data'] ?? $response->json()) : [];
            if (!$response->successful()) {
                Log::error('[NilaiController@index] Gagal mengambil data nilai: ' . $response->body());
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
            $responseMhs = Http::get('http://query-mahasiswa-service:8103/api/sync/mahasiswa');
            $mahasiswa = $responseMhs->successful() ? $responseMhs->json() : [];

            $responseMatkul = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            $matakuliah = $responseMatkul->successful()
                ? collect($responseMatkul->json())->pluck('namamatkul', 'kodematkul')->toArray()
                : [];
        } catch (\Exception $e) {
            Log::error('[NilaiController@create] Exception: ' . $e->getMessage());
            $mahasiswa = $matakuliah = [];
        }

        return view('nilai.create', compact('mahasiswa', 'matakuliah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'kodematkul' => 'required',
            'nilaiangka' => 'required|numeric',
        ]);

        try {
            $payload = $request->only('nim', 'kodematkul', 'nilaiangka');

            $response = Http::post('http://nilai-service:8005/api/nilai', $payload);

            if ($response->successful()) {
                return redirect()->route('nilai.index')->with('success', 'Data nilai berhasil disimpan.');
            } else {
                Log::error('[NilaiController@store] Gagal menyimpan nilai: ' . $response->body());
                return back()->with('error', 'Gagal menyimpan data nilai.');
            }
        } catch (\Exception $e) {
            Log::error('[NilaiController@store] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data nilai.');
        }
    }

    public function edit($nim, $kodematkul)
    {
        try {
            $response = Http::get("http://query-nilai-service:8105/api/sync/nilai/{$nim}/{$kodematkul}");
            if (!$response->successful()) {
                return redirect()->route('nilai.index')->with('error', 'Data nilai tidak ditemukan.');
            }
            $nilai = $response->json();

            $responseMhs = Http::get('http://query-mahasiswa-service:8103/api/sync/mahasiswa');
            $mahasiswa = $responseMhs->successful() ? $responseMhs->json() : [];

            $responseMatkul = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            $matakuliah = $responseMatkul->successful()
                ? collect($responseMatkul->json())->pluck('namamatkul', 'kodematkul')->toArray()
                : [];

            return view('nilai.edit', compact('nilai', 'mahasiswa', 'matakuliah'));
        } catch (\Exception $e) {
            Log::error('[NilaiController@edit] Exception: ' . $e->getMessage());
            return redirect()->route('nilai.index')->with('error', 'Terjadi kesalahan saat mengambil data nilai.');
        }
    }

    public function update(Request $request, $nim, $kodematkul)
    {
        $request->validate([
            'nilaiangka' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $payload = $request->only('nilaiangka');
            $response = Http::put("http://nilai-service:8005/api/nilai/{$nim}/{$kodematkul}", $payload);

            if ($response->successful()) {
                return redirect()->route('nilai.index')->with('success', 'Nilai berhasil diperbarui.');
            } else {
                Log::error('[NilaiController@update] Gagal update: ' . $response->body());
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
                Log::error('[NilaiController@destroy] Gagal hapus: ' . $response->body());
                return back()->with('error', 'Gagal menghapus nilai.');
            }
        } catch (\Exception $e) {
            Log::error('[NilaiController@destroy] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus nilai.');
        }
    }
}
