<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class TrxTermin extends Model {

    protected $table = 'MD_TERMIN';
    protected $primaryKey = 'MD_TERMIN_ID_INT';
    
    protected $fillable =[
        'MD_TERMIN_ID_INT',
        'MD_TERMIN_DESC_CHAR',
        'MD_TERMIN_PROGRESS_INT',
        'MD_TERMIN_BAYAR_INT',
        'MD_TERMIN_STATUS_INT'
    ];
}
