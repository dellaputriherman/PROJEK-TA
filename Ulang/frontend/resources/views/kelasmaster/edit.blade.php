@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Kelas Master</h1>

    <form action="{{ route('kelasmaster.update', $kelasmaster['kodekelas']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kodekelas" class="form-label">Kode Kelas</label>
            <input type="text" class="form-control" id="kodekelas" name="kodekelas" required maxlength="20" value="{{ old('kodekelas', $kelasmaster['kodekelas']) }}" required>
            @error('kodekelas')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="namakelas" class="form-label">Nama Kelas</label>
            <input type="text" class="form-control" id="namakelas" name="namakelas" required maxlength="100" value="{{ old('namakelas', $kelasmaster['namakelas']) }}">
            @error('namakelas')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="kodejurusan" class="form-label">Jurusan</label>
            <select class="form-select" id="kodejurusan" name="kodejurusan" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach ($jurusan as $kode => $nama)
                    <option value="{{ $kode }}" {{ (old('kodejurusan', $kelasmaster['kodejurusan'] ?? '') == $kode) ? 'selected' : '' }}>
                        {{ $kode }} - {{ $nama }}
                    </option>
                @endforeach
            </select>
            @error('kodejurusan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="kodeprodi" class="form-label">Prodi</label>
            <select class="form-select" id="kodeprodi" name="kodeprodi" required>
                <option value="">-- Pilih Prodi --</option>
                @foreach ($prodi as $kode => $nama)
                    <option value="{{ $kode }}" {{ (old('kodeprodi', $kelasmaster['kodeprodi']) == $kode) ? 'selected' : '' }}>
                        {{ $kode }} - {{ $nama }}
                    </option>
                @endforeach
            </select>
            @error('kodeprodi')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('kelasmaster.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
