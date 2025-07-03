<?php namespace App\Http\Requests\LandAcquisition;

use App\Http\Requests\Request;

class addDataSertifikat extends Request {

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
                    'LA_SERTIFIKAT_DATE'=>'required',
                    'LA_SK_IJIN_LOKASI_NOCHAR'=>'required'
		];
	}
        
         public function messages() 
        {
            return[
                   'LA_SERTIFIKAT_DATE.required'=>'Tanggal Document Not Defined...',
                   'LA_SK_IJIN_LOKASI_NOCHAR.required'=>'No. Ijin Lokasi Not Defined...'
            ];
          
        }

}
