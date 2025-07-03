<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class VariantOrder extends Model {

    protected $table = 'SPK_VARIANTORDER';
    protected $primaryKey = 'SPK_VO_NOCHAR';
    
    protected $fillable =[
        'SPK_VO_ID_INT',
        'SPK_VO_NOCHAR',
        'SPK_TRANS_NOCHAR',
        'MD_VENDOR_ID_INT',
        'ID_PEKERJAAN',
        'TAX_SCHEME_ID_INT',
        'PROJECT_NO_CHAR',
        'SPK_VO_RETENSI_INT',
        'SPK_VO_JNS_PEKERJAAN',
        'SPK_VO_SYARAT_LAIN',
        'SPK_VO_NOTES',
        'SPK_VO_DPP',
        'SPK_VO_PPN',
        'SPK_VO_TOTAL',
        'SPK_VO_APPROVE_INT',
        'SPK_VO_REQUEST_BY',
        'SPK_VO_REQUEST_DTTIME',
        'SPK_VO_APPROVE_ONE_BY',
        'SPK_VO_APPROVE_SECOND_BY',
        'SPK_VO_APPROVE_THIRD_BY',
        'SPK_VO_CANCEL_BY',
        'SPK_VO_APPROVE_ONE_DTTIME',
        'SPK_VO_APPROVE_SECOND_DTTIME',
        'SPK_VO_APPROVE_THIRD_DTTIME',
        'SPK_VO_CANCEL_DTTIME',
        'SPK_VO_TYPE_INT',
        'SPK_VO_TRX_DATE',
        'SPK_VO_TIME_PERIOD_INT',
        'SPK_VO_REF_NO'
    ];
}
