@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Jadwal Kuliah</h1>

    <form action="{{ route('jadwalkuliah.update', [$jadwalkuliah['kodekelas'], $jadwalkuliah['kodematkul']]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kodekelas" class="form-label">Kode Kelas</label>
            <select class="form-select" id="kodekelas" name="kodekelas" disabled>
                @foreach ($kelasmaster as $kelas)
                    <option value="{{ $kelas['kodekelas'] }}"
                        {{ $jadwalkuliah['kodekelas'] == $kelas['kodekelas'] ? 'selected' : '' }}>
                        {{ $kelas['kodekelas'] }} - {{ $kelas['namakelas'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kodematkul" class="form-label">Kode Mata Kuliah</label>
            <select class="form-select" id="kodematkul" name="kodematkul" disabled>
                @foreach ($matakuliah as $mk)
                    <option value="{{ $mk['kodematkul'] }}"
                        {{ $jadwalkuliah['kodematkul'] == $mk['kodematkul'] ? 'selected' : '' }}>
                        {{ $mk['kodematkul'] }} - {{ $mk['namamatkul'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="hari" class="form-label">Hari</label>
            <input type="text" class="form-control" id="hari" name="hari"
                   value="{{ old('hari', $jadwalkuliah['hari']) }}" required maxlength="20">
            @error('hari')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jammulai" class="form-label">Jam Mulai</label>
            <input type="time" class="form-control" id="jammulai" name="jammulai"
                   value="{{ old('jammulai', $jadwalkuliah['jammulai']) }}" required>
            @error('jammulai')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jamselesai" class="form-label">Jam Selesai</label>
            <input type="time" class="form-control" id="jamselesai" name="jamselesai"
                   value="{{ old('jamselesai', $jadwalkuliah['jamselesai']) }}" required>
            @error('jamselesai')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ruangan" class="form-label">Ruangan</label>
            <input type="text" class="form-control" id="ruangan" name="ruangan"
                   value="{{ old('ruangan', $jadwalkuliah['ruangan']) }}" maxlength="50">
            @error('ruangan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nip" class="form-label">Dosen (NIP)</label>
            <select class="form-select" id="nip" name="nip" required>
                <option value="">-- Pilih Dosen --</option>
                @foreach ($dosen as $dsn)
                    <option value="{{ $dsn['nip'] }}"
                        {{ old('nip', $jadwalkuliah['nip']) == $dsn['nip'] ? 'selected' : '' }}>
                        {{ $dsn['nip'] }} - {{ $dsn['nama'] ?? $dsn['namadosen'] }}
                    </option>
                @endforeach
            </select>
            @error('nip')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('jadwalkuliah.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
