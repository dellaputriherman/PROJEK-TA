@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Nilai</h1>

    <form action="{{ route('nilai.store') }}" method="POST">
        @csrf
         <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" required>
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
            <label for="nilaiangka" class="form-label">Nilai</label>
            <input type="text" class="form-control" id="nilaiangka" name="nilaiangka" required>
        </div>


        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
