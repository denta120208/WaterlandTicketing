<?php namespace App\Model\SitePlan;

use Illuminate\Database\Eloquent\Model;

class SitePlan extends Model {
    protected $table = 'MD_SITE_PLAN';
    public $timestamps = false;
    protected $fillable = [
        'ID_SITE_PLAN',
        'URI_SITE_PLAN',
        'STATUS_SITE_PLAN',
        'PROJECT_NO_CHAR',
        'LOT_LEVEL_ID_INT',
        'SITE_WIDTH',
        'SITE_HEIGHT',
        'URI_NAME',
        'URI_DESC',
        'SITE_PLAN_FLAG',
        'IS_DELETE',
        'SITE_PLAN_URLMOBILE',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
//    public function stockUnit(){
//        return $this->hasOne('App\Model\StockUnitModel', 'SITE_PLAN_ID', 'ID_SITE_PLAN');
//    }
}
