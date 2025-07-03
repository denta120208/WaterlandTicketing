<?php


namespace App\Model\Engineerings;

use Illuminate\Database\Eloquent\Model;

class UtilsBilling extends Model
{
    protected $table='UTILS_BILLING';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['ID_BILLING',
        'ID_TENANT',
        'ID_UTILS_TENANT',
        'ID_FORMULA',
        'ID_METER',
        'UTILS_METER_MULTIPLIER',
        'ID_CATEGORY',
        'BILLING_TYPE',
        'INVOICE_UTIL_CHAR',
        'BILLING_DATE',
        'BILLING_METER_START_LWBP',
        'BILLING_METER_END_LWBP',
        'BILLING_METER_LWBP_DIFF',
        'BILLING_AMOUNT_LWBP',
        'BILLING_AMOUNT_RELIABILITY',
        'BILLING_METER_START_WBP',
        'BILLING_METER_END_WBP',
        'BILLING_METER_WBP_DIFF',
        'BILLING_AMOUNT_WBP',
        'BILLING_METER_BILLBOARD_DAY',
        'BILLING_METER_BILLBOARD_HOUR',
        'BILLING_BILLBOARD_NUM',
        'IS_HANDLING',
        'BILLING_HANDLING_FEE_NUM',
        'IS_BPJU',
        'BILLING_BPJU_NUM',
        'IS_LOST_FACTOR',
        'BILLING_LOST_FACTOR_NUM',
        'IS_ADMIN',
        'BILLING_ADMIN_NUM',
        'IS_PPJU',
        'BILLING_PPJU_NUM',
        'BILLING_STATUS',
        'PROJECT_NO_CHAR',
        'FROM_MOBILE',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'];

    public function utils_meter_type(){
        return $this->belongsTo('App\Model\Engineerings\UtilsType', 'BILLING_TYPE', 'id');
    }
}
