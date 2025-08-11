@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Kelas</h1>

    <form action="{{ route('kelas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" required maxlength="20" value="{{ old('nim') }}">
            @error('nim')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
        <label for="kodekelas" class="form-label">Kode Kelas</label>
            <select class="form-select" id="kodekelas" name="kodekelas" required>
                <option value="">-- Pilih Kode Kelas --</option>
                @foreach ($kelasmaster as $kelas)
                    <option value="{{ $kelas['kodekelas'] }}" {{ old('kodekelas') == $kelas['kodekelas'] ? 'selected' : '' }}>
                        {{ $kelas['kodekelas'] }} - {{ $kelas['namakelas'] ?? '' }}
                    </option>
                @endforeach
            </select>
            @error('kodekelas')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

         <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <input type="text" class="form-control" id="semester" name="semester" required maxlength="20" value="{{ old('semester') }}">
            @error('semester')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="tahunajaran" class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" id="tahunajaran" name="tahunajaran" required maxlength="20" placeholder="Contoh: 2024/2025" value="{{ old('tahunajaran') }}">
            @error('tahunajaran')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>


        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
