<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StockUnitApartmentModel extends Model {


    protected $table = 'MD_STOCK_COM';
    protected $primaryKey = 'ID_UNIT_APARTMENT_INT';

    protected $fillable =[
        'ID_TYPE_INT',
        'ID_VIEW_APT',
        'FLOOR_INT',
        'NOUNIT_CHAR',
        'STATUS_INT',
        'ID_TOWER_INT',
        'CANCELLATION_INT',
        'ON_RELEASE_STAT_INT',
        'ON_RESERVE_STAT_INT',
        'ON_SOLD_STAT_INT',
        'TOWER_CHAR',
        'PRICE_STAGE_POS_INT',
        'DUMMY_STATUS',
        'CREATED_BY_CHAR',
        'UPDATED_BY_CHAR',
        'PROJECT_NO_CHAR',
        'COUNT_DEBTOR_ACCT',
        'COORD_AREA_CHAR',
        'POLY_COORD_INT',
        'STYLE_TOP_INT',
        'STYLE_LEFT_INT',
        'STYLE_HEIGHT_INT',
        'STYLE_WIDTH_INT',
        'PROGRESS_TECH_INT',
        'SPK_TECH_INT',
        'ID_STOCK_REF_NO',
        'MD_JALAN_CHAR',
        'MD_KEL_CHAR',
        'MD_KEC_CHAR',
        'MD_KOTA_CHAR',
        'UNIT_LRATE',
        'UNIT_BRATE',
        'PPB_INDUK_NOCHAR',
        'PPB_PECAHAN_NOCHAR',
        'IMB_INDUK_NOCHAR',
        'IMB_PECAHAN_NOCHAR'
    ];

     public function scopeIdunit($query,$id){
         return $query->where('ID_UNIT_APARTMENT_INT',$id);
     }

    public function scopeNounit($query,$no){
         return $query->where('NOUNIT_CHAR',$no);
     }

   public function jenisUnit() {
         return $this->belongsTo('App\Model\JenisUnitApt', 'ID_TYPE_INT', 'ID_TYPE_INT');
     }

    public function viewUnit() {
         return $this->belongsTo('App\Model\ViewUnitApt','ID_VIEW_APT','ID_VIEW_APT');
     }

     public function towerUnit() {
         return $this->belongsTo('App\Model\TowerApt','ID_TOWER_INT','ID_TOWER_INT');
     }

}
