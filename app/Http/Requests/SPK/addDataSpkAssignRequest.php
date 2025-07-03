<?php namespace App\Http\Requests\SPK;

use App\Http\Requests\Request;

class addDataSpkAssignRequest extends Request {

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
                    'SPK_ASSIGN_NAME'=>'required',
                    'SPK_ASSIGN_JOB_NAME'=>'required',
		];
	}
        
         public function messages() 
        {
            return[
                   'SPK_ASSIGN_NAME.required'=>'Name Not Defined...',
                   'SPK_ASSIGN_JOB_NAME.required'=>'Job Name Not Defined...'
            ];
          
        }

}
