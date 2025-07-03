<?php


namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class MaintenaceItem extends Model
{
    protected $table='MT_ITEMS';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['id',
        'ID_ITEM',
        'BARCODE_CHAR',
        'MT_STATUS',
        'MT_DATE',
        'MT_NOTES',
        'TECH_NAME',
        'TECH_NOTE',
        'MT_FINISH_DATE',
        'PROJECT_NO_CHAR',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',];
    public function mt_detail(){
        return $this->belongsTo('App\Model\Assets\MaintenanceItemDetail', 'ID_MT', 'id');
    }
    public function item_asset(){
        return $this->belongsTo('App\Model\Assets\AssetItem', 'ID_ITEM', 'id');
    }
    public function item_status(){
        return $this->belongsTo('App\Model\Assets\AssetStatus', 'MT_STATUS', 'id');
    }
}
