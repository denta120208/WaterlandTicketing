<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class ApprovalRequest extends Request {

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
			'pass_approval' => 'required',
		];
	}
        
         public function messages() 
        {
            return[
                   'pass_approval.required' => 'Please input password',
            ];
          
        }

}
