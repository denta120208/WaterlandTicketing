<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveChangedCustName extends Request {

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
                          'SLIP_NO_CHAR'=>'required',
                          'CUSTOMER_NAME_CHAR'=>'required',
                          'KODE_STOK_UNIQUE_ID_CHAR'=>'required',
                          'NEW_CUSTOMER_CHAR'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'SLIP_NO_CHAR.required'=>'No. Booking Entry not defined,please Choose Booking Entry',
                   'CUSTOMER_NAME_CHAR.required'=>'Customer not defined....',
                   'KODE_STOK_UNIQUE_ID_CHAR.required'=>'No. Unit not defined....',
                   'NEW_CUSTOMER_CHAR.required'=>'New Customer Must be Set...',
            ];
          
        }

}
