<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class BpuTrans extends Model {

    protected $table = 'BT_TRANS';
    protected $primaryKey = 'BT_TRANS_ID_INT';
  //  protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable =[
        'BT_TRANS_ID_INT',
        'BT_TRANS_NOCHAR',
        'BT_CATEGORY_ID_INT',
        'CR_DIVISI_INT',
        'MD_VENDOR_ID_INT',
        'BT_TRANS_AMOUNT_INT',
        'BT_TRANS_STATUS_INT',
        'BT_TRANS_REQUEST_DATE',
        'BT_TRANS_REQUEST_CHAR',
        'BT_TRANS_APPR_DATE',
        'BT_TRANS_APPR_CHAR',
        'PROJECT_NO_CHAR',
        'BT_DOC_TYPE',
        'BT_CHEQUE_POSITION',
        'BT_TRANS_DESC',
        'BT_TRANS_NO_CEK',
        'BT_TRANS_TRANSFER_DATE',
        'ACC_JOURNAL_NOCHAR',
        'BT_ESTIMASI_PTDP',
        'BOOKING_ENTRY_CODE_INT',
        'KODE_STOK_UNIQUE_ID_CHAR',
        'DEBTOR_ACCT_CHAR',
        'UPDATE_INT',
        'RAB_DTL_ID_INT',
        'RAB_REQUIRE'
    ];
}
