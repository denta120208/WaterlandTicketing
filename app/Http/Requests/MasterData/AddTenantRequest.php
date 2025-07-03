<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class AddTenantRequest extends Request {

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
			'MD_TENANT_NAME_CHAR'=> 'required',
            'MD_TENANT_ADDRESS1' => 'required',
            'MD_TENANT_CITY_CHAR' => 'required',
            'MD_TENANT_POSCODE' => 'required',
            'MD_TENANT_TELP' => 'required',
            'MD_TENANT_EMAIL' => 'required',
            'MD_TENANT_DIRECTOR' => 'required',
            'MD_TENANT_NPWP'=>'required',
//            'MD_VENDOR_BANK_NAME'=>'required',
//            'MD_VENDOR_BANK_LOCATION'=>'required',
//            'MD_VENDOR_BANK_ACCOUNT'=>'required',
//            'MD_VENDOR_BANK_ACCOUNT_NAME'=>'required'
        ];
	}

         public function messages()
        {
            return [
                    'MD_TENANT_NAME_CHAR.required' => 'Company Not Defined...',
                    'MD_TENANT_ADDRESS1.max' => 'Address Not Defined...',
                    'MD_TENANT_CITY_CHAR.max' => 'City Not Defined...',
                    'MD_TENANT_POSCODE.max' => 'PostCode Not Defined...',
                    'MD_TENANT_TELP.max' => 'Telp Not Defined...',
                    'MD_TENANT_EMAIL.max' => 'Email Not Defined...',
                    'MD_TENANT_DIRECTOR.max' => 'Owner/PIC Company Not Defined...',
                    'MD_TENANT_NPWP'=>'NPWP Not Defined...',
//                    'MD_VENDOR_BANK_NAME'=>'Bank Account Name Not Defined...',
//                    'MD_VENDOR_BANK_LOCATION'=>'Bank Location Name Not Defined...',
//                    'MD_VENDOR_BANK_ACCOUNT'=>'Bank Account Number Not Defined...',
//                    'MD_VENDOR_BANK_ACCOUNT_NAME'=>'Bank Account Owners Name'
            ];
    }

}
