<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StockTransModel extends Model
{
    protected $table = 'STOCK_TRANS';
    protected $primaryKey = 'STOCK_TRANS_INT';

    protected $fillable =[
        'ID_ITEM_INT',
        'ITEM_CODE',
        'ITEM_NAME',
        'ITEM_SPEC',
        'STOCK_OPEN_BAL',
        'OPEN_PRICE',
        'STOCK_IN',
        'STOCK_OUT',
        'STOCK_AJD',
        'STOCK_RETUR_IN',
        'STOCK_RETUR_OUT',
        'CLOSE_BAL',
        'CLOSING_PRICE',
        'STOK_PERIOD',
        'PROJECT_NO_CHAR'
    ];
}
