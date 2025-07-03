<?php namespace App\Model\ProjectLedger;

use Illuminate\Database\Eloquent\Model;

class ApproveBudget extends Model {
    protected $table = 'PL_APPROVE_BUDGET';
    public $timestamps = false;
    protected $fillable = [
        'ID_APPROVE',
        'THN_BUDGET',
        'APPROVE_BY',
        'APPROVE_AT',
        'created_at', 
        'created_by'
    ];
    public function budget(){
        return $this->hasOne('App\Model\ProjectLedger\Budget', 'THN_BUDGET', 'THN_BUDGET');
    }
}
