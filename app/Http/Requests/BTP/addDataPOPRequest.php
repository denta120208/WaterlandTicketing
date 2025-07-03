<?php namespace App\Http\Requests\BTP;

use App\Http\Requests\Request;

class addDataPOPRequest extends Request {

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
			'POP_TYPE'=>'required',
            'POP_TRX_DATE'=>'required',
            'POP_TRX_PERKIRAAN'=>'required',
            'POP_AMOUNT_INT'=>'required',
            'POP_URAIAN_TEXT'=>'required',
            'POP_TUJUAN_TEXT'=>'required',
            'CR_DIVISI_NAME'=>'required',
            'MD_VENDOR_ID_INT_POP'=>'required',
//                        'POP_DIMOHON_CHAR'=>'required',
//                        'POP_DIKETAHUI_CHAR'=>'required',
//                        'POP_DISETUJUI_CHAR'=>'required',
		];
	}

        public function messages()
        {
            return[
                   'POP_TYPE.required' => 'POP - Please Select POP Type ',
                   'POP_TRX_DATE.required' => 'POP - Please Select Trans Date',
                   'POP_TRX_PERKIRAAN.required' => 'POP - Please Select Estimasi Date',
                   'POP_AMOUNT_INT.required' => 'POP - Please Input Amount Date',
                   'POP_URAIAN_TEXT.required' => 'POP - Please input Desription Date',
                   'POP_TUJUAN_TEXT.required' => 'POP - Please Input Tujuan Date',
                   'CR_DIVISI_NAME.required' => 'POP - Please Input Division',
                   'MD_VENDOR_ID_INT_POP.required' => 'POP - Please Select Kontraktor',
//                   'POP_DIMOHON_CHAR.required' => 'POP - Please Input Request By',
//                   'POP_DIKETAHUI_CHAR.required' => 'POP - Please Input Signed By',
//                   'POP_DISETUJUI_CHAR.required' => 'POP - Please Input Approve By'
            ];

        }

}
