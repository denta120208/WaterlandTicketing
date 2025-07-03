<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class PaymentPlanDetailRequest extends Request {

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
			'PAYMENT_CODE_NO'=>'required',
                        'KODE_CASH_CHAR'=>'required',
                        'TRX_TYPE_CODE'=>'required',
                        'FREQ_NUM'=>'required',
                        'INTERVAL_NUM'=>'required',
                        'AMOUNT_FIX_INT'=>'required'
                    
		];
	}
        
         public function messages()
         {
             return[
                  'PAYMENT_CODE_NO.required'=>'Code required',
                  'KODE_CASH_CHAR.required'=>'Description required',
                  'TRX_TYPE_CODE.required'=>'Trx Type required',
                  'FREQ_NUM.required'=>'Freq required',
                  'INTERVAL_NUM.required'=>'Interval required',
                  'AMOUNT_FIX_INT.required'=>'Amount required'
             ];
             
         }

}
