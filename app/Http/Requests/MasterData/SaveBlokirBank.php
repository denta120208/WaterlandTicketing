<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class SaveBlokirBank extends Request {
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
                          'MD_BLOKIR_DESC_CHAR'=>'required',
		];
	}
        
        public function messages() 
        {
            return[
                   'MD_BLOKIR_NAME_CHAR.required'=>'Blokir Name Not Define....',
                   'MD_BLOKIR_DESC_CHAR.required'=>'Description not defined....'
            ];
          
        }

}
