<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SaveBlokirUnit extends Request {
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
                          'MD_BLOKIR_NAME_CHAR'=>'required',
		];
	}
        
        public function messages() 
        {
            return[
                   'MD_BLOKIR_NAME_CHAR.required'=>'Blokir Name Not Defined...',
            ];
          
        }

}
