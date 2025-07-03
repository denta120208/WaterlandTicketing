<?php namespace App\Http\Requests\AccountPayable;

use App\Http\Requests\Request;

class VoucherRequest extends Request {
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'ACC_NAME_CHAR'=>'required',
            //'ACC_NAME_CHAR_TAX'=>'required',
            'inv_date' => ['required'],
//            'COM_1' => ['required'],
//            'COM_2' => ['required'],
//            'COM_3' => ['required'],
//            'COM_4' => ['required'],
//            'COM_5' => ['required'],
//            'COM_6' => ['required'],
//            'COM_7' => ['required'],
//            'COM_8' => ['required'],
//            'COM_9' => ['required'],
//            'COM_10' => ['required'],
//            'COM_11' => ['required'],
//            'COM_12' => ['required'],
//            'COM_13' => ['required'],
//            'COM_14' => ['required'],
            'COM_IN_2' => ['required_if:COM_2,true'],
            'COM_IN_3' => ['required_if:COM_3,true']
        ];
    }
    public function messages()
    {
        return [
            'ACC_NAME_CHAR.required'=>'Please Input A/C Name',
            //'ACC_NAME_CHAR_TAX.required'=>'Please Input A/C Name (Tax)',
            'inv_date.required' => 'Tanggal Penagihan wajib diisi',
//            'COM_1.required' => 'Surat Pengantar wajib disertakan',
//            'COM_2.required' => 'Kwitansi(Bermaterai) wajib disertakan',
//            'COM_3.required' => 'Faktur Pajak wajib disertakan',
//            'COM_4.required' => 'Fotocpy SIUJK wajib disertakan',
//            'COM_5.required' => 'BA Penagihan wajib disertakan',
//            'COM_6.required' => 'BA Pemeriksaan Lapangan wajib disertakan',
//            'COM_7.required' => 'BA ST wajib disertakan',
//            'COM_8.required' => 'Surat Pernyataan wajib disertakan',
//            'COM_9.required' => 'Siteplan (Mapping) wajib disertakan',
//            'COM_10.required' => 'Dokumentasi (Foto) wajib disertakan',
//            'COM_11.required' => 'Fotocopy SPK wajib disertakan',
//            'COM_12.required' => 'Fotocopy SKPK wajib disertakan',
//            'COM_13.required' => 'Progres Pekerjaan Lapangan wajib disertakan',
//            'COM_14.required' => 'Fotocopy Ijin Pentahapan Kerja wajib disertakan',
            'COM_IN_2.required_if' => 'Nomor Invoice Wajib Diisi',
            'COM_IN_3.required_if' => 'Nomor Faktur Pajak Wajib Diisi'
        ];
    }
}
