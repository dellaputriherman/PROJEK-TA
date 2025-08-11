@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Absensi</h1>

   <form action="{{ route('absensi.update', ['nim' => $absensi['nim'], 'kodematkul' => $absensi['kodematkul'], 'tanggal' => \Carbon\Carbon::parse($absensi['tanggal'])->format('Y-m-d')]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" value="{{ $absensi['nim'] }}" required>
        </div>

        <div class="mb-3">
            <label for="kodekelas" class="form-label">Kode Kelas</label>
            <select class="form-select" id="kodekelas" name="kodekelas" readonly>
                @foreach ($kelasmaster as $km)
                    <option value="{{ $km['kodekelas'] }}"
                        {{ $km['kodekelas'] == $absensi['kodekelas'] ? 'selected' : '' }}>
                        {{ $km['kodekelas'] }} - {{ $km['namakelas'] }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="kodekelas" value="{{ $absensi['kodekelas'] }}">
        </div>


        <div class="mb-3">
            <label for="kodematkul" class="form-label">Matakuliah</label>
            <select class="form-control" id="kodematkul" name="kodematkul" disabled>
                @foreach($matakuliah as $kode => $namamatkul)
                    <option value="{{ $kode }}" {{ $absensi['kodematkul'] == $kode ? 'selected' : '' }}>{{ $namamatkul }}</option>
                @endforeach
            </select>
            <input type="hidden" name="kodematkul" value="{{ $absensi['kodematkul'] }}">
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ \Carbon\Carbon::parse($absensi['tanggal'])->format('Y-m-d') }}" readonly>
        </div>
        <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::parse($absensi['tanggal'])->format('Y-m-d') }}">

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="">-- Pilih Status --</option>
                <option value="Hadir" {{ $absensi['status'] == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Sakit" {{ $absensi['status'] == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Izin" {{ $absensi['status'] == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Alfa" {{ $absensi['status'] == 'Alfa' ? 'selected' : '' }}>Alfa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
