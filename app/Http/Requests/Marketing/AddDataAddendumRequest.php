<?php namespace App\Http\Requests\Marketing;

use App\Http\Requests\Request;

class AddDataAddendumRequest extends Request {

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
			// 'LOT_STOCK_NO'=> 'required',
            'MD_TENANT_NAME_CHAR' => 'required',
            //'PSM_TRANS_TYPE'=> 'required',
            'SHOP_NAME_CHAR' => 'required',
            'PSM_TRANS_START_DATE' => 'required',
            'PSM_TRANS_END_DATE' => 'required',
            'PSM_TRANS_DP_PERSEN' => 'required',
            'PSM_TRANS_TIME_PERIOD_SCHED' => 'required',
            // 'PSM_TRANS_RENT_NUM'=>'required',
            // 'PSM_TRANS_SC_NUM'=>'required',
//            'PSM_TRANS_DEPOSIT_MONTH'=>'required',
            //'PSM_TRANS_DEPOSIT_TYPE'=>'required',
            //'PSM_TRANS_DESCRIPTION'=>'required',
//            'PSM_TRANS_GRASS_PERIOD'=>'required',
            'PSM_CATEGORY_NAME'=>'required',
            'PSM_TRANS_DESCRIPTION'=>'required'
        ];
	}

         public function messages()
        {
            return [
                // 'LOT_STOCK_NO.required' => 'Lot Number Not Defined...',
                'MD_TENANT_NAME_CHAR.max' => 'Tenant Not Defined...',
                //'PSM_TRANS_TYPE.max' => 'Type Not Defined...',
                'SHOP_NAME_CHAR.max' => 'Shop Name Not Defined...',
                'PSM_TRANS_START_DATE.max' => 'Start Date Not Defined...',
                'PSM_TRANS_END_DATE.max' => 'Start Date Not Defined...',
                'PSM_TRANS_DP_PERSEN.max' => 'DP Persen Not Defined...',
                'PSM_TRANS_TIME_PERIOD_SCHED.max' => 'Period Sched Not Defined...',
                // 'PSM_TRANS_RENT_NUM'=>'Rent Amount Not Defined...',
                // 'PSM_TRANS_SC_NUM'=>'Service Charge Not Defined...',
//                'PSM_TRANS_DEPOSIT_MONTH'=>'Deposito Month Not Defined...',
                //'PSM_TRANS_DEPOSIT_TYPE'=>'Deposito Type Not Defined...',
                //'PSM_TRANS_DESCRIPTION'=>'Desription Payment Not Defined...',
//                'PSM_TRANS_GRASS_PERIOD'=>'Grass Period Not Defined...',
                'PSM_CATEGORY_NAME'=>'Shop Type Not Defined...',
                'PSM_TRANS_DESCRIPTION'=>'Description Payment Not Defined...',
            ];
    }
}
