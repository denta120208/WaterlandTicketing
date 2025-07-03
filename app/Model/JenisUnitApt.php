<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JenisUnitApt extends Model {

    protected $table = 'MD_JENIS_STOCK_APT';
    protected $primaryKey = 'ID_TYPE_INT';
    
     protected $fillable =[
         'ID_TYPE_INT',
         'JENIS_UNIT_CHAR',
         'LUAS_UNIT_FLOAT',
         'LUAS_BANGUNAN_FLOAT',
         'PROJECT_NO_CHAR'
     ];
     
     
      public function typesUnit(){
       return $this->hasMany('App\Model\StockUnitApartmentModel','ID_TYPE_INT');
       
    }
     
     

}
