<?php namespace App\Http\Requests\BTP;

use App\Http\Requests\Request;

class addDataPOPAJBRequest extends Request {

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
                        'CR_DIVISI_NAME'=>'required',
                        'POP_TRX_DATE'=>'required',
                        'POP_TRX_PERKIRAAN'=>'required',
                        'POP_AMOUNT_INT'=>'required',
                        'CR_DIVISI_NAME'=>'required',
//                        'POP_DIMOHON_CHAR'=>'required',
//                        'POP_DIKETAHUI_CHAR'=>'required',
//                        'POP_DISETUJUI_CHAR'=>'required',
		];
	}

         public function messages()
        {
            return[
                   'CR_DIVISI_NAME.required' => 'POP Other - Please Select Divisi Name ',
                   'POP_TRX_DATE.required' => 'POP Other - Please Select Trans Date',
                   'POP_TRX_PERKIRAAN.required' => 'POP Other - Please Select Estimasi Date',
                   'POP_AMOUNT_INT.required' => 'POP Other - Please Input Amount Date',
                   'CR_DIVISI_NAME.required' => 'POP Other - Please Input Division',
//                   'POP_DIMOHON_CHAR.required' => 'AJB - Please Input Request By',
//                   'POP_DIKETAHUI_CHAR.required' => 'AJB - Please Input Signed By',
//                   'POP_DISETUJUI_CHAR.required' => 'AJB - Please Input Approve By'
            ];

        }

}
