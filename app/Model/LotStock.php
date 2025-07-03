<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LotStock extends Model {

	//
    protected $table = 'LOT_STOCK';
    protected $primaryKey = 'LOT_STOCK_ID_INT';

    protected $fillable =[
        'LOT_STOCK_ID_INT',
        'LOT_STOCK_NO',
        'LOT_LEVEL_ID_INT',
        'LOT_TYPE_ID_INT',
        'LOT_ZONE_ID_INT',
        'id_unit',
        'LOT_STOCK_SQM',
        'LOT_STOCK_SQM_SC',
        'ON_RELEASE_STAT_INT',
        'ON_RENT_STAT_INT',
        'COUNT_DEBTOR_NUM',
        'PROJECT_NO_CHAR',
        'IS_DELETE',
        'LOT_CREATED_BY',
        'LOT_CREATED_DATE',
        'LOT_UPDATE_BY',
        'LOT_UPDATE_DATE',
        'LOT_DELETE_BY',
        'LOT_DELETE_DATE'
    ];


}
