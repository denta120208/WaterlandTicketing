<?php namespace App\Http\Requests\SPK;

use App\Http\Requests\Request;

class addDataTenderRequest extends Request {

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
                    'SPK_TENDER_NAME_CHAR'=>'required',
                    'SPK_TENDER_NOMINAL_INT'=>'required',
                    'SPK_QTY_WIN'=>'required'
		];
	}
        
         public function messages() 
        {
            return[
                   'SPK_TENDER_NAME_CHAR.required'=>'Tender Name Not Defined...',
                   'SPK_TENDER_NOMINAL_INT.required'=>'Nominal Not Defined...',
                   'SPK_QTY_WIN.required'=>'Nominal Not Defined...'
            ];
          
        }

}
