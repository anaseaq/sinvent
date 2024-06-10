<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang'; // Sesuaikan dengan nama tabel yang benar

    protected $fillable = ['merk', 'seri', 'spesifikasi', 'stok', 'kategori_id', 'foto'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    // Uncomment jika diperlukan
    public function barangkeluar()
    {
        return $this->hasMany(Barangkeluar::class);
    }

    public function barangmasuk()
    {
        return $this->hasMany(Barangmasuk::class);
    }
}
