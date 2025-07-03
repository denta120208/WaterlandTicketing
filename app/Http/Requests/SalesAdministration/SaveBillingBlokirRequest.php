<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveBillingBlokirRequest extends Request {

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
			'BLOKIR_PAYMENT'=>'required',
                        'TGL_REALISASI_DATE'=>'required',
                        'backdate'=>'required',
                        'ACC_NAME_CHAR'=>'required',
                        'statusBlokir'=>'required'
		];
	}
         public function messages() 
        {
            return[
                   'BLOKIR_PAYMENT.required'=>'Please Input Payment Input Field',
                   'TGL_REALISASI_DATE.required'=>'Please Input Transaction Date Input Field',
                   'backdate.required'=>'Cannot Created Journal Back Date...',
                   'ACC_NAME_CHAR.required'=>'Rekening Payment Must be Set...',
                   'statusBlokir.required'=>'You Cant Input Nominal Bigger Than Total Payment',
            ];
          
        }

}
