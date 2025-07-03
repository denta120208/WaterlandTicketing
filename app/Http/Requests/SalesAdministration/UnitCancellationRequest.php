<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class UnitCancellationRequest extends Request {

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
                          'ID_REASON_CODE_APT'=>'required',
                          'TGL_CANCELLATION_DTTIME'=>'required',
                          'IS_REFUND'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'ID_REASON_CODE_APT.required'=>'Reason not defined,please Select Reason Code',
                   'TGL_CANCELLATION_DTTIME.required'=>'Cancellation Date not defined,please Choose Cancellation date',
                   'IS_REFUND.required'=>'Refund not defined,please Select Refund',
            ];
          
        }

}
