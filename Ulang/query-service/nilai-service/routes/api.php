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

Route::get('/sync/nilai', [NilaiController::class, 'index']);
Route::get('/sync/nilai/{nim}/{kodematkul}', [NilaiController::class, 'show']);
Route::put('/sync/nilai/{nim}/{kodematkul}', [NilaiController::class, 'update']);
Route::delete('/sync/nilai/{nim}/{kodematkul}', [NilaiController::class, 'destroy']);
// Route::get('/nilai', [NilaiController::class, 'index']);
// Route::post('/sync/nilai', [NilaiController::class, 'store']);
