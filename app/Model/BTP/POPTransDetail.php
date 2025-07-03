<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class POPTransDetail extends Model {

    protected $table = 'POP_TRANS_DETAIL';
    protected $primaryKey = 'POP_TRANS_DTL_ID_INT';
    protected $timestamp = false;

    protected $fillable =[
        'POP_TRANS_DTL_ID_INT',
        'POP_NO_CHAR',
        'KODE_STOK_UNIQUE_ID_CHAR',
        'POP_TRANS_DTL_ITEM_DOC_CHAR',
        'DEBTOR_ACCT_CHAR',
        'NOTARIS_NAME',
        'NOTARIS_ID_INT',
        'BOOKING_ENTRY_CODE_INT',
        'POP_TRANS_DTL_DESC',
        'POP_TRANS_DTL_AMOUNT_INT',
        'POP_STATUS_DTL_INT',
        'PROJECT_NO_CHAR',
        'POP_TYPE'
    ];
}
