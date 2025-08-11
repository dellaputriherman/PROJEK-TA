@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Kelas Master</h1>

    <form action="{{ route('kelasmaster.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="kodekelas" class="form-label">Kode Kelas</label>
            <input type="text" class="form-control" id="kodekelas" name="kodekelas" required maxlength="20" value="{{ old('kodekelas') }}">
            @error('kodekelas')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="namakelas" class="form-label">Nama Kelas</label>
            <input type="text" class="form-control" id="namakelas" name="namakelas" required maxlength="100" value="{{ old('namakelas') }}">
            @error('namakelas')
                <small class="text-danger">{{ $message }}</small>
            @enderror
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

        <div class="mb-3">
            <label for="kodeprodi" class="form-label">Prodi</label>
            <select class="form-select" id="kodeprodi" name="kodeprodi" required>
                <option value="">-- Pilih Prodi --</option>
                @foreach ($prodi as $kode => $nama)
                    <option value="{{ $kode }}" {{ old('kodeprodi') == $kode ? 'selected' : '' }}>
                        {{ $kode }} - {{ $nama }}
                    </option>
                @endforeach
            </select>
            @error('kodeprodi')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('kelasmaster.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
