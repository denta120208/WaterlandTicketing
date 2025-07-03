<?php namespace App\Http\Requests\Marketing;

use App\Http\Requests\Request;

class AddDataLOIRequest extends Request {

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
            'LOI_TRANS_OCCU_START_DATE' => 'required',
            'LOI_TRANS_OCCU_END_DATE' => 'required',
            'LOI_TRANS_DP_PERSEN' => 'required',
            'LOI_TRANS_TIME_PERIOD_SCHED' => 'required',
            'LOI_TRANS_RENT_NUM'=>'required',
            'LOI_TRANS_SC_NUM'=>'required',
            'PSM_CATEGORY_NAME'=>'required'
//            'LOI_TRANS_DEPOSIT_MONTH'=>'required',
//            'LOI_TRANS_DEPOSIT_TYPE'=>'required',
//            'LOI_TRANS_DESCRIPTION'=>'required'
        ];
	}

    public function messages()
        {
            return [
                'LOT_STOCK_NO.required' => 'Lot Number Not Defined...',
                'MD_TENANT_NAME_CHAR.max' => 'Tenant Not Defined...',
                'SHOP_NAME_CHAR.max' => 'Shop Name Not Defined...',
                'LOI_TRANS_OCCU_START_DATE.max' => 'Start Date Not Defined...',
                'LOI_TRANS_OCCU_END_DATE.max' => 'Start Date Not Defined...',
                'LOI_TRANS_DP_PERSEN.max' => 'DP Persen Not Defined...',
                'LOI_TRANS_TIME_PERIOD_SCHED.max' => 'Period Sched Not Defined...',
                'LOI_TRANS_RENT_NUM'=>'Rent Amount Not Defined...',
                'LOI_TRANS_SC_NUM'=>'Service Charge Not Defined...',
                'PSM_CATEGORY_NAME'=>'Tenant Category Not Defined...',
//                'LOI_TRANS_DEPOSIT_MONTH'=>'Deposito Month Not Defined...',
//                'LOI_TRANS_DEPOSIT_TYPE'=>'Deposito Type Not Defined...',
//                'LOI_TRANS_DESCRIPTION'=>'Desription Payment Not Defined...',
            ];
    }
}
