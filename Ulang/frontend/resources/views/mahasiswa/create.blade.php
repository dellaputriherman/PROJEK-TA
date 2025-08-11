@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Mahasiswa</h1>

    <form action="{{ route('mahasiswa.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" required>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>

           <div class="mb-3">
            <label for="tempatlahir" class="form-label">Tempat Lahir</label>
            <input type="text" class="form-control" id="tempatlahir" name="tempatlahir" required>
        </div>

        <div class="mb-3">
            <label for="tanggallahir" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="tanggallahir" name="tanggallahir" required>
        </div>

       <div class="mb-3">
            <label for="jeniskelamin" class="form-label">Jenis Kelamin</label>
            <select class="form-control" id="jeniskelamin" name="jeniskelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="kodejurusan" class="form-label">Kode Jurusan</label>
            <select class="form-select" id="kodejurusan" name="kodejurusan" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach ($jurusan as $kode => $nama)
                    <option value="{{ $kode }}">{{ $kode }} - {{ $nama }}</option>
                @endforeach
            </select>
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
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
