<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\RabbitMQPublisher;

class MahasiswaController extends Controller
{
    public function show($nim)
    {
        $mhs = Mahasiswa::where('nim', $nim)->first();
        if (!$mhs) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($mhs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|unique:mahasiswa,nim|max:20',
            'nama' => 'required|string|max:255',
            'tempatlahir' => 'required|string|max:100',
            'tanggallahir' => 'required|date',
            'jeniskelamin' => 'required|in:L,P',
            'kodejurusan' => 'required',
            'kodeprodi' => 'required',
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
            Log::error('Gagal ambil data jurusan/prodi: ' . $e->getMessage());
        }

        if (!$jurusan || !$prodi) {
            return response()->json(['errors' => ['sinkronisasi' => ['Data jurusan/prodi tidak valid']]], 422);
        }

        $mahasiswa = Mahasiswa::create($request->only([
            'nim', 'nama', 'tempatlahir', 'tanggallahir', 'jeniskelamin',
            'kodejurusan', 'kodeprodi'
        ]));

        $payload = [
            'nim' => $mahasiswa->nim,
            'nama' => $mahasiswa->nama,
            'tempatlahir' => $mahasiswa->tempatlahir,
            'tanggallahir' => $mahasiswa->tanggallahir,
            'jeniskelamin' => $mahasiswa->jeniskelamin,
            'kodejurusan' => $mahasiswa->kodejurusan,
            'namajurusan' => $jurusan['namajurusan'] ?? null,
            'kodeprodi' => $mahasiswa->kodeprodi,
            'namaprodi' => $prodi['namaprodi'] ?? null,
        ];

        try {
            $res = Http::timeout(5)->post('http://query-mahasiswa-service:8103/api/sync/mahasiswa', $payload);

            if (!$res->successful()) {
                throw new \Exception("Sinkron ke query-mahasiswa gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena HTTP gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'mahasiswa', 'create');
        }

        return response()->json($mahasiswa, 201);
    }


    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nim' => 'required|max:20|unique:mahasiswa,nim,' . $mahasiswa->id,
            'nama' => 'required|string|max:255',
            'tempatlahir' => 'required|string|max:100',
            'tanggallahir' => 'required|date',
            'jeniskelamin' => 'required|in:L,P',
            'kodejurusan' => 'required',
            'kodeprodi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jurusanResp = Http::get("http://query-jurusanprodi-service:8102/api/sync/jurusan/{$request->kodejurusan}");
        $prodiResp = Http::get("http://query-jurusanprodi-service:8102/api/sync/prodi/{$request->kodeprodi}");

        $jurusan = $jurusanResp->json();
        $prodi = $prodiResp->json();

        if (!$jurusanResp->successful() || !$jurusan) {
            return response()->json(['errors' => ['kodejurusan' => ['Kode jurusan tidak ditemukan']]], 422);
        }

        if (!$prodiResp->successful() || !$prodi) {
            return response()->json(['errors' => ['kodeprodi' => ['Kode prodi tidak ditemukan']]], 422);
        }

        $mahasiswa->update($request->only([
            'nim', 'nama', 'tempatlahir', 'tanggallahir', 'jeniskelamin',
            'kodejurusan', 'kodeprodi'
        ]));

        try {
            Http::put("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$nim}", [
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'tempatlahir' => $mahasiswa->tempatlahir,
                'tanggallahir' => $mahasiswa->tanggallahir,
                'jeniskelamin' => $mahasiswa->jeniskelamin,
                'kodejurusan' => $mahasiswa->kodejurusan,
                'namajurusan' => $jurusan['namajurusan'] ?? null,
                'kodeprodi' => $mahasiswa->kodeprodi,
                'namaprodi' => $prodi['namaprodi'] ?? null,
            ]);
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish([
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'tempatlahir' => $mahasiswa->tempatlahir,
                'tanggallahir' => $mahasiswa->tanggallahir,
                'jeniskelamin' => $mahasiswa->jeniskelamin,
                'kodejurusan' => $mahasiswa->kodejurusan,
                'namajurusan' => $jurusan['namajurusan'] ?? null,
                'kodeprodi' => $mahasiswa->kodeprodi,
                'namaprodi' => $prodi['namaprodi'] ?? null,
            ], 'mahasiswa', 'update');
        }

        return response()->json($mahasiswa);
    }

    public function destroy($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
        $mahasiswa->delete();

        try {
            Http::delete("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$nim}");
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish([
                'nim' => $nim
            ], 'mahasiswa', 'delete');
        }

        return response()->json(['message' => 'Data mahasiswa berhasil dihapus']);
    }
}
