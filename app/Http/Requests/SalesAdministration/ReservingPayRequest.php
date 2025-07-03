<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class ReservingPayRequest extends Request {

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
			'RESERVING_FEE_NUM' =>'required',
		];
	}
        
         public function messages() 
        {
            return[
                   'RESERVING_FEE_NUM.required' => 'Please input Reserving Fee ',
            ];
          
        }

}
