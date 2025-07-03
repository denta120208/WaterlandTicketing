<?php namespace App\Model\AccountPayble;

use Illuminate\Database\Eloquent\Model;

class AccountPayble extends Model {
    protected $table = 'AP_VOUCHER';
    public $timestamps = false;
    protected $fillable = [
        'AP_VOUCHER_ID_INT',
        'AP_VOCHER_NOCHAR',
        'AP_VOUCHER_SERT',
        'INVOICE_NUMBER_NUM',
        'SPK_TRANS_NOCHAR',
        'PROJECT_CODE',
        'ACC_SOURCODE_CHAR',
        'PROJECT_NO_CHAR',
        'AP_VOUCHER_DTTIME',
        'AP_VOUCHER_TRX_DATE',
        'AP_VOUCHER_REF_NOCHAR',
        'AP_VOUCHER_REF_DESC',
        'AP_VOUCHER_CURR_CHAR',
        'AP_VOUCHER_PAYMENT_INT',
        'AP_VOUCHER_PAYMENT_STATUS',
        'AP_VOUCHER_RATE_INT',
        'AP_VOUCHER_STATUS_INT',
        'AP_VOUCHER_CREATED_BY',
        'AP_VOUCHER_ACCT_CHAR',
        'AP_VOUCHER_TAX_CHAR',
        'AP_VOUCHER_ACCT_DATE',
        'AP_VOUCHER_TAX_DATE',
        'AP_VOUCHER_FP_CHAR',
        'AP_VOUCHER_VENDOR_CHAR',
        'AP_VOUCHER_EXP_DATE',
        'AP_VOUCHER_PAYMENT_NOCHAR',
        'AP_VOUCHER_INV',
        'AP_TOTAL_PAYMENT',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'AP_INPUTED_DTTIME',
        'AP_ACRUED_AMT'
    ];
    public function voucher_dtl(){
        return $this->hasOne('App\Model\AccountPayble\APDetail', 'AP_VOCHER_NOCHAR', 'AP_VOCHER_NOCHAR');
    }
}
