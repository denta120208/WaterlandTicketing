<?php namespace App\Model\ProjectLedger;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model {
    protected $table = 'PL_BUDGET';
    public $timestamps = false;
    protected $fillable = [
        'ID_BUDGET',
        'THN_BUDGET',
        'ID_BUDGET_PEKERJAAN',
        'STATUS_BUDGET',
        'ID_REVISI',
        'NAMA_PEKERJAAN',
        'QTY_BUDGET', 
        'NILAI_BUDGET',
        'TOTAL_NILAI',
        'IS_MASTER', 
        'ID_MASTER', 
        'ID_UOM', 
        'PROJECT_NO_CHAR_BUDGET',
        'QTY_REALISASI',
        'TOTAL_REALISASI',
        'QTY_SPK',
        'TOTAL_SPK',
        'created_at', 
        'created_by', 
        'updated_at', 
        'updated_by',
        'IS_UNBUDGETED'
    ];
    
    public function pekerjaan(){
        return $this->hasOne('App\Model\ProjectLedger\MasterPekerjaan', 'ID_PEKERJAAN', 'ID_BUDGET_PEKERJAAN');
    }
    public function uom(){
        return $this->hasOne('App\Model\ProjectLedger\MasterUnit', 'id_unit', 'ID_UOM');
    }
}
