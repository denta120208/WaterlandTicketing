<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class NUPPayRequest extends Request {

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
			'BOOKING_FEE_NUM'=>'required'
		];
	}
        
         public function messages() 
        {
            return[
                   'BOOKING_FEE_NUM.required' => 'Please input NUP Fee ',
            ];
          
        }

}
