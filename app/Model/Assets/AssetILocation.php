<?php

namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class AssetILocation extends Model{
    protected $connection= 'astel';
    protected $table ='master_lokasi';
    protected $fillable =[
        'id',
        'nama_lokasi',
        'is_deleted',
        'project_code',
        'created_at',
    ];
}