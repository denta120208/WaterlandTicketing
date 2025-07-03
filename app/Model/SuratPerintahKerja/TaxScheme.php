<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class TaxScheme extends Model {

    protected $table = 'TAX_SCHEME_HEADER';
   // protected $primaryKey = 'TAX_SCHEME_CODE';

    protected $fillable =[
        'TAX_SCHEME_ID_INT',
        'TAX_SCHEME_CODE',
        'TAX_SCHEME_DESC_CHAR'
    ];
}
