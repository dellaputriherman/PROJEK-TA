@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Prodi</h1>

    <form action="{{ route('jurusanprodi.prodi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="kodeprodi" class="form-label">Kode Prodi</label>
            <input type="text" class="form-control" id="kodeprodi" name="kodeprodi" required>
        </div>
        <div class="mb-3">
            <label for="namaprodi" class="form-label">Nama Prodi</label>
            <input type="text" class="form-control" id="namaprodi" name="namaprodi" required>
        </div>
        <div class="mb-3">
            <label for="kodejurusan" class="form-label">Jurusan</label>
            <select class="form-select" id="kodejurusan" name="kodejurusan" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach ($jurusan as $kode => $nama)
                    <option value="{{ $kode }}">{{ $kode }} - {{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('jurusanprodi.prodi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
