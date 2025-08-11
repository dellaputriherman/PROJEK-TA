<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KelasmasterController;

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

Route::post('/kelasmaster', [KelasmasterController::class, 'store']);
Route::put('/kelasmaster/{kodekelas}', [KelasmasterController::class, 'update']);
Route::delete('/kelasmaster/{kodekelas}', [KelasmasterController::class, 'destroy']);
Route::get('/kelasmaster/{kodekelas}', [KelasmasterController::class, 'show']);

