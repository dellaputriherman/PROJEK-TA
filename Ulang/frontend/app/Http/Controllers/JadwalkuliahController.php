<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JadwalkuliahController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-jadwalkuliah-service:8108/api/sync/jadwalkuliah');

            if ($response->successful()) {
                $jadwalkuliah = $response->json();
            } else {
                Log::error('Gagal mengambil data jadwalkuliah: ' . $response->body());
                $jadwalkuliah = [];
            }

        } catch (\Exception $e) {
            Log::error('Exception saat ambil jadwalkuliah: ' . $e->getMessage());
            $jadwalkuliah = [];
        }

        return view('jadwalkuliah.index', compact('jadwalkuliah'));
    }

    public function create()
    {

        try {
            $kelasResp = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');
            $kelasmaster = $kelasResp->successful() ? $kelasResp->json() : [];
        } catch (\Exception $e) {
            Log::error('Gagal ambil data kelasmaster: ' . $e->getMessage());
            $kelasmaster = [];
        }

        try {
            $matkulResp = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            $matakuliah = $matkulResp->successful() ? $matkulResp->json() : [];
        } catch (\Exception $e) {
            Log::error('Gagal ambil data matakuliah: ' . $e->getMessage());
            $matakuliah = [];
        }

        try {
            $dosenResp = Http::get('http://query-dosen-service:8101/api/sync/dosen');
            $dosen = $dosenResp->successful() ? $dosenResp->json() : [];
        } catch (\Exception $e) {
            Log::error('Gagal ambil data dosen: ' . $e->getMessage());
            $dosen = [];
        }

        return view('jadwalkuliah.create', compact('kelasmaster', 'matakuliah', 'dosen'));
    }

    public function store(Request $request)
    {
        $response = Http::post('http://jadwalkuliah-service:8008/api/jadwalkuliah', [
            'kodekelas'  => $request->kodekelas,
            'kodematkul' => $request->kodematkul,
            'hari'       => $request->hari,
            'jammulai'   => $request->jammulai,
            'jamselesai' => $request->jamselesai,
            'ruangan'    => $request->ruangan,
            'nip'        => $request->nip,
        ]);

        if ($response->successful()) {
            return redirect()->route('jadwalkuliah.index')->with('success', 'Data jadwal berhasil disimpan.');
        } else {
            return back()->withErrors($response->json('errors') ?? ['error' => 'Gagal menyimpan data.'])->withInput();
        }
    }

    public function edit($kodekelas, $kodematkul)
    {
        try {
            $response = Http::get("http://query-jadwalkuliah-service:8108/api/sync/jadwalkuliah/{$kodekelas}/{$kodematkul}");
            $kelasResp = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');
            $matkulResp = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            $dosenResp = Http::get('http://query-dosen-service:8101/api/sync/dosen');

            if ($response->successful() && $kelasResp->successful() && $matkulResp->successful() && $dosenResp->successful()) {
                $jadwalkuliah = $response->json();
                $kelasmaster = $kelasResp->json();
                $matakuliah = $matkulResp->json();
                $dosen = $dosenResp->json();
            } else {
                Log::error('Gagal mengambil data jadwalkuliah, kelasmaster, matakuliah atau dosen');
                return redirect()->route('jadwalkuliah.index')->with('error', 'Data tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil data jadwalkuliah: ' . $e->getMessage());
            return redirect()->route('jadwalkuliah.index')->with('error', 'Gagal mengambil data.');
        }

        return view('jadwalkuliah.edit', compact('jadwalkuliah', 'kelasmaster', 'matakuliah', 'dosen'));
    }


    public function update(Request $request, $kodekelas, $kodematkul)
    {
        $response = Http::put("http://jadwalkuliah-service:8008/api/jadwalkuliah/{$kodekelas}/{$kodematkul}", [
            'hari'       => $request->hari,
            'jammulai'   => $request->jammulai,
            'jamselesai' => $request->jamselesai,
            'ruangan'    => $request->ruangan,
            'nip'        => $request->nip,
        ]);

        if ($response->successful()) {
            return redirect()->route('jadwalkuliah.index')->with('success', 'Data jadwal berhasil diperbarui.');
        } else {
            return back()->withErrors($response->json('errors') ?? ['error' => 'Gagal memperbarui data.'])->withInput();
        }
    }


    public function destroy($kodekelas, $kodematkul)
    {
        $response = Http::delete("http://jadwalkuliah-service:8008/api/jadwalkuliah/{$kodekelas}/{$kodematkul}");

        if ($response->successful()) {
            return redirect()->route('jadwalkuliah.index')->with('success', 'Data jadwal berhasil dihapus.');
        } else {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
