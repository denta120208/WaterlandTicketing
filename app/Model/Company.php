<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {

	//
    protected $table = 'MD_COMPANY';
    protected $primaryKey = 'ID_COMPANY_INT';
    
    protected $fillable =[
       'ID_COMPANY_INT', 
       'COMPANY_NAME',
       'COMPANY_LOKASI',
       'COMPANY_ADDRESS',
       'COMPANY_CITY',
       'COMPANY_PHONE',
       'COMPANY_NPWP'
    ];

}
