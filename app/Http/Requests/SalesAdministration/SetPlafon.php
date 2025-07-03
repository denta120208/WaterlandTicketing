<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SetPlafon extends Request {

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
			'PLAFON_AMOUNT_INT'=>'required',
                        'ACC_NAME_CHAR'=>'required',
                        'backdate'=>'required',
                        'TGL_AKAD_DATE'=>'required',
                        'PLAFON_DATE'=>'required',
                        'BANK_LOCATION_CHAR'=>'required'
		];
	}
        
         public function messages() 
        {
            return[
                   'PLAFON_AMOUNT_INT.required' => 'Please input Plafon Amount ',
                   'ACC_NAME_CHAR.required' => 'Please input Rekening Payment ',
                   'backdate.required' => 'Cannot create Journal Back Date.... ',
                   'TGL_AKAD_DATE.required' => 'Transaction Date Must be Set.... ',
                   'PLAFON_DATE.required' => 'Plafon Date Must be Set.... ',
                   'BANK_LOCATION_CHAR.required' => 'Bank (KCP) Must be Set.... ',
            ];
          
        }

}
