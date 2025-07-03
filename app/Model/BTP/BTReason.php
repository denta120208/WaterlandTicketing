<?php namespace App\Model\BTP;

use Illuminate\Database\Eloquent\Model;

class BTReason extends Model {

    protected $table = 'BT_REASON';
    protected $primaryKey = 'BT_REASON_ID_INT';
    public $timestamps = false;

    protected $fillable =[
        'BT_REASON_ID_INT',
        'BT_TRANS_NOCHAR',
        'BT_REASON_DESC',
        'BT_REASON_STATUS_INT',
        'PROJECT_NO_CHAR',
        'BT_REASON_REQUEST_DATE',
        'BT_REASON_REQUEST_CHAR',
        'BT_REASON_APPROVE_DATE',
        'BT_REASON_APPROVE_CHAR'
    ];
}
