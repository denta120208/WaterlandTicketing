<?php namespace App\Model\AccountPayble;

use Illuminate\Database\Eloquent\Model;

class APDetail extends Model {
    protected $table = 'AP_VOUCHER_DETAIL';
    public $timestamps = false;
    protected $primaryKey = 'AP_VOUCHER_DTL_ID';
    protected $fillable = [
        'AP_VOUCHER_DTL_ID',
        'AP_VOCHER_NOCHAR',
        'PROJECT_NO_CHAR',
        'DEBTOR_ACCT_CHAR',
        'ID_CUSTOMER_CHAR',
        'AP_VOUCHER_DTTIME',
        'AP_VOUCHER_TRX_DATE',
        'ACC_NOP_CHAR', // Parent COA
        'ACC_NO_CHAR', // Nomor COA
        'ACC_NAME_CHAR',
        'AP_VOCHER_DTL_DESC_CHAR',
        'AP_VOUCHER_DTL_AMOUNT_INT',
        'AP_VOUCHER_TRX_TYPE',
        'AP_VOUCHER_STATUS',
        'AP_VOUCHER_PAYMENT_STATUS',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
    public function voucher(){
        return $this->hasOne('App\Model\AccountPayble\AccountPayble', 'AP_VOCHER_NOCHAR', 'AP_VOCHER_NOCHAR');
    }
}
