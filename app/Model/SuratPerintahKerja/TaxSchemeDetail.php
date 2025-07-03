<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class TaxSchemeDetail extends Model {

    protected $table = 'TAX_SCHEME_DETAIL';
    protected $primaryKey = 'TAX_SCHEME_DT_ID_INT';
    
    protected $fillable =[
        'TAX_SCHEME_DT_ID_INT',
        'TAX_SCHEME_CODE',
        'TAX_SCHEME_DESC_CHAR',
        'TAX_SCHEME_RATE_INT',
        'ACC_NO_CHAR',
        'TAX_SCHEME_DEDUCT_INT'
    ];
}
