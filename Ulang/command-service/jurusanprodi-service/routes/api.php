<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\AbsensiController;
use App\Models\Jurusan;

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
// Jurusan
Route::post('/jurusan', [JurusanController::class, 'store']);
Route::put('/jurusan/{kodejurusan}', [JurusanController::class, 'update']);
Route::delete('/jurusan/{kodejurusan}', [JurusanController::class, 'destroy']);
Route::get('/jurusan/{kodejurusan}', [JurusanController::class, 'show']);
// Prodi
Route::post('/prodi', [ProdiController::class, 'store']);
Route::put('/prodi/{kodeprodi}', [ProdiController::class, 'update']);
Route::delete('/prodi/{kodeprodi}', [ProdiController::class, 'destroy']);
Route::get('/prodi/{kodeprodi}', [ProdiController::class, 'show']);
