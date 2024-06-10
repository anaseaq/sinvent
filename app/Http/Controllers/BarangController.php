<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class BarangController extends Controller
{
    // No need to explicitly use ValidatesRequests, it's already in the base Controller

    public function index(Request $request)
    {
        $rsetBarang = Barang::with('kategori')->latest()->paginate(10);

        return view('barang.index', compact('rsetBarang'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $akategori = Kategori::all();
        return view('barang.create', compact('akategori'));
    }

    public function store(Request $request)
    {
        // validate form
        $request->validate([
            'merk'          => 'required',
            'seri'          => 'required',
            'spesifikasi'   => 'required',
            'kategori_id'   => 'required|not_in:blank',
            'foto'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // upload image
        $foto = $request->file('foto');
        $foto->storeAs('public/foto', $foto->hashName());

        // create post
        Barang::create([
            'merk'          => $request->merk,
            'seri'          => $request->seri,
            'spesifikasi'   => $request->spesifikasi,
            'kategori_id'   => $request->kategori_id,
            'foto'          => $foto->hashName()
        ]);

        // redirect to index
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id)
    {
        $rsetBarang = Barang::findOrFail($id);

        return view('barang.show', compact('rsetBarang'));
    }

    public function edit(string $id)
    {
        $akategori = Kategori::all();
        $rsetBarang = Barang::findOrFail($id);
        $selectedKategori = Kategori::findOrFail($rsetBarang->kategori_id);

        return view('barang.edit', compact('rsetBarang', 'akategori', 'selectedKategori'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'merk'          => 'required',
            'seri'          => 'required',
            'spesifikasi'   => 'required',
            'kategori_id'   => 'required|not_in:blank',
            'foto'          => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $rsetBarang = Barang::findOrFail($id);

        if ($request->hasFile('foto')) {
            // upload new image
            $foto = $request->file('foto');
            $foto->storeAs('public/foto', $foto->hashName());

            // delete old image
            Storage::delete('public/foto/' . $rsetBarang->foto);

            // update post with new image
            $rsetBarang->update([
                'merk'          => $request->merk,
                'seri'          => $request->seri,
                'spesifikasi'   => $request->spesifikasi,
                'kategori_id'   => $request->kategori_id,
                'foto'          => $foto->hashName()
            ]);
        } else {
            // update post without image
            $rsetBarang->update([
                'merk'          => $request->merk,
                'seri'          => $request->seri,
                'spesifikasi'   => $request->spesifikasi,
                'kategori_id'   => $request->kategori_id,
            ]);
        }

        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(string $id)
    {
        
        if (DB::table('barangmasuk')->where('barang_id', $id)->exists() ||
            DB::table('barangkeluar')->where('barang_id', $id)->exists()){
            return redirect()->route('barang.index')->with(['gagal' => 'Data Gagal Dihapus karena barang terdapat pada barangmasuk/barangkeluar']);
        } else {
            $rsetBarang = Barang::find($id);
            $rsetBarang->delete();
            return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }

    }
}
