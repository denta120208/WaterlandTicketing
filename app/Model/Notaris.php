<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notaris extends Model {

      
      protected $table = 'MD_NOTARIS';
      protected $primaryKey = 'NOTARIS_ID_INT';
        
      protected $fillable =[
                'NOTARIS_ID_INT',
                'NOTARIS_NAME'
      ];

}
