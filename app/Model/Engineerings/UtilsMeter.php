<?php


namespace App\Model\Engineerings;

use Illuminate\Database\Eloquent\Model;

class UtilsMeter extends Model
{
    protected $table='UTILS_METER';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['ID_METER',
        'UTILS_METER_CHAR',
        'METER_STAND_START_LWBP',
        'METER_STAND_END_LWBP',
        'METER_STAND_START_WBP',
        'METER_STAND_END_WBP',
        'UTILS_METER_STATUS',
        'UTILS_METER_TYPE',
        'UTILS_METER_DT_TYPE',
        'PROJECT_NO_CHAR',
        'UTILS_METER_CAPS',
        'UTILS_METER_MULTIPLIER',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',];
    public function utils_meter_type(){
        return $this->belongsTo('App\Model\Engineerings\UtilsType', 'UTILS_METER_TYPE', 'id');
    }
}
