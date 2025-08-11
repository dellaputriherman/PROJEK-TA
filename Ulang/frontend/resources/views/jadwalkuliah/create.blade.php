@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Jadwal Kuliah</h1>

    <form action="{{ route('jadwalkuliah.store') }}" method="POST">
        @csrf

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
            <label for="kodematkul" class="form-label">Matakuliah</label>
            <select class="form-select" id="kodematkul" name="kodematkul" required>
            <option value="">-- Pilih Matakuliah --</option>
            @foreach ($matakuliah as $matkul)
                <option value="{{ $matkul['kodematkul'] }}">{{ $matkul['kodematkul'] }} - {{ $matkul['namamatkul'] }}</option>
            @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="hari" class="form-label">Hari</label>
            <input type="text" class="form-control" id="hari" name="hari" required maxlength="20" value="{{ old('hari') }}">
            @error('hari')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jammulai" class="form-label">Jam Mulai</label>
            <input type="time" class="form-control" id="jammulai" name="jammulai" required value="{{ old('jammulai') }}">
            @error('jammulai')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jamselesai" class="form-label">Jam Selesai</label>
            <input type="time" class="form-control" id="jamselesai" name="jamselesai" required value="{{ old('jamselesai') }}">
            @error('jamselesai')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ruangan" class="form-label">Ruangan (opsional)</label>
            <input type="text" class="form-control" id="ruangan" name="ruangan" maxlength="50" value="{{ old('ruangan') }}">
            @error('ruangan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nip" class="form-label">NIP Dosen</label>
            <select class="form-select" id="nip" name="nip" required>
            <option value="">-- Pilih Dosen --</option>
            @foreach ($dosen as $dosen)
                <option value="{{ $dosen['nip'] }}" {{ old('nip') == $dosen['nip'] ? 'selected' : '' }}>
                    {{ $dosen['nip'] }} - {{ $dosen['nama'] ?? ($dosen['namadosen'] ?? '') }}
                </option>
            @endforeach
             </select>

            @error('nip')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('jadwalkuliah.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
