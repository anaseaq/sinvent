<?php

namespace App\Http\Controllers;

use App\Models\Barangkeluar;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarangkeluarController extends Controller
{
    public function index(Request $request)
    {
        $rsetBarangkeluar = Barangkeluar::with('barang')->latest()->paginate(10);

        return view('barangkeluar.index', compact('rsetBarangkeluar'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $abarang = Barang::all();
        return view('barangkeluar.create', compact('abarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_keluar'    => 'required',
            'qty_keluar'    => 'required|numeric|min:1',
            'barang_id'     => 'required|not_in:blank',
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::find($request->barang_id);
            $barangMasukT = $barang->barangmasuk()->latest('tgl_masuk')->first();

            $errors = [];

            if ($barangMasukT && $request->tgl_keluar < $barangMasukT->tgl_masuk) {
                $errors['tgl_keluar'] = 'Tanggal barang keluar tidak boleh kurang dari tanggal masuk';
            }

            if ($request->qty_keluar > $barang->stok) {
                $errors['qty_keluar'] = 'Jumlah keluar tidak boleh melebihi stok yang tersedia';
            }

            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }

            $existingEntry = Barangkeluar::where('tgl_keluar', $request->tgl_keluar)
                                        ->where('barang_id', $request->barang_id)
                                        ->lockForUpdate()
                                        ->first();

            if ($existingEntry) {
                $existingEntry->qty_keluar += $request->qty_keluar;
                $existingEntry->save();
            } else {
                Barangkeluar::create([
                    'tgl_keluar'    => $request->tgl_keluar,
                    'qty_keluar'    => $request->qty_keluar,
                    'barang_id'     => $request->barang_id
                ]);
            }

            DB::commit();

            return redirect()->route('barangkeluar.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $rsetBarangkeluar = DB::table('barangkeluar')
            ->join('barang', 'barangkeluar.barang_id', '=', 'barang.id')
            ->select('barangkeluar.*', 'barang.merk as merk', 'barang.seri as seri')
            ->where('barangkeluar.id', $id)
            ->first();

        if (!$rsetBarangkeluar) {
            abort(404);
        }

        return view('barangkeluar.show', compact('rsetBarangkeluar'));
    }

    public function edit(string $id)
    {
        $abarang = Barang::all();
        $rsetBarangkeluar = Barangkeluar::findOrFail($id);
        $selectedBarang = Barang::findOrFail($rsetBarangkeluar->barang_id);

        return view('barangkeluar.edit', compact('rsetBarangkeluar', 'abarang', 'selectedBarang'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tgl_keluar'    => 'required',
            'qty_keluar'    => 'required|numeric|min:1',
            'barang_id'     => 'required|not_in:blank',
        ]);

        try {
            DB::beginTransaction();

            $rsetBarangkeluar = Barangkeluar::findOrFail($id);

            $rsetBarangkeluar->update([
                'tgl_keluar'    => $request->tgl_keluar,
                'qty_keluar'    => $request->qty_keluar,
                'barang_id'     => $request->barang_id
            ]);

            DB::commit();

            return redirect()->route('barangkeluar.index')->with(['success' => 'Data Berhasil Diubah!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        $rsetBarangkeluar = Barangkeluar::findOrFail($id);

        $rsetBarangkeluar->delete();

        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
