<?php

namespace App\Http\Controllers;

use App\Models\Kelasmaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\RabbitMQPublisher;

class KelasmasterController extends Controller
{
    public function show($kodekelas)
    {
        $kelas = KelasMaster::where('kodekelas', $kodekelas)->first();

        if (!$kelas) {
            return response()->json([
                'errors' => ['kodekelas' => ['Kode kelas tidak ditemukan']]
            ], 404);
        }

        return response()->json($kelas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodekelas'   => 'required|string|max:20|unique:kelasmaster,kodekelas',
            'namakelas'   => 'required|string|max:100',
            'kodejurusan' => 'required|string|max:20',
            'kodeprodi'   => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil data jurusan & prodi
        $jurusan = null;
        $prodi = null;

        try {
            $jurusanResp = Http::timeout(5)->get("http://query-jurusanprodi-service:8102/api/sync/jurusan/{$request->kodejurusan}");
            $jurusan = $jurusanResp->successful() ? $jurusanResp->json() : null;

            $prodiResp = Http::timeout(5)->get("http://query-jurusanprodi-service:8102/api/sync/prodi/{$request->kodeprodi}");
            $prodi = $prodiResp->successful() ? $prodiResp->json() : null;
        } catch (\Exception $e) {
            Log::error("Gagal ambil data jurusan/prodi: " . $e->getMessage());
        }

        if (!$jurusan || !$prodi) {
            return response()->json([
                'errors' => ['sinkronisasi' => ['Data jurusan/prodi tidak valid di service']]
            ], 422);
        }

        $kelas = KelasMaster::create($request->only([
            'kodekelas', 'namakelas', 'kodejurusan', 'kodeprodi'
        ]));

        $payload = [
            'kodekelas'   => $kelas->kodekelas,
            'namakelas'   => $kelas->namakelas,
            'kodejurusan' => $kelas->kodejurusan,
            'namajurusan' => $jurusan['namajurusan'] ?? null,
            'kodeprodi'   => $kelas->kodeprodi,
            'namaprodi'   => $prodi['namaprodi'] ?? null,
        ];

        try {
            $res = Http::post('http://query-kelasmaster-service:8106/api/sync/kelasmaster', $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkronisasi HTTP gagal dengan status: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning('Sinkronisasi gagal, fallback ke RabbitMQ: ' . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'kelasmaster', 'create');
        }

        return response()->json($kelas, 201);
    }

    public function update(Request $request, $kodekelas)
    {
        $kelas = KelasMaster::where('kodekelas', $kodekelas)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'kodekelas'   => 'required|string|max:20|unique:kelasmaster,kodekelas,' . $kelas->id,
            'namakelas'   => 'required|string|max:100',
            'kodejurusan' => 'required|string|max:20',
            'kodeprodi'   => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $jurusanResp = Http::get("http://query-jurusanprodi-service:8102/api/sync/jurusan/{$request->kodejurusan}");
            $jurusan = $jurusanResp->successful() ? $jurusanResp->json() : null;

            $prodiResp = Http::get("http://query-jurusanprodi-service:8102/api/sync/prodi/{$request->kodeprodi}");
            $prodi = $prodiResp->successful() ? $prodiResp->json() : null;
        } catch (\Exception $e) {
            Log::error("Gagal ambil data jurusan/prodi: " . $e->getMessage());
            $jurusan = null;
            $prodi = null;
        }

        if (!$jurusan) {
            return response()->json([
                'errors' => ['kodejurusan' => ['Kode jurusan tidak ditemukan di service']]
            ], 422);
        }

        if (!$prodi) {
            return response()->json([
                'errors' => ['kodeprodi' => ['Kode prodi tidak ditemukan di service']]
            ], 422);
        }

        $kelas->update($request->only([
            'kodekelas', 'namakelas', 'kodejurusan', 'kodeprodi'
        ]));

        $payload = [
            'kodekelas'   => $kelas->kodekelas,
            'namakelas'   => $kelas->namakelas,
            'kodejurusan' => $kelas->kodejurusan,
            'namajurusan' => $jurusan['namajurusan'] ?? null,
            'kodeprodi'   => $kelas->kodeprodi,
            'namaprodi'   => $prodi['namaprodi'] ?? null,
        ];

        try {
            $res = Http::put("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$kodekelas}", $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron update gagal dengan status: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning('Sinkron update gagal, fallback ke RabbitMQ: ' . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'kelasmaster', 'update');
        }

        return response()->json($kelas);
    }

    public function destroy($kodekelas)
    {
        $kelas = KelasMaster::where('kodekelas', $kodekelas)->firstOrFail();
        $kelas->delete();

        try {
            $res = Http::delete("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$kodekelas}");
            if (!$res->successful()) {
                throw new \Exception("Gagal delete sync dengan status: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning('Delete gagal, fallback ke RabbitMQ: ' . $e->getMessage());
            (new RabbitMQPublisher)->publish(['kodekelas' => $kodekelas], 'kelasmaster', 'delete');
        }

        return response()->json(['message' => 'Data kelas berhasil dihapus.']);
    }
}
