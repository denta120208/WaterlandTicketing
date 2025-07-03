<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class StockUnitReqValEdit extends Request {

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
//			 'BLOK_NO_CHAR'=>'required|max:5',
//                         'KAVLING_NO_CHAR'=>'required|max:5',
//                         'NO_UNIT_CHAR'=>'required|max:5',
                       'LUAS_TANAH_DEC'=>'required|max:25',
                       'RATE_TANAH_NUM'=>'required|max:25',
		];
	}
        
         public function messages()
         {
             return[
//                  'BLOK_NO_CHAR.required' => 'Blok Number required',
//                  'BLOK_NO_CHAR.max' => 'Blok Number max 5 char',
//                  'KAVLING_NO_CHAR.required' => 'Kavling Number required',
//                  'KAVLING_NO_CHAR.max' => 'Kavling Number max 5 char',
//                  'NO_UNIT_CHAR.required' => 'Unit Number required',
//                  'NO_UNIT_CHAR.max' => 'Unit Number max 5 char',
                  'LUAS_TANAH_DEC.required' => 'Land Area required',
                  'LUAS_TANAH_DEC.max' => 'Land Area max 25 char',
                  'RATE_TANAH_NUM.required'=> 'Rate Tanah required',
                  'RATE_TANAH_NUM.max'=> 'Rate Tanah max 25 char',
             ];
             
         }

}
