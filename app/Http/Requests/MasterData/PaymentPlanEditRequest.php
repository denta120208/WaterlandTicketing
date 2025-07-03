<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class PaymentPlanEditRequest extends Request {

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
			
                        'PAYMENT_PLAN_DESC_CHAR'=>'required'
		];
	}
        
        public function messages()
         {
             return[
                   'PAYMENT_PLAN_DESC_CHAR.required' => 'Payment Plan Name required ',
             ];
             
         }

}
