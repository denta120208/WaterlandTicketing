<?php namespace App\Http\Requests\Marketing;

use App\Http\Requests\Request;

class AddDataRevenueSharingRequest extends Request {

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
			'PSM_RS_START_DATE'=> 'required',
            'PSM_RS_END_DATE' => 'required',
            'PSM_RS_LOW_NUM' => 'required',
            'PSM_RS_LOW_RATE' => 'required',
            'PSM_RS_HIGH_NUM' => 'required',
            'PSM_RS_HIGH_RATE' => 'required',
            'PSM_RS_MIN_AMT'=>'required'
        ];
	}

    public function messages()
    {
        return [
            'PSM_RS_START_DATE.required' => 'RS Start Date Not Defined...',
            'PSM_RS_END_DATE.required' => 'RS End Date Not Defined...',
            'PSM_RS_LOW_NUM.required' => 'Low Amount Not Defined...',
            'PSM_RS_LOW_RATE.required' => 'Low Rate (%) Not Defined...',
            'PSM_RS_HIGH_NUM.required' => 'High Amount Not Defined...',
            'PSM_RS_HIGH_RATE.required' => 'High Rate (%) Not Defined...',
            'PSM_RS_MIN_AMT.required' => 'Minimum Amount Charge Not Defined...',
        ];
    }
}
