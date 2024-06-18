<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\LoginController;
use \App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('/products', \App\Http\Controllers\ProductController::class)->middleware('auth');
Route::resource('/barang', \App\Http\Controllers\BarangController::class)->middleware('auth');
Route::resource('/kategori', \App\Http\Controllers\KategoriController::class)->middleware('auth');
Route::resource('/barangmasuk', \App\Http\Controllers\BarangmasukController::class)->middleware('auth');
Route::resource('/barangkeluar', \App\Http\Controllers\BarangkeluarController::class)->middleware('auth');

Route::get('login', [LoginController::class,'index'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class,'authenticate']);

Route::get('logout', [LoginController::class,'logout']);
Route::post('logout', [LoginController::class,'logout']);

Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store']);