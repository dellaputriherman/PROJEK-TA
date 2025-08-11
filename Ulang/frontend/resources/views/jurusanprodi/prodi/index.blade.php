@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Daftar Prodi</h1>

    <a href="{{ route('jurusanprodi.prodi.create') }}" class="btn btn-success mb-3">+ Prodi</a>
     <style>
        .table-bordered-custom th,
        .table-bordered-custom td {
            text-align: center; /* Ini membuat isi kolom dan header rata tengah */
            vertical-align: middle;
        }

        .table-dark th {
            background-color: rgb(33, 31, 31) !important; /* Kolom header hitam */
            color: white;
            text-align: center; /* Header teks rata tengah */
        }
    </style>

   <table class="table table-bordered table-striped table-bordered-custom">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode prodi</th>
                <th>Nama Prodi</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($prodi as $index => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['kodeprodi'] }}</td>
                    <td>{{ $item['namaprodi'] }}</td>
                    <td>{{ $item['kodejurusan'] }}</td>
                    <td>
                        <a href="{{ route('jurusanprodi.prodi.edit', $item['kodeprodi']) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('jurusanprodi.prodi.destroy', $item['kodeprodi']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada data prodi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
