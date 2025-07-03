<?php namespace App\Http\Requests\BTP;

use App\Http\Requests\Request;

class addDataBPURequest extends Request {

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
                        'BT_CHEQUE_POSITION'=>'required',
                        'BT_CATEGORY_ID_INT'=>'required',
                        'BT_DOC_TYPE'=>'required',
			            'CR_DIVISI_INT'=>'required',
                        'MD_VENDOR_ID_INT'=>'required',
                        'BT_TRANS_DESC'=>'required'
		];
	}

         public function messages()
        {
            return[
                   'BT_CHEQUE_POSITION.required' => 'BT - Please Select Cheque Position ',
                   'BT_CATEGORY_ID_INT.required' => 'BT - Please Select Category',
                   'BT_DOC_TYPE.required' => 'BT - Please Select Document Type ',
                   'CR_DIVISI_INT.required' => 'BT - Please Select Division ',
                   'MD_VENDOR_ID_INT.required' => 'BT - Please Select Supplier ',
                   'BT_TRANS_DESC.required' => 'BT - Please input Description '
            ];

        }

}
