@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Daftar Jurusan</h1>

    <a href="{{ route('jurusanprodi.jurusan.create') }}" class="btn btn-success mb-3">+ Jurusan</a>
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
                <th>Kode Jurusan</th>
                <th>Nama Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jurusan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['kodejurusan'] }}</td>
                    <td>{{ $item['namajurusan'] }}</td>
                    <td>
                        <a href="{{ route('jurusanprodi.jurusan.edit', $item['kodejurusan']) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('jurusanprodi.jurusan.destroy', $item['kodejurusan']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada data jurusan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
