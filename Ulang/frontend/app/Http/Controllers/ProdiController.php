<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProdiController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');

            if ($response->successful()) {
                $prodi = $response->json();
            } else {
                Log::error('Gagal mengambil data prodi: ' . $response->body());
                $prodi = [];
            }

        } catch (\Exception $e) {
            Log::error('Exception saat ambil prodi: ' . $e->getMessage());
            $prodi = [];
        }

        return view('jurusanprodi.prodi.index', compact('prodi'));
    }

    public function create()
    {
        try {
            $response = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');

            if ($response->successful()) {
                $data = $response->json();
                $jurusan = [];

                foreach ($data as $item) {
                    $jurusan[$item['kodejurusan']] = $item['namajurusan'];
                }

            } else {
                Log::error('Gagal mengambil data jurusan: ' . $response->body());
                $jurusan = [];
            }

        } catch (\Exception $e) {
            Log::error('Exception saat ambil jurusan: ' . $e->getMessage());
            $jurusan = [];
        }

        return view('jurusanprodi.prodi.create', compact('jurusan'));
    }

    public function store(Request $request)
    {
        $response = Http::post('http://jurusanprodi-service:8002/api/prodi', [
            'kodeprodi' => $request->kodeprodi,
            'namaprodi' => $request->namaprodi,
            'kodejurusan' => $request->kodejurusan,

        ]);

        if ($response->successful()) {
            return redirect()->route('jurusanprodi.prodi.index')->with('success', 'Data berhasil disimpan.');
        } else {
            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($kodeprodi)
    {
        $response = Http::get("http://query-jurusanprodi-service:8102/api/sync/prodi/$kodeprodi");

        if ($response->successful()) {
            $prodi = $response->json();

            $jurusanResponse = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');
            $jurusan = [];

            if ($jurusanResponse->successful()) {
                foreach ($jurusanResponse->json() as $item) {
                    $jurusan[$item['kodejurusan']] = $item['namajurusan'];
                }
            }

            return view('jurusanprodi.prodi.edit', compact('prodi', 'jurusan'));
        } else {
            return redirect()->route('jurusanprodi.prodi.index')->with('error', 'Data prodi tidak ditemukan.');
        }
    }


    public function update(Request $request, $kodeprodi)
    {
        $response = Http::put("http://jurusanprodi-service:8002/api/prodi/$kodeprodi", [
            'kodeprodi' => $request->kodeprodi,
            'namaprodi' => $request->namaprodi,
            'kodejurusan' => $request->kodejurusan,
        ]);

        if ($response->successful()) {
            return redirect()->route('jurusanprodi.prodi.index')->with('success', 'Data berhasil diperbarui.');
        } else {
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($kodeprodi)
    {
        $response = Http::delete("http://jurusanprodi-service:8002/api/prodi/$kodeprodi");

        if ($response->successful()) {
            return redirect()->route('jurusanprodi.prodi.index')->with('success', 'Data berhasil dihapus.');
        } else {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

}
