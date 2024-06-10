<?php

namespace App\Http\Controllers;

use App\Models\Barangmasuk;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangmasukController extends Controller
{
    public function index(Request $request)
    {
        $rsetBarangmasuk = Barangmasuk::with('barang')->latest()->paginate(10);

        return view('barangmasuk.index', compact('rsetBarangmasuk'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $abarang = Barang::all();
        return view('barangmasuk.create', compact('abarang'));
    }

    public function store(Request $request)
    {
        // validate form
        $request->validate([
            'tgl_masuk'    => 'required',
            'qty_masuk'    => 'required|numeric|min:1',
            'barang_id'    => 'required|not_in:blank',
        ]);

        $existingEntry = Barangmasuk::where('tgl_masuk', $request->tgl_masuk)
                                    ->where('barang_id', $request->barang_id)
                                    ->first();

            if ($existingEntry) {
                // Jika entri ada, tambahkan qty_masuk
                $existingEntry->qty_masuk += $request->qty_masuk;
                $existingEntry->save();
            } else {
                // Jika tidak ada, buat entri baru
                Barangmasuk::create([
                'tgl_masuk'    => $request->tgl_masuk,
                'qty_masuk'    => $request->qty_masuk,
                'barang_id'    => $request->barang_id
                ]);
            }


        // redirect to index
        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id)
    {
        $rsetBarangmasuk = Barangmasuk::findOrFail($id);

        // return view
        return view('barangmasuk.show', compact('rsetBarangmasuk'));
    }

    public function edit(string $id)
    {
        $abarang = Barang::all();
        $rsetBarangmasuk = Barangmasuk::findOrFail($id);
        $selectedBarang = Barang::findOrFail($rsetBarangmasuk->barang_id);

        return view('barangmasuk.edit', compact('rsetBarangmasuk', 'abarang', 'selectedBarang'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tgl_masuk'    => 'required',
            'qty_masuk'    => 'required|numeric|min:1',
            'barang_id'    => 'required|not_in:blank',
        ]);

        $rsetBarangmasuk = Barangmasuk::findOrFail($id);

        $rsetBarangmasuk->update([
            'tgl_masuk'    => $request->tgl_masuk,
            'qty_masuk'    => $request->qty_masuk,
            'barang_id'    => $request->barang_id
        ]);

        // Redirect to the index page with a success message
        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(string $id)
    {
        $rsetBarangmasuk = Barangmasuk::findOrFail($id);

        // delete post
        $rsetBarangmasuk->delete();

        // redirect to index
        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
