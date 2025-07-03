<?php namespace App\Http\Requests\SPK;

use App\Http\Requests\Request;

class spkListRequest extends Request {

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
                    'stDate'=>'required',
                    'enDate'=>'required',
                    'status_type'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'stDate.required'=>'Start Date Not Defined...',
                   'enDate.required'=>'End Date Not Defined...',
                   'status_type.required'=>'Status Not Defined...'
            ];
          
        }

}
