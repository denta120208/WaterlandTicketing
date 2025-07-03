<?php namespace App\Http\Requests\LandAcquisition;

use App\Http\Requests\Request;

class addDataSuratIjin extends Request {

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
                    'LA_SK_TGL_PENDAFTARAN'=>'required',
                    'LA_SK_PERMOHONAN_NOCHAR'=>'required',
                    'LA_SK_TGL_TANDA_TERIMA'=>'required',
                    'LA_SK_TANDA_TERIMA_NOCHAR'=>'required',
                    'LA_SK_LOKASI'=>'required',
                    'LA_DESA_NAME_CHAR'=>'required',
                    'LA_DESA_KECAMATAN'=>'required',
                    'LA_DESA_KABUPATEN'=>'required'
		];
	}
        
         public function messages() 
        {
            return[
                   'LA_SK_TGL_PENDAFTARAN.required'=>'Tanggal Pendaftaran Not Defined...',
                   'LA_SK_PERMOHONAN_NOCHAR.required'=>'No Permohonan Not Defined...',
                   'LA_SK_TGL_TANDA_TERIMA.required'=>'Tanggal Tanda Terima Not Defined...',
                   'LA_SK_TANDA_TERIMA_NOCHAR.required'=>'No Tanda Terima Type Not Defined...',
                   'LA_SK_LOKASI.required'=>'Lokasi Not Defined...',
                   'LA_DESA_NAME_CHAR.required'=>'Nama Desa Not Defined...',
                   'LA_DESA_KECAMATAN.required'=>'Kecamatan Date Not Defined...',
                   'LA_DESA_KABUPATEN.required'=>'Kabupaten Not Defined...'
            ];
          
        }

}
