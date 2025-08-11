<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KelasmasterController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');

            if ($response->successful()) {
                $kelasmaster = $response->json();
            } else {
                Log::error('Gagal mengambil data kelasmaster: ' . $response->body());
                $kelasmaster = [];
            }

        } catch (\Exception $e) {
            Log::error('Exception saat ambil kelasmaster: ' . $e->getMessage());
            $kelasmaster = [];
        }

        return view('kelasmaster.index', compact('kelasmaster'));
    }

   public function create()
    {
        try {
            $respJurusan = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');
            $jurusan = $respJurusan->successful()
                ? collect($respJurusan->json())->pluck('namajurusan', 'kodejurusan')->toArray()
                : [];

            $respProdi = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');
            $prodi = $respProdi->successful()
                ? collect($respProdi->json())->pluck('namaprodi', 'kodeprodi')->toArray()
                : [];

        } catch (\Exception $e) {
            Log::error('Exception saat ambil jurusan/prodi: ' . $e->getMessage());
            $jurusan = [];
            $prodi = [];
        }

        return view('kelasmaster.create', compact('jurusan', 'prodi'));
    }

    public function store(Request $request)
    {
        $response = Http::post('http://kelasmaster-service:8006/api/kelasmaster', [
            'kodekelas'   => $request->kodekelas,
            'namakelas'   => $request->namakelas,
            'kodejurusan' => $request->kodejurusan,
            'kodeprodi'   => $request->kodeprodi,
        ]);

        if ($response->successful()) {
            return redirect()->route('kelasmaster.index')->with('success', 'Data kelas berhasil disimpan.');
        } else {
            return back()->withErrors($response->json('errors') ?? ['error' => 'Gagal menyimpan data.'])->withInput();
        }
    }

    public function edit($kodekelas)
    {
        try {
            $respKelas = Http::get("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$kodekelas}");
            $respJurusan = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');
            $respProdi = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');

            if ($respKelas->successful() && $respJurusan->successful() && $respProdi->successful()) {
                $kelasmaster = $respKelas->json();
                $jurusan = collect($respJurusan->json())->pluck('namajurusan', 'kodejurusan')->toArray();
                $prodi = collect($respProdi->json())->pluck('namaprodi', 'kodeprodi')->toArray();
            } else {
                Log::error('Gagal mengambil data kelasmaster/jurusan/prodi');
                return redirect()->route('kelasmaster.index')->with('error', 'Data tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil data kelasmaster: ' . $e->getMessage());
            return redirect()->route('kelasmaster.index')->with('error', 'Gagal mengambil data.');
        }

        return view('kelasmaster.edit', compact('kelasmaster', 'jurusan', 'prodi'));
    }

    public function update(Request $request, $kodekelas)
    {
        $response = Http::put("http://kelasmaster-service:8006/api/kelasmaster/{$kodekelas}", [
            'kodekelas'   => $request->kodekelas,
            'namakelas'   => $request->namakelas,
            'kodejurusan' => $request->kodejurusan,
            'kodeprodi'   => $request->kodeprodi,
        ]);

        if ($response->successful()) {
            return redirect()->route('kelasmaster.index')->with('success', 'Data kelas berhasil diperbarui.');
        } else {
            return back()->withErrors($response->json('errors') ?? ['error' => 'Gagal memperbarui data.'])->withInput();
        }
    }

    public function destroy($kodekelas)
    {
        $response = Http::delete("http://kelasmaster-service:8006/api/kelasmaster/{$kodekelas}");

        if ($response->successful()) {
            return redirect()->route('kelasmaster.index')->with('success', 'Data kelas berhasil dihapus.');
        } else {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
