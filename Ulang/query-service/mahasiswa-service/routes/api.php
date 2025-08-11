<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;


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


Route::get('/sync/mahasiswa', [MahasiswaController::class, 'index']);
Route::get('/sync/mahasiswa/{nim}', [MahasiswaController::class, 'show']);
Route::put('/sync/mahasiswa/{nim}', [MahasiswaController::class, 'update']);
Route::delete('/sync/mahasiswa/{nim}', [MahasiswaController::class, 'destroy']);
// Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
// Route::get('mahasiswa/{nim}', [MahasiswaController::class, 'show']);
// Route::post('/sync/mahasiswa', [MahasiswaController::class, 'store']);
