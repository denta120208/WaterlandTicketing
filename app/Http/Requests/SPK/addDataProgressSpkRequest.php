<?php namespace App\Http\Requests\SPK;

use App\Http\Requests\Request;

class addDataProgressSpkRequest extends Request {

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
                    'TrxDate'=>'required',
                    'MD_TERMIN_DESC_CHAR'=>'required',
                    'MD_TERMIN_PROGRESS_INT'=>'required',
                    'MD_TERMIN_BAYAR_INT'=>'required',
                    'SPK_SERTIFIKAT_PAYMENT'=>'required',
//                    'SPK_SERTIFIKAT_DPP'=>'required',
//                    'SPK_SERTFIKAT_SIGN1'=>'required',
//                    'SPK_SERTFIKAT_SIGN1_POSITION'=>'required',
//                    'SPK_SERTFIKAT_SIGN2'=>'required',
//                    'SPK_SERTFIKAT_SIGN2_POSITION'=>'required',
//                    'SPK_SERTFIKAT_SIGN3'=>'required',
//                    'SPK_SERTFIKAT_SIGN3_POSITION'=>'required',
//                    'SPK_SERTFIKAT_SIGN4'=>'required',
//                    'SPK_SERTFIKAT_SIGN4_POSITION'=>'required',
//                    'SPK_SERTFIKAT_SIGN5'=>'required',
//                    'SPK_SERTFIKAT_SIGN5_POSITION'=>'required',
//                    'SPK_SERTFIKAT_SIGN6'=>'required',
//                    'SPK_SERTFIKAT_SIGN6_POSITION'=>'required',
                    'SPK_SERTIFIKAT_DENDA_INT'=>'required',
                    'statusProses'=>'required'
		];
	}
        
        public function messages() 
        {
            return[
                   'TrxDate.required'=>'Transaction Not Defined...',
                   'MD_TERMIN_DESC_CHAR.required'=>'Trx Type Not Defined...',
                   'MD_TERMIN_PROGRESS_INT.required'=>'Prog(%) Not Defined...',
                   'MD_TERMIN_BAYAR_INT.required'=>'Pay(%) Not Defined...',
                   'SPK_SERTIFIKAT_PAYMENT.required'=>'Payment Not Defined...',
//                   'SPK_SERTIFIKAT_DPP.required'=>'DPP Not Defined...',
//                   'SPK_SERTFIKAT_SIGN1.required'=>'Sign 1 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN1_POSITION.required'=>'Sign 1 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN2.required'=>'Sign 2 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN2_POSITION.required'=>'Sign 2 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN3.required'=>'Sign 3 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN3_POSITION.required'=>'Sign 3 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN4.required'=>'Sign 4 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN4_POSITION.required'=>'Sign 4 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN5.required'=>'Sign 5 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN5_POSITION.required'=>'Sign 5 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN6.required'=>'Sign 6 Not Defined...',
//                   'SPK_SERTFIKAT_SIGN6_POSITION.required'=>'Sign 6 Not Defined...',
                   'SPK_SERTIFIKAT_DENDA_INT.required'=>'Denda Value Not Defined...',
                   'statusProses.required'=>'Your DPP Bigger Than SPK Value....',
            ];
          
        }

}
