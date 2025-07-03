<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class BookingEntryRequestRev extends Request {

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
                          'TOTAL_PRICE'=>'required',
                          'TIME_PERIOD'=>'required',
                          'CUSTOMER_NAME_CHAR'=>'required',
                          'SALES_EXEC_CHAR'=>'required',
                          'BILLING_GENERATE'=>'required',
                          'SERAH_UNIT_ESTIMASI_DATE'=>'required'
                          
		];
	}
        
        public function messages() 
        {
            return[
                   'TOTAL_PRICE.required'=>'Price not defined,please Select sales Type and Tax Scheme',
                   'TIME_PERIOD.required'=>'Please Input Period',
                   'CUSTOMER_NAME_CHAR.required'=>'Please Input Customer Data',
                   'SALES_EXEC_CHAR.required'=>'Please Input Sales Data',
                   'BILLING_GENERATE.required'=>'Please Select Auto Generate',
                   'SERAH_UNIT_ESTIMASI_DATE.required'=>'Please Select Estimasi Date',
            ];
          
        }

}
