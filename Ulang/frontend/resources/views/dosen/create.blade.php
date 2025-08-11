@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Dosen</h1>

    <form action="{{ route('dosen.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nip" class="form-label">NIP</label>
            <input type="text" class="form-control" id="nip" name="nip" required>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
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


        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('dosen.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
