<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveBillingBuyBackRequest extends Request {

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
			'PAID_BILL_AMOUNT_NUM'=>'required',
                        'TGL_PEMBAYARAN_DATE'=>'required',
                        'backdate'=>'required',
                        'statusBlokir'=>'required'
		];
	}
         public function messages() 
        {
            return[
                   'PAID_BILL_AMOUNT_NUM.required'=>'Please Input Payment Input Field',
                   'TGL_PEMBAYARAN_DATE.required'=>'Please Input Transaction Date Input Field',
                   'backdate.required'=>'Cannot Created Journal Back Date...',
                   'statusBlokir.required'=>'You Cant Input Nominal Bigger Than Total Payment',
            ];
          
        }

}
