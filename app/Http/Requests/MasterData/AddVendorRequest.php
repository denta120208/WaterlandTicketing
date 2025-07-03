<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class AddVendorRequest extends Request {

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
			'MD_VENDOR_NAME_CHAR'=> 'required',
            'TYPE_VENDOR_NAME' => 'required',
            'MD_VENDOR_ADDRESS1' => 'required',
            'MD_VENDOR_CITY_CHAR' => 'required',
            'MD_VENDOR_POSCODE' => 'required',
            'MD_VENDOR_TELP' => 'required',
            'MD_VENDOR_EMAIL' => 'required',
            'MD_VENDOR_DIRECTOR' => 'required',
            'MD_VENDOR_NPWP'=>'required',
//            'MD_VENDOR_BANK_NAME'=>'required',
//            'MD_VENDOR_BANK_LOCATION'=>'required',
//            'MD_VENDOR_BANK_ACCOUNT'=>'required',
//            'MD_VENDOR_BANK_ACCOUNT_NAME'=>'required'
        ];
	}

         public function messages()
        {
            return [
                    'MD_VENDOR_NAME_CHAR.required' => 'Company Not Defined...',
                    'TYPE_VENDOR_NAME.max' => 'Type Not Defined...',
                    'MD_VENDOR_ADDRESS1.max' => 'Address Not Defined...',
                    'MD_VENDOR_CITY_CHAR.max' => 'City Not Defined...',
                    'MD_VENDOR_POSCODE.max' => 'PostCode Not Defined...',
                    'MD_VENDOR_TELP.max' => 'Telp Not Defined...',
                    'MD_VENDOR_EMAIL.max' => 'Email Not Defined...',
                    'MD_VENDOR_DIRECTOR.max' => 'Owner/PIC Company Not Defined...',
                    'MD_VENDOR_NPWP'=>'NPWP Not Defined...',
                    'MD_VENDOR_BANK_NAME'=>'Bank Account Name Not Defined...',
                    'MD_VENDOR_BANK_LOCATION'=>'Bank Location Name Not Defined...',
                    'MD_VENDOR_BANK_ACCOUNT'=>'Bank Account Number Not Defined...',
                    'MD_VENDOR_BANK_ACCOUNT_NAME'=>'Bank Account Owners Name'
            ];
    }

}
