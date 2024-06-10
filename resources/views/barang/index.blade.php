@extends('layouts.adm-main')


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
		<div class="pull-left">
		    <h2>DAFTAR BARANG</h2>
		</div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @elseif ($message = Session::get('gagal'))
            <div class="alert alert-warning">
                <p>{{ $message }}</p>
            </div>
        @endif
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('barang.create') }}" class="btn btn-md btn-success mb-3">TAMBAH ITEM</a>
                    </div>

                </div>


                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>MERK</th>
                            <th>SERI</th>
                            <th>SPESIFIKASI</th>
                            <th>STOK</th>
                            <th>DESKRIPSI</th>
                            <th>KATEGORI</th>
                            <th>FOTO</th>
                            <th style="width: 15%">AKSI</th>


                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rsetBarang as $rowbarang)
                            <tr>
                                <td>{{ $rowbarang->id }}</td>
                                <td>{{ $rowbarang->merk  }}</td>
                                <td>{{ $rowbarang->seri  }}</td>
                                <td>{{ $rowbarang->spesifikasi  }}</td>
                                <td>{{ $rowbarang->stok  }}</td>
                                <td>{{ $rowbarang->kategori->kategori  }}</td>
                                <td>{{ $rowbarang->kategori->jenis  }}</td>
                                <td class="text-center">
                                    <img src="{{ asset('storage/foto/'.$rowbarang->foto) }}" class="rounded" style="width: 150px">
                                </td>
                                <td class="text-center">
                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('barang.destroy', $rowbarang->id) }}" method="POST">
                                        <a href="{{ route('barang.show', $rowbarang->id) }}" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('barang.edit', $rowbarang->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <div class="alert">
                                Data barang belum tersedia!
                            </div>
                        @endforelse
                    </tbody>
                   
                </table>
                {!! $rsetBarang->links('pagination::bootstrap-5') !!}

            </div>
        </div>
    </div>
@endsection