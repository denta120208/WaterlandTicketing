<?php namespace App\Http\Requests\Marketing;

use App\Http\Requests\Request;

class AddConfirmationLetterRequest extends Request {

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
            'MD_TENANT_NAME_CHAR' => 'required',
            'SHOP_NAME_CHAR' => 'required',
            'SKS_TRANS_START_DATE' => 'required',
            'SKS_TRANS_END_DATE' => 'required',
            'SKS_TRANS_DP_PERSEN' => 'required',
            'SKS_TRANS_TIME_PERIOD_SCHED' => 'required',
            'SKS_TRANS_RENT_NUM'=>'required',
            'SKS_TRANS_SC_NUM'=>'required',
            //'SKS_DEPOSIT_MONTH'=>'required',
//            'SKS_DEPOSIT_TYPE'=>'required',
//            'SKS_TRANS_DESCRIPTION'=>'required'
        ];
	}

    public function messages()
    {
        return [
                'LOT_STOCK_NO.required' => 'Lot Number Not Defined...',
                'MD_TENANT_NAME_CHAR.max' => 'Tenant Not Defined...',
                'SHOP_NAME_CHAR.max' => 'Shop Name Not Defined...',
                'SKS_TRANS_START_DATE.max' => 'Start Date Not Defined...',
                'SKS_TRANS_END_DATE.max' => 'Start Date Not Defined...',
                'SKS_TRANS_DP_PERSEN.max' => 'DP Persen Not Defined...',
                'SKS_TRANS_TIME_PERIOD_SCHED.max' => 'Period Sched Not Defined...',
                'SKS_TRANS_RENT_NUM'=>'Rent Amount Not Defined...',
                'SKS_TRANS_SC_NUM'=>'Service Charge Not Defined...',
                //'SKS_DEPOSIT_MONTH'=>'Deposito Month Not Defined...',
//                'SKS_DEPOSIT_TYPE'=>'Deposito Type Not Defined...',
//                'SKS_TRANS_DESCRIPTION'=>'Desription Payment Not Defined...',
        ];
    }

}
