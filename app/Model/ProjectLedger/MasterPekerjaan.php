<?php namespace App\Model\ProjectLedger;

use Illuminate\Database\Eloquent\Model;

class MasterPekerjaan extends Model {
    protected $table = 'PL_MASTER_PEKERJAAN';
    public $timestamps = false;
    protected $fillable = [
        'ID_PEKERJAAN',
        'NAMA_PEKERJAAN',
        'STATUS_PEKERJAAN',
        'PROJECT_NO_CHAR',
        'IS_MASTER',
        'created_at', 
        'created_by', 
        'updated_at', 
        'updated_by'
    ];
}
