<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveAddCostManual extends Request {
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
                          'SA_ADDCOST_TYPE_DESC_ADD_COST'=>'required',
                          'SA_ADDCOST_AMOUNT_INT_ADD_COST'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'SA_ADDCOST_TYPE_DESC_ADD_COST.required'=>'Trx Type Not Defined...',
                   'SA_ADDCOST_AMOUNT_INT_ADD_COST.required'=>'Amount not defined....'
            ];
          
        }

}
