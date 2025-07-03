<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SaveGeneratePO extends Request {

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
            'CR_DIVISI_NAME'=>'required',
		    'MD_VENDOR_NAME_CHAR'=>'required',
            'PO_TYPE_NAME'=>'required',
//            'PO_TRANS_TERMS_INT'=>'required',
            'PO_TRANS_DESC'=>'required'
		];
	}
    public function messages()
    {
        return[
            'CR_DIVISI_NAME.required'=>'Please Choose Division',
            'MD_VENDOR_NAME_CHAR.required'=>'Please Choose Supplier',
            'PO_TYPE_NAME.required'=>'Please Choose Type',
//            'PO_TRANS_TERMS_INT.required'=>'Please Input Terms PO',
            'PO_TRANS_DESC.required'=>'Please Input Description'
        ];
    }
}
