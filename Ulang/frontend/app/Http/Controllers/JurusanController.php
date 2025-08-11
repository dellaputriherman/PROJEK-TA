<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JurusanController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');

            if ($response->successful()) {
                 $jurusan = $response->json();
            } else {
                Log::error('Gagal mengambil data jurusan: ' . $response->body());
                $jurusan = [];
            }

        } catch (\Exception $e) {
            Log::error('Exception saat ambil jurusan: ' . $e->getMessage());
            $jurusan = [];
        }

        return view('jurusanprodi.jurusan.index', compact('jurusan'));
    }

    public function create()
    {
        return view('jurusanprodi.jurusan.create');
    }

    public function store(Request $request)
    {
        $response = Http::post('http://jurusanprodi-service:8002/api/jurusan', [
            'kodejurusan' => $request->kodejurusan,
            'namajurusan' => $request->namajurusan,
        ]);

        if ($response->successful()) {
            return redirect()->route('jurusanprodi.jurusan.index')->with('success', 'Data berhasil disimpan.');
        } else {
            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($kodejurusan)
    {
        $response = Http::get("http://query-jurusanprodi-service:8102/api/sync/jurusan/$kodejurusan");

        if ($response->successful()) {
            $jurusan = $response->json();
            return view('jurusanprodi.jurusan.edit', compact('jurusan'));
        } else {
            return redirect()->route('jurusanprodi.jurusan.index')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $kodejurusan)
    {
        $response = Http::put("http://jurusanprodi-service:8002/api/jurusan/$kodejurusan", [
            'kodejurusan' => $request->kodejurusan,
            'namajurusan' => $request->namajurusan,
        ]);

        if ($response->successful()) {
            return redirect()->route('jurusanprodi.jurusan.index')->with('success', 'Data berhasil diperbarui.');
        } else {
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($kodejurusan)
    {
        $response = Http::delete("http://jurusanprodi-service:8002/api/jurusan/{$kodejurusan}");

        if ($response->successful()) {
            return redirect()->route('jurusanprodi.jurusan.index')->with('success', 'Data berhasil dihapus.');
        } else {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
