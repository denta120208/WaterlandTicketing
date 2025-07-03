<?php namespace App\Http\Requests\SPK;

use App\Http\Requests\Request;

class addDataVoRequest extends Request {

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
                    'SPK_VO_TYPE_INT'=>'required',
                    'SPK_VO_TRX_DATE'=>'required',
                    'SPK_VO_DPP'=>'required',
//                    'SPK_VO_PPN'=>'required',
                    'SPK_VO_TOTAL'=>'required',
                    'SPK_VO_JNS_PEKERJAAN'=>'required',
                    'SPK_VO_SYARAT_LAIN'=>'required',
                    'SPK_VO_NOTES'=>'required',
                    
		];
	}
        
         public function messages() 
        {
            return[
                   'SPK_VO_TYPE_INT.required'=>'Variant Type Not Defined...',
                   'SPK_VO_TRX_DATE.required'=>'Transaction Date Not Defined...',
                   'SPK_VO_DPP.required'=>'DPP VO Not Defined...',
//                   'SPK_VO_PPN.required'=>'PPN VO  Not Defined...',
                   'SPK_VO_TOTAL.required'=>'Total VO  Not Defined...',
                   'SPK_VO_JNS_PEKERJAAN.required'=>'Job Description Not Defined...',
                   'SPK_VO_SYARAT_LAIN.required'=>'Other Requirements Not Defined...',
                   'SPK_VO_NOTES.required'=>'CC Not Defined...'
            ];
          
        }

}
