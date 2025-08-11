<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\RabbitMQPublisher;

class NilaiController extends Controller
{
    public function show($nim, $kodematkul)
    {
        $nilai = Nilai::where('nim', $nim)->where('kodematkul', $kodematkul)->first();

        if (!$nilai) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($nilai);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'kodematkul' => 'required|string|max:10',
            'nilaiangka' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (Nilai::where('nim', $request->nim)->where('kodematkul', $request->kodematkul)->exists()) {
            return response()->json(['message' => 'Data nilai sudah ada'], 409);
        }

        $mahasiswa = null;
        $matakuliah = null;

        try {
            $mahasiswaResp = Http::timeout(5)->get("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$request->nim}");
            $mahasiswa = $mahasiswaResp->successful() ? $mahasiswaResp->json() : null;

            $matakuliahResp = Http::timeout(5)->get("http://query-matakuliah-service:8104/api/sync/matakuliah/{$request->kodematkul}");
            $matakuliah = $matakuliahResp->successful() ? $matakuliahResp->json() : null;
        } catch (\Exception $e) {
            Log::error("Gagal ambil data referensi mahasiswa/matakuliah: " . $e->getMessage());
        }

        if (!$mahasiswa || !$matakuliah) {
            return response()->json(['errors' => ['referensi' => ['Mahasiswa atau matakuliah tidak valid']]], 422);
        }

        $nilai = Nilai::create($request->only(['nim', 'kodematkul', 'nilaiangka']));

        $payload = [
            'nim' => $nilai->nim,
            'nama' => $mahasiswa['nama'] ?? null,
            'kodematkul' => $nilai->kodematkul,
            'namamatkul' => $matakuliah['namamatkul'] ?? null,
            'nilaiangka' => $nilai->nilaiangka,
        ];

        try {
            $res = Http::timeout(5)->post('http://query-nilai-service:8105/api/sync/nilai', $payload);

            if (!$res->successful()) {
                throw new \Exception("Sinkron ke query-nilai gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena HTTP gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'nilai', 'create');
        }

        return response()->json($nilai, 201);
    }

    public function update(Request $request, $nim, $kodematkul)
    {
        $nilai = Nilai::where('nim', $nim)->where('kodematkul', $kodematkul)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nilaiangka' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nilai->update(['nilaiangka' => $request->nilaiangka]);

        $mahasiswa = null;
        $matakuliah = null;

        try {
            $mahasiswaResp = Http::timeout(5)->get("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$nim}");
            $mahasiswa = $mahasiswaResp->successful() ? $mahasiswaResp->json() : null;

            $matakuliahResp = Http::timeout(5)->get("http://query-matakuliah-service:8104/api/sync/matakuliah/{$kodematkul}");
            $matakuliah = $matakuliahResp->successful() ? $matakuliahResp->json() : null;
        } catch (\Exception $e) {
            Log::error("Gagal ambil data referensi mahasiswa/matakuliah: " . $e->getMessage());
        }

        $payload = [
            'nim' => $nilai->nim,
            'nama' => $mahasiswa['nama'] ?? null,
            'kodematkul' => $nilai->kodematkul,
            'namamatkul' => $matakuliah['namamatkul'] ?? null,
            'nilaiangka' => $nilai->nilaiangka,
        ];

        try {
            Http::timeout(5)->put("http://query-nilai-service:8105/api/sync/nilai/{$nim}/{$kodematkul}", $payload);
        } catch (\Exception $e) {
            Log::warning("Sinkronisasi gagal, fallback ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'nilai', 'update');
        }

        return response()->json($nilai);
    }

    public function destroy($nim, $kodematkul)
    {
        $nilai = Nilai::where('nim', $nim)->where('kodematkul', $kodematkul)->firstOrFail();
        $nilai->delete();

        try {
            Http::timeout(5)->delete("http://query-nilai-service:8105/api/sync/nilai/{$nim}/{$kodematkul}");
        } catch (\Exception $e) {
            Log::warning("Sinkronisasi hapus gagal, fallback ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish([
                'nim' => $nim,
                'kodematkul' => $kodematkul
            ], 'nilai', 'delete');
        }

        return response()->json(['message' => 'Data nilai berhasil dihapus']);
    }
}
