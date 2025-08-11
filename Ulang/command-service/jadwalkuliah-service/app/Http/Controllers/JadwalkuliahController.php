<?php

namespace App\Http\Controllers;

use App\Models\Jadwalkuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\RabbitMQPublisher;

class JadwalkuliahController extends Controller
{
    public function show($kodekelas, $kodematkul)
    {
        $jadwal = Jadwalkuliah::where('kodekelas', $kodekelas)
            ->where('kodematkul', $kodematkul)
            ->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($jadwal);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodekelas'  => 'required|string|max:20',
            'kodematkul' => 'required|string|max:20',
            'hari'       => 'required|string|max:20',
            'jammulai'   => 'required|date_format:H:i',
            'jamselesai' => 'required|date_format:H:i|after:jammulai',
            'ruangan'    => 'nullable|string|max:50',
            'nip'        => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil data Kelasmaster
        try {
            $kelasResp = Http::timeout(5)->get("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$request->kodekelas}");
            $kelas = $kelasResp->successful() ? $kelasResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data kelasmaster: ' . $e->getMessage());
            $kelas = null;
        }

        if (!$kelas) {
            return response()->json(['errors' => ['kodekelas' => ['Kode kelas tidak ditemukan di service']]], 422);
        }

        // Ambil data Matakuliah
        try {
            $matkulResp = Http::timeout(5)->get("http://query-matakuliah-service:8104/api/sync/matakuliah/{$request->kodematkul}");
            $matkul = $matkulResp->successful() ? $matkulResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data matakuliah: ' . $e->getMessage());
            $matkul = null;
        }

        if (!$matkul) {
            return response()->json(['errors' => ['kodematkul' => ['Kode matkul tidak ditemukan di service']]], 422);
        }

        // Ambil data Dosen
        try {
            $dosenResp = Http::timeout(5)->get("http://query-dosen-service:8101/api/sync/dosen/{$request->nip}");
            $dosen = $dosenResp->successful() ? $dosenResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data dosen: ' . $e->getMessage());
            $dosen = null;
        }

        if (!$dosen) {
            return response()->json(['errors' => ['nip' => ['NIP dosen tidak ditemukan di service']]], 422);
        }

        // Simpan ke MySQL (model)
        $jadwal = Jadwalkuliah::create($request->only([
            'kodekelas', 'kodematkul', 'hari', 'jammulai', 'jamselesai', 'ruangan', 'nip'
        ]));

        $payload = [
            'kodekelas'  => $jadwal->kodekelas,
            'namakelas'  => $kelas['namakelas'] ?? null,
            'kodematkul' => $jadwal->kodematkul,
            'namamatkul' => $matkul['namamatkul'] ?? null,
            'hari'       => $jadwal->hari,
            'jammulai'   => $jadwal->jammulai,
            'jamselesai' => $jadwal->jamselesai,
            'ruangan'    => $jadwal->ruangan,
            'nip'        => $jadwal->nip,
            'namadosen'  => $dosen['nama'] ?? $dosen['namadosen'] ?? null,
        ];

        try {
            $res = Http::timeout(5)->post('http://query-jadwalkuliah-service:8108/api/sync/jadwalkuliah', $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron ke query-service gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena sinkron HTTP gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'jadwalkuliah', 'create');
        }

        return response()->json($jadwal, 201);
    }

    public function update(Request $request, $kodekelas, $kodematkul)
    {
        $jadwal = Jadwalkuliah::where('kodekelas', $kodekelas)
            ->where('kodematkul', $kodematkul)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'hari'       => 'required|string|max:20',
            'jammulai'   => 'required|date_format:H:i',
            'jamselesai' => 'required|date_format:H:i|after:jammulai',
            'ruangan'    => 'nullable|string|max:50',
            'nip'        => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $dosenResp = Http::timeout(5)->get("http://query-dosen-service:8101/api/sync/dosen/{$request->nip}");
            $dosen = $dosenResp->successful() ? $dosenResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data dosen: ' . $e->getMessage());
            $dosen = null;
        }

        if (!$dosen) {
            return response()->json(['errors' => ['nip' => ['NIP dosen tidak ditemukan di service']]], 422);
        }

        $jadwal->update($request->only(['hari', 'jammulai', 'jamselesai', 'ruangan', 'nip']));

        try {
            $kelasResp = Http::timeout(5)->get("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$jadwal->kodekelas}");
            $kelas = $kelasResp->successful() ? $kelasResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data kelasmaster: ' . $e->getMessage());
            $kelas = null;
        }

        try {
            $matkulResp = Http::timeout(5)->get("http://query-matakuliah-service:8104/api/sync/matakuliah/{$jadwal->kodematkul}");
            $matkul = $matkulResp->successful() ? $matkulResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data matakuliah: ' . $e->getMessage());
            $matkul = null;
        }

        $payload = [
            'kodekelas'  => $jadwal->kodekelas,
            'namakelas'  => $kelas['namakelas'] ?? null,
            'kodematkul' => $jadwal->kodematkul,
            'namamatkul' => $matkul['namamatkul'] ?? null,
            'hari'       => $jadwal->hari,
            'jammulai'   => $jadwal->jammulai,
            'jamselesai' => $jadwal->jamselesai,
            'ruangan'    => $jadwal->ruangan,
            'nip'        => $jadwal->nip,
            'namadosen'  => $dosen['nama'] ?? $dosen['namadosen'] ?? null,
        ];

        try {
            $res = Http::timeout(5)->put("http://query-jadwalkuliah-service:8108/api/sync/jadwalkuliah/{$kodekelas}/{$kodematkul}", $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron update gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena sinkron update gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'jadwalkuliah', 'update');
        }

        return response()->json($jadwal);
    }

    public function destroy($kodekelas, $kodematkul)
    {
        $jadwal = Jadwalkuliah::where('kodekelas', $kodekelas)
            ->where('kodematkul', $kodematkul)
            ->firstOrFail();

        $jadwal->delete();

        $payload = [
            'kodekelas'  => $kodekelas,
            'kodematkul' => $kodematkul,
        ];

        try {
            $res = Http::timeout(5)->delete("http://query-jadwalkuliah-service:8108/api/sync/jadwalkuliah/{$kodekelas}/{$kodematkul}");
            if (!$res->successful()) {
                throw new \Exception("Gagal delete sync dengan status: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning('Delete gagal, fallback ke RabbitMQ: ' . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'jadwalkuliah', 'delete');
        }

        return response()->json(['message' => 'Data jadwal kuliah berhasil dihapus']);
    }
}
