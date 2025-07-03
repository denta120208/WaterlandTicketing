<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SaveGenerateGR extends Request {

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
		    'GR_TRX_DATE'=>'required',
            'GR_DTL_EMAIL1'=>'required',
            'GR_NOTE'=>'required'
		];
	}
    public function messages()
    {
        return[
            'GR_TRX_DATE.required'=>'Please Choose Transaction Date',
            'GR_DTL_EMAIL1.required'=>'Please Input Notification Email 1',
            'GR_NOTE.required'=>'Please Input Good Receive Notes'
        ];
    }
}
