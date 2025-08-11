@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Matakuliah</h1>

    <form action="{{ route('matakuliah.update', $matakuliah['kodematkul'])}}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kodematkul" class="form-label">Kode Matakuliah</label>
            <input type="text" name="kodematkul" class="form-control" value="{{ $matakuliah['kodematkul'] }}" required>
        </div>

        <div class="mb-3">
            <label for="namamatkul" class="form-label">Nama Matakuliah</label>
            <input type="text" name="namamatkul" class="form-control" value="{{ $matakuliah['namamatkul'] }}" required>
        </div>

        <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <input type="number" name="semester" class="form-control" value="{{ $matakuliah['semester'] }}" required>
        </div>

        <div class="mb-3">
            <label for="sks" class="form-label">SKS</label>
            <input type="number" name="sks" class="form-control" value="{{ $matakuliah['sks'] }}" required>
        </div>

        <div class="mb-3">
            <label for="jam" class="form-label">Jam</label>
            <input type="number" name="jam" class="form-control" value="{{ $matakuliah['jam'] }}" required>
        </div>

          <div class="mb-3">
            <label for="kodeprodi" class="form-label">Prodi</label>
            <select class="form-control" id="kodeprodi" name="kodeprodi" required>
                @foreach($prodi as $kode => $namaprodi)
                    <option value="{{ $kode }}" {{ $matakuliah['kodeprodi'] == $kode ? 'selected' : '' }}>{{ $namaprodi }}</option>
                @endforeach
            </select>
        </div>


        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('matakuliah.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
