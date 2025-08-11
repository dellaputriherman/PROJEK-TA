<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalkuliahController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/jadwalkuliah', [JadwalkuliahController::class, 'store']);
Route::put('/jadwalkuliah/{kodekelas}/{kodematkul}', [JadwalkuliahController::class, 'update']);
Route::delete('/jadwalkuliah/{kodekelas}/{kodematkul}', [JadwalkuliahController::class, 'destroy']);
Route::get('/jadwalkuliah/{kodekelas}/{kodematkul}', [JadwalkuliahController::class, 'show']);
