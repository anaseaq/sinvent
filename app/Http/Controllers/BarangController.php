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
        // Menggunakan Eloquent untuk mengambil data dengan relasi dan pagination
        $rsetBarang = Barang::with('kategori')->latest()->paginate(10);

        return view('barang.index', compact('rsetBarang'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        // Menggunakan Eloquent untuk mengambil semua kategori
        $akategori = Kategori::all();
        return view('barang.create', compact('akategori'));
    }

    public function store(Request $request)
    {
        // Validate form
        $request->validate([
            'merk'          => 'required',
            'seri'          => 'required',
            'spesifikasi'   => 'required',
            'kategori_id'   => 'required|not_in:blank',
            'foto'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Menggunakan transaksi untuk memastikan semua operasi berhasil atau tidak sama sekali
        DB::beginTransaction();
        try {
            // Upload image
            $foto = $request->file('foto');
            $foto->storeAs('public/foto', $foto->hashName());

            // Create barang menggunakan Eloquent
            Barang::create([
                'merk'          => $request->merk,
                'seri'          => $request->seri,
                'spesifikasi'   => $request->spesifikasi,
                'kategori_id'   => $request->kategori_id,
                'foto'          => $foto->hashName()
            ]);

            DB::commit();
            // Redirect to index
            return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the error
            return redirect()->back()->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    public function show(string $id)
    {
        // Menggunakan Query Builder untuk mengambil satu barang
        $rsetBarang = DB::table('barangs')->where('id', $id)->first();

        return view('barang.show', compact('rsetBarang'));
    }

    public function edit(string $id)
    {
        // Menggunakan Eloquent untuk mengambil semua kategori dan barang berdasarkan ID
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

        // Menggunakan Eloquent untuk mencari barang berdasarkan ID
        $rsetBarang = Barang::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($request->hasFile('foto')) {
                // Upload new image
                $foto = $request->file('foto');
                $foto->storeAs('public/foto', $foto->hashName());

                // Delete old image
                Storage::delete('public/foto/' . $rsetBarang->foto);

                // Update barang dengan gambar baru
                $rsetBarang->update([
                    'merk'          => $request->merk,
                    'seri'          => $request->seri,
                    'spesifikasi'   => $request->spesifikasi,
                    'kategori_id'   => $request->kategori_id,
                    'foto'          => $foto->hashName()
                ]);
            } else {
                // Update barang tanpa gambar baru
                $rsetBarang->update([
                    'merk'          => $request->merk,
                    'seri'          => $request->seri,
                    'spesifikasi'   => $request->spesifikasi,
                    'kategori_id'   => $request->kategori_id,
                ]);
            }

            DB::commit();
            return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Diubah!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['error' => 'Data Gagal Diubah!']);
        }
    }

    public function destroy(string $id)
    {
        // Menggunakan Query Builder untuk mengecek keberadaan barang di tabel barangmasuk dan barangkeluar
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