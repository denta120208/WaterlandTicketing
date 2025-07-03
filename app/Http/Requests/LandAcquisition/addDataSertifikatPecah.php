<?php namespace App\Http\Requests\LandAcquisition;

use App\Http\Requests\Request;

class addDataSertifikatPecah extends Request {

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
                    'KODE_STOK_UNIQUE_ID_CHAR'=>'required',
                    'LA_SERTIFIKAT_INDUK_NOCHAR'=>'required',
                    'LA_SERTIFIKAT_PECAH_POSITION'=>'required'   
		];
	}
        
         public function messages() 
        {
            return[
                   'KODE_STOK_UNIQUE_ID_CHAR.required'=>'Unit Not Defined...',
                   'LA_SERTIFIKAT_INDUK_NOCHAR.required'=>'Sertifikat Induk Not Defined...',
                   'LA_SERTIFIKAT_PECAH_POSITION.required'=>'Posisi Sertifikat  Not Defined...'
            ];
          
        }

}
