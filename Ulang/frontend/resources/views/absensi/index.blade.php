@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Daftar Absensi</h1>

        <a href="/absensi/create" class="btn btn-success mb-3">+ Absensi</a>
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
                    <th>Kelas</th>
                    <th>Matakuliah</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($absensi as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $m['nim'] }}</td>
                        <td>{{ $m['kodekelas'] }}</td>
                        <td>{{ $m['kodematkul'] }} - {{ $m['namamatkul'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($m['tanggal'])->format('Y-m-d') }}</td>
                        <td>{{ $m['status'] }}</td>
                        <td>
                          <a href="{{ route('absensi.edit', ['nim' => $m['nim'], 'kodematkul' => $m['kodematkul'], 'tanggal' => \Carbon\Carbon::parse($m['tanggal'])->format('Y-m-d')]) }}" class="btn btn-warning">Edit</a>
                          <form action="{{ route('absensi.destroy', ['nim' => $m['nim'], 'kodematkul' => $m['kodematkul'], 'tanggal' => \Carbon\Carbon::parse($m['tanggal'])->format('Y-m-d')]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
