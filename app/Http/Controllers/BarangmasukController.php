<?php

namespace App\Http\Controllers;

use App\Models\Barangmasuk;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BarangmasukController extends Controller
{
    public function index(Request $request)
    {
        $rsetBarangmasuk = Barangmasuk::with('barang')->latest()->paginate(10);

        return view('barangmasuk.index', compact('rsetBarangmasuk'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $abarang = Barang::all();
        return view('barangmasuk.create', compact('abarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_masuk'    => 'required',
            'qty_masuk'    => 'required|numeric|min:1',
            'barang_id'    => 'required|not_in:blank',
        ]);

        try {
            DB::beginTransaction();

            $existingEntry = Barangmasuk::where('tgl_masuk', $request->tgl_masuk)
                                        ->where('barang_id', $request->barang_id)
                                        ->lockForUpdate()
                                        ->first();

            if ($existingEntry) {
                $existingEntry->qty_masuk += $request->qty_masuk;
                $existingEntry->save();
            } else {
                Barangmasuk::create([
                    'tgl_masuk'    => $request->tgl_masuk,
                    'qty_masuk'    => $request->qty_masuk,
                    'barang_id'    => $request->barang_id
                ]);
            }

            DB::commit();

            return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $rsetBarangmasuk = DB::table('barangmasuk')
            ->join('barang', 'barangmasuk.barang_id', '=', 'barang.id')
            ->select('barangmasuk.*', 'barang.merk as merk', 'barang.seri as seri')
            ->where('barangmasuk.id', $id)
            ->first();

        if (!$rsetBarangmasuk) {
            abort(404); 
        }

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

        try {
            DB::beginTransaction();

            $rsetBarangmasuk = Barangmasuk::findOrFail($id);

            $rsetBarangmasuk->update([
                'tgl_masuk'    => $request->tgl_masuk,
                'qty_masuk'    => $request->qty_masuk,
                'barang_id'    => $request->barang_id
            ]);

            DB::commit();

            return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Diubah!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $rsetBarangmasuk = Barangmasuk::findOrFail($id);
            $rsetBarangmasuk->delete();

            DB::commit();

            return redirect()->route('barangmasuk.index')->with(['success' => 'Data Berhasil Dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
