<?php namespace App\Http\Requests\AccountReceivable;

use App\Http\Requests\Request;

class AddDataInvoicePayment extends Request {
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
			'TGL_BAYAR_DATE'=> 'required',
            'ACC_NAME_CHAR' => 'required',
            'PAYMENT_METHOD' => 'required'
            // 'PAYMENT_STAMP' => 'required'
        ];
	}

    public function messages()
    {
        return [
            'TGL_BAYAR_DATE.required' => 'Transaction Date Not Defined...',
            'ACC_NAME_CHAR.max' => 'Account Payment Not Defined...',
            'PAYMENT_METHOD.max' => 'Payment Method Name Not Defined...'
            // 'PAYMENT_STAMP.required' => 'Payment Stamp Name Not Defined...'
        ];
    }
}
