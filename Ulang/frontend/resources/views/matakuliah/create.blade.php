@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Matakuliah</h1>

    <form action="{{ route('matakuliah.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="kodematkul" class="form-label">Kode Matakuliah</label>
            <input type="text" class="form-control" id="kodematkul" name="kodematkul" required>
        </div>

        <div class="mb-3">
            <label for="namamatkul" class="form-label">Nama Matakuliah</label>
            <input type="text" class="form-control" id="namamatkul" name="namamatkul" required>
        </div>

        <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <input type="text" class="form-control" id="semester" name="semester" required>
        </div>

        <div class="mb-3">
            <label for="sks" class="form-label">SKS</label>
            <input type="text" class="form-control" id="sks" name="sks" required>
        </div>

        <div class="mb-3">
            <label for="jam" class="form-label">Jam</label>
            <input type="text" class="form-control" id="jam" name="jam" required>
        </div>

       <div class="mb-3">
            <label for="kodeprodi" class="form-label">Kode Prodi</label>
            <select class="form-select" id="kodeprodi" name="kodeprodi" required>
                <option value="">-- Pilih Prodi --</option>
                @foreach ($prodi as $kode => $nama)
                    <option value="{{ $kode }}">{{ $kode }} - {{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('matakuliah.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
