<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TrialBalance extends Model {

      
      protected $table = 'ACC_TRLBL';
      protected $primaryKey = 'ACC_TRLBL_ID';
        
      protected $fillable =[
                'ACC_NAME_CHAR',
                'ACC_NO_CHAR',
                'ACC_TYPE_ID_INT',
                'ACC_PERIOD',
                'ACC_OPEN_BALANCE',
                'ACC_DEBIT_BALANCE',
                'ACC_CREDIT_BALANCE',
                'ACC_CLOSE_BALANCE',
                'ACC_PROJECT_CODE',
                'ACC_RECALCULATE_INT',
                'ACC_KONSOL_INT'
      ];

}
