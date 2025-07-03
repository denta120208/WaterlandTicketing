<?php namespace App\Model\ProjectLedger;

use Illuminate\Database\Eloquent\Model;

class RevisiBudget extends Model {
    protected $table = 'PL_REVISI';
    public $timestamps = false;
    protected $fillable = [
        'ID_REVISI',
        'DOC_NO',
        'DOC_DATE',
        'DOC_APPROVE',
        'ID_BUDGET',
        'BEFORE_QTY',
        'BEFORE_PRICE',
        'AFTER_QTY',
        'AFTER_PRICE',
        'STATUS_REVISI',
        'PROJECT_NO_CHAR',
        'created_at', 
        'created_by'
    ];
    public function budget(){
        return $this->hasOne('App\Model\ProjectLedger\Budget', 'ID_BUDGET', 'ID_BUDGET');
    }
}
