@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Matakuliah</h1>

        <a href="/matakuliah/create" class="btn btn-success mb-3">+ Matakuliah</a>
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
                    <th>Kode Matakuliah</th>
                    <th>Nama Matakuliah</th>
                    <th>Semester</th>
                    <th>SKS</th>
                    <th>Jam</th>
                    <th>Prodi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($matakuliah as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $m['kodematkul'] }}</td>
                        <td>{{ $m['namamatkul'] }}</td>
                        <td>{{ $m['semester'] }}</td>
                        <td>{{ $m['sks'] }}</td>
                        <td>{{ $m['jam'] }}</td>
                        <td>{{ $m['kodeprodi'] }} - {{ $m['namaprodi'] }}</td>
                        <td>
                        <a href="{{ route('matakuliah.edit', $m['kodematkul']) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('matakuliah.destroy', $m['kodematkul']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
