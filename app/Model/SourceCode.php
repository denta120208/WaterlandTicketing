<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SourceCode extends Model {

	 protected $table = 'ACC_SOURCODE';
         protected $primaryKey = 'ACC_SOURCODE_ID';
         protected $fillable =[
                            'ACC_SOURCODE_ID',
                            'ACC_SOURCODE_CHAR',
                            'ACC_SOURCODE_DESC_CHAR',
                            'ACC_SOURCODE_TYPE',
                            'ACC_SOURCODE_COUNTER',
                            'PROJECT_NO_CHAR'
                        ];
}
