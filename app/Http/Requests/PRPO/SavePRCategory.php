<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SavePRCategory extends Request {

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
            //'UNIT_CODE'=>'required'
		];
	}
         public function messages() 
        {
            return[
                   'PR_CATEGORY_NAME_CHAR.required'=>'Please Input Category',
                   //'UNIT_CODE.required'=>'Please Input UOM'
            ];
          
        }

}
