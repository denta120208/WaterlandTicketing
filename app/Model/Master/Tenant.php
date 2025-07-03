<?php


namespace App\Model\Master;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model{
    protected $table='MD_TENANT';
    public $timestamps = false;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable=['MD_TENANT_ID_INT'
        ,'MD_TENANT_NOCHAR'
        ,'MD_TENANT_NAME_CHAR'
        ,'MD_TENANT_ADDRESS1'
        ,'MD_TENANT_ADDRESS2'
        ,'MD_TENANT_CITY_CHAR'
        ,'MD_TENANT_POSCODE'
        ,'MD_TENANT_TELP'
        ,'MD_TENANT_FAX'
        ,'MD_TENANT_EMAIL'
        ,'MD_TENANT_NPWP'
        ,'MD_TENANT_NIK'
        ,'MD_TENANT_SUJK'
        ,'TYPE_TENANT_ID_INT'
        ,'PROJECT_NO_CHAR'
        ,'MD_TENANT_BANK_NAME'
        ,'MD_TENANT_BANK_ACCOUNT'
        ,'MD_TENANT_BANK_LOCATION'
        ,'MD_TENANT_BANK_ACCOUNT_NAME'
        ,'MD_TENANT_DIRECTOR'
        ,'MD_TENANT_DIRECTOR_JOB_TITLE'
        ,'MD_TENANT_CP_NAME'
        ,'MD_TENANT_CP_NO_TELP'
        ,'MD_TENANT_CP_NO_EMAIL'
        ,'MD_TENANT_CP_NO_HP'
        ,'MD_TENANT_GM_ACTIVE'
        ,'created_at'
        ,'updated_at'
        ,'MD_TENANT_ITEM'
        ,'vendorkode'
        ,'jenis'
    ];

}
