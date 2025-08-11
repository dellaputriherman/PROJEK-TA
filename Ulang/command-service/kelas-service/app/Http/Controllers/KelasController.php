<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\RabbitMQPublisher;

class KelasController extends Controller
{
    public function show($nim, $kodekelas, $semester)
    {
        $kelas = Kelas::where('nim', $nim)
            ->where('kodekelas', $kodekelas)
            ->where('semester', $semester)
            ->first();

        if (!$kelas) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($kelas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim'        => 'required|string|max:20',
            'kodekelas'  => 'required|string|max:20',
            'semester'   => 'required|string|max:20',
            'tahunajaran'   => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek kombinasi unik
        $exists = Kelas::where('nim', $request->nim)
            ->where('kodekelas', $request->kodekelas)
            ->where('semester', $request->semester)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Data kelas sudah ada'], 409);
        }

        // Validasi kodekelas ke Kelasmaster service
        try {
            $resKelas = Http::timeout(5)->get("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$request->kodekelas}");
            if (!$resKelas->successful()) {
                return response()->json(['errors' => ['kodekelas' => ['Kode kelas tidak ditemukan di Kelasmaster service']]], 422);
            }
            $dataKelas = $resKelas->json();
        } catch (\Exception $e) {
            Log::error("Gagal ambil data kodekelas: " . $e->getMessage());
            return response()->json(['errors' => ['kodekelas' => ['Gagal terhubung ke Kelasmaster service']]], 500);
        }

        // Validasi mahasiswa ke Mahasiswa service
        try {
            $resMahasiswa = Http::timeout(5)->get("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$request->nim}");
            if (!$resMahasiswa->successful()) {
                return response()->json(['errors' => ['nim' => ['Mahasiswa tidak ditemukan di Mahasiswa service']]], 422);
            }
            $dataMahasiswa = $resMahasiswa->json();
        } catch (\Exception $e) {
            Log::error("Gagal ambil data mahasiswa: " . $e->getMessage());
            return response()->json(['errors' => ['nim' => ['Gagal terhubung ke Mahasiswa service']]], 500);
        }

        $kelas = Kelas::create($request->only(['nim', 'kodekelas', 'semester', 'tahunajaran']));

        $payload = [
            'nim'         => $kelas->nim,
            'kodekelas'   => $kelas->kodekelas,
            'semester'    => $kelas->semester,
            'tahunajaran' => $kelas->tahunajaran,
        ];

        try {
            $res = Http::timeout(5)->post('http://query-kelas-service:8107/api/sync/kelas', $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron ke query-service gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'kelas', 'create');
        }

        return response()->json($kelas, 201);
    }

    public function update(Request $request, $nim, $kodekelas, $semester)
    {
        $kelas = Kelas::where('nim', $nim)
            ->where('kodekelas', $kodekelas)
            ->where('semester', $semester)
            ->first();

        if (!$kelas) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nim'           => 'required|string|max:20',
            'kodekelas'     => 'required|string|max:20',
            'semester'      => 'required|string|max:20',
            'tahunajaran'   => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek kombinasi unik
        $exists = Kelas::where('nim', $request->nim)
            ->where('kodekelas', $request->kodekelas)
            ->where('semester', $request->semester)
            ->where('id', '!=', $kelas->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Kombinasi data sudah ada'], 409);
        }

        // Validasi kodekelas ke Kelasmaster service
        try {
            $resKelas = Http::timeout(5)->get("http://query-kelasmaster-service:8106/api/sync/kelasmaster/{$request->kodekelas}");
            if (!$resKelas->successful()) {
                return response()->json(['errors' => ['kodekelas' => ['Kode kelas tidak ditemukan di Kelasmaster service']]], 422);
            }
            $dataKelas = $resKelas->json();
        } catch (\Exception $e) {
            Log::error("Gagal ambil data kodekelas: " . $e->getMessage());
            return response()->json(['errors' => ['kodekelas' => ['Gagal terhubung ke Kelasmaster service']]], 500);
        }

        // Validasi mahasiswa ke Mahasiswa service
        try {
            $resMahasiswa = Http::timeout(5)->get("http://query-mahasiswa-service:8103/api/sync/mahasiswa/{$request->nim}");
            if (!$resMahasiswa->successful()) {
                return response()->json(['errors' => ['nim' => ['Mahasiswa tidak ditemukan di Mahasiswa service']]], 422);
            }
            $dataMahasiswa = $resMahasiswa->json();
        } catch (\Exception $e) {
            Log::error("Gagal ambil data mahasiswa: " . $e->getMessage());
            return response()->json(['errors' => ['nim' => ['Gagal terhubung ke Mahasiswa service']]], 500);
        }


        $kelas->update($request->only(['nim', 'kodekelas', 'semester', 'tahunajaran']));

        $payload = [
            'nim'        => $kelas->nim,
            'kodekelas'  => $kelas->kodekelas,
            'semester'   => $kelas->semester,
            'tahunajaran' => $kelas->tahunajaran,
        ];

        try {
            $res = Http::timeout(5)->put("http://query-kelas-service:8107/api/sync/kelas/{$nim}/{$kodekelas}/{$semester}", $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron update ke query-service gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback update ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'kelas', 'update');
        }

        return response()->json($kelas);
    }

    public function destroy($nim, $kodekelas, $semester)
    {
        $kelas = Kelas::where('nim', $nim)
            ->where('kodekelas', $kodekelas)
            ->where('semester', $semester)
            ->first();

        if (!$kelas) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $kelas->delete();

        try {
            $res = Http::timeout(5)->delete("http://query-kelas-service:8107/api/sync/kelas/{$nim}/{$kodekelas}/{$semester}");
            if (!$res->successful()) {
                throw new \Exception("Gagal hapus dari query-service");
            }
        } catch (\Exception $e) {
            Log::warning("Fallback delete ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish(['id' => $kelas->id], 'kelas', 'delete');
        }

        return response()->json(['message' => 'Data kelas berhasil dihapus']);
    }
}
