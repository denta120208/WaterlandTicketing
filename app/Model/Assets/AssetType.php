<?php

namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model{
    protected $connection= 'astel';
    protected $table ='master_jenisbarang';
    protected $fillable =[
        'id',
        'sub_kategori_id',
        'nama',
        'is_active',
        'project_code',
        'created_at',
    ];
}