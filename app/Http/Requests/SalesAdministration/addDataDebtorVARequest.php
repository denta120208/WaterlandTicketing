<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class addDataDebtorVARequest extends Request {
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
                          'VA_NUMBER'=>'required',
                          'MD_VA_BANK_CODE_CHAR'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'KODE_STOK_UNIQUE_ID_CHAR.required'=>'Unit Not Defined',
                   'VA_NUMBER.required'=>'Va Number Not Defined....',
                   'MD_VA_BANK_CODE_CHAR.required'=>'Bank Code Not Defined....'
            ];
          
        }

}
