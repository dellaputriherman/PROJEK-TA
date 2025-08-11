<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\RabbitMQPublisher;

class DosenController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:20|unique:dosen,nip',
            'nama' => 'required|string|max:255',
            'kodejurusan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jurusan = null;

        try {
            $jurusanResp = Http::timeout(5)->get("http://query-jurusanprodi-service:8102/api/sync/jurusan/{$request->kodejurusan}");
            $jurusan = $jurusanResp->successful() ? $jurusanResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data jurusan: ' . $e->getMessage());
        }

        if (!$jurusan) {
            return response()->json(['errors' => ['kodejurusan' => ['Kode jurusan tidak ditemukan']]], 422);
        }

        $dosen = Dosen::create($request->only(['nip', 'nama', 'kodejurusan']));

        $payload = [
            'nip' => $dosen->nip,
            'nama' => $dosen->nama,
            'kodejurusan' => $dosen->kodejurusan,
            'namajurusan' => $jurusan['namajurusan'] ?? null,
        ];

        try {
            $res = Http::timeout(5)->post("http://query-dosen-service:8101/api/sync/dosen", $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron gagal");
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena HTTP gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'dosen', 'create');
        }

        return response()->json($dosen, 201);
    }

    public function update(Request $request, $nip)
    {
        $dosen = Dosen::where('nip', $nip)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:10|unique:dosen,nip,' . $dosen->id,
            'nama' => 'required|string|max:255',
            'kodejurusan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jurusan = null;

        try {
            $jurusanResp = Http::timeout(5)->get("http://query-jurusanprodi-service:8102/api/sync/jurusan/{$request->kodejurusan}");
            $jurusan = $jurusanResp->successful() ? $jurusanResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data jurusan: ' . $e->getMessage());
        }

        if (!$jurusan) {
            return response()->json(['errors' => ['kodejurusan' => ['Kode jurusan tidak ditemukan']]], 422);
        }

        $dosen->update($request->only(['nip', 'nama', 'kodejurusan']));

        $payload = [
            'nip' => $dosen->nip,
            'nama' => $dosen->nama,
            'kodejurusan' => $dosen->kodejurusan,
            'namajurusan' => $jurusan['namajurusan'] ?? null,
        ];

        try {
            $res = Http::timeout(5)->put("http://query-dosen-service:8101/api/sync/dosen/{$nip}", $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron gagal");
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena HTTP gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'dosen', 'update');
        }

        return response()->json($dosen);
    }

    public function destroy($nip)
    {
        $dosen = Dosen::where('nip', $nip)->firstOrFail();
        $dosen->delete();

        try {
            $res = Http::timeout(5)->delete("http://query-dosen-service:8101/api/sync/dosen/{$nip}");
            if (!$res->successful()) {
                throw new \Exception("Sinkron gagal");
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena HTTP gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish(['nip' => $nip], 'dosen', 'delete');
        }

        return response()->json(['message' => 'Data dosen berhasil dihapus']);
    }
}
