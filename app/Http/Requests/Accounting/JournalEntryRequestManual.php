<?php namespace App\Http\Requests\Accounting;

use App\Http\Requests\Request;

class JournalEntryRequestManual extends Request {

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
            'ACC_NO_CHAR'=>'required',
            'ACC_DEBIT'=>'required',
            'ACC_CREDIT'=>'required',
		];
	}

         public function messages()
        {
            return[
                'backdate.required'=>'Cannot Created Journal Back Date...',
                'ACC_SOURCODE_DESC_CHAR.required'=>'Please Choose Transaction Type',
                'ACC_NO_CHAR.required'=>'Please Choose No. A/C',
                'ACC_DEBIT.required'=>'Please Input Debit',
                'ACC_CREDIT.required'=>'Please Input Credit'
            ];

        }

}
