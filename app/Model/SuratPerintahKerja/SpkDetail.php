<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class SpkDetail extends Model {

    protected $table = 'SPK_TRANS_DETAIL';
    protected $primaryKey = 'SPK_DETAIL_ID_INT';
    
    protected $fillable =[
        'SPK_DETAIL_ID_INT',
        'SPK_TRANS_NOCHAR',
        'KODE_STOK_UNIQUE_ID_CHAR',
        'DEBTOR_ACCT_CHAR',
        'BOOKING_ENTRY_CODE_INT',
        'SPK_DETAIL_JENIS',
        'SPK_DETAIL_INFRA',
        'SPK_DETAIL_KET',
        'SPK_DETAIL_QTY',
        'SPK_DETAIL_LUAS',
        'UOM_NAME',
        'ID_UOM',
        'SPK_DETAIL_HARGA',
        'SPK_DETAIL_JUMLAH'
    ];
}
