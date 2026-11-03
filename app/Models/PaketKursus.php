<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketKursus extends Model
{
    protected $table = 'paket_kursus';
    protected $primaryKey = 'id_paket';
    public $timestamps = false;

    protected $fillable = ['nama_paket', 'transmisi', 'jumlah_sesi', 'harga', 'status_aktif'];

    protected $casts = ['status_aktif' => 'boolean'];
}
