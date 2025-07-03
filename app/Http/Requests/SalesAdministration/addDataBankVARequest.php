<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class addDataBankVARequest extends Request {
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
                          'MD_VA_BANK_CODE_CHAR'=>'required',
                          'MD_VA_BANK_DESC_CHAR'=>'required',
                          'MD_VA_BANK_NOREK'=>'required',
                          'ACC_NO_CHAR'=>'required',
                          'ACC_NAME_CHAR'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'MD_VA_BANK_CODE_CHAR.required'=>'Bank Code Not Defined',
                   'MD_VA_BANK_DESC_CHAR.required'=>'Bank Name Not Defined....',
                   'MD_VA_BANK_NOREK.required'=>'Bank Rekening Not Defined....',
                   'ACC_NO_CHAR.required'=>'No. A/C Not Defined....',
                   'ACC_NAME_CHAR.required'=>'A/C Name Not Defined....'
            ];
          
        }

}
