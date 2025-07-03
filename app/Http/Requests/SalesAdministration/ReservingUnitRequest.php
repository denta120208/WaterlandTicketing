<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class ReservingUnitRequest extends Request {

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
                        'KODE_STOK_UNIQUE_ID_CHAR'=>'required',
                        'RESERVING_DATE'=>'required',
                        'CUSTOMER_NAME'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'KODE_STOK_UNIQUE_ID_CHAR.required'=>'Please Input Unit Number',
                   'RESERVING_DATE.required'=>'Please Input Reserving Date',
                   'CUSTOMER_NAME.required'=>'Please Input Customer Name'
            ];
          
        }

}
