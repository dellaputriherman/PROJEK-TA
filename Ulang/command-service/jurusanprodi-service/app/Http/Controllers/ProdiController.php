<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Prodi;
use App\Services\RabbitMQPublisher;

class ProdiController extends Controller
{
    public function show($kodeprodi)
    {
        $prodi = Prodi::where('kodeprodi', $kodeprodi)->first();

        if (!$prodi) {
            return response()->json([
                'errors' => ['kodeprodi' => ['Kode prodi tidak ditemukan']]
            ], 404);
        }

        return response()->json($prodi);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodeprodi' => 'required|string|max:10|unique:prodi,kodeprodi',
            'namaprodi' => 'required|string|max:255',
            'kodejurusan' => 'required|exists:jurusan,kodejurusan',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prodi = Prodi::create([
            'kodeprodi' => strtoupper($request->kodeprodi),
            'namaprodi' => $request->namaprodi,
            'kodejurusan' => $request->kodejurusan,
        ]);

        $dataPayload = [
            'kodeprodi' => $prodi->kodeprodi,
            'namaprodi' => $prodi->namaprodi,
            'kodejurusan' => $prodi->kodejurusan,
        ];

        try {
            $response = Http::post('http://query-jurusanprodi-service:8102/api/sync/prodi', $dataPayload);

            if (!$response->successful()) {
                (new RabbitMQPublisher)->publish($dataPayload, 'prodi', 'create');
            }
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish($dataPayload, 'prodi', 'create');
        }

        return response()->json($prodi, 201);
    }

    public function update(Request $request, $kodeprodi)
    {
        $prodi = Prodi::where('kodeprodi', $kodeprodi)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'kodeprodi' => 'required|string|max:10|unique:prodi,kodeprodi,' . $prodi->id,
            'namaprodi' => 'required|string|max:255',
            'kodejurusan' => 'required|exists:jurusan,kodejurusan',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prodi->update([
            'kodeprodi' => strtoupper($request->kodeprodi),
            'namaprodi' => $request->namaprodi,
            'kodejurusan' => $request->kodejurusan,
        ]);

        $dataPayload = [
            'kodeprodi' => $prodi->kodeprodi,
            'namaprodi' => $prodi->namaprodi,
            'kodejurusan' => $prodi->kodejurusan,
        ];

        try {
            $response = Http::put("http://query-jurusanprodi-service:8102/api/sync/prodi/{$kodeprodi}", $dataPayload);

            if (!$response->successful()) {
                (new RabbitMQPublisher)->publish($dataPayload, 'prodi', 'update');
            }
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish($dataPayload, 'prodi', 'update');
        }

        return response()->json($prodi);
    }

    public function destroy($kodeprodi)
    {
        $prodi = Prodi::where('kodeprodi', $kodeprodi)->firstOrFail();
        $prodi->delete();

        try {
            $response = Http::delete("http://query-jurusanprodi-service:8102/api/sync/prodi/{$kodeprodi}");

            if (!$response->successful()) {
                (new RabbitMQPublisher)->publish(['kodeprodi' => $kodeprodi], 'prodi', 'delete');
            }
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish(['kodeprodi' => $kodeprodi], 'prodi', 'delete');
        }

        return response()->json(['message' => 'Prodi berhasil dihapus'], 200);
    }

}
