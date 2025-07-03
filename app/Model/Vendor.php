<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model {
	//
        protected $table = 'MD_VENDOR';
        protected $primaryKey = 'MD_VENDOR_NOCHAR';
      protected $dateFormat = 'Y-m-d H:i:s';

        protected $fillable =[
            'MD_VENDOR_ID_INT',
            'MD_VENDOR_NOCHAR',
            'MD_VENDOR_NAME_CHAR',
            'MD_VENDOR_ADDRESS1',
            'MD_VENDOR_ADDRESS2',
            'MD_VENDOR_CITY_CHAR',
            'MD_VENDOR_POSCODE',
            'MD_VENDOR_TELP',
            'MD_VENDOR_FAX',
            'MD_VENDOR_EMAIL',
            'MD_VENDOR_NPWP',
            'MD_VENDOR_SUJK',
            'TYPE_VENDOR_ID_INT',
            'PROJECT_NO_CHAR',
            'MD_VENDOR_BANK_NAME',
            'MD_VENDOR_BANK_ACCOUNT',
            'MD_VENDOR_BANK_ACCOUNT_NAME',
            'MD_VENDOR_DIRECTOR'
        ];
}
