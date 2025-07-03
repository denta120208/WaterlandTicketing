<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GlTrans extends Model {


      protected $table = 'ACC_GLTRANS';
      //protected $primaryKey = 'ACC_JOURNAL_NOCHAR';

      protected $fillable =[
            'ACC_JOURNAL_NOCHAR',
            'PROJECT_CODE',
            'ACC_SOURCODE_CHAR',
            'PROJECT_NO_CHAR',
            'PSM_TRANS_NOCHAR',
            'MD_TENANT_ID_INT',
            'ACC_JOURNAL_DTTIME',
            'ACC_JOURNAL_TRX_DATE',
            'ACC_NOP_CHAR',
            'ACC_NO_CHAR',
            'ACC_NAME_CHAR',
            'ACC_GLTRANS_DESC_CHAR',
            'ACC_AMOUNT_INT',
            'LOT_STOCK_NO',
            'ACC_GLTRANS_REFNO'
      ];

}
