<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RabHD extends Model
{
    protected $table = 'RAB_TRANS';
  //  protected $primaryKey = 'RAB_NOCHAR';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable =[
        'RAB_NOCHAR',
        'CR_DIVISI_INT',
        'BT_CATEGORY_ID_INT',
        'RAB_DESC_CHAR',
        'RAB_TRX_DATE',
        'RAB_STATUS_INT',
        'PROJECT_NO_CHAR',
        'RAB_REQUEST_CHAR',
        'RAB_REQUEST_DATE',
        'RAB_APPROVE_CHAR',
        'RAB_APPROVE_DATE',
        'RAB_CANCEL_CHAR',
        'RAB_CANCEL_DATE',
        'created_at',
        'updated_at',
        'RAB_APPROVE_NOTE'];
}
