<?php namespace App\Http\Requests\CustomerRelation;

use App\Http\Requests\Request;

class updateCRRequest extends Request {

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
			'CR_UPDATE_INC_DATE'=>'required',
                        'CR_UPDATE_DUE_DATE'=>'required',
                        'CR_UPDATE_DESC'=>'required',
		];
	}
        
         public function messages() 
        {
            return[
                   'CR_UPDATE_INC_DATE.required' => 'Please input Progress Date ',
                   'CR_UPDATE_DUE_DATE.required' => 'Estimate/Solved Progress ',
                   'CR_UPDATE_DESC.required' => 'Please Input Solution ',
            ];
          
        }

}
