@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Mahasiswa</h1>
    <form action="{{ route('mahasiswa.update', $mahasiswa['nim']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" value="{{ $mahasiswa['nim'] }}" required>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $mahasiswa['nama'] }}" required>
        </div>

        <div class="mb-3">
            <label for="tempatlahir" class="form-label">Tempat Lahir</label>
            <input type="text" class="form-control" id="tempatlahir" name="tempatlahir" value="{{ $mahasiswa['tempatlahir'] }}" required>
        </div>

        <div class="mb-3">
            <label for="tanggallahir" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="tanggallahir" name="tanggallahir" value="{{ $mahasiswa['tanggallahir'] }}" required>
        </div>

        <div class="mb-3">
            <label for="jeniskelamin" class="form-label">Jenis Kelamin</label>
            <select class="form-control" id="jeniskelamin" name="jeniskelamin" required>
                <option value="L" {{ $mahasiswa['jeniskelamin'] == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $mahasiswa['jeniskelamin'] == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="kodejurusan" class="form-label">Jurusan</label>
            <select class="form-control" id="kodejurusan" name="kodejurusan" required>
                @foreach($jurusan as $kode => $namajurusan)
                    <option value="{{ $kode }}" {{ $mahasiswa['kodejurusan'] == $kode ? 'selected' : '' }}>{{ $namajurusan }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kodeprodi" class="form-label">Prodi</label>
            <select class="form-control" id="kodeprodi" name="kodeprodi" required>
                @foreach($prodi as $kode => $namaprodi)
                    <option value="{{ $kode }}" {{ $mahasiswa['kodeprodi'] == $kode ? 'selected' : '' }}>{{ $namaprodi }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
