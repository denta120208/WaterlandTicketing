<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SaveGeneratePR extends Request {

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
		    'PR_CATEGORY_NAME_CHAR'=>'required',
            'PR_TRANS_DESC'=>'required',
            'PR_REQUIRED_DATE'=>'required'
		];
	}
    public function messages()
    {
        return[
            'PR_CATEGORY_NAME_CHAR.required'=>'Please Input Category',
            'PR_TRANS_DESC.required'=>'Please Input Description',
            'PR_REQUIRED_DATE.required'=>'Please Input Required Date'
        ];
    }
}
