<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveBillingRequest extends Request {

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
			'BILL_PAYMENT'=>'required',
                        'TGL_PEMBAYARAN_DATE'=>'required',
                        'backdate'=>'required',
                        'ACC_NAME_CHAR'=>'required'
		];
	}
         public function messages() 
        {
            return[
                   'BILL_PAYMENT.required'=>'Please Input Payment Input Field',
                   'TGL_PEMBAYARAN_DATE.required'=>'Please Input Transaction Date Input Field',
                   'backdate.required'=>'Cannot Created Journal Back Date...',
                   'ACC_NAME_CHAR.required'=>'Rekening Payment Must be Set...'
            ];
          
        }

}
