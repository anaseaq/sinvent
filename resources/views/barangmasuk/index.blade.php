@extends('layouts.adm-main')


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
		<div class="pull-left">
		    <h2>DAFTAR BARANG MASUK</h2>
		</div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('barangmasuk.create') }}" class="btn btn-md btn-success mb-3">TAMBAH BARANG MASUK</a>
                    </div>

                </div>


                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>TANGGAL MASUK</th>
                            <th>JUMLAH MASUK</th>
                            <th>MERK BARANG</th>
                            <th>SERI BARANG</th>
                            <th style="width: 15%">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rsetBarangmasuk as $rowbarangmasuk)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $rowbarangmasuk->tgl_masuk  }}</td>
                                <td>{{ $rowbarangmasuk->qty_masuk  }}</td>
                                <td>{{ $rowbarangmasuk->barang->merk  }}</td>
                                <td>{{ $rowbarangmasuk->barang->seri  }}</td>
                                <td class="text-center">
                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('barangmasuk.destroy', $rowbarangmasuk->id) }}" method="POST">
                                        <a href="{{ route('barangmasuk.show', $rowbarangmasuk->id) }}" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('barangmasuk.edit', $rowbarangmasuk->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <div class="alert">
                                Data barang masuk belum tersedia!
                            </div>
                        @endforelse
                    </tbody>
                   
                </table>
                {!! $rsetBarangmasuk->links('pagination::bootstrap-5') !!}

            </div>
        </div>
    </div>
@endsection