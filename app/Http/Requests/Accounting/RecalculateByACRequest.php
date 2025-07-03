<?php namespace App\Http\Requests\Accounting;

use App\Http\Requests\Request;

class RecalculateByACRequest extends Request {

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
			'ACC_NO_CHAR'=>'required',
		];
	}
        
        public function messages() 
        {
            return[
                   'ACC_NO_CHAR.required'=>'Please Select No. A/C....'
            ];  
        }

}
