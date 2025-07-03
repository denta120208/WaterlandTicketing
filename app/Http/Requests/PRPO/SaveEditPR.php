<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SaveEditPR extends Request {

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
            'PR_TRANS_DESC'=>'required'
		];
	}
    public function messages()
    {
        return[
            'PR_TRANS_DESC.required'=>'Please Input Description'
        ];
    }
}
