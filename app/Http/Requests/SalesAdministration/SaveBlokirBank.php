<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveAkadKredit extends Request {
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
                          'BANK_NAME_CHAR'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'SLIP_NO_CHAR.required'=>'No. Booking Entry not defined,please Choose Booking Entry',
                   'CUSTOMER_NAME_CHAR.required'=>'Customer not defined....',
                   'BANK_NAME_CHAR.required'=>'Bank not defined....',
            ];
          
        }

}
