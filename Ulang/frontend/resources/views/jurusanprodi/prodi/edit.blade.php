@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Prodi</h1>

    <form action="{{ route('jurusanprodi.prodi.update', $prodi['kodeprodi']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kodeprodi" class="form-label">Kode Prodi</label>
            <input type="text" name="kodeprodi" id="kodeprodi" class="form-control" value="{{ $prodi['kodeprodi'] }}" required>
        </div>

        <div class="mb-3">
            <label for="namaprodi" class="form-label">Nama Prodi</label>
            <input type="text" name="namaprodi" id="namaprodi" class="form-control" value="{{ $prodi['namaprodi'] }}" required>
        </div>

         <div class="mb-3">
            <label for="kodejurusan" class="form-label">Jurusan</label>
            <select class="form-control" id="kodejurusan" name="kodejurusan" required>
                @foreach($jurusan as $kode => $namajurusan)
                    <option value="{{ $kode }}" {{ $prodi['kodejurusan'] == $kode ? 'selected' : '' }}>{{ $namajurusan }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('jurusanprodi.prodi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
