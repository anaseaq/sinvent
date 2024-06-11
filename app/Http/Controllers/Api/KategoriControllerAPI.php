<?php

namespace App\Http\Controllers\Api;

//import model Post
use App\Models\Kategori;
//import facade Validator
use Illuminate\Support\Facades\Validator;
//import Http request
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//import resource PostResource
use App\Http\Resources\KategoriResource;
use Illuminate\Support\Facades\Storage;

class KategoriControllerAPI extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $kategori = Kategori::latest()->paginate(5);

        //return collection of posts as a resource
        return new KategoriResource(true, 'List Data Posts', $kategori);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'kategori'  => 'required',
            'jenis'     => 'required|in:M,A,BHP,BTHP',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        //create kategori
        $kategori = Kategori::create([
            'kategori' => $request->kategori,
            'jenis' => $request->jenis,
        ]);

        //return response
        return new KategoriResource(true, 'Data Post Berhasil Ditambahkan!', $kategori);
    }
    
    public function show($id)
    {
        //find post by ID
        $kategori = Kategori::findOrFail($id);

        //return single post as a resource
        return new KategoriResource(true, 'Detail Data Post!', $kategori);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kategori'  => 'required',
            'jenis'     => 'required|in:M,A,BHP,BTHP',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $rsetKategori = Kategori::findOrFail($id);
        
        $rsetKategori->update([
            'kategori'  => $request->kategori,
            'jenis'     => $request->jenis,
        ]);

        //return response
        return new KategoriResource(true, 'Data Kategori Berhasil Diubah!', $rsetKategori);
    }
}