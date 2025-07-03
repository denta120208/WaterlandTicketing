<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VendorType extends Model {
	//
        protected $table = 'MD_VENDOR_TYPE';
        protected $primaryKey = 'TYPE_VENDOR_ID_INT';
     
        protected $fillable =[
            'TYPE_VENDOR_ID_INT',
            'TYPE_VENDOR_NAME'
        ];
}
