<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TowerApt extends Model {
    
    protected $table = 'MD_TOWER_APT';
    protected $primaryKey = 'ID_TOWER_INT';
    
     protected $fillable =[
         'ID_TOWER_INT',
         'TOWER_NAME',
         'TOWER_CODE',
         'PROJECT_NO_CHAR'
     ];
     
    public function towersUnit(){
       return $this->hasMany('App\Model\StockUnitApartmentModel','ID_TOWER_INT');
    }
    
    public function towersUnitre(){
       return $this->hasMany('App\Model\StockUnitModel','ID_TOWER_INT');
    }

}
