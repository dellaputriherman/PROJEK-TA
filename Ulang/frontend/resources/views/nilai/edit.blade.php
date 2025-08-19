@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Edit Nilai</h1>

        <form action="{{ route('nilai.update', ['nim' => $nilai['nim'], 'kodematkul' => $nilai['kodematkul']]) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" value="{{ $nilai['nim'] }}" required>
            </div>

               <div class="mb-3">
            <label for="kodematkul" class="form-label">Mata Kuliah</label>
            <select name="kodematkul" id="kodematkul" class="form-control" required>
                @foreach($matakuliah as $kode => $nama)
                    <option value="{{ $kode }}" {{ $nilai['kodematkul'] == $kode ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>

            <div class="mb-3">
                <label for="nilaiangka" class="form-label">Nilai</label>
                <input type="number" name="nilaiangka" id="nilaiangka" class="form-control" value="{{ $nilai['nilaiangka'] }}" min="0" max="100">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
