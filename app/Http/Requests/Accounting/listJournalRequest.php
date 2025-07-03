<?php namespace App\Http\Requests\Accounting;

use App\Http\Requests\Request;

class listJournalRequest extends Request {

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
            'edDate'=>'required',
            'ACC_SOURCECODE_DESC_CHAR'=>'required',
		];
	}

        public function messages()
        {
            return[
                   'stDate.required'=>'Please Select Start Date',
                   'edDate.required'=>'Please Select End Date',
                   'ACC_SOURCECODE_DESC_CHAR.required'=>'Please Select Source Code'
            ];
        }

}
