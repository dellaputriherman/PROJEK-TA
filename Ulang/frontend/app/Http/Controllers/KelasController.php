<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KelasController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-kelas-service:8107/api/sync/kelas');
            if ($response->successful()) {
                $kelas = $response->json();
            } else {
                Log::error('Gagal mengambil data kelas: ' . $response->body());
                $kelas = [];
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil kelas: ' . $e->getMessage());
            $kelas = [];
        }
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        try {

            $responseMhs = Http::get('http://query-mahasiswa-service:8103/api/sync/mahasiswa');
            $mahasiswa = $responseMhs->successful() ? $responseMhs->json() : [];

            $responseKelasMaster = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');
            $kelasmaster = $responseKelasMaster->successful() ? $responseKelasMaster->json() : [];
        } catch (\Exception $e) {
            Log::error('Exception saat ambil data form kelas: ' . $e->getMessage());
            $mahasiswa = $kelasmaster = [];
        }

        return view('kelas.create', compact('mahasiswa', 'kelasmaster'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim'        => 'required|string|max:20',
            'kodekelas'  => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'tahunajaran' => 'required|string|max:20',
        ]);

        try {
            $response = Http::post('http://kelas-service:8007/api/kelas', $validated);

            if ($response->successful()) {
                return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil disimpan.');
            } else {
                $errors = $response->json('errors') ?? ['error' => 'Gagal menyimpan data kelas.'];
                return back()->withErrors($errors)->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Gagal simpan data kelas: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Server tidak merespon'])->withInput();
        }
    }

    public function edit($nim, $kodekelas, $semester)
    {
        try {
            $response = Http::get("http://query-kelas-service:8107/api/sync/kelas/{$nim}/{$kodekelas}/{$semester}");

            $responseKelasMaster = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');

            if ($response->successful() && $responseKelasMaster->successful()) {
                $kelasData = $response->json();

                $kelas = isset($kelasData['data']) ? $kelasData['data'] : $kelasData;

                $kelasmaster = $responseKelasMaster->json();
            } else {
                Log::error('Gagal ambil data kelas atau kelasmaster');
                return redirect()->route('kelas.index')->with('error', 'Data tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil data kelas: ' . $e->getMessage());
            return redirect()->route('kelas.index')->with('error', 'Gagal mengambil data.');
        }

        return view('kelas.edit', compact('kelas', 'kelasmaster'));
    }


    public function update(Request $request, $nim, $kodekelas, $semester)
    {
        $validated = $request->validate([
            'nim'         => 'required|string|max:20',
            'kodekelas'   => 'required|string|max:20',
            'semester'    => 'required|string|max:20',
            'tahunajaran' => 'required|string|max:20',
        ]);

        try {
            $response = Http::put("http://kelas-service:8007/api/kelas/{$nim}/{$kodekelas}/{$semester}", $validated);

            if ($response->successful()) {
                return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
            } else {
                $errors = $response->json('errors') ?? ['error' => 'Gagal memperbarui data kelas.'];
                return back()->withErrors($errors)->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Gagal update data kelas: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Server tidak merespon'])->withInput();
        }
    }

    public function destroy($nim, $kodekelas, $semester)
    {
        try {
            $response = Http::delete("http://kelas-service:8007/api/kelas/{$nim}/{$kodekelas}/{$semester}");

            if ($response->successful()) {
                return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil dihapus.');
            } else {
                return back()->with('error', 'Gagal menghapus data kelas.');
            }
        } catch (\Exception $e) {
            Log::error('Gagal hapus data kelas: ' . $e->getMessage());
            return back()->with('error', 'Server tidak merespon');
        }
    }
}
