<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model {

    protected $table = 'PT_CASH';
    protected $primaryKey = 'PT_CASH_ID_INT';
    protected $timestamp = false;

    protected $fillable =[
        'PT_CASH_ID_INT',
        'PT_CASH_NO_CHAR',
        'PROJECT_NO_CHAR',
        'CR_DIVISI_INT',
        'PT_CASH_TRX_DATE',
        'PT_CASH_JOURNAL_DATE',
        'PT_CASH_AMOUNT_NUM',
        'ACC_NO_CHAR',
        'ACC_NOP_CHAR',
        'ACC_NAME_CHAR',
        'ACC_JOURNAL_NOCHAR',
        'PT_CASH_CREATED_BY',
        'PT_CASH_CREATED_DATE',
        'PT_CASH_MODIFY_BY',
        'PT_CASH_MODIFY_DATE',
        'PT_CASH_POSTING_BY',
        'PT_CASH_POSTING_DATE',
        'PT_CASH_STATUS_INT'
    ];
}
