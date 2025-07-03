<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class BpuTransDetail extends Model {

    protected $table = 'BT_TRANS_DETAIL';
    protected $primaryKey = 'BT_TRANS_DTL_ID_INT';
    protected $dateFormat = 'Y-m-d H:i';

    protected $fillable =[
        'BT_TRANS_DTL_ID_INT',
        'BT_TRANS_NOCHAR',
        'ID_TDP_INT',
        'TDP_NOCHAR',
        'BT_TRANS_DTL_DESC',
        'BT_TRANS_PTDP_RECEIPT_INT',
        'BT_TRANS_PTDP_COUNT_RECEIPT',
        'BT_TRANS_DTL_AMOUNT_INT',
        'BT_TRANS_PTDP_REAL_INT',
        'BT_TRANS_PTDP_BALANCE',
        'PROJECT_NO_CHAR',
        'CR_DIVISI_INT',
        'LA_TRANS_ID_INT',
        'LA_TRANS_NAMA_PEMILIK',
        'LA_TRANS_LUAS_TANAH_BAYAR'
    ];
}
