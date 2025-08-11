<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://query-absensi-service:8100/api/sync/absensi');

            if ($response->successful()) {
                $absensi = $response->json();
            } else {
                Log::error('Gagal mengambil data absensi: ' . $response->body());
                $absensi = [];
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil absensi: ' . $e->getMessage());
            $absensi = [];
        }

        return view('absensi.index', compact('absensi'));
    }

    public function create()
    {
        try {
            $responseMhs = Http::get('http://query-mahasiswa-service:8103/api/sync/mahasiswa');
            $mahasiswa = $responseMhs->successful() ? $responseMhs->json() : [];

            $responseKelas = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');
            $kelasmaster = $responseKelas->successful() ? $responseKelas->json() : [];

            $responseMatkul = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            if ($responseMatkul->successful()) {
                $matakuliah = collect($responseMatkul->json())->pluck('namamatkul', 'kodematkul')->toArray();
            } else {
                Log::error('Gagal mengambil data matakuliah: ' . $responseMatkul->body());
                $matakuliah = [];
            }
        } catch (\Exception $e) {
            Log::error('Exception saat ambil data form absensi: ' . $e->getMessage());
            $mahasiswa = $kelasmaster = $matakuliah = [];
        }

        return view('absensi.create', compact('mahasiswa', 'kelasmaster', 'matakuliah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'kodekelas' => 'required',
            'kodematkul' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Sakit,Izin,Alfa',
        ]);

        $response = Http::post('http://absensi-service:8000/api/absensi', [
            'nim' => $request->nim,
            'kodekelas' => $request->kodekelas,
            'kodematkul' => $request->kodematkul,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ]);

        if ($response->successful()) {
            return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil disimpan.');
        } else {
            return back()->with('error', 'Gagal menyimpan data absensi.')->withInput();
        }
    }

    public function edit($nim, $kodematkul, $tanggal)
    {
        try {
        $response = Http::get("http://query-absensi-service:8100/api/sync/absensi/{$nim}/{$kodematkul}/{$tanggal}");
        if (!$response->successful()) {
            return back()->with('error', 'Data absensi tidak ditemukan.');
        }
        $absensi = $response->json();

            $responseMhs = Http::get('http://query-mahasiswa-service:8103/api/sync/mahasiswa');
            $mahasiswa = $responseMhs->successful() ? $responseMhs->json() : [];

            $responseMatkul = Http::get('http://query-matakuliah-service:8104/api/sync/matakuliah');
            $matakuliahData = $responseMatkul->successful() ? $responseMatkul->json() : [];
            $matakuliah = collect($matakuliahData)->pluck('namamatkul', 'kodematkul')->toArray();

            $responseKelas = Http::get('http://query-kelasmaster-service:8106/api/sync/kelasmaster');
            $kelasmaster = $responseKelas->successful() ? $responseKelas->json() : [];

            return view('absensi.edit', compact('absensi', 'mahasiswa', 'kelasmaster', 'matakuliah'));
        } catch (\Exception $e) {
            Log::error('Error saat edit absensi: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data absensi, mahasiswa, kelas, atau matakuliah.');
        }
    }

    public function update(Request $request, $nim, $kodematkul, $tanggal)
    {
        $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
        $request->validate([
            'nim' => 'required|string|max:20',
            'kodekelas' => 'required|string|max:20',
            'kodematkul' => 'required|string|max:10',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Sakit,Izin,Alfa',
        ]);

        try {
            $payload = [
                'nim' => $request->nim,
                'kodekelas' => $request->kodekelas,
                'kodematkul' => $request->kodematkul,
                'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d'),
                'status' => $request->status,
            ];

            $response = Http::put("http://absensi-service:8000/api/absensi/{$nim}/{$kodematkul}/{$tanggal}", $payload);

            if ($response->successful()) {
                return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui.');
            } else {
                Log::error('[AbsensiController@update] Gagal memperbarui data absensi: ' . $response->body());
                return back()->with('error', 'Gagal memperbarui data absensi.')->withInput();
            }
        } catch (\Exception $e) {
            Log::error('[AbsensiController@update] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data absensi.')->withInput();
        }
    }


    public function destroy($nim, $kodematkul, $tanggal)
    {
        try {
            $response = Http::delete("http://absensi-service:8000/api/absensi/{$nim}/{$kodematkul}/{$tanggal}");

            if ($response->successful()) {
                return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil dihapus.');
            } else {
                Log::error('[AbsensiController@destroy] Gagal menghapus data absensi: ' . $response->body());
                return back()->with('error', 'Gagal menghapus data absensi.');
            }
        } catch (\Exception $e) {
            Log::error('[AbsensiController@destroy] Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data absensi.');
        }
    }
}
