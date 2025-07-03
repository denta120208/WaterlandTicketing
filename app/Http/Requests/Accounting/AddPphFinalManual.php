<?php namespace App\Http\Requests\Accounting;

use App\Http\Requests\Request;

class AddPphFinalManual extends Request {

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
                        'TRX_PPH_DATE'=>'required',
                        'TRX_PPH_AMOUNT_INT'=>'required',
                        'ACC_JOURNAL_NOCHAR'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                    'KODE_STOK_UNIQUE_ID_CHAR.required'=>'Unit Not Define....',
                    'TRX_PPH_DATE.required'=>'Transaction Date Not Define...',
                    'TRX_PPH_AMOUNT_INT.required'=>'Amount Not Define...',
                    'ACC_JOURNAL_NOCHAR.required'=>'No. Voucher Not Define...'
            ];  
        }

}
