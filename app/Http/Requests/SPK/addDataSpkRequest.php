<?php namespace App\Http\Requests\SPK;

use App\Http\Requests\Request;

class addDataSpkRequest extends Request {

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
                    'TAX_SCHEME_CODE'=>'required',
                    'SPK_RETENSI_INT'=>'required',
                    'SPK_TRANS_TERMIN'=>'required',
                    'SPK_TYPE_NAME_CHAR'=>'required',
                    'SPK_TRANS_START_DATE'=>'required',
                    'SPK_TRANS_END_DATE'=>'required',
                    'SPK_TRANS_TRX_DATE'=>'required',
                    'SPK_TRANS_NAME'=>'required',
//                    'NAMA_PEKERJAAN'=>'required',
                    'SPK_TRANS_JNS_PEKERJAAN'=>'required',
                    'MD_VENDOR_NAME_CHAR'=>'required',
                    'CR_DIVISI_NAME'=>'required',
                    'CR_DIVISI_INT'=>'required'
		];
	}

         public function messages()
        {
            return[
                   'TAX_SCHEME_CODE.required'=>'Tax scheme  Not Defined...',
                   'SPK_RETENSI_INT.required'=>'Retensi Not Defined...',
                   'SPK_TRANS_TERMIN.required'=>'Termin Not Defined...',
                   'SPK_TYPE_NAME_CHAR.required'=>'SPK Type Not Defined...',
                   'SPK_TRANS_START_DATE.required'=>'Start Date Not Defined...',
                   'SPK_TRANS_END_DATE.required'=>'End Date Not Defined...',
                   'SPK_TRANS_TRX_DATE.required'=>'Transaction Date Not Defined...',
                   'SPK_TRANS_NAME.required'=>'Name Not Defined...',
//                   'NAMA_PEKERJAAN.required'=>'Budget Name Not Defined...',
                   'SPK_TRANS_JNS_PEKERJAAN.required'=>'Job Description Not Defined...',
                   'MD_VENDOR_NAME_CHAR.required'=>'Contractor Not Defined...',
                   'CR_DIVISI_NAME.required'=>'Divisi Not Defined...',
                   'CR_DIVISI_INT.required'=>'Divisi Not Defined...'
            ];

        }

}
