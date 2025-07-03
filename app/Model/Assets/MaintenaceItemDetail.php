<?php


namespace App\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class MaintenaceItemDetail extends Model
{
    protected $table='MT_ITEMS_DTL';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['id',
        'ID_MT',
        'PART_NAME',
        'PART_VENDOR',
        'PART_SPK',
        'PROJECT_NO_CHAR',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',];
    public function mt_parent(){
        return $this->belongsTo('App\Model\Assets\MaintenanceItem', 'id', 'ID_MT');
    }
}
