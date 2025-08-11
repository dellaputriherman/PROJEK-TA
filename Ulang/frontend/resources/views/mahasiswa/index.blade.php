@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Mahasiswa</h1>

        <a href="/mahasiswa/create" class="btn btn-success mb-3">+ Mahasiswa</a>
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
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Jurusan</th>
                    <th>Prodi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mahasiswa as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $m['nim'] }}</td>
                        <td>{{ $m['nama'] }}</td>
                        <td>{{ $m['tempatlahir'] }}</td>
                        <td>{{ $m['tanggallahir'] }}</td>
                        <td>{{ $m['jeniskelamin'] }}</td>
                        <td>{{ $m['kodejurusan'] }} - {{ $m['namajurusan'] }}</td>
                        <td>{{ $m['kodeprodi'] }} - {{ $m['namaprodi'] }}</td>
                           <td>
                        <a href="{{ route('mahasiswa.edit', $m['nim']) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('mahasiswa.destroy', $m['nim']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
