<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;

    protected $fillable = ['username', 'password_hash', 'role', 'status_aktif'];
    protected $hidden = ['password_hash'];

    // Kolom password pada database Modul 5 bernama password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Fitur "remember me" tidak dipakai (tabel tidak punya kolom remember_token)
    public function getRememberTokenName()
    {
        return '';
    }
}
