<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class Spk extends Model {

    protected $table = 'SPK_TRANS';
    protected $primaryKey = 'SPK_TRANS_ID_INT';
    public $timestamps = false;

    protected $fillable =[
        'SPK_TRANS_ID_INT',
        'SPK_TRANS_NOCHAR',
        'CR_DIVISI_INT',
        'SPK_TYPE_ID_INT',
        'SPK_DOC_TYPE',
        'SPK_TRANS_NAME',
        'SPK_TRANS_REF_NO',
        'THN_BUDGET',
        'SPK_TRANS_TYPE',
        'SPK_TRANS_JNS_PEKERJAAN',
        'SPK_TRANS_TRX_DATE',
        'SPK_TRANS_START_DATE',
        'SPK_TRANS_END_DATE',
        'SPK_TIME_PERIOD',
        'SPK_TRANS_SYARAT_LAIN',
        'SPK_TRANS_NOTES',
        'SPK_TRANS_TERMIN',
        'TAX_SCHEME_ID_INT',
        'MD_VENDOR_ID_INT',
        'SPK_RETENSI_INT',
        'SPK_RETENSI_NUM',
        'SPK_TRANS_DPP',
        'SPK_TRANS_PPN',
        'SPK_TRANS_PPH',
        'SPK_TRANS_TOTAL',
        'PROJECT_NO_CHAR',
        'SPK_TRANS_REQUEST_BY',
        'SPK_TRANS_REQUEST_DTTIME',
        'SPK_TRANS_APPROVE_INT',
        'SPK_TRANS_APPROVE_ONE_BY',
        'SPK_TRANS_APPROVE_SECOND_BY',
        'SPK_TRANS_APPROVE_THIRD_BY',
        'SPK_TRANS_APPROVE_ONE_DTTIME',
        'SPK_TRANS_APPROVE_SECOND_DTTIME',
        'SPK_TRANS_APPROVE_THIRD_DTTIME',
        'ID_PEKERJAAN',
        'NILAI_BUDGET',
        'NILAI_REALISASI',
        'SERTIFIKAT_KE',
        'SPK_TRANS_REVISI',
        'SPK_TRANS_CANCEL_BY',
        'SPK_TRANS_CANCEL_DTTIME',
        'SPK_TRANS_PROGRESS_INT',
        'SPK_TRANS_ADDENDUM_INT',
        'SPK_TRANS_PAYMENT',
        'SPK_TRANS_CLOSED_BY',
        'SPK_TRANS_CLOSED_DTTIME',
        'SPK_TRANS_COM_PROJECT'
    ];
}
