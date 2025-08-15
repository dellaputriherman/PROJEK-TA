<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\KelasmasterController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\JadwalkuliahController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/mahasiswa/create', [MahasiswaController::class, 'create']);
Route::post('/mahasiswa', [MahasiswaController::class, 'store']); //
Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
Route::get('/mahasiswa/{nim}/edit', [MahasiswaController::class, 'edit'])->name('mahasiswa.edit');
Route::put('/mahasiswa/{nim}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
Route::delete('/mahasiswa/{nim}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');

Route::get('/dosen/create', [DosenController::class, 'create']);
Route::post('/dosen', [DosenController::class, 'store']);
Route::post('/dosen', [DosenController::class, 'store'])->name('dosen.store');
Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
Route::get('/dosen/{nip}/edit', [DosenController::class, 'edit'])->name('dosen.edit');
Route::put('/dosen/{nip}', [DosenController::class, 'update'])->name('dosen.update');
Route::delete('/dosen/{nip}', [DosenController::class, 'destroy'])->name('dosen.destroy');

Route::get('/matakuliah/create', [MatakuliahController::class, 'create']);
Route::post('/matakuliah', [MatakuliahController::class, 'store']);
Route::post('/matakuliah', [MatakuliahController::class, 'store'])->name('matakuliah.store');
Route::get('/matakuliah', [MatakuliahController::class, 'index'])->name('matakuliah.index');
Route::get('/matakuliah/{kodematkul}/edit', [MatakuliahController::class, 'edit'])->name('matakuliah.edit');
Route::put('/matakuliah/{kodematkul}', [MatakuliahController::class, 'update'])->name('matakuliah.update');
Route::delete('/matakuliah/{kodematkul}', [MatakuliahController::class, 'destroy'])->name('matakuliah.destroy');

Route::get('/nilai/create', [NilaiController::class, 'create']);
Route::post('/nilai', [NilaiController::class, 'store']);
Route::post('/nilai', [NilaiController::class, 'store'])->name('nilai.store');
Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
Route::get('/nilai/{nim}/{kodematkul}/edit', [NilaiController::class, 'edit'])->name('nilai.edit');
Route::put('/nilai/{nim}/{kodematkul}', [NilaiController::class, 'update'])->name('nilai.update');
Route::delete('/nilai/{nim}/{kodematkul}', [NilaiController::class, 'destroy'])->name('nilai.destroy');

Route::get('/absensi/create', [AbsensiController::class, 'create']);
Route::post('/absensi', [AbsensiController::class, 'store']);
Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
Route::get('/absensi/{nim}/{kodematkul}/{tanggal}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
Route::put('/absensi/{nim}/{kodematkul}/{tanggal}', [AbsensiController::class, 'update'])->name('absensi.update');
Route::delete('/absensi/{nim}/{kodematkul}/{tanggal}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');

Route::prefix('jurusan')->name('jurusanprodi.jurusan.')->group(function () {
    Route::get('/', [JurusanController::class, 'index'])->name('index');
    Route::get('/create', [JurusanController::class, 'create'])->name('create');
    Route::post('/', [JurusanController::class, 'store'])->name('store');
    Route::get('/{kodejurusan}/edit', [JurusanController::class, 'edit'])->name('edit');
    Route::put('/{kodejurusan}', [JurusanController::class, 'update'])->name('update');
    Route::delete('/{kodejurusan}', [JurusanController::class, 'destroy'])->name('destroy');
});
// Route::get('/jurusan/{kodejurusan}/edit', [JurusanController::class, 'edit'])->name('jurusanprodi.jurusan.edit');
// Route::post('/jurusan/store', [App\Http\Controllers\JurusanController::class, 'store'])->name('jurusanprodi.jurusan.store');

Route::prefix('prodi')->name('jurusanprodi.prodi.')->group(function () {
    Route::get('/', [ProdiController::class, 'index'])->name('index');
    Route::get('/create', [ProdiController::class, 'create'])->name('create');
    Route::post('/', [ProdiController::class, 'store'])->name('store');
    Route::get('/{kodeprodi}/edit', [ProdiController::class, 'edit'])->name('edit');
    Route::put('/{kodeprodi}', [ProdiController::class, 'update'])->name('update');
    Route::delete('/{kodeprodi}', [ProdiController::class, 'destroy'])->name('destroy');
});
// Route::post('/prodi/store', [App\Http\Controllers\ProdiController::class, 'store'])->name('jurusanprodi.prodi.store');

Route::get('/kelasmaster/create', [KelasmasterController::class, 'create'])->name('kelasmaster.create');
Route::post('/kelasmaster', [KelasmasterController::class, 'store'])->name('kelasmaster.store');
Route::get('/kelasmaster', [KelasmasterController::class, 'index'])->name('kelasmaster.index');
Route::get('/kelasmaster/{kodekelas}/edit', [KelasmasterController::class, 'edit'])->name('kelasmaster.edit');
Route::put('/kelasmaster/{kodekelas}', [KelasmasterController::class, 'update'])->name('kelasmaster.update');
Route::delete('/kelasmaster/{kodekelas}', [KelasmasterController::class, 'destroy'])->name('kelasmaster.destroy');

Route::get('/jadwalkuliah/create', [JadwalKuliahController::class, 'create'])->name('jadwalkuliah.create');
Route::post('/jadwalkuliah', [JadwalKuliahController::class, 'store'])->name('jadwalkuliah.store');
Route::get('/jadwalkuliah', [JadwalKuliahController::class, 'index'])->name('jadwalkuliah.index');
Route::get('/jadwalkuliah/{kodekelas}/{kodematkul}/edit', [JadwalKuliahController::class, 'edit'])->name('jadwalkuliah.edit');
Route::put('/jadwalkuliah/{kodekelas}/{kodematkul}', [JadwalKuliahController::class, 'update'])->name('jadwalkuliah.update');
Route::delete('/jadwalkuliah/{kodekelas}/{kodematkul}', [JadwalKuliahController::class, 'destroy'])->name('jadwalkuliah.destroy');

Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
Route::get('/kelas/{nim}/{kodekelas}/{semester}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
Route::put('/kelas/{nim}/{kodekelas}/{semester}', [KelasController::class, 'update'])->name('kelas.update');
Route::delete('/kelas/{nim}/{kodekelas}/{semester}', [KelasController::class, 'destroy'])->name('kelas.destroy');
