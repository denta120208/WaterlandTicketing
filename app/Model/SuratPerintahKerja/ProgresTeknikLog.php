<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class ProgresTeknikLog extends Model {

    protected $table = 'PROGRESS_TECH_LOG';
    protected $primaryKey = 'PROGRESS_TECH_LOG_ID_INT';
    
    protected $fillable =[
        'PROGRESS_TECH_LOG_ID_INT',
        'NOUNIT_CHAR',
        'PROGRESS_TECH_INT',
        'PROGRESS_TECH_TRX_DATE',
        'PROJECT_NO_CHAR'
    ];
}
