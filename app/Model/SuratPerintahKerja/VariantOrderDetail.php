<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class VariantOrderDetail extends Model {

    protected $table = 'SPK_VARIANTORDER_DETAIL';
    protected $primaryKey = 'SPK_VO_DETAIL_ID_INT';
    
    protected $fillable =[
        'SPK_VO_DETAIL_ID_INT',
        'SPK_VO_NOCHAR',
        'KODE_STOK_UNIQUE_ID_CHAR',
        'DEBTOR_ACCT_CHAR',
        'BOOKING_ENTRY_CODE_INT',
        'SPK_VO_DETAIL_JENIS',
        'SPK_VO_DETAIL_INFRA',
        'SPK_VO_DETAIL_KET',
        'SPK_VO_DETAIL_QTY',
        'SPK_VO_DETAIL_LUAS',
        'UOM_NAME',
        'ID_UOM',
        'SPK_VO_DETAIL_HARGA',
        'SPK_VO_DETAIL_JUMLAH'
    ];
}
