<?php

namespace App\Http\Controllers;
use App\Models\Barangkeluar;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangkeluarController extends Controller
{
    public function index(Request $request)
    {
        $rsetBarangkeluar = Barangkeluar::with('barang')->latest()->paginate(10);

        return view('barangkeluar.index', compact('rsetBarangkeluar'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $abarang = Barang::all();
        return view('barangkeluar.create',compact('abarang'));
    }

    public function store(Request $request)
    {
        //return $request;
        //validate form
        $request->validate([
            'tgl_keluar'    => 'required',
            'qty_keluar'    => 'required|numeric|min:1',
            'barang_id'     => 'required|not_in:blank',
        ]);

        $barang = Barang::find($request->barang_id);

        $barangMasukT = $barang->barangmasuk()->latest('tgl_masuk')->first();
        
        $errors = [];

        // Validasi tambahan
        if ($barangMasukT && $request->tgl_keluar < $barangMasukT->tgl_masuk) {
            $errors['tgl_keluar'] = 'Tanggal barang keluar tidak boleh kurang dari tanggal masuk';
        }
    
        if ($request->qty_keluar > $barang->stok) {
            $errors['qty_keluar'] = 'Jumlah keluar tidak boleh melebihi stok yang tersedia';
        }
    
        // Jika ada error, kembalikan dengan pesan error
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        //create post
        Barangkeluar::create([
            'tgl_keluar'    => $request->tgl_keluar,
            'qty_keluar'    => $request->qty_keluar,
            'barang_id'     => $request->barang_id
        ]);

        //redirect to index
        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetBarangkeluar = Barangkeluar::find($id);

        //return $rsetBarang;

        //return view
        return view('barangkeluar.show', compact('rsetBarangkeluar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    $abarang = Barang::all();
    $rsetBarangkeluar = Barangkeluar::find($id);
    $selectedBarang = Barang::find($rsetBarangkeluar->barang_id);

    return view('barangkeluar.edit', compact('rsetBarangkeluar', 'abarang', 'selectedBarang'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tgl_keluar'    => 'required',
            'qty_keluar'    => 'required|numeric|min:1',
            'barang_id'     => 'required|not_in:blank',
        ]);

        $rsetBarangkeluar = Barangkeluar::find($id);

        $rsetBarangkeluar->update([
            'tgl_keluar'    => $request->tgl_keluar,
            'qty_keluar'    => $request->qty_keluar,
            'barang_id'     => $request->barang_id
        ]);

        // Redirect to the index page with a success message
        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Berhasil Diubah!']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rsetBarangkeluar = Barangkeluar::find($id);

        //delete post
        $rsetBarangkeluar->delete();

        //redirect to index
        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}