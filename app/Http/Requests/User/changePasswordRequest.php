<?php namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class changePasswordRequest extends Request {

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
			'current_password' => 'required',
                        'new_password' => 'required|string|min:6',
                        'retype_password' => 'required|string|min:6',
		];
	}
        
         public function messages() 
        {
            return[
                   'current_password.required' => 'Please Input Current Password',
                   'new_password.required' => 'New Password min 6 Character ',
                   'retype_password.required' => 'ReType Password min 6 Character  '
            ];
          
        }

}
