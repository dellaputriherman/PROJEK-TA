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

Route::get('/sync/dosen', [DosenController::class, 'index']);
Route::get('/sync/dosen/{nip}', [DosenController::class, 'show']);
Route::put('/sync/dosen/{nip}', [DosenController::class, 'update']);
Route::delete('/sync/dosen/{nip}', [DosenController::class, 'destroy']);
// Route::get('/dosen', [DosenController::class, 'index']);
// Route::post('/sync/dosen', [DosenController::class, 'store']);
