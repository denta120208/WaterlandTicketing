<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RabDT extends Model
{
    protected $table = 'RAB_TRANS_DETAIL';
    //  protected $primaryKey = 'RAB_NOCHAR';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable =[
        'RAB_NOCHAR',
        'RAB_DTL_DESC_CHAR',
        'RAB_DTL_AMOUNT_NUM',
        'IS_TAKEN',
        'IS_DELETE',
        'PROJECT_NO_CHAR',
        'created_at',
        'updated_at'
    ];


}
