<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class EditBookingEntryRequest extends Request {

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
			 'TOTAL_PRICE'=>'required',
                         'TIME_PERIOD'=>'required'
		];
	}
        
         public function messages() 
        {
            return[
                   'TOTAL_PRICE.required'=>'Price not defined, please select Sales Type',
                   'TIME_PERIOD.required'=>'Please Input Period'
            ];
          
        }

}
