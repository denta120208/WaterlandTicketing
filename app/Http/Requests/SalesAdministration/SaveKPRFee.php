<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveKPRFee extends Request {
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
              'SA_KPR_FEE_BANK_ADRESS'=>'required',
              'SA_KPR_FEE_BANK_NAME'=>'required',
              'SA_KPR_FEE_REQUEST_BANK_DATE'=>'required',
              'SA_KPR_FEE_BANK_REKENING'=>'required',
              'SA_KPR_FEE_BANK_LOCATION'=>'required',
              'SA_KPR_FEE_BANK_CITY'=>'required'
		];
	}
        
    public function messages()
    {
        return[
               'KODE_STOK_UNIQUE_ID_CHAR.required'=>'Unit not Defined...',
               'SA_KPR_FEE_BANK_ADRESS.required'=>'Bank Address not defined....',
               'SA_KPR_FEE_BANK_NAME.required'=>'Bank Name not defined....',
               'SA_KPR_FEE_REQUEST_BANK_DATE.required'=>'Transaction Date not defined....',
               'SA_KPR_FEE_BANK_REKENING.required'=>'Bank Account Number not defined....',
               'SA_KPR_FEE_BANK_LOCATION.required'=>'Bank Location not defined....',
               'SA_KPR_FEE_BANK_CITY.required'=>'Bank Location City not defined....'
        ];
    }

}
