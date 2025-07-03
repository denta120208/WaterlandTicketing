<?php namespace App\Http\Requests\Accounting;

use App\Http\Requests\Request;

class AddDataUsePBK extends Request {

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
			'KODE_STOK_UNIQUE_ID_CHAR1'=>'required',
                        'TGL_TRX_PBK_DATE'=>'required',
                        'TAX_PBK_USE_DESC'=>'required',
                        'TAX_PBK_USE_AMOUNT'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                    'KODE_STOK_UNIQUE_ID_CHAR.required'=>'Unit Not Define....',
                    'TGL_TRX_PBK_DATE.required'=>'Transaction Date Not Define...',
                    'TAX_PBK_USE_AMOUNT.required'=>'Amount Use Not Define...',
                    'TAX_PBK_USE_DESC.required'=>'Description Not Define...'
            ];  
        }

}
