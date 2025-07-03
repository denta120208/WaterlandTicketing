<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model {


      protected $table = 'ACC_JOURNAL';
      protected $primaryKey = 'ACC_JOURNAL_ID';
      public $timestamps = false;

      protected $fillable =[
            'ACC_JOURNAL_NOCHAR',
            'ACC_JOURNAL_RNOCHAR',
            'INVOICE_NUMBER_NUM',
            'PROJECT_CODE',
            'ACC_SOURCODE_CHAR',
            'PROJECT_NO_CHAR',
            'ACC_JOURNAL_DTTIME',
            'ACC_JOURNAL_TRX_DATE',
            'ACC_JOURNAL_REF_NOCHAR',
            'ACC_JOURNAL_REF_DESC',
            'ACC_JOURNAL_CURR_CHAR',
            'ACC_JOURNAL_DEBIT_INT',
            'ACC_JOURNAL_CREDIT_INT',
            'ACC_JOURNAL_RATE_INT',
            'ACC_JOURNAL_FIN_APPROVED',
            'ACC_JOURNAL_APPROVED_INT',
            'ACC_JOURNAL_CREATEDBY_CHAR',
            'ACC_JOURNAL_MODIFIEDBY_CHAR',
            'ACC_JOURNAL_MODIFIEDBY_DTTIME',
            'ACC_JOURNAL_AUDITOR_CHAR',
            'ACC_JOURNAL_AUDITOR_DTTIME',
            'ACC_JOURNAL_PERIOD',
            'ACC_JOURNAL_VENDOR_CHAR',
            'ACC_JOURNAL_FP_CHAR',
            'ACC_JOURNAL_AUTOMATION',
      ];


}
