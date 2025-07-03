<?php namespace App\Http\Requests\PRPO;

use App\Http\Requests\Request;

class SavePRSubCategory extends Request {

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
			'PR_SUB_CATEGORY_NAME'=>'required',
            'UNIT_NAME'=>'required'
		];
	}
         public function messages() 
        {
            return[
                   'PR_SUB_CATEGORY_NAME.required'=>'Please Input Sub Category',
                   'UNIT_NAME.required'=>'Please Input UOM'
            ];
          
        }

}
