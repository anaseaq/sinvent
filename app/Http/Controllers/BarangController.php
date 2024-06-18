<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Barang::with('kategori');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('merk', 'LIKE', "%{$search}%")
                ->orWhere('seri', 'LIKE', "%{$search}%")
                ->orWhere('stok', 'LIKE', "%{$search}%")
                ->orWhere('spesifikasi', 'LIKE', "%{$search}%")
                ->orWhereHas('kategori', function ($q) use ($search) {
                    $q->where('kategori', 'LIKE', "%{$search}%")
                    ->orWhere('jenis', 'LIKE', "%{$search}%");
                });
            });
        }

        $rsetBarang = $query->latest()->paginate(10);

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
            $foto = $request->file('foto');
            $foto->storeAs('public/foto', $foto->hashName());

            Barang::create([
                'merk'          => $request->merk,
                'seri'          => $request->seri,
                'spesifikasi'   => $request->spesifikasi,
                'kategori_id'   => $request->kategori_id,
                'foto'          => $foto->hashName()
            ]);

            DB::commit();
            return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    public function show(string $id)
    {
        $rsetBarang = DB::table('barang')
            ->join('kategori', 'barang.kategori_id', '=', 'kategori.id')
            ->select('barang.*', 'kategori.kategori as kategori_nama')
            ->where('barang.id', $id)
            ->first();

        if (!$rsetBarang) {
            abort(404);
        }

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

        DB::beginTransaction();
        try {
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $foto->storeAs('public/foto', $foto->hashName());

                Storage::delete('public/foto/' . $rsetBarang->foto);

                $rsetBarang->update([
                    'merk'          => $request->merk,
                    'seri'          => $request->seri,
                    'spesifikasi'   => $request->spesifikasi,
                    'kategori_id'   => $request->kategori_id,
                    'foto'          => $foto->hashName()
                ]);
            } else {
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