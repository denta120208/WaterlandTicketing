<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveChangedSchedule extends Request {

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
                          'TGL_SCHEDULE_CHANGED_DTTIME'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'SLIP_NO_CHAR.required'=>'No. Booking Entry not defined,please Choose Booking Entry',
                   'CUSTOMER_NAME_CHAR.required'=>'Customer not defined....',
                   'KODE_STOK_UNIQUE_ID_CHAR.required'=>'No. Unit not defined....',
                   'TGL_SCHEDULE_CHANGED_DTTIME.required'=>'Date Changed not defined,please Choose Date Changed',
            ];
          
        }

}
