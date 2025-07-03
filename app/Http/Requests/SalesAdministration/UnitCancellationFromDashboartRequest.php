<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class UnitCancellationFromDashboartRequest extends Request {

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
//			'ON_SOLD_STATUS'=>'required',
//                        'ON_RESERVE_STATUS'=>'required',
                        'ID_UNIT_APARTMENT_INT'=>'required',
		];
	}
        
         public function messages() 
        {
            return[
//                   'ON_SOLD_STATUS.required'=>'Unit Sold !......please choose another AVAILABLE unit',
                   'ID_UNIT_APARTMENT_INT.required'=>'No selected unit,please choose one unit',
//                   'ON_RESERVE_STATUS.required'=>'Unit reserved !.....,please choose another FREE unit'
            ];
          
        }

}
