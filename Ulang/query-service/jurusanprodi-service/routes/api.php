<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\AbsensiController;

use App\Models\Mahasiswa;

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

Route::get('/sync/jurusan', [JurusanController::class, 'index']);
Route::get('/sync/jurusan/{kodejurusan}', [JurusanController::class, 'show']);
Route::put('/sync/jurusan/{kodejurusan}', [JurusanController::class, 'update']);
Route::delete('/sync/jurusan/{kodejurusan}', [JurusanController::class, 'destroy']);
// Route::get('/jurusan', [JurusanController::class, 'index']);
// Route::post('/sync/jurusan', [JurusanController::class, 'store']);

Route::get('/sync/prodi', [ProdiController::class, 'index']);
Route::get('/sync/prodi/{kodeprodi}', [ProdiController::class, 'show']);
Route::put('/sync/prodi/{kodeprodi}', [ProdiController::class, 'update']);
Route::delete('/sync/prodi/{kodeprodi}', [ProdiController::class, 'destroy']);
// Route::get('/prodi', [ProdiController::class, 'index']);
// Route::post('/sync/prodi', [ProdiController::class, 'store']);
