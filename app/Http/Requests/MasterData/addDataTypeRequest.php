<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class addDataTypeRequest extends Request {

	public function authorize(){
		return true;
	}
        
	public function rules(){
		return [
                    'JENIS_UNIT_CHAR'=>'required',
                    'LUAS_UNIT_FLOAT'=>'required',
                    'LUAS_BANGUNAN_FLOAT'=>'required'
		];
	}
        
        public function messages(){
            return[
                   'JENIS_UNIT_CHAR.required' => 'Description Not Defined',
                   'LUAS_UNIT_FLOAT.required' => 'Building Not Defined',
                   'LUAS_BANGUNAN_FLOAT.required' => 'Land Not Defined'
            ];
        }
}
