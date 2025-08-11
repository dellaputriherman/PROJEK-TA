@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Dosen</h1>

        <a href="/dosen/create" class="btn btn-success mb-3">+ Dosen</a>
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
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jurusan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dosen as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $m['nip'] }}</td>
                        <td>{{ $m['nama'] }}</td>
                        <td>{{ $m['kodejurusan'] }} - {{ $m['namajurusan'] }}</td>
                        <td>
                        <a href="{{ route('dosen.edit', $m['nip']) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('dosen.destroy', $m['nip']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
