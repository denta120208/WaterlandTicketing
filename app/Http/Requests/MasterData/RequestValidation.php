<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class RequestValidation extends Request {

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
	 *'NO_KTP_CHAR' => 'required|min:15|max:25|unique:MD_CUSTOMER',
                    'NPWP_CHAR'   => 'max:16|unique:MD_CUSTOMER',
                    'NATIONALITY_CHAR' => 'max:3',
                    'TELEPHONE_NO_CHAR' => 'max:20',
                   
                    'HANDPHONE2_CHAR' => 'max:20',
                    'FAX_NO_CHAR' => 'max:20',
                    'KODE_POS_CHAR' => 'max:10',
                    'ADDRESS1_CHAR' => 'required|max:100',
         * 'NO_KTP_CHAR.max' => 'ID Number max 25 character ',
                    'NO_KTP_CHAR.min' => 'ID Number min 15 character ',
                    'NO_KTP_CHAR.required' => 'ID Number required ',
                    'NO_KTP_CHAR.unique' => 'ID Number has already been taken and registered',
                    'NPWP_CHAR.max'   => 'NPWP Max 16 Character',
                    'NPWP_CHAR.unique'   => 'NPWP has already been taken',
                    'NATIONALITY_CHAR.max' => 'Nationality Max 3 Character',
                    'TELEPHONE_NO_CHAR.max' => 'Telephone Number Max 20 Character',
                   
                    'HANDPHONE2_CHAR.max' => 'Handphone 2 Number Max 20 Character',
                    'FAX_NO_CHAR.max' => 'Facsimile Number Max 20 Character',
                    'KODE_POS_CHAR.max' => 'Postal Code Max 10 Character',
                    'ADDRESS1_CHAR.max' => 'Address Max 100 Character',
                    'MAIL_ADDRESS_CHAR.max' => 'Mail Address Max 60 Character',
                    'ADDRESS1_CHAR.required' => 'Address Required',
	 * @return array
         * |unique:MD_CUSTOMER
         * |unique:MD_CUSTOMER
	 */
	public function rules()
	{
		return [
                    
                    'NAME_CHAR' => 'required|max:60',
                    'HANDPHONE_CHAR' => 'required|min:10|max:20',
                    'NO_KTP_CHAR' => 'required|min:10|max:30',
                    'ADDRESS1_CHAR' => 'required',
                    //'RTRW_CHAR'=>'required',
                    'KOTA_CHAR'=>'required',
                    'CORR_ADDRESS'=>'required',
                    'KELKEC_CHAR'=>'required',
                    'KOTAKODEPOS_CHAR'=>'required',
                    'HANDPHONE_CHAR'=>'required',
                    'BIRTHPLACE_CHAR'=>'required',
                    'BIRTHDATE_DTTIME'=>'required',
		];
	}
        
        public function messages()
        {
            return [
                    
                    'NAME_CHAR.required' => 'Name required',
                    'NAME_CHAR.max' => 'Name max 60 character',
                    'HANDPHONE_CHAR.max' => 'Handphone Number Max 20 Character',
                    'HANDPHONE_CHAR.min' => 'Handphone Number Min 10 Character',
                    'HANDPHONE_CHAR.required' => 'Handphone Number required',
//                    'HANDPHONE_CHAR.unique' => 'Handphone Number has already been taken',
                    'NO_KTP_CHAR.min' => 'ID Number min 10 character ',
                    'NO_KTP_CHAR.max' => 'ID Number max 30 character ',
                    'NO_KTP_CHAR.required' => 'ID Number required ',
//                    'NO_KTP_CHAR.unique' => 'ID Number has already been taken and registered',
                    'ADDRESS1_CHAR.required' => 'Address Required',
                    'ADDRESS1_CHAR.max' => 'Address Max 100 Character',
                    //'RTRW_CHAR.required'=> 'RT/RW Required',
                    //'RTRW_CHAR.max'=> 'RT/RW Maks 9 Character',
                    'KOTA_CHAR.required'=>'Kota Required',
                    'CORR_ADDRESS.required'=>'Correspondence Required',
                    'KELKEC_CHAR.required'=>'Kelurahan/Kecamatan required',
                    'KOTAKODEPOS_CHAR.required'=>'Kota/Kode Pos required',
                    'HANDPHONE_CHAR.required'=>'Handphone number required',
                    'BIRTHPLACE_CHAR.required'=>'Birthplace required',
                    'BIRTHDATE_DTTIME.required'=>'Birthdate required',
                
                    
            ];
    }

}
