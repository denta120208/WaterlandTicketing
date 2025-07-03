<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CoaMaster extends Model
{
    //

    protected $table='ACC_MD_COA';
    protected $fillable=['ACC_NO_CHAR',
        'ACC_NAME_CHAR',
        'ACC_CURR_CHAR',
        'ACC_SPEC_ID_INT',
        'ACC_GROUP_ID_CHAR',
        'ACC_TYPE_ID_INT',
        'ACC_LEVEL_INT',
        'USER_ENTRY',
        'ACTIVE_STATUS_INT',
        'ACC_NOP_CHAR',
        'PROJECT_NO_CHAR',
        'created_at',
        'updated_at'];

}
