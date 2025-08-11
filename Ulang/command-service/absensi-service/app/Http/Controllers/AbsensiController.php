<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\RabbitMQPublisher;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function show($nim, $kodematkul, $tanggal)
    {
        $tanggal = Carbon::parse($tanggal)->format('Y-m-d');

        $absensi = Absensi::where('nim', $nim)
            ->where('kodematkul', $kodematkul)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($absensi);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'kodekelas' => 'required|string|max:20',
            'kodematkul' => 'required|string|max:10',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Sakit,Izin,Alfa',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');

        // Cek duplikat data absensi
        if (Absensi::where('nim', $request->nim)
            ->where('kodematkul', $request->kodematkul)
            ->where('tanggal', $tanggal)
            ->exists()
        ) {
            return response()->json(['message' => 'Data absensi sudah ada'], 409);
        }

        $mahasiswa = null;
        $kelas = null;
        $matkul = null;

        try {
            $mahasiswaResp = Http::timeout(5)->get("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$request->nim}");
            $mahasiswa = $mahasiswaResp->successful() ? $mahasiswaResp->json() : null;

            $kelasResp = Http::timeout(5)->get("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$request->kodekelas}");
            $kelas = $kelasResp->successful() ? $kelasResp->json() : null;

            $matkulResp = Http::timeout(5)->get("http://query-matakuliah-service:8104/api/sync/matakuliah/{$request->kodematkul}");
            $matkul = $matkulResp->successful() ? $matkulResp->json() : null;
        } catch (\Exception $e) {
            Log::error("Gagal ambil data referensi absensi: " . $e->getMessage());
        }

        if (!$mahasiswa || !$kelas || !$matkul) {
            return response()->json(['errors' => ['referensi' => ['Data mahasiswa, kelas, atau matakuliah tidak valid']]], 422);
        }

        $absensi = Absensi::create([
            'nim' => $request->nim,
            'kodekelas' => $request->kodekelas,
            'kodematkul' => $request->kodematkul,
            'tanggal' => $tanggal,
            'status' => $request->status,
        ]);

        $payload = [
            'nim' => $absensi->nim,
            'namamahasiswa' => $mahasiswa['nama'] ?? null,
            'kodekelas' => $absensi->kodekelas,
            'kodematkul' => $absensi->kodematkul,
            'namamatkul' => $matkul['namamatkul'] ?? null,
            'tanggal' => $tanggal,
            'status' => $absensi->status,
        ];

        try {
            $res = Http::timeout(5)->post('http://query-absensi-service:8100/api/sync/absensi', $payload);
            if (!$res->successful()) {
                throw new \Exception("Sync gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Sync store gagal, fallback ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'absensi', 'create');
        }

        return response()->json($absensi, 201);
    }

    public function update(Request $request, $nim, $kodematkul, $tanggal)
    {
        $tanggal = Carbon::parse($tanggal)->format('Y-m-d');

        $absensi = Absensi::where('nim', $nim)
            ->where('kodematkul', $kodematkul)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'kodekelas' => 'required|string|max:20',
            'kodematkul' => 'required|string|max:10',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Sakit,Izin,Alfa',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $newTanggal = Carbon::parse($request->tanggal)->format('Y-m-d');

        $exists = Absensi::where('nim', $request->nim)
            ->where('kodematkul', $request->kodematkul)
            ->where('tanggal', $newTanggal)
            ->where('id', '!=', $absensi->id) // asumsi ada kolom id
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Kombinasi data sudah ada'], 409);
        }

        $absensi->update([
            'nim' => $request->nim,
            'kodekelas' => $request->kodekelas,
            'kodematkul' => $request->kodematkul,
            'tanggal' => $newTanggal,
            'status' => $request->status,
        ]);

        $mahasiswa = null;
        $kelas = null;
        $matkul = null;

        try {
            $mahasiswaResp = Http::timeout(5)->get("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$absensi->nim}");
            $mahasiswa = $mahasiswaResp->successful() ? $mahasiswaResp->json() : null;

            $kelasResp = Http::timeout(5)->get("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$absensi->kodekelas}");
            $kelas = $kelasResp->successful() ? $kelasResp->json() : null;

            $matkulResp = Http::timeout(5)->get("http://query-matakuliah-service:8104/api/sync/matakuliah/{$absensi->kodematkul}");
            $matkul = $matkulResp->successful() ? $matkulResp->json() : null;
        } catch (\Exception $e) {
            Log::error("Gagal ambil data referensi absensi: " . $e->getMessage());
        }

        if (!$mahasiswa || !$kelas || !$matkul) {
            return response()->json(['errors' => ['referensi' => ['Data mahasiswa, kelas, atau matakuliah tidak valid']]], 422);
        }

        $payload = [
            'nim' => $absensi->nim,
            'namamahasiswa' => $mahasiswa['nama'] ?? null,
            'kodekelas' => $absensi->kodekelas,
            'kodematkul' => $absensi->kodematkul,
            'namamatkul' => $matkul['namamatkul'] ?? null,
            'tanggal' => $newTanggal,
            'status' => $absensi->status,
        ];

        try {
            $res = Http::timeout(5)->put("http://query-absensi-service:8100/api/sync/absensi/{$absensi->nim}/{$absensi->kodematkul}/{$newTanggal}", $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron ke query-absensi gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ karena HTTP update gagal: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'absensi', 'update');
        }

        return response()->json($absensi);
    }

    public function destroy($nim, $kodematkul, $tanggal)
    {
        $tanggal = Carbon::parse($tanggal)->format('Y-m-d');

        $absensi = Absensi::where('nim', $nim)
            ->where('kodematkul', $kodematkul)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $absensi->delete();

        try {
            $res = Http::timeout(5)->delete("http://query-absensi-service:8100/api/sync/absensi/{$nim}/{$kodematkul}/{$tanggal}");
            if (!$res->successful()) {
                throw new \Exception("Gagal menghapus data dari query-absensi");
            }
        } catch (\Exception $e) {
            Log::warning("Fallback delete ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish([
                'nim' => $nim,
                'kodematkul' => $kodematkul,
                'tanggal' => $tanggal,
            ], 'absensi', 'delete');
        }

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
