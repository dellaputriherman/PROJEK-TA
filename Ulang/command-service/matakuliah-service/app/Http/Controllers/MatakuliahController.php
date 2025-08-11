<?php

namespace App\Http\Controllers;

use App\Models\Matakuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\RabbitMQPublisher;
// use Illuminate\Validation\Rule;

class MatakuliahController extends Controller
{
    public function show($kodematkul)
    {
        $matkul = Matakuliah::where('kodematkul', $kodematkul)->first();

        if (!$matkul) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($matkul);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodematkul' => 'required|unique:matakuliah,kodematkul|max:20',
            'namamatkul' => 'required|string|max:255',
            'semester' => 'required|integer|min:1',
            'sks' => 'required|integer|min:1',
            'jam' => 'required|integer|min:1',
            'kodeprodi' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil data Prodi dari query service
        $prodi = null;
        try {
            $prodiResp = Http::timeout(5)->get("http://query-jurusanprodi-service:8102/api/sync/prodi/{$request->kodeprodi}");
            $prodi = $prodiResp->successful() ? $prodiResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data prodi: ' . $e->getMessage());
        }

        if (!$prodi) {
            return response()->json(['errors' => ['kodeprodi' => ['Kode prodi tidak ditemukan']]], 422);
        }

        $matakuliah = Matakuliah::create($request->only([
            'kodematkul', 'namamatkul', 'semester', 'sks', 'jam', 'kodeprodi'
        ]));

        $payload = [
            'kodematkul' => $matakuliah->kodematkul,
            'namamatkul' => $matakuliah->namamatkul,
            'semester' => $matakuliah->semester,
            'sks' => $matakuliah->sks,
            'jam' => $matakuliah->jam,
            'kodeprodi' => $matakuliah->kodeprodi,
            'namaprodi' => $prodi['namaprodi'] ?? null,
        ];

        try {
            $res = Http::timeout(5)->post('http://query-matakuliah-service:8104/api/sync/matakuliah', $payload);
            if (!$res->successful()) {
                throw new \Exception("Sinkron ke query-service gagal: " . $res->status());
            }
        } catch (\Exception $e) {
            Log::warning("Fallback ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'matakuliah', 'create');
        }

        return response()->json($matakuliah, 201);
    }

    public function update(Request $request, $kodematkul)
    {
        $matakuliah = Matakuliah::where('kodematkul', $kodematkul)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'kodematkul' => 'required|max:20|unique:matakuliah,kodematkul,' . $matakuliah->id,
            'namamatkul' => 'required|string|max:255',
            'semester' => 'required|integer|min:1',
            'sks' => 'required|integer|min:1',
            'jam' => 'required|integer|min:1',
            'kodeprodi' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prodi = null;
        try {
            $prodiResp = Http::timeout(5)->get("http://query-jurusanprodi-service:8102/api/sync/prodi/{$request->kodeprodi}");
            $prodi = $prodiResp->successful() ? $prodiResp->json() : null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil data prodi: ' . $e->getMessage());
        }

        if (!$prodi) {
            return response()->json(['errors' => ['kodeprodi' => ['Kode prodi tidak ditemukan']]], 422);
        }

        $matakuliah->update([
            'kodematkul' => $request->kodematkul,
            'namamatkul' => $request->namamatkul,
            'semester' => $request->semester,
            'sks' => $request->sks,
            'jam' => $request->jam,
            'kodeprodi' => $request->kodeprodi,
            'namaprodi' => $prodi['namaprodi'] ?? null,
        ]);

        $payload = [
            'kodematkul' => $matakuliah->kodematkul,
            'namamatkul' => $matakuliah->namamatkul,
            'semester' => $matakuliah->semester,
            'sks' => $matakuliah->sks,
            'jam' => $matakuliah->jam,
            'kodeprodi' => $matakuliah->kodeprodi,
            'namaprodi' => $prodi['namaprodi'],
        ];

        try {
            $res = Http::timeout(5)->put("http://query-matakuliah-service:8104/api/sync/matakuliah/{$kodematkul}", $payload);
            if (!$res->successful()) {
                throw new \Exception("Gagal update ke query-service");
            }
        } catch (\Exception $e) {
            Log::warning("Fallback update ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish($payload, 'matakuliah', 'update');
        }

        return response()->json($matakuliah);
    }


    public function destroy($kodematkul)
    {
        $matakuliah = Matakuliah::where('kodematkul', $kodematkul)->firstOrFail();
        $matakuliah->delete();

        try {
            $res = Http::timeout(5)->delete("http://query-matakuliah-service:8104/api/sync/matakuliah/{$kodematkul}");
            if (!$res->successful()) {
                throw new \Exception("Gagal hapus dari query-service");
            }
        } catch (\Exception $e) {
            Log::warning("Fallback delete ke RabbitMQ: " . $e->getMessage());
            (new RabbitMQPublisher)->publish(['kodematkul' => $kodematkul], 'matakuliah', 'delete');
        }

        return response()->json(['message' => 'Data matakuliah berhasil dihapus']);
    }
}
