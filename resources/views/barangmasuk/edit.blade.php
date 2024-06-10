@extends('layouts.adm-main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-left">
                    <h2>EDIT BARANG MASUK</h2>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('barangmasuk.update',$rsetBarangmasuk->id) }}" method="POST" enctype="multipart/form-data">                    
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label class="font-weight-bold">TANGGAL MASUK</label>
                                <input type="date" class="form-control @error('tgl_masuk') is-invalid @enderror" name="tgl_masuk" value="{{ old('tgl_masuk',$rsetBarangmasuk->tgl_masuk) }}" placeholder="Masukkan Tanggal Barang masuk...">
                           
                                <!-- error message untuk merk -->
                                @error('tgl_masuk')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">JUMLAH MASUK</label>
                                <input type="text" class="form-control @error('qty_masuk') is-invalid @enderror" name="qty_masuk" min=1 value="{{ old('qty_masuk',$rsetBarangmasuk->qty_masuk) }}" placeholder="Masukkan Jumlah Barang masuk..">
                           
                                <!-- error message untuk seri -->
                                @error('qty_masuk')
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