<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model {


      protected $table = 'ACC_MD_COA';
      protected $primaryKey = 'ACC_NO_CHAR';

      protected $fillable =[
                'ACC_NO_CHAR',
                'ACC_NAME_CHAR',
                'ACC_CURR_CHAR',
                'ACC_NOP_CHAR',
                'ACC_TYPE_ID_INT',
                'PROJECT_NO_CHAR',
                 'ID_BUSINESS_INT'
      ];

}
