<?php namespace App\Http\Requests\MasterData;

use App\Http\Requests\Request;

class BrokerAddRequest extends Request {

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
			'NAMA_BROKER_CHAR'=> 'required|max:50',
//                        'HP_CHAR' => 'required|min:10|max:15|unique:MD_BROKER',
		];
	}
        
         public function messages()
        {
            return [
                    'NAMA_BROKER_CHAR.required' => 'Name required',
                    'NAMA_BROKER_CHAR.max' => 'Name max 60 character',
//                    'HP_CHAR.max' => 'Handphone Number Max 15 Character',
//                    'HP_CHAR.min' => 'Handphone Number Min 10 Character',
//                    'HP_CHAR.required' => 'Handphone Number required',
//                    'HP_CHAR.unique' => 'Handphone Number has already been taken',
                    
                   
            ];
    }

}
