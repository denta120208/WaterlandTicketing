<?php namespace App\Http\Requests\AccountReceivable;

use App\Http\Requests\Request;

class AddDataCreditNotes extends Request {
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
			//'LOT_STOCK_NO'=> 'required',
            'CN_TRANS_TRX_DATE' => 'required',
            'INVOICE_TRANS_TYPE_DESC' => 'required',
            'CN_TRANS_DESC' => 'required',
            'CN_TRANS_AMOUNT' => 'required',
            'MD_TENANT_NAME_CHAR'=> 'required',
        ];
	}

    public function messages()
    {
        return [
            //'LOT_STOCK_NO.required' => 'Lot Not Defined...',
            'CN_TRANS_TRX_DATE.required' => 'Transaction Date Not Defined...',
            'INVOICE_TRANS_TYPE_DESC.required' => 'Invoice Type Not Defined...',
            'CN_TRANS_DESC.required' => 'Description Not Defined...',
            'CN_TRANS_AMOUNT.required' => 'Amount Not Defined...',
            'MD_TENANT_NAME_CHAR.required' => 'Tenant Not Defined...'
        ];
    }
}
