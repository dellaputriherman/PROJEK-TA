@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Jadwal Kuliah</h1>

        <a href="{{ route('jadwalkuliah.create') }}" class="btn btn-success mb-3">+ Jadwal Kuliah</a>

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
                    <th>Kode Matkul</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Ruangan</th>
                    <th>Dosen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jadwalkuliah as $j)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $j['kodekelas'] }}</td>
                        <td>{{ $j['kodematkul'] }} - {{ $j['namamatkul'] }}</td>
                        <td>{{ $j['hari'] }}</td>
                        <td>{{ $j['jammulai'] }}</td>
                        <td>{{ $j['jamselesai'] }}</td>
                        <td>{{ $j['ruangan'] ?? '-' }}</td>
                        <td>{{ $j['nip'] }} - {{ $j['namadosen'] }}</td>
                        <td>
                            <a href="{{ route('jadwalkuliah.edit', [$j['kodekelas'], $j['kodematkul']]) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('jadwalkuliah.destroy', [$j['kodekelas'], $j['kodematkul']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Data jadwal kuliah belum tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
