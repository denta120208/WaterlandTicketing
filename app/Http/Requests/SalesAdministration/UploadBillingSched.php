<?php

namespace App\Http\Requests\SalesAdministration;

use App\Http\Requests\Request;

class UploadBillingSched extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'sheet'=>'required',
            'KODE_STOK_UNIQUE_ID_CHAR'=>'required'
            
        ];
    }

    public function messages() {
        return [
            'sheet.required'=>'Please Upload Spreadsheet file',
            'KODE_STOK_UNIQUE_ID_CHAR.required'=>'Please Select Kode Unit',
        ];
    }

}
