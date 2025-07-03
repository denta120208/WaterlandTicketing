<?php


namespace App\Model\Engineerings;

use Illuminate\Database\Eloquent\Model;

class UtilsCategory extends Model{
    protected $table='UTILS_CATEGORY';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['UTILS_CATEGORY_ID_INT',
        'UTILS_CATEGORY_NAME',
        'IS_DELETE',
        'created_at',
        'updated_at'];

}
