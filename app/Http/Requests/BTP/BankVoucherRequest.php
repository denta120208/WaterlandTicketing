<?php namespace App\Http\Requests\BTP;

use App\Http\Requests\Request;

class BankVoucherRequest extends Request {

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
            'ACC_SOURCODE_DESC_CHAR'=>'required',
            'BV_DESC'=>'required',
		];
	}

         public function messages()
        {
            return[
                'backdate.required'=>'Cannot Created Journal Back Date...',
                'ACC_SOURCODE_DESC_CHAR.required'=>'Please Choose Transaction Type',
                'BV_DESC.required'=>'Please input Description',
            ];

        }

}
