<?php namespace App\Model\SuratPerintahKerja;

use Illuminate\Database\Eloquent\Model;

class SpkType extends Model {

    protected $table = 'SPK_TYPE';
    protected $primaryKey = 'SPK_TYPE_ID_INT';

    protected $fillable =[
        'SPK_TYPE_ID_INT',
        'SPK_TYPE_NAME_CHAR',
        'ACC_NOP_CHAR',
        'ACC_NO_CHAR',
        'ACC_NAME_CHAR'
    ];
}
