<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

	//
    protected $table = 'MD_CUSTOMER';
    protected $primaryKey = 'CUSTOMER_ID_INT';
    
    protected $fillable =[
       'NAME_CHAR', 
       'ADDRESS1_CHAR',
       'KOTA_CHAR',
       'RTRW_CHAR',
       'CORR_ADDRESS',
       'KOTAKODEPOS_CHAR',
       'KELKEC_CHAR',
       'ADDRESS2_CHAR', 
       'EMAIL_ADDRESS_CHAR' , 
       'TELEPHONE_NO_CHAR' , 
       'HANDPHONE_CHAR' , 
       'HANDPHONE2_CHAR' , 
       'FAX_NO_CHAR' , 
       'NO_KTP_CHAR' , 
       'NPWP_CHAR' , 
       'NATIONALITY_CHAR' , 
       'RELIGION_CHAR' , 
       'BIRTHPLACE_CHAR' , 
       'BIRTHDATE_DTTIME' , 
       'SEX_NUM' , 
       'MARITAL_STATUS' , 
       'BANK_ACCOUNT_CHAR',
       'BANK_ACCOUNT_NAME_CHAR',
       'BANK_ACCOUNT_OWNER_CHAR',
       'STATUS_CUSTOMER_CHAR' ,
       'NUP_COUNT_INT',
       'CREATED_BY_CHAR',
       'CUST_PROJ_ID_CHAR',
       'PEKERJAAN_CHAR',
       'PROJECT_NO_CHAR',
    ];
    
     
       
    
   public function custToNUP(){
       return $this->hasMany('App\Model\NoUrutPelangganModel','CUSTOMER_ID_INT');
   }
   
   public function delete(){
       $this->custToNUP()->delete();
       return parent::delete();
   }
   
   public function forceDelete(){
       $this->custToNUP()->withTrashed()->forceDelete();
       return parent::forceDelete();
   }
   
   

}
