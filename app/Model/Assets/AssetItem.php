<?php

namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class AssetItem extends Model{
    protected $connection= 'astel';
    protected $table ='master_item';
    protected $fillable =[
        'nama_item',
        'room_id',
        'room_number',
        'barcode_char',
        'status_item',
        'nama_toko',
        'description',
        'brand',
        'type_jenis_item',
        'type_jenis_itemno',
        'tgl_beli',
        'tgl_garansi_akhir',
        'is_deleted',
        'project_code',
        'created_at',
        'update_at',
        'created_by',
        'updated_by'
    ];
}