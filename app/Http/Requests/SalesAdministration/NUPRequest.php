<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class NUPRequest extends Request {

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
	 *'DESC_TYPE_CHAR_1'=>'required'
         *  'DESC_TYPE_CHAR_1.required' => 'Please Choose Minimal 1 NUP Type and Input NUP Type Description ',
	 * @return array
	 */
	public function rules()
	{
		return [
                    'NAME_CHAR'=>'required',
                    'ADDRESS1_CHAR'=>'required',
                    'NO_KTP_CHAR'=>'required',
                    'NPWP_CHAR'=>'required',
                    'HANDPHONE_CHAR'=>'required',
                    'EMAIL_ADDRESS_CHAR'=>'required',
                    'NAMA_BROKER_CHAR'=>'required',
                    'NOUNIT_CHAR1'=>'required',
                    'NOUNIT_CHAR2'=>'required',
                    'NOUNIT_CHAR3'=>'required'
                    //'sheet_payment'=>'required'
		];
	}

         public function messages()
        {
            return[
                    'NAME_CHAR.required'=>'Name is Not Define...!',
                    'ADDRESS1_CHAR.required'=>'Address is Not Define...!',
                    'NO_KTP_CHAR.required'=>'No. KTP is Not Define...!',
                    'NPWP_CHAR.required'=>'NPWP is Not Define...!',
                    'HANDPHONE_CHAR.required'=>'Handphone is Not Define...!',
                    'EMAIL_ADDRESS_CHAR.required'=>'Email is Not Define...!',
                    'NAMA_BROKER_CHAR.required'=>'Nama Agen/Sales is Not Define...!',
                    'NOUNIT_CHAR1.required'=>'Unit 1 is Not Define...!',
                    'NOUNIT_CHAR2.required'=>'Unit 2 is Not Define...!',
                    'NOUNIT_CHAR3.required'=>'Unit 3 is Not Define...!',
                    //'sheet_payment.required'=>'Attachment Payment Not Define...!'
            ];
          
        }

}
