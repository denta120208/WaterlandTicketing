<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class StockUnitReqVal extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
                    'NOUNIT_CHAR'=>'required',
                    'JENIS_UNIT_CHAR'=>'required',
                    'DESC_VIEW_APT'=>'required',
                    'TOWER_CHAR'=>'required',
                    'IS_HOEK'=>'required',
                    'ON_SOLD_STAT_INT'=>'required',
                    'ON_RELEASE_STAT_INT'=>'urequired',
                    'LUAS_UNIT_FLOAT'=>'required',
                    'LUAS_BANGUNAN_FLOAT'=>'required'
                        
		];
	}
        
         public function messages()
         {
             return[
                  'NOUNIT_CHAR.required' => 'No Unit Apartemen required',
                  'JENIS_UNIT_CHAR.required' => 'Unit Type required',
                  'TOWER_CHAR.required' => 'Tower / Cluster required',
                  'IS_HOEK.required' => 'Release Status required',
                  'ON_SOLD_STAT_INT.required' => 'Availability Status required',
                  'ON_RELEASE_STAT_INT.required' => 'Release Status required',
                  'LUAS_UNIT_FLOAT.required'=>'Luas Tanah required',
                  'LUAS_BANGUNAN_FLOAT.required'=>'Luas Bangunan required'
             ];
             
         }

}
