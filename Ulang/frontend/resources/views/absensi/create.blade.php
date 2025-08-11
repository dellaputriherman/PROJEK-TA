@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Absensi</h1>

    <form action="{{ route('absensi.store') }}" method="POST">
        @csrf
            <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" required>
        </div>

          <div class="mb-3">
            <label for="kodekelas" class="form-label">Kelas</label>
            <select class="form-select" id="kodekelas" name="kodekelas" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelasmaster as $kelas)
                    <option value="{{ $kelas['kodekelas'] }}">{{ $kelas['kodekelas'] }} - {{ $kelas['namakelas'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kodematkul" class="form-label">Matakuliah</label>
            <select class="form-select" id="kodematkul" name="kodematkul" required>
                <option value="">-- Pilih Matakuliah --</option>
                @foreach ($matakuliah as $kode => $nama)
                    <option value="{{ $kode }}">{{ $kode }} - {{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>

      <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" id="status" name="status" required>
            <option value="">-- Pilih Status --</option>
            <option value="Hadir">Hadir</option>
            <option value="Sakit">Sakit</option>
            <option value="Izin">Izin</option>
            <option value="Alfa">Alfa</option>
        </select>
    </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
