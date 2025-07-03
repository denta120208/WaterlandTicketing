<?php namespace App\Http\Requests\masterdata;

use App\Http\Requests\Request;

class RequestEditCustValidation extends Request {

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
                    'NAME_CHAR' => 'required|max:60',
                    'NO_KTP_CHAR' => 'required|min:10|max:30',
                    //'NPWP_CHAR'   => 'max:16',
                    //'NATIONALITY_CHAR' => 'max:3',
                    'TELEPHONE_NO_CHAR' => 'max:20',
                    'HANDPHONE_CHAR' => 'required|min:10|max:20',
                    'HANDPHONE2_CHAR' => 'max:20',
                    'FAX_NO_CHAR' => 'max:20',
                    'KODE_POS_CHAR' => 'max:10',
                    'ADDRESS1_CHAR' => 'max:100',
                    'MAIL_ADDRESS_CHAR' => 'max:60',
		];
	}
        public function messages()
        {
            return [
                    'NAME_CHAR.required' => 'Name required',
                    'NAME_CHAR.max' => 'Name max 60 character',
                    'NO_KTP_CHAR.max' => 'ID Number max 30 character ',
                    'NO_KTP_CHAR.min' => 'ID Number min 10 character ',
                    'NO_KTP_CHAR.required' => 'ID Number required ',
                    //'NPWP_CHAR.max'   => 'NPWP Max 16 Character',
                    //'NATIONALITY_CHAR.max' => 'Nationality Max 3 Character',
                    'TELEPHONE_NO_CHAR.max' => 'Telephone Number Max 20 Character',
                    'HANDPHONE_CHAR.max' => 'Handphone Number Max 20 Character',
                    'HANDPHONE_CHAR.min' => 'Handphone Number Min 10 Character',
                    'HANDPHONE_CHAR.required' => 'Handphone Number required',
                    'HANDPHONE2_CHAR.max' => 'Handphone 2 Number Max 20 Character',
                    'FAX_NO_CHAR.max' => 'Facsimile Number Max 20 Character',
                    'KODE_POS_CHAR.max' => 'Postal Code Max 10 Character',
                    'ADDRESS1_CHAR.max' => 'Address Max 100 Character',
                    'MAIL_ADDRESS_CHAR.max' => 'Mail Address Max 60 Character',
                    
                    
            ];
    }

}
