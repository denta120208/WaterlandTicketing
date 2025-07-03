<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class BankVoucher extends Model {


      protected $table = 'BV_TRANS';
      protected $primaryKey = 'BV_ID_INT';
    // protected $dateFormat = 'Y-m-d H:i:s.u0';
    protected $timestamp = false;

      protected $fillable =[
                'BV_ID_INT',
                'BV_NO_CHAR',
                'BV_REF_NO_CHAR',
                'INVOICE_NUMBER_NUM',
                'INVOICE_DATE',
                'BV_DOC_TYPE',
                'BV_PROJECT_CODE',
                'ACC_SOURCODE_CHAR',
                'BV_TRX_DATE',
                'BV_CEK_NOCHAR',
                'BV_DESC',
                'BV_DEBIT_NUM',
                'BV_CREDIT_NUM',
                'BV_STATUS_INT',
                'PROJECT_NO_CHAR',
                'BV_REQUEST_CHAR',
                'BV_REQUEST_DATE',
                'BV_POSTING_GL_CHAR',
                'BOOKING_ENTRY_CODE_INT',
                'KODE_STOK_UNIQUE_ID_CHAR',
                'DEBTOR_ACCT_CHAR',
                'MULTIPLE_GENERATE'
      ];
//    public function getDateFormat()
//    {
//        return 'Y-m-d H:i:s.u';
//    }
}
