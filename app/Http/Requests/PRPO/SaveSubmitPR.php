<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SaveSubmitPR extends Request {

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
		];
	}
    public function messages()
    {
        return[
            'CR_DIVISI_NAME.required'=>'Please Input Divisi',
        ];
    }
}
