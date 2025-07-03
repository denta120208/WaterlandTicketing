<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class SaveDataBank extends Request {

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
			'BANK_NAME_CHAR'=> 'required',
                        'BANK_ACTIVE_INT' => 'required',
		];
	}
        
         public function messages()
        {
            return [
                    'BANK_NAME_CHAR.required' => 'Bank Name Not Defined...',
                    'BANK_ACTIVE_INT.max' => 'Please Select Bank Status...',  
            ];
    }

}
