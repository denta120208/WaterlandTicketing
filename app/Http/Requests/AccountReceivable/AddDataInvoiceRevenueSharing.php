<?php namespace App\Http\Requests\AccountReceivable;

use App\Http\Requests\Request;

class AddDataInvoiceRevenueSharing extends Request {
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
			'LOT_STOCK_NO'=> 'required',
            'TGL_SCHEDULE_DATE' => 'required',
            'INVOICE_TRANS_TYPE_DESC' => 'required',
            'INVOICE_TRANS_DESC_CHAR' => 'required',
            'INVOICE_TRANS_TOTAL' => 'required',
        ];
	}

    public function messages()
    {
        return [
            'LOT_STOCK_NO.required' => 'Lot Not Defined...',
            'TGL_SCHEDULE_DATE.required' => 'Transaction Date Not Defined...',
            'INVOICE_TRANS_TYPE_DESC.required' => 'Invoice Type Not Defined...',
            'INVOICE_TRANS_DESC_CHAR.required' => 'Description Not Defined...',
            'INVOICE_TRANS_TOTAL.required' => 'Amount Not Defined...'
        ];
    }
}
