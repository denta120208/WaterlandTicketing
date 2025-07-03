<?php


namespace App\Model\Engineerings;

use Illuminate\Database\Eloquent\Model;

class UtilsTenant extends Model
{
    protected $table='UTILS_TENANTS';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['ID_UTILS_TENANT',
        'PSM_TRANS_NOCHAR',
        'ID_TENANT',
        'ID_FORMULA',
        'ID_METER',
        'METER_STAND_START_LWBP',
        'METER_STAND_END_LWBP',
        'METER_STAND_START_WBP',
        'METER_STAND_END_WBP',
        'TENANT_STATUS',
        'METER_TYPE',
        'PROJECT_NO_CHAR',
        'created_at',
        'created_by'
    ];
    public function utils_meter_type(){
        return $this->belongsTo('App\Model\Engineerings\UtilsType', 'METER_TYPE', 'id');
    }
    public function tenant_name(){
        return $this->belongsTo('App\Model\Master\Tenant', 'ID_TENANT', 'MD_TENANT_ID_INT');
    }
    public function meter_name(){
        return $this->belongsTo('App\Model\Engineerings\UtilsMeter', 'ID_METER', 'ID_METER');
    }
    public function form_name(){
        return $this->belongsTo('App\Model\Engineerings\UtilsFormula', 'ID_FORMULA', 'ID_U_FORMULA');
    }
//    public function tenant_name(){
//        return $this->belongsTo('App\Model\Master\Tenant', 'ID_TENANT', 'MD_TENANT_ID_INT');
//    }
}
