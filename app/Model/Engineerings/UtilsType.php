<?php


namespace App\Model\Engineerings;

use Illuminate\Database\Eloquent\Model;

class UtilsType extends Model{
    protected $table='UTILS_TYPE';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['id',
        'UTILS_TYPE_NAME',
        'UTILS_TYPE_STATUS',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'];

}
