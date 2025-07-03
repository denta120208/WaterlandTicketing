<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SavePaidKPRFee extends Request {
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
              'backdate'=>'required',
              'ACC_NAME_CHAR'=>'required'
		];
	}
        
    public function messages()
    {
        return[
               'backdate.required'=>'Cannot Create Transaction Back Date....',
               'ACC_NAME_CHAR.required'=>'Rekening Payment not defined....'
        ];
    }

}
