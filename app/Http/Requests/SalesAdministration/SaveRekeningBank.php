<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveRekeningBank extends Request {
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
                          'ACC_NAME_CHAR'=>'required',
                          'SA_REKENING_BANK_TRX_DATE'=>'required',
                          'SA_REKENING_BANK_AMOUNT'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'ACC_NAME_CHAR.required'=>'Bank A/C not Defined...',
                   'SA_REKENING_BANK_TRX_DATE.required'=>'Transaction Date not defined....',
                   'SA_REKENING_BANK_AMOUNT.required'=>'Rekening Amount not defined....',
            ];
          
        }

}
