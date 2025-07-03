<?php namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class SuratPeringatanManual extends Request {
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
                          'SA_SP_KETERANGAN'=>'required',
                          'TGL_SP_DATE'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'SA_SP_KETERANGAN.required'=>'Keterangan Not Defined...',
                   'TGL_SP_DATE.required'=>'Tanggal Surat Peringatan not defined....'
            ];
          
        }

}
