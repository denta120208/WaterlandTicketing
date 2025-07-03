<?php namespace App\Http\Requests\AccountReceivable;

use App\Http\Requests\Request;

class prosesUploadDownloadVARequest extends Request {
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
                          'transaction'=>'required'
		];
	}

        public function messages()
        {
            return[
                   'transaction.required'=>'Transaction Not Defined'
            ];

        }

}
