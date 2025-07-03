<?php namespace App\Model\ProjectLedger;

use Illuminate\Database\Eloquent\Model;

class MasterUnit extends Model {
    protected $table = 'PL_UOM';
    public $timestamps = false;
    protected $fillable = [
        'id_unit',
        'UNIT_NAME',
        'UNIT_CODE',
        'created_at', 
        'created_by', 
        'updated_at', 
        'updated_by'
    ];
}
