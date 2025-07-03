<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SaveSubmitPO extends Request {

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
		    'PR_TRANS_NOCHAR'=>'required',
		    'PR_TYPE'=>'required',
            'PO_TYPE_NAME'=>'required',
            'CR_DIVISI_NAME'=>'required',
            'PR_CATEGORY_NAME_CHAR'=>'required',
		];
	}
    public function messages()
    {
        return[
            'PR_TRANS_NOCHAR.required'=>'Please Input Document PR',
            'PR_TYPE.required'=>'Please Input PR Type',
            'PO_TYPE_NAME.required'=>'Please Input PO Type',
            'CR_DIVISI_NAME.required'=>'Please Input Divisi',
            'PR_CATEGORY_NAME_CHAR.required'=>'Please Input Category Name',
        ];
    }
}
