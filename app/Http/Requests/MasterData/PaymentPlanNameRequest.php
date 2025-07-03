<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class PaymentPlanNameRequest extends Request {

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
			'PAYMENT_PLAN_CODE_CHAR'=>'required|unique:MD_PAYMENTPLAN_HEADER',
                        'PAYMENT_PLAN_NAME_CHAR'=>'required'
		];
	}
        
        public function messages()
         {
             return[
                   'PAYMENT_PLAN_CODE_CHAR.required' => 'Payment Plan Code required ',
                   'PAYMENT_PLAN_CODE_CHAR.unique' => 'Payment Plan Code has already been taken and registered',
                   'PAYMENT_PLAN_NAME_CHAR.required' => 'Payment Plan Name required ',
             ];
             
         }

}
