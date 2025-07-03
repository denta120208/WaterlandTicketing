<?php namespace App\Http\Requests\LotMaster;

use App\Http\Requests\Request;

class addDataLotMaster extends Request {

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
            'LOT_STOCK_NO'=>'required',
            'LOT_ZONE_DESC'=>'required',
            'LOT_TYPE_DESC'=>'required',
            'LOT_LEVEL_DESC'=>'required',
            'UNIT_NAME'=>'required',
            'LOT_STOCK_SQM'=>'required'
		];
	}

	public function messages()
    {
        return[
           'LOT_STOCK_NO.required' => 'Please Input Lot Number ',
           'LOT_ZONE_DESC.required' => 'Please Select Zone',
           'LOT_TYPE_DESC.required' => 'Please Select Type',
           'LOT_LEVEL_DESC.required' => 'Please Select Level',
           'UNIT_NAME.required' => 'Please Select UOM',
           'LOT_STOCK_SQM.required' => 'Please Input Sqm number'
        ];
    }

}
