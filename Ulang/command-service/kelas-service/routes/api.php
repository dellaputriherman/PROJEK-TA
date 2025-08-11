<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KelasController;

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
Route::post('/kelas', [KelasController::class, 'store']);
Route::put('/kelas/{nim}/{kodekelas}/{semester}', [KelasController::class, 'update']);
Route::delete('/kelas/{nim}/{kodekelas}/{semester}', [KelasController::class, 'destroy']);
Route::get('/kelas/{nim}/{kodekelas}/{semester}', [KelasController::class, 'show']);
