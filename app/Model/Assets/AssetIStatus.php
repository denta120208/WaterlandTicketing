<?php

namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class AssetIStatus extends Model{
    protected $connection= 'astel';
    protected $table ='status_item';
    protected $fillable =[
        'id',
        'name',
        'is_active',
        'need_approval'
    ];
}