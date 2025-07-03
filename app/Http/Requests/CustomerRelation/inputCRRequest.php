<?php namespace App\Http\Requests\CustomerRelation;

use App\Http\Requests\Request;

class inputCRRequest extends Request {

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
			'CR_TITLE_CHAR'=>'required',
                        'CR_STATUS_CHAR'=>'required',
                        'CR_TYPE_INT'=>'required',
                        'CR_DESC'=>'required',
                        'CR_DIVISI_INT'=>'required',
                        'CR_INCIDENT_DTTIME'=>'required',
                        'CR_INCIDENT_DUE_DATE'=>'required'
		];
	}
        
         public function messages() 
        {
            return[
                   'CR_TITLE_CHAR.required' => 'Please input Title ',
                   'CR_STATUS_CHAR.required' => 'Please Select CR Status ',
                   'CR_TYPE_INT.required' => 'Please Select CR Type ',
                   'CR_DESC.required' => 'Please Input Desription ',
                   'CR_DIVISI_INT.required' => 'Please Select Divisi ',
                   'CR_INCIDENT_DTTIME.required' => 'Please Input Incident Date ',
                   'CR_INCIDENT_DUE_DATE.required' => 'Please Input Estimate Date ',
            ];
          
        }

}
