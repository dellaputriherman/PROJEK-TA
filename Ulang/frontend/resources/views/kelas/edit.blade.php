@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Kelas</h1>

    <form action="{{ route('kelas.update', [$kelas['nim'], $kelas['kodekelas'], $kelas['semester']]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" maxlength="20"
                   value="{{ old('nim', $kelas['nim']) }}" required>
            @error('nim')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

          <div class="mb-3">
            <label for="kodekelas" class="form-label">Kode Kelas</label>
            <select class="form-select" id="kodekelas" name="kodekelas" readonly>
            @foreach ($kelasmaster as $km)
                <option value="{{ $km['kodekelas'] }}"
                    {{ $km['kodekelas'] == $kelas['kodekelas'] ? 'selected' : '' }}>
                    {{ $km['kodekelas'] }} - {{ $km['namakelas'] }}
                </option>
            @endforeach
             </select>
            <input type="hidden" name="kodekelas" value="{{ $kelas['kodekelas'] }}">
         </div>

        <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <input type="text" class="form-control" id="semester" name="semester" maxlength="20"
                value="{{ old('semester', $kelas['semester'] ?? '') }}" required>
            @error('semester')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

       <div class="mb-3">
        <label for="tahunajaran" class="form-label">Tahun Ajaran</label>
        <input type="text" class="form-control" id="tahunajaran" name="tahunajaran" maxlength="20"
            placeholder="Contoh: 2024/2025"
            value="{{ old('tahunajaran', $kelas['tahunajaran'] ?? '') }}" required>
        @error('tahunajaran')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>


        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
