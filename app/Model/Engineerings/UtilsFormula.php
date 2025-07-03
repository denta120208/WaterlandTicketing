<?php


namespace App\Model\Engineerings;

use Illuminate\Database\Eloquent\Model;

class UtilsFormula extends Model
{
    protected $table='UTILS_FORMULA';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['ID_U_FORMULA',
        'NAME_U_FORMULA',
        'UTILS_TYPE',
        'UTILS_CATEGORY_ID_INT',
        'UTILS_HIGH_RATE',
        'UTILS_LOW_RATE',
        'UTILS_BILLBOARD_RATE',
        'UTILS_RELIABILITY_RATE',
        'UTILS_HANDLING_FEE_RATE',
        'UTILS_HANDLING_FEE_FIXAMT',
        'UTILS_BPJU_RATE',
        'UTILS_LOST_FACTOR_RATE',
        'UTILS_LOST_FACTOR_FIXAMT',
        'UTILS_KVA_RATE',
        'UTILS_ADMIN_RATE',
        'UTILS_PPJU_RATE',
        'UTILS_STATUS',
        'PROJECT_NO_CHAR',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'];

    public function utils_type(){
        return $this->belongsTo('App\Model\Engineerings\UtilsType', 'UTILS_TYPE', 'id');
    }

    public function utils_category(){
        return $this->belongsTo('App\Model\Engineerings\UtilsCategory', 'UTILS_CATEGORY_ID_INT', 'UTILS_CATEGORY_ID_INT');
    }
}
