<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OptTrans extends Model
{
    protected $table = 'OPT_TRANS';
    protected $primaryKey = 'OPT_TRANS_ID_INT';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable =[
        'OPT_TRANS_NOCHAR',
        'ID_UNIT_MPS',
        'BOOKING_ID_INT_MPS',
        'NOUNIT_CHAR',
        'UNIT_LB',
        'CUSTOMER_ID_INT',
        'NAME_CHAR',
        'HANDOVER_DATE',
        'TGL_INV_DATE',
        'PROJECT_NO_CHAR',
        'ID_TOWER_INT',
        'OPT_TRANS_STATUS_INT',
        'OPT_TRANS_REQUEST_DATE',
        'OPT_TRANS_REQUEST_CHAR',
        'OPT_TRANS_APPR_DATE',
        'OPT_TRANS_APPR_CHAR',
        'OPT_FLOOR',
        'OPT_TRANS_SERVICE_TYPE',
        'OPT_TRANS_SERVICE_DUEDATE',
        'OPT_TRANSDT_ID',
        'OPT_TRANS_SC_FREQ',
        'OPT_TRANS_SC_PERIOD',
        'OPT_TRANS_FREESC_STATUS',
        'OPT_TRANS_FREESC_DATE',
        'OPT_TRANS_DEDUCT_TAX_NT',
        'OPT_TRANS_DEPOSIT',
        'OPT_TRANS_VANUMBER',
        'created_at',
        'updated_at'];
}
