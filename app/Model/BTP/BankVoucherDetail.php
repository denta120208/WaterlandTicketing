<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class BankVoucherDetail extends Model {


      protected $table = 'BV_TRANS_DETAIL';
      protected $primaryKey = 'BV_DTL_ID_INT';
    protected $timestamp = false;
//    protected $dateFormat = 'Y-m-d H:i';
      protected $fillable =[
                'BV_DTL_ID_INT',
                'BV_NO_CHAR',
                'BT_REF_NO_CHAR_DETAIL',
                'PROJECT_CODE',
                'ACC_SOURCODE_CHAR',
                'PROJECT_NO_CHAR',
                'BV_TRX_DATE',
                'ACC_NO_CHAR',
                'ACC_NOP_CHAR',
                'ACC_NAME_CHAR',
                'BV_AMOUNT',
                'BV_DTL_DESC_CHAR',
                'BOOKING_ENTRY_CODE_INT',
                'KODE_STOK_UNIQUE_ID_CHAR',
                'DEBTOR_ACCT_CHAR'
      ];

}
