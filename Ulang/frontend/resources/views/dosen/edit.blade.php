@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Edit Dosen</h1>

        <form action="{{ route('dosen.update', $dosen['nip']) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nip" class="form-label">NIP</label>
                <input type="text" name="nip" id="nip" class="form-control" value="{{ $dosen['nip'] }}" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ $dosen['nama'] }}" required>
            </div>

             <div class="mb-3">
            <label for="kodejurusan" class="form-label">Jurusan</label>
            <select class="form-control" id="kodejurusan" name="kodejurusan" required>
                @foreach($jurusan as $kode => $namajurusan)
                    <option value="{{ $kode }}" {{ $dosen['kodejurusan'] == $kode ? 'selected' : '' }}>{{ $namajurusan }}</option>
                @endforeach
            </select>
        </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('dosen.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
