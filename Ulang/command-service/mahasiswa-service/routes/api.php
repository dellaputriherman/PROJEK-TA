<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;




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

// Mahasiswa
// Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
// Route::put('/mahasiswa/{nim}', [MahasiswaController::class, 'update']);
// Route::delete('/mahasiswa/{nim}', [MahasiswaController::class, 'destroy']);


Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
Route::put('/mahasiswa/{nim}', [MahasiswaController::class, 'update']);
Route::delete('/mahasiswa/{nim}', [MahasiswaController::class, 'destroy']);
Route::get('/mahasiswa/{nim}', [MahasiswaController::class, 'show']);
