<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class POPTrans extends Model {

    protected $table = 'POP_TRANS';
    protected $primaryKey = 'POP_ID_INT';
    protected $timestamp = false;

    protected $fillable =[
        'POP_ID_INT',
        'POP_NO_CHAR',
        'CR_DIVISI_INT',
        'POP_TYPE',
        'PROJECT_NO_CHAR',
        'POP_TRX_DATE',
        'POP_AMOUNT_INT',
        'POP_TRX_PERKIRAAN',
        'POP_URAIAN_TEXT',
        'POP_TUJUAN_TEXT',
        'POP_STATUS_INT',
        'MD_VENDOR_ID_INT',
        'POP_REQUEST_CHAR',
        'POP_REQUEST_DATE',
        'POP_APPROVE_CHAR',
        'POP_APPROVE_DATE',
        'POP_TRANS_NOCEK',
        'ACC_JOURNAL_NOCHAR',
        'POP_TRANS_TRANSFER_DATE',
        'POP_DIMOHON_CHAR',
        'POP_DIKETAHUI_CHAR',
        'POP_DISETUJUI_CHAR'
    ];
}
