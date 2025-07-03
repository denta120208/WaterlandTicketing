<?php

namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model{
    protected $connection= 'astel';
    protected $table ='master_kategori';
    protected $fillable =[
        'id',
        'nama',
        'is_active',
        'counter',
        'kategori_code',
        'created_at'
    ];
}