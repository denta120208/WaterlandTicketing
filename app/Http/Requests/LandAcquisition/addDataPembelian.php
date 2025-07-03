<?php namespace App\Http\Requests\LandAcquisition;

use App\Http\Requests\Request;

class addDataPembelian extends Request {

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
                    'LA_TRANS_TRX_DATE'=>'required',
                    'LA_DESA_NAME_CHAR'=>'required',
                    'LA_SK_IJIN_LOKASI_NOCHAR'=>'required',
                    'LA_TRANS_LUAS_TANAH_SURAT'=>'required',
                    'LA_TRANS_LUAS_TANAH_SURAT_STATUS'=>'required',
                    'LA_TRANS_LUAS_TANAH_BAYAR'=>'required',
                    'LA_TRANS_LUAS_TANAH_BAYAR_STATUS'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'LA_TRANS_TRX_DATE.required'=>'Transaction Date Not Defined...',
                   'LA_DESA_NAME_CHAR.required'=>'Desa Not Defined...',
                   'LA_SK_IJIN_LOKASI_NOCHAR.required'=>'Ijin Lokasi Not Defined...',
                   'LA_TRANS_LUAS_TANAH_SURAT.required'=>'Luas Tanah Surat Not Defined...',
                   'LA_TRANS_LUAS_TANAH_SURAT_STATUS.required'=>'Tidak bisa 0 pada Luas Tanah (Surat) m2',
                   'LA_TRANS_LUAS_TANAH_BAYAR.required'=>'Luas Tanah Dibayar Not Defined...',
                   'LA_TRANS_LUAS_TANAH_BAYAR_STATUS.required'=>'Tidak bisa 0 pada Luas Tanah Dibayar'
            ];
          
        }

}
