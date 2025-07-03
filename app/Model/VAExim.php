<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VAExim extends Model {

    protected $table = 'VA_EXIM';
    protected $primaryKey = 'VA_EXIM_ID_INT';
    
    protected $fillable =[
        'VA_EXIM_ID_INT',
        'PROJECT_NO_CHAR',
        'VA_EXIM_TRX_DATE',
        'VA_EXIM_TOTAL_AMT_NUM',
        'VA_EXIM_TOTAL_AMT_INT',
        'MD_VA_BANK_ID_INT',
        'VA_NUMBER',
        'VA_EXIM_CURRENCY',
        'PROJECT_DESC',
        'CUSTOMER_NAME_CHAR',
        'KODE_STOK_UNIQUE_ID_CHAR',
        'VA_EXIM_OPEN_PERIOD',
        'VA_EXIM_CLOSE_PERIOD',
        'VA_EXIM_SUBBILL',
        'VA_EXIM_CSV_TEXT'
    ];
}
