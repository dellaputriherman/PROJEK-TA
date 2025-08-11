<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Jurusan;
use App\Services\RabbitMQPublisher;

class JurusanController extends Controller
{
    public function show($kodejurusan)
    {
        $jurusan = Jurusan::where('kodejurusan', $kodejurusan)->first();

        if (!$jurusan) {
            return response()->json([
                'errors' => ['kodejurusan' => ['Kode jurusan tidak ditemukan']]
            ], 404);
        }

        return response()->json($jurusan);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodejurusan' => 'required|string|max:10|unique:jurusan,kodejurusan',
            'namajurusan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jurusan = Jurusan::create($request->only(['kodejurusan', 'namajurusan']));

        $dataPayload = [
            'kodejurusan' => $jurusan->kodejurusan,
            'namajurusan' => $jurusan->namajurusan,
        ];

        try {
            $response = Http::post('http://query-jurusanprodi-service:8102/api/sync/jurusan', $dataPayload);

            if (!$response->successful()) {
                (new RabbitMQPublisher)->publish($dataPayload, 'jurusan', 'create');
            }
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish($dataPayload, 'jurusan', 'create');
        }

        return response()->json($jurusan, 201);
    }

    public function update(Request $request, $kodejurusan)
    {
        $jurusan = Jurusan::where('kodejurusan', $kodejurusan)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'kodejurusan' => 'required|string|max:10|unique:jurusan,kodejurusan,' . $jurusan->id,
            'namajurusan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jurusan->update([
            'kodejurusan' => $request->kodejurusan,
            'namajurusan' => $request->namajurusan,
        ]);

        $dataPayload = [
            'kodejurusan' => $jurusan->kodejurusan,
            'namajurusan' => $jurusan->namajurusan,
        ];

        try {
            $response = Http::put("http://query-jurusanprodi-service:8102/api/sync/jurusan/{$kodejurusan}", $dataPayload);

            if (!$response->successful()) {
                (new RabbitMQPublisher)->publish($dataPayload, 'jurusan', 'update');
            }
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish($dataPayload, 'jurusan', 'update');
        }

        return response()->json($jurusan);
    }

    public function destroy($kodejurusan)
    {
        $jurusan = Jurusan::where('kodejurusan', $kodejurusan)->firstOrFail();
        $jurusan->delete();

        try {
            $response = Http::delete("http://query-jurusanprodi-service:8102/api/sync/jurusan/{$kodejurusan}");

            if (!$response->successful()) {
                (new RabbitMQPublisher)->publish(['kodejurusan' => $kodejurusan], 'jurusan', 'delete');
            }
        } catch (\Exception $e) {
            (new RabbitMQPublisher)->publish(['kodejurusan' => $kodejurusan], 'jurusan', 'delete');
        }

        return response()->json(['message' => 'Jurusan berhasil dihapus'], 200);
    }

}
