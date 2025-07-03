<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class BpuCount extends Model {

    protected $table = 'BT_COUNT';
    protected $primaryKey = 'BT_COUNT_ID_INT';
   // protected $timestamp = false;

    protected $fillable =[
        'BT_COUNT_ID_INT',
        'PROJECT_NO_CHAR',
        'BT_DOC_TYPE',
        'BT_COUNT_YEAR',
        'BT_COUNT_MONTH',
        'CR_DIVISI_INT',
        'BT_COUNT_INT'
    ];
}
