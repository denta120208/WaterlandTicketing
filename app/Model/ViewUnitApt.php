<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ViewUnitApt extends Model {
    
    protected $table = 'MD_VIEW_STOCK_APT';
    protected $primaryKey = 'ID_VIEW_APT';
    
     protected $fillable =[
         'ID_VIEW_APT',
         'DESC_VIEW_APT'
     ];
     
     
    public function viewunit(){
       return $this->hasMany('App\Model\StockUnitApartmentModel','ID_VIEW_APT');
    }
     
}
