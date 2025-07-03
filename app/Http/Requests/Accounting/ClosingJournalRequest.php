<?php namespace App\Http\Requests\Accounting;

use App\Http\Requests\Request;

class ClosingJournalRequest extends Request {

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
			'close_period'=>'required',
		];
	}
        
        public function messages() 
        {
            return[
                   'close_period.required'=>'This is the last month of year, you can not do closing on this step....'
            ];  
        }

}
