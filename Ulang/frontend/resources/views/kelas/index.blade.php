@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Kelas</h1>

        <a href="{{ route('kelas.create') }}" class="btn btn-success mb-3">+ Kelas</a>

        <style>
            .table-bordered-custom th,
            .table-bordered-custom td {
                text-align: center; /* Isi kolom dan header rata tengah */
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
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>tahunajaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelas as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $k['nim'] }}</td>
                        <td>{{ $k['kodekelas'] }}</td>
                        <td>{{ $k['semester'] }}</td>
                        <td>{{ $k['tahunajaran'] }}</td>
                        <td>
                            <a href="{{ route('kelas.edit', [$k['nim'], $k['kodekelas'], $k['semester']]) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('kelas.destroy', [$k['nim'], $k['kodekelas'], $k['semester']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Data kelas belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
