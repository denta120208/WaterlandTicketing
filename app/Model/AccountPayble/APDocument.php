<?php namespace App\Model\AccountPayble;

use Illuminate\Database\Eloquent\Model;

class APDocument extends Model {
    protected $table = 'AP_DOC_COMPONENT';
    public $timestamps = false;
    protected $fillable = [
        'DOC_COM_NAME',
        'DOC_COM_STATUS',
        'DOC_COM_KET',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
}
