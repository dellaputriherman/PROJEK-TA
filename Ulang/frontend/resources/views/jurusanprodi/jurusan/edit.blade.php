@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Jurusan</h1>

    <form action="{{ route('jurusanprodi.jurusan.update', $jurusan['kodejurusan']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kodejurusan" class="form-label">Kode Jurusan</label>
            <input type="text" class="form-control" id="kodejurusan" name="kodejurusan" value="{{ $jurusan['kodejurusan'] }}" required>
        </div>

        <div class="mb-3">
            <label for="namajurusan" class="form-label">Nama Jurusan</label>
            <input type="text" class="form-control" id="namajurusan" name="namajurusan" value="{{ $jurusan['namajurusan'] }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('jurusanprodi.jurusan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
