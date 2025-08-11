@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Nilai</h1>

        <a href="/nilai/create" class="btn btn-success mb-3">+ Nilai</a>
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
                    <th>Matakuliah</th>
                    <th>Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nilai as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $m['nim'] }}</td>
                        <td>{{ $m['kodematkul'] }} - {{ $m['namamatkul'] }}</td>
                        <td>{{ $m['nilaiangka'] }}</td>
                        <td>
                        <a href="{{ route('nilai.edit', ['nim' => $m['nim'], 'kodematkul' => $m['kodematkul']]) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('nilai.destroy', ['nim' => $m['nim'], 'kodematkul' => $m['kodematkul']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
