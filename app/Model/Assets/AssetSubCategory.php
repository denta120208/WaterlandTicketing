<?php

namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class AssetSubCategory extends Model{
    protected $connection= 'astel';
    protected $table ='master_subkategori';
    protected $fillable =[
        'id',
        'nama',
        'kategori_id',
        'is_active',
        'sub_kategori_code',
        'project',
        'created_at',
    ];
}