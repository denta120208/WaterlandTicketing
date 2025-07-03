<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model {
    protected $table = 'CR_DIVISI';
    protected $primaryKey = 'CR_DIVISI_INT';
    
    protected $fillable =[
        'CR_DIVISI_INT',
        'CR_DIVISI_NAME',
        'CR_DIVISI_CODE'
    ];
}
