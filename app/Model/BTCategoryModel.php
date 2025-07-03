<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BTCategoryModel extends Model {

      protected $table = 'BT_CATEGORY';
      protected $primaryKey = 'BT_CATEGORY_ID_INT';
      
      protected $fillable =[
        'BT_CATEGORY_ID_INT',
        'BT_CATEGORY_NAME',
        'BT_CATEGORY_ESTIMATE_DAY'
    ];

}
