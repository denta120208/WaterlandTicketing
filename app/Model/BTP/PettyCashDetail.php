<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class PettyCashDetail extends Model {

    protected $table = 'PT_CASH_DETAIL';
    protected $primaryKey = 'PT_CASH_DTL_ID_INT';
    protected $timestamp = false;

    protected $fillable =[
        'PT_CASH_DTL_ID_INT',
        'PT_CASH_NO_CHAR',
        'PT_CASH_DESC',
        'PT_CASH_DTL_TRX_DATE',
        'ACC_NO_CHAR',
        'ACC_NOP_CHAR',
        'ACC_NAME_CHAR',
        'PT_CASH_DTL_AMOUNT_NUM',
        'PROJECT_NO_CHAR',
        'CR_DIVISI_INT'
    ];
}
