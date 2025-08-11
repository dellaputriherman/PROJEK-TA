<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MahasiswaController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-mahasiswa-service:8103/api/sync/mahasiswa');

            if ($response->successful()) {
                $mahasiswa = $response->json();
            } else {
                Log::error('Gagal mengambil data mahasiswa: ' . $response->body());
                $mahasiswa = [];
            }

        } catch (\Exception $e) {
            Log::error('Exception saat ambil mahasiswa: ' . $e->getMessage());
            $mahasiswa = [];
        }

        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function create()
    {
        try {
            $responseJurusan = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');
            $responseProdi = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');

            if ($responseJurusan->successful() && $responseProdi->successful()) {
                $jurusanData = $responseJurusan->json();
                $prodiData = $responseProdi->json();

                $jurusan = collect($jurusanData)->pluck('namajurusan', 'kodejurusan')->toArray();
                $prodi = collect($prodiData)->pluck('namaprodi', 'kodeprodi')->toArray();
            } else {
                Log::error('Gagal mengambil data jurusan/prodi');
                $jurusan = [];
                $prodi = [];
            }

        } catch (\Exception $e) {
            Log::error('Exception saat ambil jurusan/prodi: ' . $e->getMessage());
            $jurusan = [];
            $prodi = [];
        }

        return view('mahasiswa.create', compact('jurusan', 'prodi'));
    }

    public function store(Request $request)
    {
        $response = Http::post('http://mahasiswa-service:8003/api/mahasiswa', [
            'nim' => $request->nim,
            'nama' => $request->nama,
            'tempatlahir' => $request->tempatlahir,
            'tanggallahir' => $request->tanggallahir,
            'jeniskelamin' => $request->jeniskelamin,
            'kodejurusan' => $request->kodejurusan,
            'kodeprodi' => $request->kodeprodi,
        ]);

        if ($response->successful()) {
            return redirect()->route('mahasiswa.index')->with('success', 'Data berhasil disimpan.');
        } else {
            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($nim)
    {
        try {
            $response = Http::get("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$nim}");

            if ($response->successful()) {
                $mahasiswa = $response->json();

                $responseJurusan = Http::get('http://query-jurusanprodi-service:8102/api/sync/jurusan');
                $responseProdi = Http::get('http://query-jurusanprodi-service:8102/api/sync/prodi');

                if ($responseJurusan->successful() && $responseProdi->successful()) {
                    $jurusanData = $responseJurusan->json();
                    $prodiData = $responseProdi->json();

                    $jurusan = collect($jurusanData)->pluck('namajurusan', 'kodejurusan')->toArray();
                    $prodi = collect($prodiData)->pluck('namaprodi', 'kodeprodi')->toArray();
                } else {
                    Log::error('Gagal mengambil data jurusan/prodi');
                    $jurusan = [];
                    $prodi = [];
                }

                return view('mahasiswa.edit', compact('mahasiswa', 'jurusan', 'prodi'));
            } else {
                return redirect()->route('mahasiswa.index')->with('error', 'Data mahasiswa tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil data mahasiswa: ' . $e->getMessage());
            return redirect()->route('mahasiswa.index')->with('error', 'Gagal mengambil data mahasiswa.');
        }
    }

    public function update(Request $request, $nim)
    {
        $response = Http::put("http://mahasiswa-service:8003/api/mahasiswa/{$nim}", [
            'nim' => $request->nim,
            'nama' => $request->nama,
            'tempatlahir' => $request->tempatlahir,
            'tanggallahir' => $request->tanggallahir,
            'jeniskelamin' => $request->jeniskelamin,
            'kodejurusan' => $request->kodejurusan,
            'kodeprodi' => $request->kodeprodi,
        ]);

        if ($response->successful()) {
            return redirect()->route('mahasiswa.index')->with('success', 'Data berhasil diperbarui.');
        } else {
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($nim)
    {
        $response = Http::delete("http://mahasiswa-service:8003/api/mahasiswa/{$nim}");

        if ($response->successful()) {
            return redirect()->route('mahasiswa.index')->with('success', 'Data berhasil dihapus.');
        } else {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
