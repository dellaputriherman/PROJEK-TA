@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Kelas Master</h1>

        <a href="{{ route('kelasmaster.create') }}" class="btn btn-success mb-3">+ Kelas Master</a>

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
                    <th>Kode Kelas</th>
                    <th>Nama Kelas</th>
                    <th>Jurusan</th>
                    <th>Prodi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelasmaster as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $k['kodekelas'] }}</td>
                        <td>{{ $k['namakelas'] }}</td>
                        <td>{{ $k['kodejurusan'] }} - {{ $k['namajurusan'] }}</td>
                        <td>{{ $k['kodeprodi'] }} - {{ $k['namaprodi'] }}</td>
                        <td>
                            <a href="{{ route('kelasmaster.edit', $k['kodekelas']) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('kelasmaster.destroy', $k['kodekelas']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Data kelas master belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
