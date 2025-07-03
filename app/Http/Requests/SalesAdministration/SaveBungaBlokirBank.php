<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveBungaBlokirBank extends Request {
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
                          'SA_BUNGA_BANK_TRX_DATE'=>'required',
                          'SA_BUNGA_BANK_DEPOSITO_AMT'=>'required',
                          'SA_BUNGA_BANK_ESCROW_AMT'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'ACC_NAME_CHAR.required'=>'Bank A/C not Defined...',
                   'SA_BUNGA_BANK_TRX_DATE.required'=>'Transaction Date not defined....',
                   'SA_BUNGA_BANK_DEPOSITO_AMT.required'=>'Deposito Amount not defined....',
                   'SA_BUNGA_BANK_ESCROW_AMT.required'=>'Escrow Amount not defined....'
            ];
          
        }

}
