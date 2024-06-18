<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Kategori::select('id', 'kategori', 'jenis', 
            \DB::raw('(CASE
                WHEN jenis = "M" THEN "Modal"
                WHEN jenis = "A" THEN "Alat"
                WHEN jenis = "BHP" THEN "Bahan Habis Pakai"
                ELSE "Bahan Tidak Habis Pakai"
                END) AS ketKategori'));

        if ($search) {
            $query->where('kategori', 'LIKE', "%{$search}%")
                ->orWhere('jenis', 'LIKE', "%{$search}%");
        }

        $rsetKategori = $query->paginate(10);

        return view('kategori.index', compact('rsetKategori'));
    }

    public function create()
    {
        $aKategori = [
            'blank' => 'Pilih Kategori',
            'M' => 'Barang Modal',
            'A' => 'Alat',
            'BHP' => 'Bahan Habis Pakai',
            'BTHP' => 'Bahan Tidak Habis Pakai'
        ];

        return view('kategori.create', compact('aKategori'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required',
            'jenis' => 'required|in:M,A,BHP,BTHP',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        DB::beginTransaction();
        try {
            Kategori::create([
                'kategori' => $request->kategori,
                'jenis' => $request->jenis,
            ]);
            DB::commit();
            return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => 'Data Gagal Disimpan!'])->withInput();
        }
    }

    public function show($id)
    {
        $rsetKategori = Kategori::findOrFail($id);
        return view('kategori.show', compact('rsetKategori'));
    }

    public function edit($id)
    {
        $aKategori = [
            'blank' => 'Pilih Kategori',
            'M' => 'Barang Modal',
            'A' => 'Alat',
            'BHP' => 'Bahan Habis Pakai',
            'BTHP' => 'Bahan Tidak Habis Pakai'
        ];

        $rsetKategori = Kategori::findOrFail($id);

        return view('kategori.edit', compact('rsetKategori', 'aKategori'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kategori'  => 'required',
            'jenis'     => 'required|in:M,A,BHP,BTHP',
        ]);
        
        $rsetKategori = Kategori::findOrFail($id);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        DB::beginTransaction();
        try {
            $rsetKategori->update([
                'kategori'  => $request->kategori,
                'jenis'     => $request->jenis,
            ]);
            DB::commit();
            return redirect()->route('kategori.index')->with(['success' => 'Data berhasil diperbarui!']);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => 'Data Gagal Diperbarui!'])->withInput();
        }
    }

    public function destroy(string $id)
    {
        $existsInBarang = DB::table('barang')
                    ->where('kategori_id', $id)
                    ->exists();

        if ($existsInBarang) {
            return redirect()->route('kategori.index')->with(['gagal' => 'Data Gagal Dihapus Karena Kategori digunakan']);
        } else {
            $deleted = DB::table('kategori')
                        ->where('id', $id)
                        ->delete();

            if ($deleted) {
                return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);
            } else {
                return redirect()->route('kategori.index')->with(['gagal' => 'Data Gagal Dihapus!']);
            }
        }
    }
    
    private function normalizeIds() 
    {
        $kategoris = Kategori::all();
        $counter = 1;

        foreach ($kategoris as $kategori) {
            $kategori->id = $counter;
            $kategori->save();
            $counter++;
        }

        DB::statement('ALTER TABLE kategori AUTO_INCREMENT = ' . ($counter) . ';');
    }
}
