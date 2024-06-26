@extends('layouts.adm-main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-left">
                    <h2>EDIT BARANG KELUAR</h2>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('barangkeluar.update',$rsetBarangkeluar->id) }}" method="POST" enctype="multipart/form-data">                    
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label class="font-weight-bold">TANGGAL KELUAR</label>
                                <input type="date" class="form-control @error('tgl_keluar') is-invalid @enderror" name="tgl_keluar" value="{{ old('tgl_keluar',$rsetBarangkeluar->tgl_keluar) }}" placeholder="Masukkan Tanggal Barang Keluar...">
                           
                                <!-- error message untuk merk -->
                                @error('tgl_keluar')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">JUMLAH KELUAR</label>
                                <input type="text" class="form-control @error('qty_keluar') is-invalid @enderror" name="qty_keluar" value="{{ old('qty_keluar',$rsetBarangkeluar->qty_keluar) }}" placeholder="Masukkan Jumlah Barang Keluar..">
                           
                                <!-- error message untuk seri -->
                                @error('qty_keluar')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">MERK BARANG</label>
                                <select class="form-control" name="barang_id" aria-label="Default select example">
                                    @foreach ($abarang as $barang)
                                        @if ($selectedBarang && $selectedBarang->id == $barang->id)
                                            <option value="{{ $barang->id }}" selected>{{ $barang->merk }} - {{ $barang->seri }}</option>
                                        @else
                                            <option value="{{ $barang->id }}">{{ $barang->merk }} - {{ $barang->seri }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            
                                <!-- error message untuk kategori -->
                                @error('barang_id')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-md btn-primary">SIMPAN</button>
                            <button type="reset" class="btn btn-md btn-warning">RESET</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection