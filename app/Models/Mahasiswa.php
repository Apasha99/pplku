<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mahasiswa extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'nim';
    protected $table = 'mahasiswa';
    protected $fillable = [
        'nama',
        'nim',
        'angkatan',
        'status',
        'nip',
        'alamat',
        'kabkota',
        'provinsi',
        'username',
        'noHandphone',
        'iduser',
        'jalur_masuk',
        'cekPKL',
        'cekSkripsi'
    ];

    public function irs()
    {
        return $this->hasMany(IRS::class, 'nim', 'nim');
    }

    public function pkl(){
        return $this->hasOne(PKL::class, 'nim', 'nim');
    }
    
    public function khs()
    {
        return $this->hasMany(KHS::class, 'nim', 'nim');
    }

    public function skripsi(){
        return $this->hasOne(Skripsi::class, 'nim', 'nim');
    }
    
    public function getImageURL(){
        if($this->foto){
            return url("storage/" . $this->foto);
        }
        return "https://api.dicebear.com/6.x/fun-emoji/svg?seed=($this=>name)";
    }

}

