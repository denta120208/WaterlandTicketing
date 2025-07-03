<?php namespace App\Http\Requests\Marketing;

use App\Http\Requests\Request;

class AddDataSchedDiscountRequest extends Request {

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
			'PSM_TRANS_DISC_TYPE'=> 'required',
            'PSM_SCHED_DISC_AMT' => 'required',
        ];
	}

    public function messages()
    {
        return [
            'PSM_TRANS_DISC_TYPE.required' => 'Disc. Schedule Type Not Defined...',
            'PSM_SCHED_DISC_AMT.max' => 'Disc. Schedule Amount Not Defined...',
        ];
    }
}
