<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class SpkAssign extends Model {

    protected $table = 'SPK_ASSIGN';
    protected $primaryKey = 'SPK_ASSIGN_ID_INT';
    
    protected $fillable =[
        'SPK_ASSIGN_ID_INT',
        'SPK_ASSIGN_NAME',
        'SPK_ASSIGN_JOB_NAME',
        'SPK_ASSIGN_TYPE',
        'PROJECT_NO_CHAR',
        'LEAD_PROJECT',
        'IS_DELETE',
        'IS_DEFAULT'
    ];
}
