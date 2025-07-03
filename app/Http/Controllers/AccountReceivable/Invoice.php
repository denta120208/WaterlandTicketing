<?php

namespace App\Http\Controllers\AccountReceivable;

use App\Model\Company;
use App\Model\Counter;
use App\Model\Divisi;
use App\Model\GlTrans;
use App\Model\Journal;
use App\Model\ProjectModel;
use App\Model\RabDT;
use App\Model\RabHD;
use App\Model\SuratPerintahKerja\SpkAssign;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel;

use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\BTP;
use Carbon\Carbon;
use View;
use Session;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\LogActivity\LogActivityController;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use \PDF;
use Response;

use App\Http\Controllers\Util\utilArray;
use App\Http\Controllers\Util\utilConverter;
use App\Http\Controllers\Util\utilGenerator;
use Illuminate\Support\Str;

define("ERROR_ROUTE_KWT_INV", "invoice.listdatainvoice");

class Invoice extends Controller {

    public function listGenerateInvoice(){
        $project_no = session('current_project');
        $dataGenerateInv = 0;
        $dateCutOff = '';

        return View::make('page.accountreceivable.listDataGenerateInv',
            ['project_no'=>$project_no,'dataGenerateInv'=>$dataGenerateInv,
             'dateCutOff'=>$dateCutOff]);
    }

    public function listTapingInvoice(){
        $project_no = session('current_project');
        
        $dataGenerateInv = 0;
        $dateCutOff = '';

        return View::make('page.accountreceivable.listDataTapingInv',
            ['project_no'=>$project_no,'dataGenerateInv'=>$dataGenerateInv,
                'dateCutOff'=>$dateCutOff]);
    }

    public function listTapingInvoiceSchedule(){
        $project_no = session('current_project');

        $tapSchedRental = DB::Select("Select a.PSM_SCHEDULE_ID_INT,c.MD_TENANT_NAME_CHAR,b.SHOP_NAME_CHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                               a.DESC_CHAR,(CASE WHEN c.MD_TENANT_PPH_INT = 1 THEN 'Potong Tenant' ELSE 'Potong Sendiri' END) as MD_TENANT_PPH_INT,
                                               ISNULL(a.BASE_AMOUNT_NUM,0) as BASE_AMOUNT_NUM,ISNULL(a.DISC_NUM,0) as DISC_NUM,
                                              (ISNULL(a.BASE_AMOUNT_NUM,0) - ISNULL(a.DISC_NUM,0)) as DPP_AMOUNT,a.TRX_CODE,
                                              CAST(round(isnull(((ISNULL(a.BASE_AMOUNT_NUM,0) - ISNULL(a.DISC_NUM,0))*d.PPNBM_NUM),0),0) as numeric(18,0)) as PPN_PRICE_NUM,
                                              CAST(round(((ISNULL(a.BASE_AMOUNT_NUM,0) - ISNULL(a.DISC_NUM,0)) + isnull(((ISNULL(a.BASE_AMOUNT_NUM,0) - ISNULL(a.DISC_NUM,0))*d.PPNBM_NUM),0)),0) as numeric(18,0)) as BILL_AMOUNT
                                        from PSM_SCHEDULE as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                        INNER JOIN MD_TENANT as c ON b.MD_TENANT_ID_INT = c.MD_TENANT_ID_INT
                                        INNER JOIN MD_PROJECT as d ON a.PROJECT_NO_CHAR = d.PROJECT_NO_CHAR
                                        where a.IS_TAPING_INV = 1
                                        AND a.PROJECT_NO_CHAR = '".$project_no."'
                                        and a.IS_GENERATE_INV = 0");

        return View::make('page.accountreceivable.listDataTapingInvSchedule',
            ['project_no'=>$project_no,'tapSchedRental'=>$tapSchedRental]);
    }

    public function viewListGenerateInvoice(Request $request){
        $project_no = session('current_project');

        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataGenerateInv = 1;
        $dateCreate = date_create($request->cutOffDate);
        $dateCutOff = date_format($dateCreate,'d M Y');

        $dataCatgory = $request->category;

        if($dataCatgory == 'Rental')
        {
            $dataInvoice = DB::select("exec sp_invoice_rent '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'SecurityDeposit')
        {
            $dataInvoice = DB::select("exec sp_invoice_secure_dep '".$request->cutOffDate."','".$project_no."'");
        }
        elseif($dataCatgory == 'ServiceCharge')
        {
            $dataInvoice = DB::select("exec sp_invoice_sc '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Utility')
        {
            $dataInvoice = DB::select("exec sp_invoice_utility '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'CasualLeasing')
        {
            $dataInvoice = DB::select("exec sp_invoice_casual '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Others')
        {
            $dataInvoice = DB::select("exec sp_invoice_others '".$request->cutOffDate."','".$project_no."'");
        }
         
        return View::make('page.accountreceivable.listDataGenerateInv',
            ['project_no'=>$project_no,'dataGenerateInv'=>$dataGenerateInv,'dataProject'=>$dataProject,
            'dateCutOff'=>$dateCutOff,'dateCutOffReal'=>$request->cutOffDate,'dataInvoice'=>$dataInvoice,'dataCatgory'=>$dataCatgory,
            'cutoff'=>$request->cutOffDate]);
    }

    public function viewListTapingInvoice(Request $request){
        $project_no = session('current_project');

        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataGenerateInv = 1;
        $dateCreate = date_create($request->cutOffDate);
        $dateCutOff = date_format($dateCreate,'d M Y');

        $dataCatgory = $request->category;

        if($dataCatgory == 'Rental')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_rent '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'SecurityDeposit')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_secure_dep '".$request->cutOffDate."','".$project_no."'");
        }
        elseif($dataCatgory == 'ServiceCharge')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_sc '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Utility')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_utility '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'CasualLeasing')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_casual '".$request->cutOffDate."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Others')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_others '".$request->cutOffDate."','".$project_no."'");
        }

        return View::make('page.accountreceivable.listDataTapingInv',
            ['project_no'=>$project_no,'dataGenerateInv'=>$dataGenerateInv,'dataProject'=>$dataProject,
                'dateCutOff'=>$dateCutOff,'dateCutOffReal'=>$request->cutOffDate,'dataInvoice'=>$dataInvoice,'dataCatgory'=>$dataCatgory,
                'cutoff'=>$request->cutOffDate]);
    }

    public function viewGetListGenerateInvoice($cut_off, $type){
        $project_no = session('current_project');

        $cut_off = base64_decode($cut_off, TRUE);
        $type = base64_decode($type, TRUE);

        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataGenerateInv = 1;
        $dateCreate = date_create($cut_off);
        $dateCutOff = date_format($dateCreate,'d M Y');

        $dataCatgory = $type;

        if($dataCatgory == 'Rental')
        {
            $dataInvoice = DB::select("exec sp_invoice_rent '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'SecurityDeposit')
        {
            $dataInvoice = DB::select("exec sp_invoice_secure_dep '".$cut_off."','".$project_no."'");
        }
        elseif($dataCatgory == 'ServiceCharge')
        {
            $dataInvoice = DB::select("exec sp_invoice_sc '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Utility')
        {
            $dataInvoice = DB::select("exec sp_invoice_utility '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'CasualLeasing')
        {
            $dataInvoice = DB::select("exec sp_invoice_casual '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Others')
        {
            $dataInvoice = DB::select("exec sp_invoice_others '".$cut_off."','".$project_no."'");
        }

        return View::make('page.accountreceivable.listDataGenerateInv',
            ['project_no'=>$project_no,'dataGenerateInv'=>$dataGenerateInv,'dataProject'=>$dataProject,
                'dateCutOff'=>$dateCutOff,'dateCutOffReal'=>$cut_off,'dataInvoice'=>$dataInvoice,'dataCatgory'=>$dataCatgory,
                'cutoff'=>$cut_off]);
    }

    public function viewGetListTapingInvoice($cut_off, $type){
        $project_no = session('current_project');

        $cut_off = base64_decode($cut_off, TRUE);
        $type = base64_decode($type, TRUE);

        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataGenerateInv = 1;
        $dateCreate = date_create($cut_off);
        $dateCutOff = date_format($dateCreate,'d M Y');

        $dataCatgory = $type;

        if($dataCatgory == 'Rental')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_rent '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'SecurityDeposit')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_secure_dep '".$cut_off."','".$project_no."'");
        }
        elseif($dataCatgory == 'ServiceCharge')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_sc '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Utility')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_utility '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'CasualLeasing')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_casual '".$cut_off."','".$project_no."'");
        }
        elseif ($dataCatgory == 'Others')
        {
            $dataInvoice = DB::select("exec sp_tap_invoice_others '".$cut_off."','".$project_no."'");
        }

        return View::make('page.accountreceivable.listDataTapingInv',
            ['project_no'=>$project_no,'dataGenerateInv'=>$dataGenerateInv,'dataProject'=>$dataProject,
                'dateCutOff'=>$dateCutOff,'dateCutOffReal'=>$cut_off,'dataInvoice'=>$dataInvoice,'dataCatgory'=>$dataCatgory,
                'cutoff'=>$cut_off]);
    }

    public function editDescription(Request $request) {
        $project_no = session('current_project');

        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        try {
            \DB::beginTransaction();

            DB::table('PSM_SCHEDULE')->where('PSM_SCHEDULE_ID_INT', $request->SCHEDULE_ID_EDIT)->update([
                'DESC_CHAR' => $request->DESCRIPTION_EDIT
            ]);

            $action = "EDIT DESCRIPTION SCHEDULE";
            $description = 'Edit Description Schedule With ID : '.$request->SCHEDULE_ID_EDIT.', Description : '.$request->DESCRIPTION_EDIT;
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect('/invoice/viewgetlistgenerateinvoice/' . base64_encode($request->CUT_OFF_POST) . '/' . base64_encode($request->TYPE_POST))->with('error', 'Failed update data, errmsg : ' . $ex);
        }

        \Session::flash('success', 'Description has been updated...');
        return redirect('/invoice/viewgetlistgenerateinvoice/' . base64_encode($request->CUT_OFF_POST) . '/' . base64_encode($request->TYPE_POST));
    }

    public function editTapingDescription(Request $request) {
        if(Session::get('id') == '') {
            Session::flush();
            return redirect('/login');
        }

        //dd($request->all());

        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        try {
            DB::beginTransaction();

            DB::table('PSM_SCHEDULE')->where('PSM_SCHEDULE_ID_INT', $request->SCHEDULE_ID_EDIT)->update([
                'DESC_CHAR' => $request->DESCRIPTION_EDIT
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            //return redirect()->route('invoice.viewgetlisttapinginvoice', array('cut_off'=>$request->CUT_OFF_POST,'type'=>$request->TYPE_POST))->with('error', 'Failed update data, errmsg : ' . $ex);
            return redirect('/invoice/viewgetlisttapinginvoice/' . base64_encode($request->CUT_OFF_POST) . '/' . base64_encode($request->TYPE_POST))->with('error', 'Failed update data, errmsg : ' . $ex);
        }
        //dd('test');

        \Session::flash('success', 'Description has been updated...');
        //return Redirect::route('invoice.viewgetlisttapinginvoice/'.$request->CUT_OFF_POST.'/'.$request->TYPE_POST);
        return redirect('/invoice/viewgetlisttapinginvoice/' . base64_encode($request->CUT_OFF_POST) . '/' . base64_encode($request->TYPE_POST));
    }

    public function generateInvoiceRental(){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $generator = new utilGenerator;
            $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $converter = new utilConverter();
            $dataRentSC = \Request::all();
            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $docDate = Carbon::parse($dataRentSC['docDate']);
            $dueDate = Carbon::parse($dataRentSC['dueDate']);
            $yearTaxPeriod = substr($docDate->year,2,4);

            if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error', 'Document Date or Due Date Cannot be Empty');
            }

            if($dataRentSC['backdate'] == "")
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }

            if($dataRentSC['selectall'] == 'all')
            {
                $dataInvRentSC = DB::select("exec sp_invoice_rent '".$dataRentSC['cutoff']."','".$project_no."'");

                foreach($dataInvRentSC as $data)
                {
                    $cekDataTax = DB::table('TAX_MD_FP')
                        ->where('PROJECT_NO_CHAR','=',$project_no)
                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                        ->where('IS_TAKEN','=',0)
                        ->where('IS_DELETE','=',0)
                        ->count();

                    if ($cekDataTax <= 0)
                    {
                        return redirect()->route('invoice.listgenerateinvoice')
                            ->with('error','Tax Number not found, contact yout tax department ');
                    }
                    else
                    {
                        $taxNumber = DB::table('TAX_MD_FP')
                            ->where('PROJECT_NO_CHAR','=',$project_no)
                            ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                            ->where('IS_TAKEN','=',0)
                            ->where('IS_DELETE','=',0)
                            ->first();

                        $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT); ;

                        DB::table('TAX_MD_FP')
                            ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                            ->update([
                                'IS_TAKEN'=>1,
                                'UPDATED_BY'=>$userName,
                                'updated_at'=>$dateNow,
                            ]);
                    }

                    $cekDataInvoice = DB::table('INVOICE_TRANS')
                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                        ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
                        ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                        ->count();

                    if ($cekDataInvoice <= 0)
                    {
                        $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                        $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                        $Counter = str_pad($counter->inv_rent_count, 5, "0", STR_PAD_LEFT);
                        $Year = substr($docDate->year, 2);
                        $Month = $docDate->month;
                        $monthRomawi = $converter->getRomawi($Month);

                        Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                            ->update(['inv_rent_count'=>$counter->inv_rent_count + 1]);

                        $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                        DB::table('INVOICE_TRANS')
                            ->insert([
                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                'INVOICE_FP_NOCHAR'=>$numberTax,
                                'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                'DOC_TYPE'=>'B',
                                'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                'TGL_SCHEDULE_DATE'=>$docDate,
                                'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                'INVOICE_TRANS_DPP'=>($data->DPP_AMOUNT),
                                'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                'INVOICE_TRANS_PPH'=>($data->DPP_AMOUNT * 0.1),
                                'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                'PROJECT_NO_CHAR'=>$project_no,
                                'INVOICE_CREATE_CHAR'=>$userName,
                                'INVOICE_CREATE_DATE'=>$dateNow,
                                'FROM_SCHEDULE'=>1,
                                'JOURNAL_STATUS_INT'=>1,
                                'created_at'=>$dateNow,
                                'updated_at'=>$dateNow
                            ]);

                        DB::table('PSM_SCHEDULE')
                            ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                            ->update([
                                'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                'updated_at'=>$dateNow
                            ]);

                        //Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $data->BILL_AMOUNT;

                        if ($docDate <= Carbon::parse('2022-03-31'))
                        {
                            $UNEARNED = round($BillAmount / 1.1);
                            $PPN = round($UNEARNED * 0.1);
                        }
                        else
                        {
                            $UNEARNED = round($BillAmount / $dataProject['DPPBM_NUM']);
                            $PPN = round($UNEARNED * $dataProject['PPNBM_NUM']);
                        }


                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003001')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '650005001')
                                {
                                    $nilaiAmount = $UNEARNED * -1;
                                    $totalKredit += $UNEARNED;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                        }

                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                            ->update([
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'updated_at'=>$dateNow
                            ]);
                    }
                }
            }
            else
            {
                if (count($dataRentSC['billing']) > 0)
                {
                    for($i=0;  $i < count($dataRentSC['billing']); $i++){
                        if ($dataRentSC['billing'][$i] <> 0)
                        {
                            $dataInvRentSC = DB::select("exec sp_invoice_rent_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");

                            foreach($dataInvRentSC as $data)
                            {
                                $cekDataTax = DB::table('TAX_MD_FP')
                                    ->where('PROJECT_NO_CHAR','=',$project_no)
                                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                    ->where('IS_TAKEN','=',0)
                                    ->where('IS_DELETE','=',0)
                                    ->count();

                                if ($cekDataTax <= 0)
                                {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Tax Number not found, contact yout tax department ');
                                }
                                else
                                {
                                    $taxNumber = DB::table('TAX_MD_FP')
                                        ->where('PROJECT_NO_CHAR','=',$project_no)
                                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                        ->where('IS_TAKEN','=',0)
                                        ->where('IS_DELETE','=',0)
                                        ->first();

                                    $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                                    DB::table('TAX_MD_FP')
                                        ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                                        ->update([
                                            'IS_TAKEN'=>1,
                                            'UPDATED_BY'=>$userName,
                                            'updated_at'=>$dateNow,
                                        ]);
                                }

                                $cekDataInvoice = DB::table('INVOICE_TRANS')
                                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                    ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
                                    ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                                    ->count();

                                if ($cekDataInvoice <= 0)
                                {
                                    $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                                    $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                                    $Counter = str_pad($counter->inv_rent_count, 5, "0", STR_PAD_LEFT);
                                    $Year = substr($docDate->year, 2);
                                    $Month = $docDate->month;
                                    $monthRomawi = $converter->getRomawi($Month);

                                    Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                                        ->update(['inv_rent_count'=>$counter->inv_rent_count + 1]);

                                    $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                                    DB::table('INVOICE_TRANS')
                                        ->insert([
                                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                            'INVOICE_FP_NOCHAR'=>$numberTax,
                                            'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                            'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                            'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                            'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                            'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                            'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                            'DOC_TYPE'=>'B',
                                            'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                            'TGL_SCHEDULE_DATE'=>$docDate,
                                            'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                            'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                            'INVOICE_TRANS_DPP'=>($data->DPP_AMOUNT),
                                            'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                            'INVOICE_TRANS_PPH'=>($data->DPP_AMOUNT * 0.1),
                                            'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                            'PROJECT_NO_CHAR'=>$project_no,
                                            'INVOICE_CREATE_CHAR'=>$userName,
                                            'INVOICE_CREATE_DATE'=>$dateNow,
                                            'FROM_SCHEDULE'=>1,
                                            'JOURNAL_STATUS_INT'=>1,
                                            'created_at'=>$dateNow,
                                            'updated_at'=>$dateNow
                                        ]);

                                    DB::table('PSM_SCHEDULE')
                                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                        ->update([
                                            'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                            'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                            'updated_at'=>$dateNow
                                        ]);

                                    //Create Journal
                                    $Year = substr($dateNow->year, 2);
                                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                                    try {
                                        $countTable->save();
                                    } catch (QueryException $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                    }

                                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                    $tahunRK = $docDate->year;

                                    $period_no = $tahunRK.''.$bulanRK;

                                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->first();
                                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;
                                    
                                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;
                                    
                                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                    $totalDebit = 0;
                                    $totalKredit = 0;
                                    
                                    $BillAmount = $data->BILL_AMOUNT;

                                    if ($docDate <= Carbon::parse('2022-03-31'))
                                    {
                                        $UNEARNED = round($BillAmount / 1.1);
                                        $PPN = round($UNEARNED * 0.1);
                                    }
                                    else
                                    {
                                        $UNEARNED = round($BillAmount / $dataProject['DPPBM_NUM']);
                                        $PPN = round($UNEARNED * $dataProject['PPNBM_NUM']);
                                    }

                                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->get();
                                    foreach($dataTrxtype as $trx)
                                    {
                                        if ($trx->MD_TRX_MODE == 'Debit')
                                        {
                                            if($trx->ACC_NO_CHAR == '150003001')
                                            {
                                                $nilaiAmount = $BillAmount;
                                                $totalDebit += $BillAmount;
                                            }
                                        }elseif($trx->MD_TRX_MODE == 'Kredit')
                                        {
                                            if($trx->ACC_NO_CHAR == '650005001')
                                            {
                                                $nilaiAmount = $UNEARNED * -1;
                                                $totalKredit += $UNEARNED;
                                            }
                                            elseif($trx->ACC_NO_CHAR == '630002012')
                                            {
                                                $nilaiAmount = $PPN * -1;
                                                $totalKredit += $PPN;
                                            }
                                        }

                                        $datacoa = DB::table('ACC_MD_COA')
                                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                            ->first();

                                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                        $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                        $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                        try{
                                            GlTrans::create($inputGlTrans);
                                        } catch (Exception $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')
                                                ->with('error','Failed update counter table, errmsg : '.$ex);
                                        }
                                    }

                                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                                    $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                    $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                                    try {
                                        Journal::create($inputJournal);
                                    } catch (QueryException $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                    }

                                    DB::table('INVOICE_TRANS')
                                        ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                        ->update([
                                            'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                            'updated_at'=>$dateNow
                                        ]);
                                }
                            }
                        }
                    }
                }
            }

            \Session::flash('message', 'Generate Invoice Rental Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME']);
            $action = "GENERATE RT DATA";
            $description = 'Generate Invoice Rental Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listgenerateinvoice')->with('error','Failed generate data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listgenerateinvoice')->with('success',$description.' Successfully');
    }

    public function generateInvoiceCasual(){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $generator = new utilGenerator;
            $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $converter = new utilConverter();
            $dataRentSC = \Request::all();
            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $docDate = Carbon::parse($dataRentSC['docDate']);
            $dueDate = Carbon::parse($dataRentSC['dueDate']);
            $yearTaxPeriod = substr($docDate->year,2,4);

            if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '' )
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error', 'Document Date or Due Date Cannot be Empty');
            }

            if($dataRentSC['backdate'] == "")
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }

            if($dataRentSC['selectall'] == 'all')
            {
                $dataInvRentSC = DB::select("exec sp_invoice_casual '".$dataRentSC['cutoff']."','".$project_no."'");

                foreach($dataInvRentSC as $data)
                {
                    $cekDataTax = DB::table('TAX_MD_FP')
                        ->where('PROJECT_NO_CHAR','=',$project_no)
                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                        ->where('IS_TAKEN','=',0)
                        ->where('IS_DELETE','=',0)
                        ->count();

                    if ($cekDataTax <= 0)
                    {
                        return redirect()->route('invoice.listgenerateinvoice')
                            ->with('error','Tax Number not found, contact yout tax department ');
                    }
                    else
                    {
                        $taxNumber = DB::table('TAX_MD_FP')
                            ->where('PROJECT_NO_CHAR','=',$project_no)
                            ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                            ->where('IS_TAKEN','=',0)
                            ->where('IS_DELETE','=',0)
                            ->first();

                        $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                        DB::table('TAX_MD_FP')
                            ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                            ->update([
                                'IS_TAKEN'=>1,
                                'UPDATED_BY'=>$userName,
                                'updated_at'=>$dateNow,
                            ]);
                    }

                    $cekDataInvoice = DB::table('INVOICE_TRANS')
                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                        ->whereIn('INVOICE_TRANS_TYPE',['CL'])
                        ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                        ->count();

                    if ($cekDataInvoice <= 0)
                    {
                        $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                        $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                        $Counter = str_pad($counter->inv_casual_count, 5, "0", STR_PAD_LEFT);
                        $Year = substr($docDate->year, 2);
                        $Month = $docDate->month;
                        $monthRomawi = $converter->getRomawi($Month);

                        Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                            ->update(['inv_casual_count'=>$counter->inv_casual_count + 1]);

                        $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                        DB::table('INVOICE_TRANS')
                            ->insert([
                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                'INVOICE_FP_NOCHAR'=>$numberTax,
                                'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                'DOC_TYPE'=>'B',
                                'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                'TGL_SCHEDULE_DATE'=>$docDate,
                                'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                'INVOICE_TRANS_DPP'=>($data->DPP_AMOUNT),
                                'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                'INVOICE_TRANS_PPH'=>($data->BASE_AMOUNT_NUM * 0.1),
                                'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                'PROJECT_NO_CHAR'=>$project_no,
                                'INVOICE_CREATE_CHAR'=>$userName,
                                'INVOICE_CREATE_DATE'=>$dateNow,
                                'FROM_SCHEDULE'=>1,
                                'JOURNAL_STATUS_INT'=>1,
                                'created_at'=>$dateNow,
                                'updated_at'=>$dateNow
                            ]);

                        DB::table('PSM_SCHEDULE')
                            ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                            ->update([
                                'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                'updated_at'=>$dateNow
                            ]);

                        //Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $data->BILL_AMOUNT;
                        if ($docDate <= Carbon::parse('2022-03-31'))
                        {
                            $UNEARNED = round($BillAmount / 1.1);
                            $PPN = round($UNEARNED * 0.1);
                        }
                        else
                        {
                            $UNEARNED = round($BillAmount / $dataProject['DPPBM_NUM']);
                            $PPN = round($UNEARNED * $dataProject['PPNBM_NUM']);
                        }

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003004')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '650001002')
                                {
                                    $nilaiAmount = $UNEARNED * -1;
                                    $totalKredit += $UNEARNED;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan Casual ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan Casual ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                        }

                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                            ->update([
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'updated_at'=>$dateNow
                            ]);
                    }
                }
            }
            else
            {
                if (count($dataRentSC['billing']) > 0)
                {
                    for($i=0;  $i < count($dataRentSC['billing']); $i++){
                        if ($dataRentSC['billing'][$i] <> 0)
                        {
                            $dataInvRentSC = DB::select("exec sp_invoice_casual_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");

                            foreach($dataInvRentSC as $data)
                            {
                                $cekDataTax = DB::table('TAX_MD_FP')
                                    ->where('PROJECT_NO_CHAR','=',$project_no)
                                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                    ->where('IS_TAKEN','=',0)
                                    ->where('IS_DELETE','=',0)
                                    ->count();

                                if ($cekDataTax <= 0)
                                {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Tax Number not found, contact yout tax department ');
                                }
                                else
                                {
                                    $taxNumber = DB::table('TAX_MD_FP')
                                        ->where('PROJECT_NO_CHAR','=',$project_no)
                                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                        ->where('IS_TAKEN','=',0)
                                        ->where('IS_DELETE','=',0)
                                        ->first();

                                    $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                                    DB::table('TAX_MD_FP')
                                        ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                                        ->update([
                                            'IS_TAKEN'=>1,
                                            'UPDATED_BY'=>$userName,
                                            'updated_at'=>$dateNow,
                                        ]);
                                }

                                $cekDataInvoice = DB::table('INVOICE_TRANS')
                                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                    ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
                                    ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                                    ->count();

                                if ($cekDataInvoice <= 0)
                                {
                                    $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                                    $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                                    $Counter = str_pad($counter->inv_casual_count, 5, "0", STR_PAD_LEFT);
                                    $Year = substr($docDate->year, 2);
                                    $Month = $docDate->month;
                                    $monthRomawi = $converter->getRomawi($Month);

                                    Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                                        ->update(['inv_casual_count'=>$counter->inv_casual_count + 1]);

                                    $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                                    DB::table('INVOICE_TRANS')
                                        ->insert([
                                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                            'INVOICE_FP_NOCHAR'=>$numberTax,
                                            'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                            'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                            'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                            'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                            'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                            'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                            'DOC_TYPE'=>'B',
                                            'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                            'TGL_SCHEDULE_DATE'=>$docDate,
                                            'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                            'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                            'INVOICE_TRANS_DPP'=>($data->DPP_AMOUNT),
                                            'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                            'INVOICE_TRANS_PPH'=>($data->BASE_AMOUNT_NUM * 0.1),
                                            'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                            'PROJECT_NO_CHAR'=>$project_no,
                                            'INVOICE_CREATE_CHAR'=>$userName,
                                            'INVOICE_CREATE_DATE'=>$dateNow,
                                            'FROM_SCHEDULE'=>1,
                                            'JOURNAL_STATUS_INT'=>1,
                                            'created_at'=>$dateNow,
                                            'updated_at'=>$dateNow
                                        ]);

                                    DB::table('PSM_SCHEDULE')
                                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                        ->update([
                                            'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                            'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                            'updated_at'=>$dateNow
                                        ]);

                                    // //Create Journal
                                    $Year = substr($dateNow->year, 2);
                                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                                    try {
                                        $countTable->save();
                                    } catch (QueryException $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                    }

                                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                    $tahunRK = $docDate->year;

                                    $period_no = $tahunRK.''.$bulanRK;

                                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->first();
                                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                    $totalDebit = 0;
                                    $totalKredit = 0;

                                    $BillAmount = $data->BILL_AMOUNT;
                                    if ($docDate <= Carbon::parse('2022-03-31'))
                                    {
                                        $UNEARNED = round($BillAmount / 1.1);
                                        $PPN = round($UNEARNED * 0.1);
                                    }
                                    else
                                    {
                                        $UNEARNED = round($BillAmount / $dataProject['DPPBM_NUM']);
                                        $PPN = round($UNEARNED * $dataProject['PPNBM_NUM']);
                                    }

                                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->get();
                                    foreach($dataTrxtype as $trx)
                                    {
                                        if ($trx->MD_TRX_MODE == 'Debit')
                                        {
                                            if($trx->ACC_NO_CHAR == '150003004')
                                            {
                                                $nilaiAmount = $BillAmount;
                                                $totalDebit += $BillAmount;
                                            }
                                        }elseif($trx->MD_TRX_MODE == 'Kredit')
                                        {
                                            if($trx->ACC_NO_CHAR == '650001002')
                                            {
                                                $nilaiAmount = $UNEARNED * -1;
                                                $totalKredit += $UNEARNED;
                                            }
                                            elseif($trx->ACC_NO_CHAR == '630002012')
                                            {
                                                $nilaiAmount = $PPN * -1;
                                                $totalKredit += $PPN;
                                            }
                                        }

                                        $datacoa = DB::table('ACC_MD_COA')
                                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                            ->first();

                                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                        $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan Casual ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                        $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                        try{
                                            GlTrans::create($inputGlTrans);
                                        } catch (Exception $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')
                                                ->with('error','Failed update counter table, errmsg : '.$ex);
                                        }
                                    }

                                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                                    $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                    $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan Casual ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                                    try {
                                        Journal::create($inputJournal);
                                    } catch (QueryException $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                    }

                                    DB::table('INVOICE_TRANS')
                                        ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                        ->update([
                                            'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                            'updated_at'=>$dateNow
                                        ]);
                                }
                            }
                        }
                    }
                }
            }

            \Session::flash('message', 'Generate Invoice Casual Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME']);
            $action = "GENERATE CL DATA";
            $description = 'Generate Invoice Casual Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed generate data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listgenerateinvoice')->with('success',$description.' Successfully');
    }

    public function generateInvoiceOthers(){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $generator = new utilGenerator;
            $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $converter = new utilConverter();
            $dataRentSC = \Request::all();
            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $docDate = Carbon::parse($dataRentSC['docDate']);
            $dueDate = Carbon::parse($dataRentSC['dueDate']);
            $yearTaxPeriod = substr($docDate->year,2,4);

            if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error', 'Document Date or Due Date Cannot be Empty');
            }

            if($dataRentSC['backdate'] == "")
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }

            if($dataRentSC['selectall'] == 'all')
            {
                $dataInvRentSC = DB::select("exec sp_invoice_others '".$dataRentSC['cutoff']."','".$project_no."'");

                foreach($dataInvRentSC as $data)
                {
                    $cekDataTax = DB::table('TAX_MD_FP')
                        ->where('PROJECT_NO_CHAR','=',$project_no)
                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                        ->where('IS_TAKEN','=',0)
                        ->where('IS_DELETE','=',0)
                        ->count();

                    if ($cekDataTax <= 0)
                    {
                        return redirect()->route('invoice.listgenerateinvoice')
                            ->with('error','Tax Number not found, contact yout tax department ');
                    }
                    else
                    {
                        $taxNumber = DB::table('TAX_MD_FP')
                            ->where('PROJECT_NO_CHAR','=',$project_no)
                            ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                            ->where('IS_TAKEN','=',0)
                            ->where('IS_DELETE','=',0)
                            ->first();

                        $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                        DB::table('TAX_MD_FP')
                            ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                            ->update([
                                'IS_TAKEN'=>1,
                                'UPDATED_BY'=>$userName,
                                'updated_at'=>$dateNow,
                            ]);
                    }

                    $cekDataInvoice = DB::table('INVOICE_TRANS')
                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                        ->whereIn('INVOICE_TRANS_TYPE',['OT'])
                        ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                        ->count();

                    if ($cekDataInvoice <= 0)
                    {
                        $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                        $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                        $Counter = str_pad($counter->inv_ot_count, 5, "0", STR_PAD_LEFT);
                        $Year = substr($docDate->year, 2);
                        $Month = $docDate->month;
                        $monthRomawi = $converter->getRomawi($Month);

                        Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                            ->update(['inv_ot_count'=>$counter->inv_ot_count + 1]);

                        $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                        DB::table('INVOICE_TRANS')
                            ->insert([
                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                'INVOICE_FP_NOCHAR'=>$numberTax,
                                'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                'DOC_TYPE'=>'B',
                                'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                'TGL_SCHEDULE_DATE'=>$docDate,
                                'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                'INVOICE_TRANS_DPP'=>($data->DPP_AMOUNT),
                                'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                'INVOICE_TRANS_PPH'=>($data->BASE_AMOUNT_NUM * 0.1),
                                'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                'PROJECT_NO_CHAR'=>$project_no,
                                'INVOICE_CREATE_CHAR'=>$userName,
                                'INVOICE_CREATE_DATE'=>$dateNow,
                                'FROM_SCHEDULE'=>1,
                                'JOURNAL_STATUS_INT'=>1,
                                'created_at'=>$dateNow,
                                'updated_at'=>$dateNow
                            ]);

                        DB::table('PSM_SCHEDULE')
                            ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                            ->update([
                                'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                'updated_at'=>$dateNow
                            ]);

                        if($data->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $BillAmount = $data->BILL_AMOUNT;
                            if ($docDate <= Carbon::parse('2022-03-31'))
                            {
                                $OTHERS = round($BillAmount / 1.1);
                                $PPN = round($OTHERS * 0.1);
                            }
                            else
                            {
                                $OTHERS = round($BillAmount / $dataProject['DPPBM_NUM']);
                                $PPN = round($OTHERS * $dataProject['PPNBM_NUM']);
                            }

                            $BebanPajak = round($OTHERS * 0.1);
                            $UangMukaPPH = round($OTHERS * 0.1);

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->get();
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003006')
                                    {
                                        $nilaiAmount = $BillAmount;
                                        $totalDebit += $BillAmount;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '980100002')
                                    {
                                        $nilaiAmount = $BebanPajak;
                                        $totalDebit += $BebanPajak;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '912301999')
                                    {
                                        $nilaiAmount = $OTHERS * -1;
                                        $totalKredit += $OTHERS;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012')
                                    {
                                        $nilaiAmount = $PPN * -1;
                                        $totalKredit += $PPN;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '170002009')
                                    {
                                        $nilaiAmount = $UangMukaPPH * -1;
                                        $totalKredit += $UangMukaPPH;
                                    }
                                }

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                ->update([
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'updated_at'=>$dateNow
                                ]);
                        }
                        elseif($data->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTBU')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $BillAmount = $data->BILL_AMOUNT;
                            if ($docDate <= Carbon::parse('2022-03-31'))
                            {
                                $OTHERS = round($BillAmount / 1.1);
                                $PPN = round($OTHERS * 0.1);
                            }
                            else
                            {
                                $OTHERS = round($BillAmount / $dataProject['DPPBM_NUM']);
                                $PPN = round($OTHERS * $dataProject['PPNBM_NUM']);
                            }
                            $BebanPajak = round($OTHERS * 0.1);
                            $UangMukaPPH = round($OTHERS * 0.1);

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTBU')->get();
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003006')
                                    {
                                        $nilaiAmount = $BillAmount;
                                        $totalDebit += $BillAmount;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '980100002')
                                    {
                                        $nilaiAmount = $BebanPajak;
                                        $totalDebit += $BebanPajak;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '912301999')
                                    {
                                        $nilaiAmount = $OTHERS * -1;
                                        $totalKredit += $OTHERS;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012')
                                    {
                                        $nilaiAmount = $PPN * -1;
                                        $totalKredit += $PPN;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '170002007')
                                    {
                                        $nilaiAmount = $UangMukaPPH * -1;
                                        $totalKredit += $UangMukaPPH;
                                    }
                                }

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                ->update([
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'updated_at'=>$dateNow
                                ]);
                        }
                    }
                }
            }
            else
            {
                if (count($dataRentSC['billing']) > 0)
                {
                    for($i=0;  $i < count($dataRentSC['billing']); $i++){
                        if ($dataRentSC['billing'][$i] <> 0)
                        {
                            $dataInvRentSC = DB::select("exec sp_invoice_others_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");

                            foreach($dataInvRentSC as $data)
                            {
                                $cekDataTax = DB::table('TAX_MD_FP')
                                    ->where('PROJECT_NO_CHAR','=',$project_no)
                                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                    ->where('IS_TAKEN','=',0)
                                    ->where('IS_DELETE','=',0)
                                    ->count();

                                if ($cekDataTax <= 0)
                                {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Tax Number not found, contact yout tax department ');
                                }
                                else
                                {
                                    $taxNumber = DB::table('TAX_MD_FP')
                                        ->where('PROJECT_NO_CHAR','=',$project_no)
                                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                        ->where('IS_TAKEN','=',0)
                                        ->where('IS_DELETE','=',0)
                                        ->first();

                                    $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                                    DB::table('TAX_MD_FP')
                                        ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                                        ->update([
                                            'IS_TAKEN'=>1,
                                            'UPDATED_BY'=>$userName,
                                            'updated_at'=>$dateNow,
                                        ]);
                                }

                                $cekDataInvoice = DB::table('INVOICE_TRANS')
                                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                    ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
                                    ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                                    ->count();

                                if ($cekDataInvoice <= 0)
                                {
                                    $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                                    $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                                    $Counter = str_pad($counter->inv_ot_count, 5, "0", STR_PAD_LEFT);
                                    $Year = substr($docDate->year, 2);
                                    $Month = $docDate->month;
                                    $monthRomawi = $converter->getRomawi($Month);

                                    Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                                        ->update(['inv_ot_count'=>$counter->inv_ot_count + 1]);

                                    $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                                    DB::table('INVOICE_TRANS')
                                        ->insert([
                                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                            'INVOICE_FP_NOCHAR'=>$numberTax,
                                            'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                            'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                            'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                            'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                            'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                            'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                            'DOC_TYPE'=>'B',
                                            'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                            'TGL_SCHEDULE_DATE'=>$docDate,
                                            'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                            'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                            'INVOICE_TRANS_DPP'=>($data->DPP_AMOUNT),
                                            'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                            'INVOICE_TRANS_PPH'=>($data->BASE_AMOUNT_NUM * 0.1),
                                            'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                            'PROJECT_NO_CHAR'=>$project_no,
                                            'INVOICE_CREATE_CHAR'=>$userName,
                                            'INVOICE_CREATE_DATE'=>$dateNow,
                                            'FROM_SCHEDULE'=>1,
                                            'JOURNAL_STATUS_INT'=>1,
                                            'created_at'=>$dateNow,
                                            'updated_at'=>$dateNow
                                        ]);

                                    DB::table('PSM_SCHEDULE')
                                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                        ->update([
                                            'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                            'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                            'updated_at'=>$dateNow
                                        ]);

                                    if($data->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                                    {
                                        //Create Journal
                                        $Year = substr($dateNow->year, 2);
                                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                                        try {
                                            $countTable->save();
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                        $tahunRK = $docDate->year;

                                        $period_no = $tahunRK.''.$bulanRK;

                                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->first();
                                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                        $totalDebit = 0;
                                        $totalKredit = 0;

                                        $BillAmount = $data->BILL_AMOUNT;
                                        if ($docDate <= Carbon::parse('2022-03-31'))
                                        {
                                            $OTHERS = round($BillAmount / 1.1);
                                            $PPN = round($OTHERS * 0.1);
                                        }
                                        else
                                        {
                                            $OTHERS = round($BillAmount / $dataProject['DPPBM_NUM']);
                                            $PPN = round($OTHERS * $dataProject['PPNBM_NUM']);
                                        }
                                        $BebanPajak = round($OTHERS * 0.1);
                                        $UangMukaPPH = round($OTHERS * 0.1);

                                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->get();
                                        foreach($dataTrxtype as $trx)
                                        {
                                            if ($trx->MD_TRX_MODE == 'Debit')
                                            {
                                                if($trx->ACC_NO_CHAR == '150003006')
                                                {
                                                    $nilaiAmount = $BillAmount;
                                                    $totalDebit += $BillAmount;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '980100002')
                                                {
                                                    $nilaiAmount = $BebanPajak;
                                                    $totalDebit += $BebanPajak;
                                                }
                                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                                            {
                                                if($trx->ACC_NO_CHAR == '912301999')
                                                {
                                                    $nilaiAmount = $OTHERS * -1;
                                                    $totalKredit += $OTHERS;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '630002012')
                                                {
                                                    $nilaiAmount = $PPN * -1;
                                                    $totalKredit += $PPN;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '170002009')
                                                {
                                                    $nilaiAmount = $UangMukaPPH * -1;
                                                    $totalKredit += $UangMukaPPH;
                                                }
                                            }

                                            $datacoa = DB::table('ACC_MD_COA')
                                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                                ->first();

                                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                            $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                            $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                            try{
                                                GlTrans::create($inputGlTrans);
                                            } catch (Exception $ex) {
                                                return redirect()->route('invoice.listgenerateinvoice')
                                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                                            }
                                        }

                                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                                        $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                        $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                                        try {
                                            Journal::create($inputJournal);
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        DB::table('INVOICE_TRANS')
                                            ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                            ->update([
                                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                                'updated_at'=>$dateNow
                                            ]);
                                    }
                                    elseif($data->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                                    {
                                        //Create Journal
                                        $Year = substr($dateNow->year, 2);
                                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                                        try {
                                            $countTable->save();
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                        $tahunRK = $docDate->year;

                                        $period_no = $tahunRK.''.$bulanRK;

                                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTBU')->first();
                                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                        $totalDebit = 0;
                                        $totalKredit = 0;

                                        $BillAmount = $data->BILL_AMOUNT;
                                        if ($docDate <= Carbon::parse('2022-03-31'))
                                        {
                                            $OTHERS = round($BillAmount / 1.1);
                                            $PPN = round($OTHERS * 0.1);
                                        }
                                        else
                                        {
                                            $OTHERS = round($BillAmount / $dataProject['DPPBM_NUM']);
                                            $PPN = round($OTHERS * $dataProject['PPNBM_NUM']);
                                        }
                                        $BebanPajak = round($OTHERS * 0.1);
                                        $UangMukaPPH = round($OTHERS * 0.1);

                                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTBU')->get();
                                        foreach($dataTrxtype as $trx)
                                        {
                                            if ($trx->MD_TRX_MODE == 'Debit')
                                            {
                                                if($trx->ACC_NO_CHAR == '150003006')
                                                {
                                                    $nilaiAmount = $BillAmount;
                                                    $totalDebit += $BillAmount;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '980100002')
                                                {
                                                    $nilaiAmount = $BebanPajak;
                                                    $totalDebit += $BebanPajak;
                                                }
                                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                                            {
                                                if($trx->ACC_NO_CHAR == '912301999')
                                                {
                                                    $nilaiAmount = $OTHERS * -1;
                                                    $totalKredit += $OTHERS;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '630002012')
                                                {
                                                    $nilaiAmount = $PPN * -1;
                                                    $totalKredit += $PPN;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '170002007')
                                                {
                                                    $nilaiAmount = $UangMukaPPH * -1;
                                                    $totalKredit += $UangMukaPPH;
                                                }
                                            }

                                            $datacoa = DB::table('ACC_MD_COA')
                                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                                ->first();

                                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                            $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                            $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                            try{
                                                GlTrans::create($inputGlTrans);
                                            } catch (Exception $ex) {
                                                return redirect()->route('invoice.listgenerateinvoice')
                                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                                            }
                                        }

                                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                                        $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                        $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan Lain-lain ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                                        try {
                                            Journal::create($inputJournal);
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        DB::table('INVOICE_TRANS')
                                            ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                            ->update([
                                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                                'updated_at'=>$dateNow
                                            ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            \Session::flash('message', 'Generate Invoice Others Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME']);
            $action = "GENERATE INV OT DATA";
            $description = 'Generate Invoice Others Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed generate data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listgenerateinvoice')->with('success',$description.' Successfully');
    }

    public function generateInvoiceServiceCharge(){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $generator = new utilGenerator;
            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $converter = new utilConverter();
            $dataRentSC = \Request::all();
            $date = Carbon::parse(Carbon::now());
            $docDate = Carbon::parse($dataRentSC['docDate']);
            $dueDate = Carbon::parse($dataRentSC['dueDate']);
            $yearTaxPeriod = substr($docDate->year,2,4);

            if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error', 'Document Date or Due Date Cannot be Empty');
            }

            if($dataRentSC['backdate'] == "")
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }

            if($dataRentSC['selectall'] == 'all')
            {
                $dataInvRentSC = DB::select("exec sp_invoice_sc '".$dataRentSC['cutoff']."','".$project_no."'");

                foreach($dataInvRentSC as $data)
                {
                    $cekDataTax = DB::table('TAX_MD_FP')
                        ->where('PROJECT_NO_CHAR','=',$project_no)
                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                        ->where('IS_TAKEN','=',0)
                        ->where('IS_DELETE','=',0)
                        ->count();

                    if ($cekDataTax <= 0)
                    {
                        return redirect()->route('invoice.listgenerateinvoice')
                            ->with('error','Tax Number not found, contact yout tax department ');
                    }
                    else
                    {
                        $taxNumber = DB::table('TAX_MD_FP')
                            ->where('PROJECT_NO_CHAR','=',$project_no)
                            ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                            ->where('IS_TAKEN','=',0)
                            ->where('IS_DELETE','=',0)
                            ->first();

                        $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                        DB::table('TAX_MD_FP')
                            ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                            ->update([
                                'IS_TAKEN'=>1,
                                'UPDATED_BY'=>$userName,
                                'updated_at'=>$dateNow,
                            ]);
                    }

                    $cekDataInvoice = DB::table('INVOICE_TRANS')
                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                        ->whereIn('INVOICE_TRANS_TYPE',['SC'])
                        ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                        ->count();

                    if ($cekDataInvoice <= 0)
                    {
                        $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                        $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                        $Counter = str_pad($counter->inv_sc_count, 5, "0", STR_PAD_LEFT);
                        $Year = substr($docDate->year, 2);
                        $Month = $docDate->month;
                        $monthRomawi = $converter->getRomawi($Month);

                        Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                            ->update(['inv_sc_count'=>$counter->inv_sc_count + 1]);
                        
                        $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                        DB::table('INVOICE_TRANS')
                            ->insert([
                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                'INVOICE_FP_NOCHAR'=>$numberTax,
                                'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                'DOC_TYPE'=>'B',
                                'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                'TGL_SCHEDULE_DATE'=>$docDate,
                                'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                'INVOICE_TRANS_DPP'=>$data->DPP_AMOUNT,
                                'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                'INVOICE_TRANS_PPH'=>($data->DPP_AMOUNT * 0.1),
                                'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                'PROJECT_NO_CHAR'=>$project_no,
                                'INVOICE_CREATE_CHAR'=>$userName,
                                'INVOICE_CREATE_DATE'=>$date,
                                'FROM_SCHEDULE'=>$data->FROM_SCHEDULE,
                                'JOURNAL_STATUS_INT'=>1,
                                'created_at'=>$date,
                                'updated_at'=>$date
                            ]);

                        if($data->FROM_SCHEDULE == 1)
                        {
                            DB::table('PSM_SCHEDULE')
                                ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                    'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                    'updated_at'=>$dateNow
                                ]);
                        }

                        if($data->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $BillAmount = $data->BILL_AMOUNT;
                            if ($docDate <= Carbon::parse('2022-03-31'))
                            {
                                $SERVICECHARGE = round($BillAmount / 1.1);
                                $PPN = round($SERVICECHARGE * 0.1);
                            }
                            else
                            {
                                $SERVICECHARGE = round($BillAmount / $dataProject['DPPBM_NUM']);
                                $PPN = round($SERVICECHARGE * $dataProject['PPNBM_NUM']);
                            }

                            $BebanPajak = round($SERVICECHARGE * 0.1);
                            $UangMukaPPH = round($SERVICECHARGE * $dataProject['PPNBM_NUM']);

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->get();
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003001')
                                    {
                                        $nilaiAmount = $BillAmount;
                                        $totalDebit += $BillAmount;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '980100002')
                                    {
                                        $nilaiAmount = $BebanPajak;
                                        $totalDebit += $BebanPajak;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '912200002')
                                    {
                                        $nilaiAmount = $SERVICECHARGE * -1;
                                        $totalKredit += $SERVICECHARGE;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012')
                                    {
                                        $nilaiAmount = $PPN * -1;
                                        $totalKredit += $PPN;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '170002009')
                                    {
                                        $nilaiAmount = $UangMukaPPH * -1;
                                        $totalKredit += $UangMukaPPH;
                                    }
                                }

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']="Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                ->update([
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'updated_at'=>$dateNow
                                ]);
                        }
                        elseif($data->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $BillAmount = $data->BILL_AMOUNT;
                            if ($docDate <= Carbon::parse('2022-03-31'))
                            {
                                $SERVICECHARGE = round($BillAmount / 1.1);
                                $PPN = round($SERVICECHARGE * 0.1);
                            }
                            else
                            {
                                $SERVICECHARGE = round($BillAmount / $dataProject['DPPBM_NUM']);
                                $PPN = round($SERVICECHARGE * $dataProject['PPNBM_NUM']);
                            }
                            $BebanPajak = round($SERVICECHARGE * 0.1);
                            $UangMukaPPH = round($SERVICECHARGE * 0.1);

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->get();
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003001')
                                    {
                                        $nilaiAmount = $BillAmount;
                                        $totalDebit += $BillAmount;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '980100002')
                                    {
                                        $nilaiAmount = $BebanPajak;
                                        $totalDebit += $BebanPajak;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '912200002')
                                    {
                                        $nilaiAmount = $SERVICECHARGE * -1;
                                        $totalKredit += $SERVICECHARGE;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012')
                                    {
                                        $nilaiAmount = $PPN * -1;
                                        $totalKredit += $PPN;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '170002007')
                                    {
                                        $nilaiAmount = $UangMukaPPH * -1;
                                        $totalKredit += $UangMukaPPH;
                                    }
                                }

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']="Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                ->update([
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'updated_at'=>$dateNow
                                ]);
                        }
                    }
                }
            }
            else
            {
                if (count($dataRentSC['billing']) > 0)
                {
                    for($i=0;  $i < count($dataRentSC['billing']); $i++){
                        if ($dataRentSC['billing'][$i] <> 0)
                        {
                            $dataInvRentSC = DB::select("exec sp_invoice_sc_byID '".$dataRentSC['cutoff']."','".$project_no."',".$dataRentSC['billing'][$i]);

                            foreach($dataInvRentSC as $data)
                            {
                                $cekDataTax = DB::table('TAX_MD_FP')
                                    ->where('PROJECT_NO_CHAR','=',$project_no)
                                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                    ->where('IS_TAKEN','=',0)
                                    ->where('IS_DELETE','=',0)
                                    ->count();

                                if ($cekDataTax <= 0)
                                {
                                    return redirect()->route('invoice.listgenerateinvoice')
                                        ->with('error','Tax Number not found, contact yout tax department ');
                                }
                                else
                                {
                                    $taxNumber = DB::table('TAX_MD_FP')
                                        ->where('PROJECT_NO_CHAR','=',$project_no)
                                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                                        ->where('IS_TAKEN','=',0)
                                        ->where('IS_DELETE','=',0)
                                        ->first();

                                    $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                                    DB::table('TAX_MD_FP')
                                        ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                                        ->update([
                                            'IS_TAKEN'=>1,
                                            'UPDATED_BY'=>$userName,
                                            'updated_at'=>$dateNow,
                                        ]);
                                }

                                $cekDataInvoice = DB::table('INVOICE_TRANS')
                                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                    ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
                                    ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                                    ->count();

                                if ($cekDataInvoice <= 0)
                                {
                                    $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                                    $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                                    $Counter = str_pad($counter->inv_sc_count, 5, "0", STR_PAD_LEFT);
                                    $Year = substr($docDate->year, 2);
                                    $Month = $docDate->month;
                                    $monthRomawi = $converter->getRomawi($Month);

                                    Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                                        ->update(['inv_sc_count'=>$counter->inv_sc_count + 1]);

                                    $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                                    DB::table('INVOICE_TRANS')
                                        ->insert([
                                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                            'INVOICE_FP_NOCHAR'=>$numberTax,
                                            'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                            'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                            'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                            'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                            'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                            'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                            'DOC_TYPE'=>'B',
                                            'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                            'TGL_SCHEDULE_DATE'=>$docDate,
                                            'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                            'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                            'INVOICE_TRANS_DPP'=>$data->DPP_AMOUNT,
                                            'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                                            'INVOICE_TRANS_PPH'=>($data->DPP_AMOUNT * 0.1),
                                            'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                            'PROJECT_NO_CHAR'=>$project_no,
                                            'INVOICE_CREATE_CHAR'=>$userName,
                                            'INVOICE_CREATE_DATE'=>$date,
                                            'FROM_SCHEDULE'=>$data->FROM_SCHEDULE,
                                            'JOURNAL_STATUS_INT'=>1,
                                            'created_at'=>$date,
                                            'updated_at'=>$date
                                        ]);

                                    if($data->FROM_SCHEDULE == 1)
                                    {
                                        DB::table('PSM_SCHEDULE')
                                            ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                            ->update([
                                                'INVOICE_NUMBER_CHAR'=>$noInvoice,
                                                'SCHEDULE_STATUS_INT'=>2, // generate invoice
                                                'updated_at'=>$dateNow
                                            ]);
                                    }

                                    if($data->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                                    {
                                        //Create Journal
                                        $Year = substr($dateNow->year, 2);
                                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                                        try {
                                            $countTable->save();
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                        $tahunRK = $docDate->year;

                                        $period_no = $tahunRK.''.$bulanRK;

                                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->first();
                                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                        $totalDebit = 0;
                                        $totalKredit = 0;

                                        $BillAmount = $data->BILL_AMOUNT;
                                        if ($docDate <= Carbon::parse('2022-03-31'))
                                        {
                                            $SERVICECHARGE = round($BillAmount / 1.1);
                                            $PPN = round($SERVICECHARGE * 0.1);
                                        }
                                        else
                                        {
                                            $SERVICECHARGE = round($BillAmount / $dataProject['DPPBM_NUM']);
                                            $PPN = round($SERVICECHARGE * $dataProject['PPNBM_NUM']);
                                        }
                                        $BebanPajak = round($SERVICECHARGE * 0.1);
                                        $UangMukaPPH = round($SERVICECHARGE * 0.1);

                                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->get();
                                        foreach($dataTrxtype as $trx)
                                        {
                                            if ($trx->MD_TRX_MODE == 'Debit')
                                            {
                                                if($trx->ACC_NO_CHAR == '150003001')
                                                {
                                                    $nilaiAmount = $BillAmount;
                                                    $totalDebit += $BillAmount;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '980100002')
                                                {
                                                    $nilaiAmount = $BebanPajak;
                                                    $totalDebit += $BebanPajak;
                                                }
                                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                                            {
                                                if($trx->ACC_NO_CHAR == '912200002')
                                                {
                                                    $nilaiAmount = $SERVICECHARGE * -1;
                                                    $totalKredit += $SERVICECHARGE;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '630002012')
                                                {
                                                    $nilaiAmount = $PPN * -1;
                                                    $totalKredit += $PPN;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '170002009')
                                                {
                                                    $nilaiAmount = $UangMukaPPH * -1;
                                                    $totalKredit += $UangMukaPPH;
                                                }
                                            }

                                            $datacoa = DB::table('ACC_MD_COA')
                                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                                ->first();

                                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                            $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                            $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                            try{
                                                GlTrans::create($inputGlTrans);
                                            } catch (Exception $ex) {
                                                return redirect()->route('invoice.listgenerateinvoice')
                                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                                            }
                                        }

                                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                                        $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                        $inputJournal['ACC_JOURNAL_REF_DESC']="Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                                        try {
                                            Journal::create($inputJournal);
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        DB::table('INVOICE_TRANS')
                                            ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                            ->update([
                                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                                'updated_at'=>$dateNow
                                            ]);
                                    }
                                    elseif($data->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                                    {
                                        //Create Journal
                                        $Year = substr($dateNow->year, 2);
                                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                                        try {
                                            $countTable->save();
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                        $tahunRK = $docDate->year;

                                        $period_no = $tahunRK.''.$bulanRK;

                                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->first();
                                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                        $totalDebit = 0;
                                        $totalKredit = 0;

                                        $BillAmount = $data->BILL_AMOUNT;
                                        if ($docDate <= Carbon::parse('2022-03-31'))
                                        {
                                            $SERVICECHARGE = round($BillAmount / 1.1);
                                            $PPN = round($SERVICECHARGE * 0.1);
                                        }
                                        else
                                        {
                                            $SERVICECHARGE = round($BillAmount / $dataProject['DPPBM_NUM']);
                                            $PPN = round($SERVICECHARGE * $dataProject['PPNBM_NUM']);
                                        }
                                        $BebanPajak = round($SERVICECHARGE * 0.1);
                                        $UangMukaPPH = round($SERVICECHARGE * 0.1);

                                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->get();
                                        foreach($dataTrxtype as $trx)
                                        {
                                            if ($trx->MD_TRX_MODE == 'Debit')
                                            {
                                                if($trx->ACC_NO_CHAR == '150003001')
                                                {
                                                    $nilaiAmount = $BillAmount;
                                                    $totalDebit += $BillAmount;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '980100002')
                                                {
                                                    $nilaiAmount = $BebanPajak;
                                                    $totalDebit += $BebanPajak;
                                                }
                                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                                            {
                                                if($trx->ACC_NO_CHAR == '912200002')
                                                {
                                                    $nilaiAmount = $SERVICECHARGE * -1;
                                                    $totalKredit += $SERVICECHARGE;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '630002012')
                                                {
                                                    $nilaiAmount = $PPN * -1;
                                                    $totalKredit += $PPN;
                                                }
                                                elseif($trx->ACC_NO_CHAR == '170002007')
                                                {
                                                    $nilaiAmount = $UangMukaPPH * -1;
                                                    $totalKredit += $UangMukaPPH;
                                                }
                                            }

                                            $datacoa = DB::table('ACC_MD_COA')
                                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                                ->first();

                                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                            $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                            $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                            try{
                                                GlTrans::create($inputGlTrans);
                                            } catch (Exception $ex) {
                                                return redirect()->route('invoice.listgenerateinvoice')
                                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                                            }
                                        }

                                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                                        $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                        $inputJournal['ACC_JOURNAL_REF_DESC']="Service Charge ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                                        try {
                                            Journal::create($inputJournal);
                                        } catch (QueryException $ex) {
                                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                        }

                                        DB::table('INVOICE_TRANS')
                                            ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                            ->update([
                                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                                'updated_at'=>$dateNow
                                            ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            \Session::flash('message', 'Generate Invoice Service Charge Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME']);
            $action = "GENERATE INV SC DATA";
            $description = 'Generate Invoice Service Charge Cut Off '.$dataRentSC['docDate'].' Project '.$dataProject['PROJECT_NAME'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed generate data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listgenerateinvoice')
            ->with('success',$description.' Successfully');
    }

    public function generateInvoiceUtility(){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $converter = new utilConverter();
            $dataUtility = \Request::all();
            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $docDate = Carbon::parse($dataUtility['docDate']);
            $dueDate = Carbon::parse($dataUtility['dueDate']);
            $yearTaxPeriod = substr($docDate->year,2,4);

            $date = Carbon::parse(Carbon::now());

            if ($dataUtility['docDate'] == '' || $dataUtility['dueDate'] == '')
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error', 'Document Date or Due Date Cannot be Empty');
            }

            if($dataUtility['backdate'] == "")
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }


            $docDate = Carbon::parse($dataUtility['docDate']);
            $dueDate = Carbon::parse($dataUtility['dueDate']);

            if($dataUtility['selectall'] == 'all')
            {
                $dataInvUtility = DB::select("exec sp_invoice_utility '".$dataUtility['cutoff']."','".$project_no."'");

                foreach($dataInvUtility as $data)
                {
                    $cekDataInvoice = DB::select("select *
                    from INVOICE_TRANS_DETAIL as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                    where a.ID_BILLING = ".$data->ID_BILLING."
                    and b.INVOICE_STATUS_INT NOT IN (0)");

                    if (count($cekDataInvoice) <= 0)
                    {
                        $cekDataInvoice = DB::table('INVOICE_TRANS')
                            ->where('PSM_TRANS_NOCHAR','=',$data->PSM_TRANS_NOCHAR)
                            ->where('LOT_STOCK_NO','=',$data->LOT_STOCK_NO)
                            ->where('TGL_SCHEDULE_DATE','=',$docDate)
                            ->where('TGL_SCHEDULE_DUE_DATE','=',$dueDate)
                            ->where('INVOICE_TRANS_TYPE','=','UT')
                            ->where('JOURNAL_STATUS_INT','=',0)
                            ->whereNotIn('INVOICE_STATUS_INT',[0,2,3,4])
                            ->count();

                        if ($cekDataInvoice > 0)
                        {
                            $dataInvoice = DB::table('INVOICE_TRANS')
                                ->where('PSM_TRANS_NOCHAR','=',$data->PSM_TRANS_NOCHAR)
                                ->where('LOT_STOCK_NO','=',$data->LOT_STOCK_NO)
                                ->where('TGL_SCHEDULE_DATE','=',$docDate)
                                ->where('TGL_SCHEDULE_DUE_DATE','=',$dueDate)
                                ->where('INVOICE_TRANS_TYPE','=','UT')
                                ->where('JOURNAL_STATUS_INT','=',0)
                                ->whereNotIn('INVOICE_STATUS_INT',[0,2,3,4])
                                ->first();

                            DB::table('INVOICE_TRANS_DETAIL')
                                ->insert([
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoice->INVOICE_TRANS_NOCHAR,
                                    'ID_BILLING'=>$data->ID_BILLING,
                                    'BILLING_TYPE'=>$data->BILLING_TYPE,
                                    'UTILS_TYPE_NAME'=>$data->UTILS_TYPE_NAME,
                                    'INVOICE_TRANS_DTL_DESC'=>'',
                                    'INVOICE_TRANS_DTL_DPP'=>$data->DPP,
                                    'INVOICE_TRANS_DTL_PPN'=>$data->PPN,
                                    'INVOICE_TRANS_DTL_PPH'=>($data->DPP * 0.1),
                                    'INVOICE_TRANS_DTL_TOTAL'=>$data->TOTAL,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);

                            // if (($dataInvoice->INVOICE_TRANS_TOTAL + $data->TOTAL) >= 5000000)
                            // {
                            //     $dutyStamp = 10000;
                            // }
                            // else
                            // {
                                $dutyStamp = 0;
                            // }

                            DB::table('INVOICE_TRANS')
                                ->where('PSM_TRANS_NOCHAR','=',$data->PSM_TRANS_NOCHAR)
                                ->where('LOT_STOCK_NO','=',$data->LOT_STOCK_NO)
                                ->where('TGL_SCHEDULE_DATE','=',$docDate)
                                ->where('TGL_SCHEDULE_DUE_DATE','=',$dueDate)
                                ->where('INVOICE_TRANS_TYPE','=','UT')
                                ->where('JOURNAL_STATUS_INT','=',0)
                                ->whereNotIn('INVOICE_STATUS_INT',[0,2,3])
                                ->update([
                                    'TRANS_CODE'=>$dataUtility['TRANS_CODE'],
                                    'INVOICE_TRANS_DPP'=>$dataInvoice->INVOICE_TRANS_DPP + $data->DPP,
                                    'INVOICE_TRANS_PPN'=>$dataInvoice->INVOICE_TRANS_PPN + $data->PPN,
                                    'INVOICE_TRANS_PPH'=>$dataInvoice->INVOICE_TRANS_PPH + ($data->DPP * 0.1),
                                    'INVOICE_TRANS_TOTAL'=>$dataInvoice->INVOICE_TRANS_TOTAL + $data->TOTAL,
                                    'DUTY_STAMP'=>$dutyStamp,
                                    'updated_at'=>$date
                                ]);

                            // $dataUtil = DB::table('UTILS_BILLING')
                            //     ->where('ID_BILLING','=',$data->ID_BILLING)
                            //     ->first();
                            // dd($dataUtil);

                            DB::table('UTILS_BILLING')
                                ->where('ID_BILLING','=',$data->ID_BILLING)
                                ->update([
                                    'INVOICE_UTIL_CHAR'=>$dataInvoice->INVOICE_TRANS_NOCHAR,
                                    'BILLING_STATUS'=>3, // Invoice
                                    'updated_at'=>$date
                                ]);
                        }
                        else
                        {
                            $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                            $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                            $Counter = str_pad($counter->inv_util_count, 5, "0", STR_PAD_LEFT);
                            $Year = substr($docDate->year, 2);
                            $Month = $docDate->month;
                            $monthRomawi = $converter->getRomawi($Month);

                            Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                                ->update(['inv_util_count'=>$counter->inv_util_count + 1]);

                            $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-UT/'.$monthRomawi.'/'.$Year;

                            $dateBillDate = Carbon::parse($data->BILLING_DATE);
                            $monthBillDate = str_pad($dateBillDate->month, 2, "0", STR_PAD_LEFT);
                            $yearBillDate = str_pad($dateBillDate->month, 2, "0", STR_PAD_LEFT);

                            // if ($data->TOTAL >= 5000000)
                            // {
                            //     $dutyStamp = 10000;
                            // }
                            // else
                            // {
                                $dutyStamp = 0;
                            // }

                            DB::table('INVOICE_TRANS')
                                ->insert([
                                    'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                    'PSM_SCHEDULE_ID_INT'=>0,
                                    'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                    'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                    'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                    'INVOICE_TRANS_TYPE'=>'UT',
                                    'TRANS_CODE'=>$dataUtility['TRANS_CODE'],
                                    'DOC_TYPE'=>'B',
                                    'INVOICE_TRANS_DESC_CHAR'=>'Utility '.$monthBillDate.'#'.$yearBillDate,
                                    'TGL_SCHEDULE_DATE'=>$docDate,
                                    'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                    'INVOICE_FP_NOCHAR'=>'0',
                                    'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                    'INVOICE_TRANS_DPP'=>$data->DPP,
                                    'INVOICE_TRANS_PPN'=>$data->PPN,
                                    'INVOICE_TRANS_PPH'=>($data->DPP * 0.1),
                                    'INVOICE_TRANS_TOTAL'=>$data->TOTAL,
                                    'DUTY_STAMP'=>$dutyStamp,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'INVOICE_CREATE_CHAR'=>$userName,
                                    'INVOICE_CREATE_DATE'=>$date,
                                    'FROM_SCHEDULE'=>1,
                                    'JOURNAL_STATUS_INT'=>0,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_TRANS_DETAIL')
                                ->insert([
                                    'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                    'ID_BILLING'=>$data->ID_BILLING,
                                    'BILLING_TYPE'=>$data->BILLING_TYPE,
                                    'UTILS_TYPE_NAME'=>$data->UTILS_TYPE_NAME,
                                    'INVOICE_TRANS_DTL_DESC'=>'',
                                    'INVOICE_TRANS_DTL_DPP'=>$data->DPP,
                                    'INVOICE_TRANS_DTL_PPN'=>$data->PPN,
                                    'INVOICE_TRANS_DTL_PPH'=>($data->DPP * 0.1),
                                    'INVOICE_TRANS_DTL_TOTAL'=>$data->TOTAL,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('UTILS_BILLING')
                                ->where('ID_BILLING','=',$data->ID_BILLING)
                                ->update([
                                    'INVOICE_UTIL_CHAR'=>$noInvoice,
                                    'BILLING_STATUS'=>3, // invoice
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                }
            }
            else
            {
                if (count($dataUtility['billing']) > 0)
                {
                    for($i=0;  $i < count($dataUtility['billing']); $i++){
                        if ($dataUtility['billing'][$i] <> 0)
                        {
                            $dataInvUtility = DB::select("exec sp_invoice_utility_byID '".$dataUtility['cutoff']."','".$project_no."',".$dataUtility['billing'][$i]);

                            foreach($dataInvUtility as $data)
                            {
                                $cekDataInvoice = DB::select("select *
                                from INVOICE_TRANS_DETAIL as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                where a.ID_BILLING = ".$data->ID_BILLING."
                                and b.INVOICE_STATUS_INT NOT IN (0)");

                                if (count($cekDataInvoice) <= 0)
                                {
                                    $cekDataInvoice = DB::table('INVOICE_TRANS')
                                        ->where('PSM_TRANS_NOCHAR','=',$data->PSM_TRANS_NOCHAR)
                                        ->where('LOT_STOCK_NO','=',$data->LOT_STOCK_NO)
                                        ->where('TGL_SCHEDULE_DATE','=',$docDate)
                                        ->where('TGL_SCHEDULE_DUE_DATE','=',$dueDate)
                                        ->where('INVOICE_TRANS_TYPE','=','UT')
                                        ->where('JOURNAL_STATUS_INT','=',0)
                                        ->whereNotIn('INVOICE_STATUS_INT',[0,2,3])
                                        ->count();

                                    if ($cekDataInvoice > 0)
                                    {
                                        $dataInvoice = DB::table('INVOICE_TRANS')
                                            ->where('PSM_TRANS_NOCHAR','=',$data->PSM_TRANS_NOCHAR)
                                            ->where('LOT_STOCK_NO','=',$data->LOT_STOCK_NO)
                                            ->where('TGL_SCHEDULE_DATE','=',$docDate)
                                            ->where('TGL_SCHEDULE_DUE_DATE','=',$dueDate)
                                            ->where('INVOICE_TRANS_TYPE','=','UT')
                                            ->where('JOURNAL_STATUS_INT','=',0)
                                            ->whereNotIn('INVOICE_STATUS_INT',[0,2,3])
                                            ->first();

                                        DB::table('INVOICE_TRANS_DETAIL')
                                            ->insert([
                                                'INVOICE_TRANS_NOCHAR'=>$dataInvoice->INVOICE_TRANS_NOCHAR,
                                                'ID_BILLING'=>$data->ID_BILLING,
                                                'BILLING_TYPE'=>$data->BILLING_TYPE,
                                                'UTILS_TYPE_NAME'=>$data->UTILS_TYPE_NAME,
                                                'INVOICE_TRANS_DTL_DESC'=>'',
                                                'INVOICE_TRANS_DTL_DPP'=>$data->DPP,
                                                'INVOICE_TRANS_DTL_PPN'=>$data->PPN,
                                                'INVOICE_TRANS_DTL_PPH'=>($data->DPP * 0.1),
                                                'INVOICE_TRANS_DTL_TOTAL'=>$data->TOTAL,
                                                'PROJECT_NO_CHAR'=>$project_no,
                                                'created_at'=>$date,
                                                'updated_at'=>$date
                                            ]);

                                        // if (($dataInvoice->INVOICE_TRANS_TOTAL + $data->TOTAL) >= 5000000)
                                        // {
                                        //     $dutyStamp = 10000;
                                        // }
                                        // else
                                        // {
                                            $dutyStamp = 0;
                                        // }

                                        DB::table('INVOICE_TRANS')
                                            ->where('PSM_TRANS_NOCHAR','=',$data->PSM_TRANS_NOCHAR)
                                            ->where('LOT_STOCK_NO','=',$data->LOT_STOCK_NO)
                                            ->where('TGL_SCHEDULE_DATE','=',$docDate)
                                            ->where('TGL_SCHEDULE_DUE_DATE','=',$dueDate)
                                            ->where('INVOICE_TRANS_TYPE','=','UT')
                                            ->where('JOURNAL_STATUS_INT','=',0)
                                            ->whereNotIn('INVOICE_STATUS_INT',[0,2,3])
                                            ->update([
                                                'TRANS_CODE'=>$dataUtility['TRANS_CODE'],
                                                'INVOICE_TRANS_DPP'=>$dataInvoice->INVOICE_TRANS_DPP + $data->DPP,
                                                'INVOICE_TRANS_PPN'=>$dataInvoice->INVOICE_TRANS_PPN + $data->PPN,
                                                'INVOICE_TRANS_PPH'=>$dataInvoice->INVOICE_TRANS_PPH + ($data->DPP * 0.1),
                                                'INVOICE_TRANS_TOTAL'=>$dataInvoice->INVOICE_TRANS_TOTAL + $data->TOTAL,
                                                'DUTY_STAMP'=>$dutyStamp,
                                                'updated_at'=>$date
                                            ]);

                                        // $dataUtil = DB::table('UTILS_BILLING')
                                        //     ->where('ID_BILLING','=',$data->ID_BILLING)
                                        //     ->first();
                                        // dd($dataUtil);

                                        DB::table('UTILS_BILLING')
                                            ->where('ID_BILLING','=',$data->ID_BILLING)
                                            ->update([
                                                'BILLING_STATUS'=>3, // invoice
                                                'updated_at'=>$date
                                            ]);
                                    }
                                    else
                                    {
                                        $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                                        $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                                        $Counter = str_pad($counter->inv_util_count, 5, "0", STR_PAD_LEFT);
                                        $Year = substr($docDate->year, 2);
                                        $Month = $docDate->month;
                                        $monthRomawi = $converter->getRomawi($Month);

                                        Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                                            ->update(['inv_util_count'=>$counter->inv_util_count + 1]);

                                        $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-UT/'.$monthRomawi.'/'.$Year;

                                        $dateBillDate = Carbon::parse($data->BILLING_DATE);
                                        $monthBillDate = str_pad($dateBillDate->month, 2, "0", STR_PAD_LEFT);
                                        $yearBillDate = str_pad($dateBillDate->month, 2, "0", STR_PAD_LEFT);

                                        // if (($data->TOTAL) >= 5000000)
                                        // {
                                        //     $dutyStamp = 10000;
                                        // }
                                        // else
                                        // {
                                            $dutyStamp = 0;
                                        // }

                                        DB::table('INVOICE_TRANS')
                                            ->insert([
                                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                                'PSM_SCHEDULE_ID_INT'=>0,
                                                'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                                'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                                'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                                'INVOICE_TRANS_TYPE'=>'UT',
                                                'TRANS_CODE'=>$dataUtility['TRANS_CODE'],
                                                'DOC_TYPE'=>'B',
                                                'INVOICE_TRANS_DESC_CHAR'=>'Utility '.$monthBillDate.'#'.$yearBillDate,
                                                'TGL_SCHEDULE_DATE'=>$docDate,
                                                'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                                'INVOICE_FP_NOCHAR'=>'0',
                                                'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                                'INVOICE_TRANS_DPP'=>$data->DPP,
                                                'INVOICE_TRANS_PPN'=>$data->PPN,
                                                'INVOICE_TRANS_PPH'=>($data->DPP * 0.1),
                                                'INVOICE_TRANS_TOTAL'=>$data->TOTAL,
                                                'DUTY_STAMP'=>$dutyStamp,
                                                'PROJECT_NO_CHAR'=>$project_no,
                                                'INVOICE_CREATE_CHAR'=>$userName,
                                                'INVOICE_CREATE_DATE'=>$date,
                                                'FROM_SCHEDULE'=>1,
                                                'JOURNAL_STATUS_INT'=>0,
                                                'created_at'=>$date,
                                                'updated_at'=>$date
                                            ]);

                                        DB::table('INVOICE_TRANS_DETAIL')
                                            ->insert([
                                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                                'ID_BILLING'=>$data->ID_BILLING,
                                                'BILLING_TYPE'=>$data->BILLING_TYPE,
                                                'UTILS_TYPE_NAME'=>$data->UTILS_TYPE_NAME,
                                                'INVOICE_TRANS_DTL_DESC'=>'',
                                                'INVOICE_TRANS_DTL_DPP'=>$data->DPP,
                                                'INVOICE_TRANS_DTL_PPN'=>$data->PPN,
                                                'INVOICE_TRANS_DTL_PPH'=>($data->DPP * 0.1),
                                                'INVOICE_TRANS_DTL_TOTAL'=>$data->TOTAL,
                                                'PROJECT_NO_CHAR'=>$project_no,
                                                'created_at'=>$date,
                                                'updated_at'=>$date
                                            ]);

                                        DB::table('UTILS_BILLING')
                                            ->where('ID_BILLING','=',$data->ID_BILLING)
                                            ->update([
                                                'BILLING_STATUS'=>3, // invoice
                                                'updated_at'=>$date
                                            ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            \Session::flash('message', 'Generate Invoice Util Cut Off '.$dataUtility['cutoff'].' Project '.$dataProject['PROJECT_NAME']);
            $action = "GENERATE DATA UTIL";
            $description = 'Generate Invoice Util Cut Off '.$dataUtility['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listgenerateinvoice')->with('success',$description.' Successfully');
    }

    public function saveToLog($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Invoice';
        $module = 'Finance In Flow';
        $by = $userName;
        $table = 'INVOICE_TRANS';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogInvBP($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Credit PPh';
        $module = 'Finance In Flow';
        $by = $userName;
        $table = 'INVOICE_TRANS_BP';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLog1($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Invoice';
        $module = 'Finance In Flow';
        $by = $userName;
        $table = 'INVOICE_PAYMENT';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function listDataInvoice(){
        $project_no = session('current_project');
        $dataListInv = 0;

        $billingType = DB::table('INVOICE_TRANS_TYPE')->get();

        $secureDepType = DB::table('PSM_SECURE_DEP_TYPE')
            ->where('IS_DELETE','=',0)
            ->get();

        return View::make('page.accountreceivable.listDataInvoice',
            ['project_no'=>$project_no,'dataListInv'=>$dataListInv,
             'billingType'=>$billingType,'secureDepType'=>$secureDepType]);
    }

    public function changePPHStatusInvoice(Request $request) {
        $project_no = session('current_project');
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            DB::table('INVOICE_TRANS')->where('INVOICE_TRANS_ID_INT', $request->INVOICE_ID)->update([
                'MD_TENANT_PPH_INT' => $request->PPH_STATUS_POST,
                'updated_at' => $dateNow
            ]);

            $action = "CHANGE PPH STATUS INVOICE";
            $description = 'Change PPH Status ' . $request->PPH_STATUS_POST == 1 ? "Potong Tenant" : "Potong Sendiri" . ' Invoice : '.$request->INVOICE_TRANS_NOCHAR.' succesfully';
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listdatainvoice')->with('error', 'Failed change data, errmsg : ' . $ex);
        }

        \Session::flash('success', 'PPH Status '.$request->INVOICE_TRANS_NOCHAR.' has been changed...');
        return redirect()->route('invoice.listdatainvoice');
    }

    public function viewListDataInvoice(Request $requestall){
        $project_no = session('current_project');
        $dataListInv = 1;

        $INVOICE_TRANS_NOCHAR = $requestall->INVOICE_TRANS_NOCHAR;
        $MD_TENANT_NAME_CHAR = $requestall->MD_TENANT_NAME_CHAR;
        $startDate = $requestall->startDate;
        $endDate = $requestall->endDate;
        $INVOICE_STATUS_INT = $requestall->INVOICE_STATUS_INT;
        $TYPE_INV = $requestall->TYPE_INV;
        
        $billingType = DB::table('INVOICE_TRANS_TYPE')->get();
        $secureDepType = DB::table('PSM_SECURE_DEP_TYPE')
            ->where('IS_DELETE','=',0)
            ->get();

        if ($INVOICE_TRANS_NOCHAR <> ''){
            $dataInvoiceNumber = "AND a.INVOICE_TRANS_NOCHAR like '%".$INVOICE_TRANS_NOCHAR."%'";
        }else{
            $dataInvoiceNumber = " ";
        }

        if ($MD_TENANT_NAME_CHAR <> ''){
            $dataTenant = "AND c.MD_TENANT_NAME_CHAR like '%".$MD_TENANT_NAME_CHAR."%'";
        }else{
            $dataTenant = " ";
        }

        if ($INVOICE_STATUS_INT <> 'ALL'){
            $dataStatus = "AND a.INVOICE_STATUS_INT = ".$INVOICE_STATUS_INT;
        }else{
            $dataStatus = " ";
        }

        if ($startDate <> '' && $endDate <> ''){
            $dataPeriod = "AND a.TGL_SCHEDULE_DATE between '".$startDate."' AND '".$endDate."'";
        }else{
            $dataPeriod = " ";
        }

        if($TYPE_INV <> ''){
            $dataTypeInv = "AND a.INVOICE_TRANS_TYPE IN ('".$TYPE_INV."')";
        }else{
            $dataTypeInv = " ";
        }

        $dataInvoice = DB::select("select a.INVOICE_TRANS_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,c.MD_TENANT_NAME_CHAR,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                           a.INVOICE_TRANS_DESC_CHAR,a.INVOICE_TRANS_DPP,a.INVOICE_TRANS_PPN,a.INVOICE_TRANS_TOTAL,
                                           CASE
                                                WHEN a.INVOICE_STATUS_INT = 0 THEN 'VOID'
                                                WHEN a.INVOICE_STATUS_INT = 1 THEN 'INVOICE'
                                                WHEN a.INVOICE_STATUS_INT = 2 THEN 'REQ. PAYMENT'
                                                WHEN a.INVOICE_STATUS_INT = 3 THEN 'PARTIAL PAYMENT'
                                                WHEN a.INVOICE_STATUS_INT = 4 THEN 'PAID'
                                            ELSE 'NONE' END as INVOICE_STATUS_INT,
                                            a.INVOICE_AUTOMATION_INT,a.INVOICE_TRANS_TYPE,a.JOURNAL_STATUS_INT,a.DUTY_STAMP,b.SHOP_NAME_CHAR,
                                            a.DOC_TYPE,
                                            (CASE WHEN a.MD_TENANT_PPH_INT = 1 THEN 'Potong Tenant' WHEN a.MD_TENANT_PPH_INT = 0 THEN 'Potong Sendiri' ELSE '' END) as MD_TENANT_PPH_INT,
                                            a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_RNOCHAR,d.ACC_JOURNAL_APPROVED_INT,a.IS_EXPORT_FAKTUR
                                    from INVOICE_TRANS as a LEFT JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                    LEFT JOIN MD_TENANT as c ON a.MD_TENANT_ID_INT = c.MD_TENANT_ID_INT
                                    LEFT JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                    where a.PROJECT_NO_CHAR = '".$project_no."' ".$dataInvoiceNumber." ".$dataTenant." ".$dataStatus." ".$dataPeriod." ".$dataTypeInv.
                                    " AND a.INVOICE_STATUS_INT NOT IN (0)
                                    AND a.IS_HIDE = 0
                                    ORDER BY a.LOT_STOCK_NO,a.TGL_SCHEDULE_DATE,a.INVOICE_TRANS_TYPE");

        return View::make('page.accountreceivable.listDataInvoice',
            ['project_no'=>$project_no,'dataListInv'=>$dataListInv,
             'dataInvoice'=>$dataInvoice,'billingType'=>$billingType,
             'secureDepType'=>$secureDepType]);
    }

    public function voidInvoice($INVOICE_TRANS_ID_INT){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
            ->first();

        if($dataInvoice->JOURNAL_STATUS_INT == 1
                && ($dataInvoice->INVOICE_TRANS_TYPE == 'RT' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DP' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'OT' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'RS' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'SC' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'UT' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'CL'))
        {
            $explodeFP1 = explode(".",$dataInvoice->INVOICE_FP_NOCHAR);
            $explodeFPYear = explode("-",$explodeFP1[1]);
        }

        $dataJournal = Journal::where('ACC_JOURNAL_RNOCHAR','=',$dataInvoice->ACC_JOURNAL_NOCHAR)->first();

        if(isset($dataJournal))
        {
            if ($dataJournal['ACC_JOURNAL_APPROVED_INT'] == 0)
            {
                Journal::where('ACC_JOURNAL_RNOCHAR','=',$dataInvoice->ACC_JOURNAL_NOCHAR)
                    ->update([
                        'ACC_JOURNAL_APPROVED_INT'=>2,
                        'ACC_JOURNAL_AUDITOR_CHAR'=>Session::get('name'),
                        'ACC_JOURNAL_AUDITOR_DTTIME'=>$dateNow
                    ]);
            }
        }

        $updateInvoice = DB::table('INVOICE_TRANS')
                        ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
                        ->update([
                            'INVOICE_STATUS_INT'=>0, // void
                            'INVOICE_AUDITOR_CHAR'=>Session::get('name'),
                            'INVOICE_AUDITOR_DATE'=>$dateNow,
                            'updated_at'=>$dateNow
                        ]);

        if($updateInvoice)
        {
            if ($dataInvoice->IS_EXPORT_FAKTUR == 0) // Active
            {
                if ($dataInvoice->JOURNAL_STATUS_INT == 1)
                {
                    DB::table('TAX_MD_FP')
                        ->where('TAX_MD_FP_YEAR_CHAR','=',$explodeFPYear[1])
                        ->where('TAX_MD_FP_KODE_CHAR','=',$explodeFPYear[0])
                        ->where('TAX_MD_FP_NOCHAR','=',$explodeFP1[2])
                        ->update([
                            'IS_TAKEN'=>0,
                            'UPDATED_BY'=>Session::get('name'),
                            'updated_at'=>$dateNow
                        ]);
                }
            }
        }

        if ($dataInvoice->FROM_SCHEDULE == 1)
        {
            if ($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
            {
                $dataInvoiceDetail = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                    ->get();

                foreach ($dataInvoiceDetail as $detail)
                {
                    DB::table('UTILS_BILLING')
                        ->where('ID_BILLING','=',$detail->ID_BILLING)
                        ->update([
                            'BILLING_STATUS'=>2,
                            'updated_at'=>$dateNow,
                            'updated_by'=>Session::get('name')
                        ]);
                }
            }
            elseif ($dataInvoice->INVOICE_TRANS_TYPE == 'RT' || $dataInvoice->INVOICE_TRANS_TYPE == 'DP' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'SC' || $dataInvoice->INVOICE_TRANS_TYPE == 'CL')
            {
                DB::table('PSM_SCHEDULE')
                    ->where('PSM_SCHEDULE_ID_INT','=',$dataInvoice->PSM_SCHEDULE_ID_INT)
                    ->update([
                        'SCHEDULE_STATUS_INT'=>1, // schedule aktif
                        'INVOICE_NUMBER_CHAR'=>NULL,
                        'updated_at'=>$dateNow
                    ]);
            }
        }

        $action = "VOID INVOICE DATA";
        $description = 'Void Invoice Data : '.$dataInvoice->INVOICE_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('invoice.listdatainvoice')
            ->with('success',$description);
    }

    public function postingInvoice($INVOICE_TRANS_ID_INT){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
            ->first();

        $docDate = Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE);
        $yearTaxPeriod = substr($docDate->year,2,4);
        $generator = new utilGenerator;

        try {
            \DB::beginTransaction();

            $cekDataTax = DB::table('TAX_MD_FP')
                ->where('PROJECT_NO_CHAR','=',$project_no)
                ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                ->where('IS_TAKEN','=',0)
                ->where('IS_DELETE','=',0)
                ->count();

            if ($cekDataTax <= 0)
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error','Tax Number not found, contact yout tax department ');
            }
            else
            {
            if ($dataInvoice->INVOICE_TRANS_TYPE == 'CL' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DP' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'OT' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'RS' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'SC' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'UT' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'RT')
                {
                    $taxNumber = DB::table('TAX_MD_FP')
                        ->where('PROJECT_NO_CHAR','=',$project_no)
                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                        ->where('IS_TAKEN','=',0)
                        ->where('IS_DELETE','=',0)
                        ->first();

                    $numberTax = $dataInvoice->TRANS_CODE.'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.str_pad($taxNumber->TAX_MD_FP_NOCHAR, 8, "0", STR_PAD_LEFT);

                    DB::table('TAX_MD_FP')
                        ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                        ->update([
                            'IS_TAKEN'=>1,
                            'UPDATED_BY'=>$userName,
                            'updated_at'=>$dateNow,
                        ]);
                }
                else
                {
                    $numberTax = '';
                }
            }

            $periodProject = (int)($dataProject['YEAR_PERIOD'].''.str_pad($dataProject['MONTH_PERIOD'], 2, "0", STR_PAD_LEFT));

            if ($dataInvoice->PSM_TRANS_NOCHAR == '')
            {
                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                    ->first();

                $tenantId = $dataTenant->MD_TENANT_ID_INT;
                $noPSM = '';
                $lotNo = '';
            }
            else
            {
                $dataPSM = DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                    ->first();

                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                    ->first();

                $tenantId = $dataPSM->MD_TENANT_ID_INT;
                $noPSM = $dataPSM->PSM_TRANS_NOCHAR;
                $lotNo = $dataPSM->LOT_STOCK_NO;
            }

            $docDate = Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE);
            $bulanDoc = $docDate->month;
            $tahunDoc = $docDate->year;

            $periodDoc = (int)($tahunDoc.''.str_pad($bulanDoc, 2, "0", STR_PAD_LEFT));

            if ($periodDoc < $periodProject)
            {
                return redirect()->route('invoice.listdatainvoice')
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }

            if ($dataInvoice->PSM_TRANS_NOCHAR == '')
            {
                if($dataInvoice->INVOICE_TRANS_TYPE == 'OT') {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();

                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->first();
                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                    $OTHERS = $dataInvoice->INVOICE_TRANS_DPP;
                    $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                    $BebanPajak = round($OTHERS * 0.1);
                    $UangMukaPPH = round($OTHERS * 0.1);

                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->get();
                    foreach($dataTrxtype as $trx)
                    {
                        if ($trx->MD_TRX_MODE == 'Debit')
                        {
                            if($trx->ACC_NO_CHAR == '150003006')
                            {
                                $nilaiAmount = $BillAmount;
                                $totalDebit += $BillAmount;
                            }
                            elseif($trx->ACC_NO_CHAR == '980100002')
                            {
                                $nilaiAmount = $BebanPajak;
                                $totalDebit += $BebanPajak;
                            }
                        }elseif($trx->MD_TRX_MODE == 'Kredit')
                        {
                            if($trx->ACC_NO_CHAR == '912301999')
                            {
                                $nilaiAmount = $OTHERS * -1;
                                $totalKredit += $OTHERS;
                            }
                            elseif($trx->ACC_NO_CHAR == '630002012')
                            {
                                $nilaiAmount = $PPN * -1;
                                $totalKredit += $PPN;
                            }
                            elseif($trx->ACC_NO_CHAR == '170002009')
                            {
                                $nilaiAmount = $UangMukaPPH * -1;
                                $totalKredit += $UangMukaPPH;
                            }
                        }

                        $datacoa = DB::table('ACC_MD_COA')
                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                            ->first();

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'RS') {
                    if($dataInvoice->MD_TENANT_PPH_INT == 0) // Perorangan (Potong Sendiri)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSP')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $BAGIHASIL = $dataInvoice->INVOICE_TRANS_DPP; // round($BillAmount / $dataProject['DPPBM_NUM']);
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN; // round($BillAmount / $dataProject['DPPBM_NUM']);
                        $BebanPajak = round($BAGIHASIL * 0.1);
                        $UangMukaPPH = round($BAGIHASIL * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSP')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301024')
                                {
                                    $nilaiAmount = $BAGIHASIL * -1;
                                    $totalKredit += $BAGIHASIL;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) // Badan Usaha (Potong Tenant)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSBU')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;

                        $BAGIHASIL = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;
                        $BebanPajak = round($BAGIHASIL * 0.1);
                        $UangMukaPPH = round($BAGIHASIL * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSBU')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301024')
                                {
                                    $nilaiAmount = $BAGIHASIL * -1;
                                    $totalKredit += $BAGIHASIL;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002007')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'UT') {
                    $sumElectric = DB::table('INVOICE_TRANS_DETAIL')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                        ->whereIn('BILLING_TYPE',[1,2])
                        ->sum('INVOICE_TRANS_DTL_DPP');

                    $sumWater = DB::table('INVOICE_TRANS_DETAIL')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                        ->whereIn('BILLING_TYPE',[3])
                        ->sum('INVOICE_TRANS_DTL_DPP');

                    $sumGas = DB::table('INVOICE_TRANS_DETAIL')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                        ->whereIn('BILLING_TYPE',[5])
                        ->sum('INVOICE_TRANS_DTL_DPP');

                    if($dataInvoice->MD_TENANT_PPH_INT == 0) // Perorangan (Potong Sendiri)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTP')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $ELECTRIC = $sumElectric;
                        $WATER = $sumWater;
                        $GAS = $sumGas;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;
                        $BebanPajak = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $UangMukaPPH = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $DutyStamp = $dataInvoice->DUTY_STAMP;

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTP')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003002')
                                {
                                    $nilaiAmount = ($BillAmount + $DutyStamp);
                                    $totalDebit += ($BillAmount + $DutyStamp);
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301001')
                                {
                                    $nilaiAmount = $ELECTRIC * -1;
                                    $totalKredit += $ELECTRIC;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301002')
                                {
                                    $nilaiAmount = $WATER * -1;
                                    $totalKredit += $WATER;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301003')
                                {
                                    $nilaiAmount = $GAS * -1;
                                    $totalKredit += $GAS;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                                elseif($trx->ACC_NO_CHAR == '952210002')
                                {
                                    $nilaiAmount = $DutyStamp * -1;
                                    $totalKredit += $DutyStamp;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) // Badan Usaha (Potong Tenant)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTBU')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $ELECTRIC = $sumElectric;
                        $WATER = $sumWater;
                        $GAS = $sumGas;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;
                        $BebanPajak = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $UangMukaPPH = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $DutyStamp = $dataInvoice->DUTY_STAMP;

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTBU')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003002')
                                {
                                    $nilaiAmount = ($BillAmount + $DutyStamp);
                                    $totalDebit += ($BillAmount + $DutyStamp);
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301001')
                                {
                                    $nilaiAmount = $ELECTRIC * -1;
                                    $totalKredit += $ELECTRIC;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301002')
                                {
                                    $nilaiAmount = $WATER * -1;
                                    $totalKredit += $WATER;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301003')
                                {
                                    $nilaiAmount = $GAS * -1;
                                    $totalKredit += $GAS;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002007')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                                elseif($trx->ACC_NO_CHAR == '952210002')
                                {
                                    $nilaiAmount = $DutyStamp * -1;
                                    $totalKredit += $DutyStamp;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'DP' || $dataInvoice->INVOICE_TRANS_TYPE == 'RT') {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->first();
                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                    $UNEARNED = $dataInvoice->INVOICE_TRANS_DPP;
                    $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->get();
                    foreach($dataTrxtype as $trx)
                    {
                        if ($trx->MD_TRX_MODE == 'Debit')
                        {
                            if($trx->ACC_NO_CHAR == '150003001')
                            {
                                $nilaiAmount = $BillAmount;
                                $totalDebit += $BillAmount;
                            }
                        }
                        elseif($trx->MD_TRX_MODE == 'Kredit')
                        {
                            if($trx->ACC_NO_CHAR == '650005001')
                            {
                                $nilaiAmount = $UNEARNED * -1;
                                $totalKredit += $UNEARNED;
                            }
                            elseif($trx->ACC_NO_CHAR == '630002012')
                            {
                                $nilaiAmount = $PPN * -1;
                                $totalKredit += $PPN;
                            }
                        }

                        $datacoa = DB::table('ACC_MD_COA')
                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                            ->first();

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'SC') {
                    if($dataInvoice->MD_TENANT_PPH_INT == 0) // Perorangan (Potong Sendiri)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $SERVICECHARGE = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                        $BebanPajak = round($SERVICECHARGE * 0.1);
                        $UangMukaPPH = round($SERVICECHARGE * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003001')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912200002')
                                {
                                    $nilaiAmount = $SERVICECHARGE * -1;
                                    $totalKredit += $SERVICECHARGE;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) // Badan Usaha (Potong Tenant)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $SERVICECHARGE = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                        $BebanPajak = round($SERVICECHARGE * 0.1);
                        $UangMukaPPH = round($SERVICECHARGE * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003001')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912200002')
                                {
                                    $nilaiAmount = $SERVICECHARGE * -1;
                                    $totalKredit += $SERVICECHARGE;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002007')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'CL') {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->first();
                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                    $UNEARNED = $dataInvoice->INVOICE_TRANS_DPP;
                    $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->get();
                    foreach($dataTrxtype as $trx)
                    {
                        if ($trx->MD_TRX_MODE == 'Debit')
                        {
                            if($trx->ACC_NO_CHAR == '150003004')
                            {
                                $nilaiAmount = $BillAmount;
                                $totalDebit += $BillAmount;
                            }
                        }
                        elseif($trx->MD_TRX_MODE == 'Kredit')
                        {
                            if($trx->ACC_NO_CHAR == '650001002')
                            {
                                $nilaiAmount = $UNEARNED * -1;
                                $totalKredit += $UNEARNED;
                            }
                            elseif($trx->ACC_NO_CHAR == '630002012')
                            {
                                $nilaiAmount = $PPN * -1;
                                $totalKredit += $PPN;
                            }
                        }

                        $datacoa = DB::table('ACC_MD_COA')
                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                            ->first();

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', Faktur '.$numberTax;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'RB') {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $sourcode = 'JM';

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = '150000000';
                    $inputGlTrans['ACC_NO_CHAR'] = '150003006';
                    $inputGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalDebit += $BillAmount;
                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = '160000000';
                    $inputGlTrans['ACC_NO_CHAR'] = '160012999';
                    $inputGlTrans['ACC_NAME_CHAR'] = 'PYMHD - Lainnya';
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount * -1;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalDebit += $BillAmount;
                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']= '';
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'DCL' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DEL' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DFO' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DRT' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DRV' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DSC' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DTLP')
                {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $sourcode = 'JM';

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;

                    $dataSecureType = DB::table('PSM_SECURE_DEP_TYPE')
                        ->where('PSM_SECURE_DEP_TYPE_CODE','=',$dataInvoice->INVOICE_TRANS_TYPE)
                        ->first();

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = '150000000';
                    $inputGlTrans['ACC_NO_CHAR'] = '150003006';
                    $inputGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalDebit += $BillAmount;

                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = $dataSecureType->ACC_NOP_CHAR;
                    $inputGlTrans['ACC_NO_CHAR'] = $dataSecureType->ACC_NO_CHAR;
                    $inputGlTrans['ACC_NAME_CHAR'] = $dataSecureType->ACC_NAME_CHAR;
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount * -1;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalKredit += $BillAmount;

                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                else {
                    return redirect()->route('invoice.listdatainvoice')
                        ->with('error','Invoice Type Not Found. Posting Fail...');
                }
            }
            else
            {
                if($dataInvoice->INVOICE_TRANS_TYPE == 'RS')
                {
                    if($dataInvoice->MD_TENANT_PPH_INT == 0) // Perorangan (Potong Sendiri)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSP')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $BAGIHASIL = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;
                        $BebanPajak = round($BAGIHASIL * 0.1);
                        $UangMukaPPH = round($BAGIHASIL * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSP')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301024')
                                {
                                    $nilaiAmount = $BAGIHASIL * -1;
                                    $totalKredit += $BAGIHASIL;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) // Badan Usaha (Potong Tenant)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSBU')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;

                        $BAGIHASIL = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;
                        $BebanPajak = round($BAGIHASIL * 0.1);
                        $UangMukaPPH = round($BAGIHASIL * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTRSBU')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301024')
                                {
                                    $nilaiAmount = $BAGIHASIL * -1;
                                    $totalKredit += $BAGIHASIL;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002007')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'DP' || $dataInvoice->INVOICE_TRANS_TYPE == 'RT')
                {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->first();
                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                    $UNEARNED = $dataInvoice->INVOICE_TRANS_DPP;
                    $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->get();
                    foreach($dataTrxtype as $trx)
                    {
                        if ($trx->MD_TRX_MODE == 'Debit')
                        {
                            if($trx->ACC_NO_CHAR == '150003001')
                            {
                                $nilaiAmount = $BillAmount;
                                $totalDebit += $BillAmount;
                            }
                        }
                        elseif($trx->MD_TRX_MODE == 'Kredit')
                        {
                            if($trx->ACC_NO_CHAR == '650005001')
                            {
                                $nilaiAmount = $UNEARNED * -1;
                                $totalKredit += $UNEARNED;
                            }
                            elseif($trx->ACC_NO_CHAR == '630002012')
                            {
                                $nilaiAmount = $PPN * -1;
                                $totalKredit += $PPN;
                            }
                        }

                        $datacoa = DB::table('ACC_MD_COA')
                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                            ->first();

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'SC')
                {
                    if($dataInvoice->MD_TENANT_PPH_INT == 0) // Perorangan (Potong Sendiri)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $SERVICECHARGE = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                        $BebanPajak = round($SERVICECHARGE * 0.1);
                        $UangMukaPPH = round($SERVICECHARGE * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCP')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003001')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912200002')
                                {
                                    $nilaiAmount = $SERVICECHARGE * -1;
                                    $totalKredit += $SERVICECHARGE;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $dataPSM->LOT_STOCK_NO;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) // Badan Usaha (Potong Tenant)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $SERVICECHARGE = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                        $BebanPajak = round($SERVICECHARGE * 0.1);
                        $UangMukaPPH = round($SERVICECHARGE * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTSCBU')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003001')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912200002')
                                {
                                    $nilaiAmount = $SERVICECHARGE * -1;
                                    $totalKredit += $SERVICECHARGE;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002007')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $dataPSM->LOT_STOCK_NO;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'CL')
                {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->first();
                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                    $UNEARNED = $dataInvoice->INVOICE_TRANS_DPP;
                    $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVCL2')->get();
                    foreach($dataTrxtype as $trx)
                    {
                        if ($trx->MD_TRX_MODE == 'Debit')
                        {
                            if($trx->ACC_NO_CHAR == '150003004')
                            {
                                $nilaiAmount = $BillAmount;
                                $totalDebit += $BillAmount;
                            }
                        }
                        elseif($trx->MD_TRX_MODE == 'Kredit')
                        {
                            if($trx->ACC_NO_CHAR == '650001002')
                            {
                                $nilaiAmount = $UNEARNED * -1;
                                $totalKredit += $UNEARNED;
                            }
                            elseif($trx->ACC_NO_CHAR == '630002012')
                            {
                                $nilaiAmount = $PPN * -1;
                                $totalKredit += $PPN;
                            }
                        }

                        $datacoa = DB::table('ACC_MD_COA')
                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                            ->first();

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'OT')
                {
                    if($dataInvoice->MD_TENANT_PPH_INT == 0) // Perorangan (Potong Sendiri)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->first();
                        
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $OTHERS = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                        $BebanPajak = round($OTHERS * 0.1);
                        $UangMukaPPH = round($OTHERS * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTP')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301999')
                                {
                                    $nilaiAmount = $OTHERS * -1;
                                    $totalKredit += $OTHERS;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) // Badan Usaha (Potong Tenant)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTBU')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $OTHERS = $dataInvoice->INVOICE_TRANS_DPP;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;

                        $BebanPajak = round($OTHERS * 0.1);
                        $UangMukaPPH = round($OTHERS * 0.1);

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTOTBU')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006')
                                {
                                    $nilaiAmount = $BillAmount;
                                    $totalDebit += $BillAmount;
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301999')
                                {
                                    $nilaiAmount = $OTHERS * -1;
                                    $totalKredit += $OTHERS;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002007')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataPSM->PSM_TRANS_NOCHAR;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $dataPSM->MD_TENANT_ID_INT;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $dataPSM->LOT_STOCK_NO;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
                {
                    $sumElectric = DB::table('INVOICE_TRANS_DETAIL')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                        ->whereIn('BILLING_TYPE',[1,2])
                        ->sum('INVOICE_TRANS_DTL_DPP');

                    $sumWater = DB::table('INVOICE_TRANS_DETAIL')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                        ->whereIn('BILLING_TYPE',[3])
                        ->sum('INVOICE_TRANS_DTL_DPP');

                    $sumGas = DB::table('INVOICE_TRANS_DETAIL')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                        ->whereIn('BILLING_TYPE',[5])
                        ->sum('INVOICE_TRANS_DTL_DPP');

                    if($dataInvoice->MD_TENANT_PPH_INT == 0) // Perorangan (Potong Sendiri)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTP')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $ELECTRIC = $sumElectric;
                        $WATER = $sumWater;
                        $GAS = $sumGas;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;
                        $BebanPajak = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $UangMukaPPH = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $DutyStamp = $dataInvoice->DUTY_STAMP;

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTP')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003002')
                                {
                                    $nilaiAmount = ($BillAmount + $DutyStamp);
                                    $totalDebit += ($BillAmount + $DutyStamp);
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301001')
                                {
                                    $nilaiAmount = $ELECTRIC * -1;
                                    $totalKredit += $ELECTRIC;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301002')
                                {
                                    $nilaiAmount = $WATER * -1;
                                    $totalKredit += $WATER;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301003')
                                {
                                    $nilaiAmount = $GAS * -1;
                                    $totalKredit += $GAS;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                                elseif($trx->ACC_NO_CHAR == '952210002')
                                {
                                    $nilaiAmount = $DutyStamp * -1;
                                    $totalKredit += $DutyStamp;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) // Badan Usaha (Potong Tenant)
                    {
                        // Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTBU')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;
                        $ELECTRIC = $sumElectric;
                        $WATER = $sumWater;
                        $GAS = $sumGas;
                        $PPN = $dataInvoice->INVOICE_TRANS_PPN;
                        $BebanPajak = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $UangMukaPPH = round($dataInvoice->INVOICE_TRANS_DPP * 0.1);
                        $DutyStamp = $dataInvoice->DUTY_STAMP;

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'AMTUTBU')->get();
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '150003002')
                                {
                                    $nilaiAmount = ($BillAmount + $DutyStamp);
                                    $totalDebit += ($BillAmount + $DutyStamp);
                                }
                                elseif($trx->ACC_NO_CHAR == '980100002')
                                {
                                    $nilaiAmount = $BebanPajak;
                                    $totalDebit += $BebanPajak;
                                }
                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '912301001')
                                {
                                    $nilaiAmount = $ELECTRIC * -1;
                                    $totalKredit += $ELECTRIC;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301002')
                                {
                                    $nilaiAmount = $WATER * -1;
                                    $totalKredit += $WATER;
                                }
                                elseif($trx->ACC_NO_CHAR == '912301003')
                                {
                                    $nilaiAmount = $GAS * -1;
                                    $totalKredit += $GAS;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012')
                                {
                                    $nilaiAmount = $PPN * -1;
                                    $totalKredit += $PPN;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002007')
                                {
                                    $nilaiAmount = $UangMukaPPH * -1;
                                    $totalKredit += $UangMukaPPH;
                                }
                                elseif($trx->ACC_NO_CHAR == '952210002')
                                {
                                    $nilaiAmount = $DutyStamp * -1;
                                    $totalKredit += $DutyStamp;
                                }
                            }

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$numberTax;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'RB')
                {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $sourcode = 'JM';

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = '150000000';
                    $inputGlTrans['ACC_NO_CHAR'] = '150003006';
                    $inputGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalDebit += $BillAmount;
                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = '160000000';
                    $inputGlTrans['ACC_NO_CHAR'] = '160012999';
                    $inputGlTrans['ACC_NAME_CHAR'] = 'PYMHD - Lainnya';
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount * -1;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalDebit += $BillAmount;
                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']= $dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']= '';
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                elseif($dataInvoice->INVOICE_TRANS_TYPE == 'DCL' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DEL' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DFO' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DRT' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DRV' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DSC' ||
                    $dataInvoice->INVOICE_TRANS_TYPE == 'DTLP')
                {
                    // Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $sourcode = 'JM';

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;

                    $dataSecureType = DB::table('PSM_SECURE_DEP_TYPE')
                        ->where('PSM_SECURE_DEP_TYPE_CODE','=',$dataInvoice->INVOICE_TRANS_TYPE)
                        ->first();

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = '150000000';
                    $inputGlTrans['ACC_NO_CHAR'] = '150003006';
                    $inputGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalDebit += $BillAmount;

                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $noPSM;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $tenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = $dataSecureType->ACC_NOP_CHAR;
                    $inputGlTrans['ACC_NO_CHAR'] = $dataSecureType->ACC_NO_CHAR;
                    $inputGlTrans['ACC_NAME_CHAR'] = $dataSecureType->ACC_NAME_CHAR;
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount * -1;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                    $totalKredit += $BillAmount;

                    try{
                        GlTrans::create($inputGlTrans);
                    } catch (Exception $ex) {
                        return redirect()->route('invoice.listdatainvoice')
                            ->with('error','Failed update counter table, errmsg : '.$ex);
                    }

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                }
                else
                {
                    return redirect()->route('invoice.listdatainvoice')
                        ->with('error','Invoice Type Not Found. Posting Fail...');
                }
            }

            DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
                ->update([
                    'INVOICE_FP_NOCHAR'=>$numberTax,
                    'JOURNAL_STATUS_INT'=>1,
                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                    'updated_at'=>$dateNow
                ]);

            $action = "POSTING INVOICE MANUAL DATA";
            $description = 'Posting Invoice Manual Data : '.$dataInvoice->INVOICE_TRANS_NOCHAR.' succesfully';
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listdatainvoice')->with('error','Posting Invoice Fail...');
        }

        return redirect()->route('invoice.listdatainvoice')->with('success',$description);
    }

    public function viewPaidInvoice($INVOICE_TRANS_ID_INT){
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
            ->first();

        if ($dataInvoice->PSM_TRANS_NOCHAR == '')
        {
            $namaTenant = '';
            $shopName = '';
        }
        else
        {
            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                ->first();

            $dataTenant = DB::table('MD_TENANT')
                ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
                ->first();

            $namaTenant = $dataTenant->MD_TENANT_NAME_CHAR;
            $shopName = $dataPSM->SHOP_NAME_CHAR;
        }

        $dataInvPayment = DB::table('INVOICE_PAYMENT')
            ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
            ->whereNotIn('INVOICE_PAYMENT_STATUS_INT',[0])
            ->get();

        $dataSumPaidInvoice = DB::table('INVOICE_PAYMENT_DETAIL')
            ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
            ->SUM('PAID_BILL_AMOUNT');

        $sumCN = 0;

        $dataCNDetail = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.CN_TRANS_DTL_AMOUNT
                            from CN_TRANS_DETAIL as a INNER JOIN CN_TRANS as b ON a.CN_TRANS_NOCHAR = b.CN_TRANS_NOCHAR
                            where a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'
                            and a.IS_DELETE = 0
                            and b.CN_TRANS_STATUS_INT = 2");

        foreach ($dataCNDetail as $cn)
        {
            $sumCN += $cn->CN_TRANS_DTL_AMOUNT;
        }

        if ($dataInvoice->INVOICE_TRANS_TYPE == 'DP' || $dataInvoice->INVOICE_TRANS_TYPE == 'RT' || $dataInvoice->INVOICE_TRANS_TYPE == 'SC')
        {
            $dataDetailInv = DB::select("Select a.INVOICE_TRANS_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,INVOICE_TRANS_DESC_CHAR,
                                               INVOICE_TRANS_DPP,INVOICE_TRANS_PPN,INVOICE_TRANS_PPH,INVOICE_TRANS_TOTAL
                                        from INVOICE_TRANS as a
                                        WHERE a.INVOICE_TRANS_ID_INT = ".$INVOICE_TRANS_ID_INT);
        }
        else
        {
            $dataDetailInv = DB::select("Select a.INVOICE_TRANS_DTL_ID_INT as INVOICE_TRANS_ID_INT,
                                            FORMAT(b.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                            (CASE WHEN (c.INVOICE_TRANS_TYPE IS NULL OR c.INVOICE_TRANS_TYPE = 'UT') THEN a.UTILS_TYPE_NAME ELSE a.INVOICE_TRANS_DTL_DESC END) as INVOICE_TRANS_DESC_CHAR,
                                            a.INVOICE_TRANS_DTL_DPP as INVOICE_TRANS_DPP,
                                            a.INVOICE_TRANS_DTL_PPN as INVOICE_TRANS_PPN,
                                            a.INVOICE_TRANS_DTL_PPH as INVOICE_TRANS_PPH,
                                            a.INVOICE_TRANS_DTL_TOTAL as INVOICE_TRANS_TOTAL
                                            from INVOICE_TRANS_DETAIL as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                            LEFT JOIN INVOICE_TRANS_TYPE as c ON b.INVOICE_TRANS_TYPE = c.INVOICE_TRANS_TYPE
                                            WHERE a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'");
        }

        if ($dataProject['ID_BUSINESS_INT'] == '3')
        {
            $dataCOABank = DB::select("Select *
                                        from ACC_MD_COA
                                        where PROJECT_NO_CHAR = '".$project_no."'
                                        AND ACC_TYPE_ID_INT = 2");

            $dataCoaOthers = DB::select("select *
                                    from ACC_MD_COA
                                    where ACC_NO_CHAR IN ('650004014','120999999')
                                    ORDER BY ACC_NO_CHAR");
        }
        else if($dataProject['ID_BUSINESS_INT'] == '4')
        {
            $dataCOABank = DB::select("Select *
                        from ACC_MD_COA
                        where PROJECT_NO_CHAR = '".$project_no."'
                        AND ACC_TYPE_ID_INT = 2");

            $dataCoaOthers = DB::select("select *
                    from ACC_MD_COA
                    where ACC_NO_CHAR IN ('650004014','120999999')
                    ORDER BY ACC_NO_CHAR");
        }
        else
        {
            $dataCOABank = DB::select("select *
                                    from ACC_MD_COA
                                    where (ACC_NO_CHAR like '1111%' OR ACC_NO_CHAR like '1112%' OR ACC_NO_CHAR like '1113%')
                                    and PROJECT_NO_CHAR = ".$project_no."
                                    ORDER BY ACC_NO_CHAR");
        }

        return View::make('page.accountreceivable.viewDetailInvoice',
            ['dataInvoice'=>$dataInvoice,'dataDetailInv'=>$dataDetailInv,
             'dataCOABank'=>$dataCOABank,'dataProject'=>$dataProject,
             'dataSumPaidInvoice'=>$dataSumPaidInvoice,'sumCN'=>$sumCN,
             'dataInvPayment'=>$dataInvPayment,'namaTenant'=>$namaTenant,
             'shopName'=>$shopName,'dataCoaOthers'=>$dataCoaOthers]);
    }

    public function saveInvoicePayment(Requests\AccountReceivable\AddDataInvoicePayment $requestInv){
        $inputDataInvPayment = $requestInv->all();
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $date = Carbon::parse(Carbon::now());

        try {
            \DB::beginTransaction();

            $invTotal = str_replace('.','',$inputDataInvPayment['INVOICE_DEBT_AMOUNT']);

            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$inputDataInvPayment['INVOICE_TRANS_NOCHAR'])
                ->first();

            if (($inputDataInvPayment['PAID_BILL_AMOUNT'] - $inputDataInvPayment['PAID_BILL_DENDA']) > $invTotal)
            {
                return redirect()->route('invoice.viewpaidinvoice',[$dataInvoice->INVOICE_TRANS_ID_INT])
                    ->with('error','Your Payment Input Bigger Than Invoice Amount');
            }

            if($inputDataInvPayment['backdate'] == "")
            {
                return redirect()->route('invoice.viewpaidinvoice',[$dataInvoice->INVOICE_TRANS_ID_INT])
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }

            DB::table('INVOICE_PAYMENT')
                ->insert([
                    'INVOICE_TRANS_NOCHAR'=>$inputDataInvPayment['INVOICE_TRANS_NOCHAR'],
                    'PSM_TRANS_NOCHAR'=>$inputDataInvPayment['PSM_TRANS_NOCHAR'],
                    'LOT_STOCK_NO'=>$inputDataInvPayment['LOT_STOCK_NO'],
                    'TGL_BAYAR_DATE'=>$inputDataInvPayment['TGL_BAYAR_DATE'],
                    'ACC_NOP_CHAR'=>$inputDataInvPayment['ACC_NOP_CHAR'],
                    'ACC_NO_CHAR'=>$inputDataInvPayment['ACC_NO_CHAR'],
                    'ACC_NAME_CHAR'=>$inputDataInvPayment['ACC_NAME_CHAR'],
                    'PAYMENT_METHOD'=>$inputDataInvPayment['PAYMENT_METHOD'],
                    'PAYMENT_STAMP'=>$inputDataInvPayment['PAYMENT_STAMP'],
                    // 'PAYMENT_STAMP'=>0,
                    'INVOICE_TRANS_TOTAL'=>$invTotal,
                    'PAID_BILL_DENDA'=>$inputDataInvPayment['PAID_BILL_DENDA'],
                    'PAID_BILL_AMOUNT'=>$inputDataInvPayment['PAID_BILL_AMOUNT'],
                    'PAID_BILL_PPH'=>(($inputDataInvPayment['PAID_BILL_AMOUNT']/1.01) * 0.1),
                    'PROJECT_NO_CHAR'=>$project_no,
                    'INVOICE_PAYMENT_REQ_CHAR'=>$userName,
                    'INVOICE_PAYMENT_REQ_DATE'=>$date,
                    'MD_TENANT_PPH_INT'=>$dataInvoice->MD_TENANT_PPH_INT,
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);

            DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$inputDataInvPayment['INVOICE_TRANS_NOCHAR'])
                ->update([
                    'INVOICE_STATUS_INT'=>2, // req.payment
                    'INVOICE_PAID_CHAR'=>$userName,
                    'INVOICE_PAID_DATE'=>$date,
                    'updated_at'=>$date
                ]);

            \Session::flash('message', 'Saving Payment Invoice '.$inputDataInvPayment['INVOICE_TRANS_NOCHAR'].' Lease Doc :. '.$inputDataInvPayment['PSM_TRANS_NOCHAR']);
            $action = "INSERT DATA";
            $description = 'Saving Payment Invoice '.$inputDataInvPayment['INVOICE_TRANS_NOCHAR'].' Lease Doc '.$inputDataInvPayment['PSM_TRANS_NOCHAR'];
            $this->saveToLog1($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.viewpaidinvoice',[$dataInvoice->INVOICE_TRANS_ID_INT])->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listdatainvoice')
            ->with('success',$description.' Successfully');
    }

    public function listDataInvoiceAppr(){
        $project_no = session('current_project');

        $dataInvoiceRENT = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                               c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN ('DP','RT')");

        $dataInvoiceSC = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                               c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN ('SC')");

        $dataInvoiceUT = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN ('UT')");

        $dataInvoiceCL = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN ('CL')");

        $dataInvoiceRS = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN ('RS')");

        $dataInvoiceOT = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        LEFT JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN ('OT')");

        $dataInvoiceSD = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        LEFT JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN (
                                            Select PSM_SECURE_DEP_TYPE_CODE
                                            from PSM_SECURE_DEP_TYPE
                                            where IS_DELETE = 0
                                        )");

        $dataInvoiceRB = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                               a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        LEFT JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 1
                                        AND b.INVOICE_TRANS_TYPE IN ('RB')");

        return View::make('page.accountreceivable.listDataInvoiceAppr',
            ['project_no'=>$project_no,'dataInvoiceRENT'=>$dataInvoiceRENT,
             'dataInvoiceSC'=>$dataInvoiceSC,'dataInvoiceUT'=>$dataInvoiceUT,
             'dataInvoiceCL'=>$dataInvoiceCL,'dataInvoiceRS'=>$dataInvoiceRS,
             'dataInvoiceOT'=>$dataInvoiceOT,'dataInvoiceSD'=>$dataInvoiceSD,
             'dataInvoiceRB'=>$dataInvoiceRB]);
    }

    public function rejectInvoicePayment($INVOICE_PAYMENT_ID_INT){
        $date = Carbon::parse(Carbon::now());
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $dataInvoicePayment = DB::table('INVOICE_PAYMENT')
                ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                ->first();

            $cekDataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                ->count();

            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                ->first();

            if ($cekDataInvoice > 0)
            {
                DB::table('INVOICE_PAYMENT')
                    ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                    ->update([
                        'INVOICE_PAYMENT_STATUS_INT'=>0, //reject
                        'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                        'INVOICE_PAYMENT_APPR_DATE'=>$date,
                        'updated_at'=>$date
                    ]);

                DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_ID_INT','=',$dataInvoice->INVOICE_TRANS_ID_INT)
                    ->update([
                        'INVOICE_STATUS_INT'=>1, //request
                        'INVOICE_PAID_CHAR'=>NULL,
                        'INVOICE_PAID_DATE'=>NULL,
                        'updated_at'=>$date
                    ]);
            }
            else
            {
                return redirect()->route('invoice.listdatainvoiceappr')
                    ->with('error','Your Invoice Not Found');
            }

            $action = "REJECT INV PAYMENT DATA";
            $description = 'Reject Inv Payment Data : '.$dataInvoice->INVOICE_TRANS_NOCHAR.' succesfully';
            $this->saveToLog1($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('invoice.listdatainvoiceappr')->with('error', 'Failed reject data, errmsg : ' . $ex);
        }
        
        return redirect()->route('invoice.listdatainvoiceappr')
            ->with('success',$description);
    }

    public function approveInvoicePayment($INVOICE_PAYMENT_ID_INT){
        $date = Carbon::parse(Carbon::now());
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $generator = new utilGenerator;

            $dataInvoicePayment = DB::table('INVOICE_PAYMENT')
                ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                ->first();

            $docDate = Carbon::parse($dataInvoicePayment->TGL_BAYAR_DATE);

            if ($dataProject['MONTH_PERIOD'] < 10)
            {
                $currDate = $dataProject['YEAR_PERIOD'].'0'.$dataProject['MONTH_PERIOD'];
            }
            else
            {
                $currDate = $dataProject['YEAR_PERIOD'].''.$dataProject['MONTH_PERIOD'];
            }

            if ($docDate->month < 10)
            {
                $rqsDate = $docDate->year.'0'.$docDate->month;
            }
            else
            {
                $rqsDate = $docDate->year.''.$docDate->month;
            }

            if($rqsDate < $currDate)
            {
                return redirect()->route('invoice.listdatainvoiceappr')
                    ->with('error','You Cannot Process Transaction Back Month');
            }

            $cekDataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                ->count();

            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                ->first();

            if($dataInvoice->PSM_TRANS_NOCHAR == '')
            {
                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                    ->first();

                $TenantId = $dataTenant->MD_TENANT_ID_INT;
                $lotNo = '';
            }
            else
            {
                $dataPSM = DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                    ->first();

                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                    ->first();

                $TenantId = $dataTenant->MD_TENANT_ID_INT;
                $lotNo = $dataPSM->LOT_STOCK_NO;
            }

            $noKwitansiPayment = $generator->KwitansiCicilanInvGenerator(ERROR_ROUTE_KWT_INV);

            $nilaiInvoice = $dataInvoicePayment->INVOICE_TRANS_TOTAL;

            if ($dataInvoice->INVOICE_TRANS_TYPE == 'UT' && $dataInvoicePayment->PAYMENT_STAMP == 1)
            {
                $nilaiPayment = ($dataInvoicePayment->PAID_BILL_AMOUNT + 10000) - $dataInvoicePayment->PAID_BILL_DENDA ;
            }
            else
            {
                $nilaiPayment = $dataInvoicePayment->PAID_BILL_AMOUNT - $dataInvoicePayment->PAID_BILL_DENDA;
            }

            if ($dataInvoice->INVOICE_TRANS_TYPE == 'UT' && $dataInvoicePayment->PAYMENT_STAMP == 1 && $dataInvoice->DUTY_STAMP > 0)
            {
                $dutyStamp = 0;
                $arStamp = 10000;
            }
            else
            {
                if ($dataInvoice->DUTY_STAMP > 0)
                {
                    $dutyStamp = 10000;
                    $arStamp = 0;
                }
                else
                {
                    $dutyStamp = 0;
                    $arStamp = 0;
                }
            }

            if ($cekDataInvoice > 0)
            {
                if($dataInvoice->PSM_TRANS_NOCHAR == '')
                {
                    if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                    {
                        //Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        //dd($countTable);
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;
                        //$project_code = $dataProject['PROJECT_CODE'];

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTP')->first();
                        //dd($trxtype);
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $bank = $nilaiPayment;
                        if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                        {
                            $uangMukaPPH = ($bank / 1.1) * 0.1;
                        }
                        else
                        {
                            $uangMukaPPH = ($bank / $dataProject['DPPBM_NUM']) * 0.1;
                        }

                        $piutangLainlain = $bank;
                        $pph = $uangMukaPPH;

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTP')->get();

                        $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                        $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                        $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                        $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                        $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                        $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                        $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                        $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalDebit += $bank;
                        //dd($inputGlTrans);
                        try{
                            GlTrans::create($inputFirstGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        //dd($dataTrxtype);
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '170002009') // UANG MUKA PPH PASAL4 (2)
                                {
                                    $nilaiAmount = $uangMukaPPH;
                                    $totalDebit += $uangMukaPPH;
                                }
                            }elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006') //Piutang Usaha Lain-lain
                                {
                                    $nilaiAmount = $piutangLainlain * -1;
                                    $totalKredit += $piutangLainlain;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002014') //Hutang 4 (2) Lain-Lain
                                {
                                    $nilaiAmount = $pph * -1;
                                    $totalKredit += $pph;
                                }
                            }
                            //dd($nilaiAmount);

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                    {
                        //Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        //dd($countTable);
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;
                        //$project_code = $dataProject['PROJECT_CODE'];

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTBU')->first();
                        //dd($trxtype);
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $bank = $nilaiPayment;
                        if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                        {
                            $piutangPPH = ($bank * 0.1);
                        }
                        else
                        {
                            $piutangPPH = (($bank/1.01) * 0.1);
                        }

                        $piutangLainlain = $bank + $piutangPPH;

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTBU')->get();

                        $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                        $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                        $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                        $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                        $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                        $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                        $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                        $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalDebit += $bank;
                        //dd($inputGlTrans);
                        try{
                            GlTrans::create($inputFirstGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        //dd($dataTrxtype);
                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '170002007') //PIUTANG PPH FINAL (4 AY 2)
                                {
                                    $nilaiAmount = $piutangPPH;
                                    $totalDebit += $piutangPPH;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '150003006') //Piutang Usaha Lain-lain
                                {
                                    $nilaiAmount = $piutangLainlain * -1;
                                    $totalKredit += $piutangLainlain;
                                }
                            }
                            //dd($nilaiAmount);

                            $datacoa = DB::table('ACC_MD_COA')
                                ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                ->first();

                            $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                            $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                            $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                            $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                            $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                }
                else
                {
                    //Create Journal
                    if($dataInvoice->INVOICE_TRANS_TYPE == 'RS')
                    {
                        if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRSP')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            $piutangUsaha = ($bank);

                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $uangMukaPPH = ($bank/1.1) * 0.1;
                            }
                            else
                            {
                                $uangMukaPPH = ($bank/$dataProject['DPPBM_NUM']) * 0.1;
                            }

                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRSP')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003006') //Piutang Usaha Lain-lain
                                    {
                                        $nilaiAmount = $piutangUsaha * -1;
                                        $totalKredit += $piutangUsaha;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002014') //Hutang 4 (2) Lain-Lain
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                        elseif($dataInvoice->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRSBU')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;

                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $piutangPPH = ($bank * 0.1);
                            }
                            else
                            {
                                $piutangPPH = (($bank/1.01) * 0.1);
                            }

                            $piutangUsaha = $bank + $piutangPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRSBU')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002007') //PIUTANG PPH FINAL (4 AY 2)
                                    {
                                        $nilaiAmount = $piutangPPH;
                                        $totalDebit += $piutangPPH;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003006') //Piutang Usaha Lain-lain
                                    {
                                        $nilaiAmount = $piutangUsaha * -1;
                                        $totalKredit += $piutangUsaha;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                    }
                    elseif($dataInvoice->INVOICE_TRANS_TYPE == 'DP' || $dataInvoice->INVOICE_TRANS_TYPE == 'RT')
                    {
                        if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRTP')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $uangMukaPPH = ($bank / 1.1) * 0.1;
                            }
                            else
                            {
                                $uangMukaPPH = ($bank / $dataProject['DPPBM_NUM']) * 0.1;
                            }


                            $piutangSewa = $bank;
                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRTP')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003001') //Piutang Sewa dan Service Charges
                                    {
                                        $nilaiAmount = $piutangSewa * -1;
                                        $totalKredit += $piutangSewa;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002014') //Hutang 4 (2) Lain-Lain
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                        elseif($dataInvoice->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRTBU')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $piutangPPH = ($bank * 0.1);
                            }
                            else
                            {
                                $piutangPPH = (($bank/1.01) * 0.1);
                            }


                            $piutangSewa = $bank + $piutangPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMRTBU')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002007') // PIUTANG PPH FINAL (4 AY 2)
                                    {
                                        $nilaiAmount = $piutangPPH;
                                        $totalDebit += $piutangPPH;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003001') //Piutang Sewa dan Service Charges
                                    {
                                        $nilaiAmount = $piutangSewa * -1;
                                        $totalKredit += $piutangSewa;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                    }
                    elseif($dataInvoice->INVOICE_TRANS_TYPE == 'SC')
                    {
                        if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMSCP')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $uangMukaPPH = ($bank / 1.1) * 0.1;
                            }
                            else
                            {
                                $uangMukaPPH = ($bank / $dataProject['DPPBM_NUM']) * 0.1;
                            }

                            $piutangServiceCharge = $bank;
                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMSCP')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003001') //Piutang Sewa dan Service Charges
                                    {
                                        $nilaiAmount = $piutangServiceCharge * -1;
                                        $totalKredit += $piutangServiceCharge;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002014') //Hutang 4 (2) Lain-Lain
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                        elseif($dataInvoice->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMSCBU')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $piutangPPH = ($bank * 0.1);
                            }
                            else
                            {
                                $piutangPPH = (($bank/1.01) * 0.1);
                            }

                            $piutangServiceCharge = $bank + $piutangPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMSCBU')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002007') // PIUTANG PPH FINAL (4 AY 2)
                                    {
                                        $nilaiAmount = $piutangPPH;
                                        $totalDebit += $piutangPPH;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003001') //Piutang Sewa dan Service Charges
                                    {
                                        $nilaiAmount = $piutangServiceCharge * -1;
                                        $totalKredit += $piutangServiceCharge;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                    }
                    elseif($dataInvoice->INVOICE_TRANS_TYPE == 'CL')
                    {
                        if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMCLP')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $uangMukaPPH = ($bank / 1.1) * 0.1;
                            }
                            else
                            {
                                $uangMukaPPH = ($bank / $dataProject['DPPBM_NUM']) * 0.1;
                            }

                            $piutangPameran = $bank;
                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMCLP')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Casual Leasing ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003004') //Piutang Pameran
                                    {
                                        $nilaiAmount = $piutangPameran * -1;
                                        $totalKredit += $piutangPameran;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002014') //Hutang 4 (2) Lain-Lain
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Casual Leasing  ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Casual Leasing ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                        elseif($dataInvoice->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMCLBU')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $piutangPPH = ($bank * 0.1);
                            }
                            else
                            {
                                $piutangPPH = (($bank/1.01) * 0.1);
                            }

                            $piutangPameran = $bank + $piutangPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMCLBU')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Casual Leasing ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002007') //PIUTANG PPH FINAL (4 AY 2)
                                    {
                                        $nilaiAmount = $piutangPPH;
                                        $totalDebit += $piutangPPH;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003004') //Piutang Pameran
                                    {
                                        $nilaiAmount = $piutangPameran * -1;
                                        $totalKredit += $piutangPameran;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Casual Leasing ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Casual Leasing ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                    }
                    elseif($dataInvoice->INVOICE_TRANS_TYPE == 'OT')
                    {
                        if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTP')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $uangMukaPPH = ($bank / 1.1) * 0.1;
                            }
                            else
                            {
                                $uangMukaPPH = ($bank / $dataProject['DPPBM_NUM']) * 0.1;
                            }

                            $piutangLainlain = $bank;
                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTP')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002009') // UANG MUKA PPH PASAL4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                }elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003006') //Piutang Usaha Lain-lain
                                    {
                                        $nilaiAmount = $piutangLainlain * -1;
                                        $totalKredit += $piutangLainlain;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002014') //Hutang 4 (2) Lain-Lain
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                        elseif($dataInvoice->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTBU')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $piutangPPH = ($bank * 0.1);
                            }
                            else
                            {
                                $piutangPPH = (($bank/1.01) * 0.1);
                            }

                            $piutangLainlain = $bank + $piutangPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMOTBU')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += $bank;
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002007') //PIUTANG PPH FINAL (4 AY 2)
                                    {
                                        $nilaiAmount = $piutangPPH;
                                        $totalDebit += $piutangPPH;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003006') //Piutang Usaha Lain-lain
                                    {
                                        $nilaiAmount = $piutangLainlain * -1;
                                        $totalKredit += $piutangLainlain;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                    }
                    elseif($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
                    {
                        if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMUTP')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment + $arStamp;

                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $uangMukaPPH = (($bank-$arStamp) / 1.1) * 0.1;
                            }
                            else
                            {
                                $uangMukaPPH = (($bank-$arStamp) / $dataProject['DPPBM_NUM']) * 0.1;
                            }

                            $piutangUtils = ($bank);
                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMUTP')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Utility ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = ($bank);
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += ($bank);
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '952210002') //MATERAI
                                    {
                                        $nilaiAmount = $dutyStamp;
                                        $totalDebit += $dutyStamp;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003002') //Piutang Listrik, Air dan Gas
                                    {
                                        $nilaiAmount = $piutangUtils * -1;
                                        $totalKredit += $piutangUtils;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002014') // Hutang 4 (2) Lain-Lain
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                }

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Utility ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Utility ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                        elseif($dataInvoice->MD_TENANT_PPH_INT == 1) //Badan Usaha (Potong Tenant)
                        {
                            //Create Journal
                            $Year = substr($dateNow->year, 2);
                            $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                            $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                            //dd($countTable);
                            $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                            $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                            try {
                                $countTable->save();
                            } catch (QueryException $ex) {
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;
                            //$project_code = $dataProject['PROJECT_CODE'];

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMUTBU')->first();
                            //dd($trxtype);
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment + $arStamp;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $piutangPPH = (($bank-$arStamp) * 0.1);
                            }
                            else
                            {
                                $piutangPPH = (($bank-$arStamp)/1.01) * 0.1;
                            }


                            $piutangUtils = $bank + $piutangPPH + $dutyStamp;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'BMUTBU')->get();

                            $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                            $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                            $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                            $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                            $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                            $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                            $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                            $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                            $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                            $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                            $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                            $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Utility ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputFirstGlTrans['ACC_AMOUNT_INT'] = ($bank);
                            $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                            $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                            $totalDebit += ($bank);
                            //dd($inputGlTrans);
                            try{
                                GlTrans::create($inputFirstGlTrans);
                            } catch (Exception $ex) {
                                return redirect()->route('invoice.listdatainvoice')
                                    ->with('error','Failed update counter table, errmsg : '.$ex);
                            }

                            //dd($dataTrxtype);
                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '170002007') //PIUTANG PPH FINAL (4 AY 2)
                                    {
                                        $nilaiAmount = $piutangPPH;
                                        $totalDebit += $piutangPPH;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '952210002') //MATERAI
                                    {
                                        $nilaiAmount = $dutyStamp;
                                        $totalDebit += $dutyStamp;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003002') // Piutang Listrik, Air dan Gas
                                    {
                                        $nilaiAmount = $piutangUtils * -1;
                                        $totalKredit += $piutangUtils;
                                    }
                                }
                                //dd($nilaiAmount);

                                $datacoa = DB::table('ACC_MD_COA')
                                    ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                                    ->first();

                                $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                                $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                                $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                                $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                                $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                                $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran Utility ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                                $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                                $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                                //dd($inputGlTrans);
                                try{
                                    GlTrans::create($inputGlTrans);
                                } catch (Exception $ex) {
                                    return redirect()->route('invoice.listdatainvoice')
                                        ->with('error','Failed update counter table, errmsg : '.$ex);
                                }
                            }

                            GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                ->where('ACC_AMOUNT_INT','=',0)->delete();

                            $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                            $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                            $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                            $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                            $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                            $inputJournal['PROJECT_NO_CHAR']=$project_no;
                            $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                            $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran Utility ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                            $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                            $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                            $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                            $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                            try {
                                Journal::create($inputJournal);
                            } catch (QueryException $ex) {
                                return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }
                        }
                    }
                    elseif($dataInvoice->INVOICE_TRANS_TYPE == 'DCL' || //Security Deposit Casual
                        $dataInvoice->INVOICE_TRANS_TYPE == 'DEL' || //Security Deposit Electric
                        $dataInvoice->INVOICE_TRANS_TYPE == 'DFO' || //Security Deposit Fit Out
                        $dataInvoice->INVOICE_TRANS_TYPE == 'DRT' || //Security Deposit Rental
                        $dataInvoice->INVOICE_TRANS_TYPE == 'DRV' || //Security Deposit Renovation
                        $dataInvoice->INVOICE_TRANS_TYPE == 'DSC' || //Security Deposit Service Charge
                        $dataInvoice->INVOICE_TRANS_TYPE == 'DTLP' //Security Deposit Telephone
                        )
                    {
                        //Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        //dd($countTable);
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;
                        //$project_code = $dataProject['PROJECT_CODE'];

                        $sourcode = 'BM';

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $bank = $nilaiPayment;

                        $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                        $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                        $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                        $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                        $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                        $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                        $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                        $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalDebit += $bank;
                        //dd($inputGlTrans);
                        try{
                            GlTrans::create($inputFirstGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                        $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                        $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputFirstGlTrans['ACC_NOP_CHAR'] = '150000000';
                        $inputFirstGlTrans['ACC_NO_CHAR'] = '150003006';
                        $inputFirstGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                        $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                        $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank * -1;
                        $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalKredit += $bank;
                        //dd($inputGlTrans);
                        try{
                            GlTrans::create($inputFirstGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    elseif($dataInvoice->INVOICE_TRANS_TYPE == 'RB')
                    {
                        //Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        //dd($countTable);
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;
                        //$project_code = $dataProject['PROJECT_CODE'];

                        $sourcode = 'BM';

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $bank = $nilaiPayment;

                        $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                        $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                        $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputFirstGlTrans['ACC_NOP_CHAR'] = $dataInvoicePayment->ACC_NOP_CHAR;
                        $inputFirstGlTrans['ACC_NO_CHAR'] = $dataInvoicePayment->ACC_NO_CHAR;
                        $inputFirstGlTrans['ACC_NAME_CHAR'] = $dataInvoicePayment->ACC_NAME_CHAR;
                        $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                        $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank;
                        $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalDebit += $bank;
                        //dd($inputGlTrans);
                        try{
                            GlTrans::create($inputFirstGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        $inputFirstGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputFirstGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputFirstGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputFirstGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputFirstGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                        $inputFirstGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                        $inputFirstGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputFirstGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputFirstGlTrans['ACC_NOP_CHAR'] = '150000000';
                        $inputFirstGlTrans['ACC_NO_CHAR'] = '150003006';
                        $inputFirstGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                        $inputFirstGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Pembayaran ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                        $inputFirstGlTrans['ACC_AMOUNT_INT'] = $bank * -1;
                        $inputFirstGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputFirstGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalKredit += $bank;
                        //dd($inputGlTrans);
                        try{
                            GlTrans::create($inputFirstGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$dataInvoice->INVOICE_TRANS_NOCHAR;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Pembayaran ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataInvoice->INVOICE_CREATE_CHAR;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }
                    }
                    else
                    {
                        return redirect()->route('invoice.listdatainvoiceappr')
                            ->with('error','Invoice Type Not Found. Create Journal Fail....');
                    }
                }
            }

            if ($cekDataInvoice > 0)
            {
                if ($dataInvoice->FROM_SCHEDULE == 1)
                {
                    if (($dataInvoice->INVOICE_TRANS_TYPE == 'DP' || $dataInvoice->INVOICE_TRANS_TYPE == 'RT' || $dataInvoice->INVOICE_TRANS_TYPE == 'CL'))
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        //dd('test1');
                        if ($nilaiPayment < $nilaiInvoice)
                        {
                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, // Partial Paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                    'PAID_BILL_PPH'=>(($nilaiPayment/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$dataInvoice->KWITANSI_NOCHAR,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif ($nilaiPayment >= $nilaiInvoice)
                        {
                            if($nilaiPayment > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>(($nilaiInvoice/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                        'PAID_BILL_PPH'=>(($nilaiPayment/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            $dataSumPaidInvoice = DB::table('INVOICE_PAYMENT_DETAIL')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->SUM('PAID_BILL_AMOUNT');

                            DB::table('PSM_SCHEDULE')
                                ->where('PSM_SCHEDULE_ID_INT','=',$dataInvoice->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$dataSumPaidInvoice,
                                    'SCHEDULE_STATUS_INT'=>3, //paid,
                                    'USER_CASHIER'=>$userName,
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, // Paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                    elseif (($dataInvoice->INVOICE_TRANS_TYPE == 'SC'))
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        if ($nilaiPayment < $nilaiInvoice)
                        {
                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                    'PAID_BILL_PPH'=>(($nilaiPayment/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, //partial paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif ($nilaiPayment >= $nilaiInvoice)
                        {
                            if($nilaiPayment > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>(($nilaiInvoice/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                        'PAID_BILL_PPH'=>(($nilaiPayment/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            $dataSumPaidInvoice = DB::table('INVOICE_PAYMENT_DETAIL')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->SUM('PAID_BILL_AMOUNT');

                            DB::table('PSM_SCHEDULE')
                                ->where('PSM_SCHEDULE_ID_INT','=',$dataInvoice->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$dataSumPaidInvoice,
                                    'SCHEDULE_STATUS_INT'=>3, //paid,
                                    'USER_CASHIER'=>$userName,
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, //paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                    elseif (($dataInvoice->INVOICE_TRANS_TYPE == 'UT'))
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        if (($nilaiPayment + $arStamp) < $nilaiInvoice)
                        {
                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, //partial paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>($nilaiPayment + $arStamp),
                                    'PAID_BILL_PPH'=>((($nilaiPayment + $arStamp)/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif (($nilaiPayment + $arStamp) >= $nilaiInvoice)
                        {
                            if(($nilaiPayment + $arStamp) > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>((($nilaiInvoice)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>($nilaiPayment + $arStamp),
                                        'PAID_BILL_PPH'=>((($nilaiPayment + $arStamp)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            $dataInvDetail = DB::table('INVOICE_TRANS_DETAIL')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                                ->get();

                            foreach ($dataInvDetail as $data)
                            {
                                DB::table('UTILS_BILLING')
                                    ->where('ID_BILLING','=',$data->ID_BILLING)
                                    ->update([
                                        'BILLING_STATUS'=>4, //paid
                                        'updated_at'=>$date
                                    ]);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, //paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                    else
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        if ($nilaiPayment < $nilaiInvoice)
                        {
                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, //partial paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                    'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif ($nilaiPayment >= $nilaiInvoice)
                        {
                            if($nilaiPayment > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>((($nilaiInvoice)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                        'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                        //'PAID_BILL_DEPOSIT'=>0,
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, //paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                }
                else
                {
                    if (($dataInvoice->INVOICE_TRANS_TYPE == 'DP' || $dataInvoice->INVOICE_TRANS_TYPE == 'RT' || $dataInvoice->INVOICE_TRANS_TYPE == 'CL'))
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        //dd('test1');
                        if ($nilaiPayment < $nilaiInvoice)
                        {
                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, // Partial Paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                    'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif ($nilaiPayment >= $nilaiInvoice)
                        {
                            if($nilaiPayment > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>((($nilaiInvoice)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                        'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            $dataSumPaidInvoice = DB::table('INVOICE_PAYMENT_DETAIL')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->SUM('PAID_BILL_AMOUNT');

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, // Paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                    elseif (($dataInvoice->INVOICE_TRANS_TYPE == 'SC'))
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        if ($nilaiPayment < $nilaiInvoice)
                        {
                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                    'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, //partial paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif ($nilaiPayment >= $nilaiInvoice)
                        {
                            if($nilaiPayment > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>((($nilaiInvoice)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                        'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, //paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                    elseif (($dataInvoice->INVOICE_TRANS_TYPE == 'UT'))
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        if ($nilaiPayment < $nilaiInvoice)
                        {
                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, //partial paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                    'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif ($nilaiPayment >= $nilaiInvoice)
                        {
                            if($nilaiPayment > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>((($nilaiInvoice)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                        'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, //paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                    else
                    {
                        DB::table('INVOICE_PAYMENT')
                            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                            ->update([
                                'INVOICE_PAYMENT_STATUS_INT'=>2, //approve
                                'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                                'INVOICE_PAYMENT_APPR_DATE'=>$date,
                                'updated_at'=>$date
                            ]);

                        if ($nilaiPayment < $nilaiInvoice)
                        {
                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>3, //partial paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);

                            DB::table('INVOICE_PAYMENT_DETAIL')
                                ->insert([
                                    'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                    'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                    'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                    'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                    'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                    'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                    'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                    'PROJECT_NO_CHAR'=>$project_no,
                                    'created_at'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                        elseif ($nilaiPayment >= $nilaiInvoice)
                        {
                            if($nilaiPayment > $nilaiInvoice)
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiInvoice,
                                        'PAID_BILL_PPH'=>((($nilaiInvoice)/1.01) * 0.1),
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }
                            else
                            {
                                DB::table('INVOICE_PAYMENT_DETAIL')
                                    ->insert([
                                        'INVOICE_PAYMENT_ID_INT'=>$dataInvoicePayment->INVOICE_PAYMENT_ID_INT,
                                        'INVOICE_TRANS_NOCHAR'=>$dataInvoicePayment->INVOICE_TRANS_NOCHAR,
                                        'LOT_STOCK_NO'=>$dataInvoicePayment->LOT_STOCK_NO,
                                        'TGL_BAYAR_DATE'=>$dataInvoicePayment->TGL_BAYAR_DATE,
                                        'PAID_BILL_AMOUNT'=>$nilaiPayment,
                                        'PAID_BILL_PPH'=>((($nilaiPayment)/1.01) * 0.1),
                                        //'PAID_BILL_DEPOSIT'=>0,
                                        'INVOICE_RECEIPT_NOCHAR'=>$noKwitansiPayment,
                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                        'PROJECT_NO_CHAR'=>$project_no,
                                        'created_at'=>$date,
                                        'updated_at'=>$date
                                    ]);
                            }

                            DB::table('INVOICE_TRANS')
                                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                                ->update([
                                    'INVOICE_STATUS_INT'=>4, //paid
                                    'INVOICE_AUDITOR_CHAR'=>$userName,
                                    'INVOICE_AUDITOR_DATE'=>$date,
                                    'updated_at'=>$date
                                ]);
                        }
                    }
                }
            }

            $action = "APPROVE INV PAYMENT DATA";
            $description = 'Approve Inv Payment Data : '.$dataInvoice->INVOICE_TRANS_NOCHAR.' succesfully';
            $this->saveToLog1($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listdatainvoiceappr')->with('error', 'Failed approve data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listdatainvoiceappr')
            ->with('success',$description);
    }

    public function PrintInvoicePerforma($PSM_SCHEDULE_ID_INT,$cutoff){
        $project_no = session('current_project');

        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $printDate = $converter->indonesian_date($dateNow, 'd F Y');

        $printBy = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataInvRentSC = DB::select("exec sp_invoice_rent_byID '".$cutoff."','".$project_no."',".$PSM_SCHEDULE_ID_INT);
        
        $dateSchedule = $converter->indonesian_date(Carbon::parse($dataInvRentSC[0]->TGL_SCHEDULE_DATE), 'd F Y');
        $periodSchedule = $converter->indonesian_date(Carbon::parse($dataInvRentSC[0]->TGL_SCHEDULE_DATE), 'F Y');

        $terbilangAmount = $converter->terbilang($dataInvRentSC[0]->BILL_AMOUNT);

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataInvRentSC[0]->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();

            $lotSqmSum = 0;
            $arrLevel = array();
            foreach($dataPSMLot as $data) {
                $dataLotCurrent = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
                $dataPSMLevel = \DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotCurrent->LOT_LEVEL_ID_INT)->first();
                $lotSqmSum += $data->LOT_STOCK_SQM;
                array_push($arrLevel, $dataPSMLevel->LOT_LEVEL_DESC);
            }
            $dataLot = $lotSqmSum . " M2";
            $dataLevel = implode(',', $arrLevel);
        }
        else {
            $dataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();

            $dataLevel = DB::table('LOT_LEVEL')
                ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                ->first();
        }

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.accountreceivable.pdfCetakInvoicePerforma2',
                ['dataInvRentSC'=>$dataInvRentSC,'printDate'=>$printDate,'printBy'=>$printBy,
                'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'dataTenant'=>$dataTenant,
                'dataLot'=>$dataLot,'dataLevel'=>$dataLevel,'dataPSM'=>$dataPSM,'dataProject'=>$dataProject,
                'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup]);
        }
        else {
            return View::make('page.accountreceivable.pdfCetakInvoicePerforma',
                ['dataInvRentSC'=>$dataInvRentSC,'printDate'=>$printDate,'printBy'=>$printBy,
                'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'dataTenant'=>$dataTenant,
                'dataLot'=>$dataLot,'dataLevel'=>$dataLevel,'dataPSM'=>$dataPSM,'dataProject'=>$dataProject,
                'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup]);
        }
    }

    public function PrintInvoicePerformaServiceCharge($PSM_SCHEDULE_ID_INT,$cutoff){
        $project_no = session('current_project');

        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $printDate = $converter->indonesian_date($dateNow, 'd F Y');

        $printBy = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataInvRentSC = DB::select("exec sp_invoice_sc_byID '".$cutoff."','".$project_no."',".$PSM_SCHEDULE_ID_INT);

        $dateSchedule = $converter->indonesian_date(Carbon::parse($dataInvRentSC[0]->TGL_SCHEDULE_DATE), 'd F Y');
        $periodSchedule = $converter->indonesian_date(Carbon::parse($dataInvRentSC[0]->TGL_SCHEDULE_DATE), 'F Y');

        $terbilangAmount = $converter->terbilang($dataInvRentSC[0]->BILL_AMOUNT);

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataInvRentSC[0]->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();

            $lotSqmSum = 0;
            $arrLevel = array();
            foreach($dataPSMLot as $data) {
                $dataLotCurrent = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
                $dataPSMLevel = \DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotCurrent->LOT_LEVEL_ID_INT)->first();
                $lotSqmSum += $data->LOT_STOCK_SQM;
                array_push($arrLevel, $dataPSMLevel->LOT_LEVEL_DESC);
            }
            $dataLot = $lotSqmSum . " M2";
            $dataLevel = implode(',', $arrLevel);
        }
        else {
            $dataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();

            $dataLevel = DB::table('LOT_LEVEL')
                ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                ->first();
        }

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.accountreceivable.pdfCetakInvoicePerforma2',
                ['dataInvRentSC'=>$dataInvRentSC,'printDate'=>$printDate,'printBy'=>$printBy,
                    'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'dataTenant'=>$dataTenant,
                    'dataLot'=>$dataLot,'dataLevel'=>$dataLevel,'dataPSM'=>$dataPSM,'dataProject'=>$dataProject,
                    'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup]);
        }
        else {
            return View::make('page.accountreceivable.pdfCetakInvoicePerforma',
                ['dataInvRentSC'=>$dataInvRentSC,'printDate'=>$printDate,'printBy'=>$printBy,
                    'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'dataTenant'=>$dataTenant,
                    'dataLot'=>$dataLot,'dataLevel'=>$dataLevel,'dataPSM'=>$dataPSM,'dataProject'=>$dataProject,
                    'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup]);
        }
    }

    public function PrintInvoicePerformaUtility($ID_BILLING,$cutoff){
        $project_no = session('current_project');

        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $printDate = $converter->indonesian_date($dateNow, 'd F Y');

        $printBy = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataInvRentUtility = DB::select("exec sp_invoice_utility_byID '".$cutoff."','".$project_no."',".$ID_BILLING);

        $dateSchedule = $converter->indonesian_date(Carbon::parse($dataInvRentUtility[0]->BILLING_DATE), 'd F Y');
        $periodSchedule = $converter->indonesian_date(Carbon::parse($dataInvRentUtility[0]->BILLING_DATE), 'F Y');

        $terbilangAmount = $converter->terbilang($dataInvRentUtility[0]->TOTAL);

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataInvRentUtility[0]->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();

            $lotSqmSum = 0;
            $arrLevel = array();
            foreach($dataPSMLot as $data) {
                $dataLotCurrent = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
                $dataPSMLevel = \DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotCurrent->LOT_LEVEL_ID_INT)->first();
                $lotSqmSum += $data->LOT_STOCK_SQM;
                array_push($arrLevel, $dataPSMLevel->LOT_LEVEL_DESC);
            }
            $dataLot = $lotSqmSum . " M2";
            $dataLevel = implode(',', $arrLevel);
        }
        else {
            $dataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();

            $dataLevel = DB::table('LOT_LEVEL')
                ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                ->first();
        }

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.accountreceivable.pdfCetakInvoicePerformaUtility2',
                ['dataInvRentUtility'=>$dataInvRentUtility,'printDate'=>$printDate,'printBy'=>$printBy,
                'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'dataTenant'=>$dataTenant,
                'dataLot'=>$dataLot,'dataLevel'=>$dataLevel,'dataPSM'=>$dataPSM,'dataProject'=>$dataProject,
                'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup]);
        }
        else {
            return View::make('page.accountreceivable.pdfCetakInvoicePerformaUtility',
                ['dataInvRentUtility'=>$dataInvRentUtility,'printDate'=>$printDate,'printBy'=>$printBy,
                'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'dataTenant'=>$dataTenant,
                'dataLot'=>$dataLot,'dataLevel'=>$dataLevel,'dataPSM'=>$dataPSM,'dataProject'=>$dataProject,
                'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup]);
        }
    }

    public function PrintInvoiceKwitansi($TYPE,$INVOICE_TRANS_ID_INT){
        $generator = new utilGenerator();
        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $printBy = trim(session('first_name') . ' ' . session('last_name'));
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $project_logo = $dataProject['logo'];

        $dataCompany = Company::where('ID_COMPANY_INT','=',$dataProject['ID_COMPANY_INT'])->first();

        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
            ->first();

        if ($dataInvoice->KWITANSI_NOCHAR == '')
        {
            $noKwitansi = $generator->KwitansiCicilanInvGenerator(ERROR_ROUTE_KWT_INV);

            DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
                ->update([
                    'KWITANSI_NOCHAR'=>$noKwitansi,
                    'updated_at'=>$dateNow
                ]);
        }
        else
        {
            $noKwitansi = $dataInvoice->KWITANSI_NOCHAR;
        }

        $printDate = $converter->indonesian_date($dataInvoice->TGL_SCHEDULE_DATE, 'd F Y');

        $tanggalDocument = Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE);

        $bulanDocument = $tanggalDocument->month;

        $kodeBulan = DB::table('MD_KODE_BULAN')
            ->where('ANGKA_BULAN','=',$bulanDocument)
            ->first();

        if($dataInvoice->INVOICE_TRANS_TYPE == 'UT' && $dataInvoice->INVOICE_AUTOMATION_INT == 1)
        {
            $dataInvoiceDetail = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.UTILS_TYPE_NAME,b.BILLING_METER_START_LWBP,b.BILLING_METER_END_LWBP,b.BILLING_METER_LWBP_DIFF,c.UTILS_LOW_RATE,b.BILLING_AMOUNT_LWBP,
                                                    b.BILLING_METER_START_WBP,b.BILLING_METER_END_WBP,BILLING_METER_WBP_DIFF,c.UTILS_HIGH_RATE,b.BILLING_AMOUNT_WBP,
                                                    b.BILLING_METER_BILLBOARD_DAY,b.BILLING_METER_BILLBOARD_HOUR,b.BILLING_BILLBOARD_NUM,c.UTILS_BILLBOARD_RATE,
                                                    b.IS_HANDLING,b.BILLING_HANDLING_FEE_NUM,b.IS_BPJU,b.BILLING_BPJU_NUM,b.IS_LOST_FACTOR,b.BILLING_LOST_FACTOR_NUM,
                                                    a.INVOICE_TRANS_DTL_DPP,a.INVOICE_TRANS_DTL_PPN,a.INVOICE_TRANS_DTL_TOTAL,
                                                    c.UTILS_KVA_RATE,c.UTILS_BILLBOARD_RATE,c.UTILS_BPJU_RATE,c.UTILS_LOST_FACTOR_RATE,c.UTILS_LOST_FACTOR_FIXAMT,
                                                    c.UTILS_HANDLING_FEE_FIXAMT,c.UTILS_HANDLING_FEE_FIXAMT,d.UTILS_METER_MULTIPLIER,
                                                    b.BILLING_AMOUNT_RELIABILITY,b.BILLING_PPJU_NUM,b.BILLING_ADMIN_NUM
                                            From INVOICE_TRANS_DETAIL as a INNER JOIN UTILS_BILLING as b ON a.ID_BILLING = b.ID_BILLING
                                            INNER JOIN UTILS_FORMULA as c ON b.ID_FORMULA = c.ID_U_FORMULA
                                            INNER JOIN UTILS_METER as d ON b.ID_METER = d.ID_METER
                                            where a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'");
        }
        else
        {
            if ($dataInvoice->INVOICE_AUTOMATION_INT == 0)
            {
                $dataInvoiceDetail = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                    ->get();
            }
            else
            {
                $dataInvoiceDetail = array(
                    array('NONE','OH',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)
                );
            }
        }

        $dateSchedule =  $converter->indonesian_date(Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE), 'd F Y');
        $periodSchedule =  $converter->indonesian_date(Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE), 'F Y');

        if($dataInvoice->MD_TENANT_PPH_INT == 1 && $TYPE == 'KWT')
        {
            $terbilangAmount = $converter->terbilang((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH));
        }
        else
        {
            if ($dataInvoice->MD_TENANT_PPH_INT == 1)
            {
                if($dataInvoice->DOC_TYPE == "D") {
                    $terbilangAmount = $converter->terbilang(($dataInvoice->INVOICE_TRANS_TOTAL + $dataInvoice->DUTY_STAMP));
                }
                else {
                    $terbilangAmount = $converter->terbilang((($dataInvoice->INVOICE_TRANS_TOTAL -  $dataInvoice->INVOICE_TRANS_PPH) + $dataInvoice->DUTY_STAMP));
                }
            }
            else
            {
                $terbilangAmount = $converter->terbilang((($dataInvoice->INVOICE_TRANS_TOTAL) + $dataInvoice->DUTY_STAMP));
            }
        }

        if ($dataInvoice->PSM_TRANS_NOCHAR == '')
        {
            $noPSM = '';
            $shopName = '';
            $noVA = '';
        }
        else
        {
            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                ->first();

            $noPSM = $dataPSM->PSM_TRANS_NOCHAR;
            $shopName = $dataPSM->SHOP_NAME_CHAR;
            $noVA = $dataPSM->PSM_TRANS_VA;
        }

        if ($dataInvoice->MD_TENANT_ID_INT == 0)
        {
            $tenantName = '';
            $tenantId = 0;
            $tenantAddres = '';
            $tenantCity = '';
            $tenantPosCode = '';
        }
        else
        {
            $dataTenant = DB::table('MD_TENANT')
                ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                ->first();

            $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
            $tenantId = $dataTenant->MD_TENANT_ID_INT;
            $tenantAddres = $dataTenant->MD_TENANT_ADDRESS1;
            $tenantCity = $dataTenant->MD_TENANT_CITY_CHAR;
            $tenantPosCode = $dataTenant->MD_TENANT_POSCODE;
        }

        if ($dataInvoice->LOT_STOCK_NO == '')
        {
            $lotNo = '';
            $lotId = 0;
            $sqm = 0;
            $lotLevel = '';
        }
        else
        {
            if(empty($dataPSM->LOT_STOCK_ID_INT)) {
                $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();

                $lotSqmSum = 0;
                $arrLot = array();
                $arrLevel = array();
                foreach($dataPSMLot as $data) {
                    $dataLotCurrent = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
                    $dataPSMLevel = \DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotCurrent->LOT_LEVEL_ID_INT)->first();
                    $lotSqmSum += $data->LOT_STOCK_SQM;
                    array_push($arrLot, $data->LOT_STOCK_NO);
                    array_push($arrLevel, $dataPSMLevel->LOT_LEVEL_DESC);
                }

                $lotNo = implode(',', $arrLot);
                $lotId = NULL;
                $sqm = $lotSqmSum;
                $lotLevel = implode(',', $arrLevel);
            }
            else {
                $dataLot = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_NO','=',$dataInvoice->LOT_STOCK_NO)
                    ->where('PROJECT_NO_CHAR','=',$project_no)
                    ->first();

                $dataLevel = DB::table('LOT_LEVEL')
                    ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                    ->first();

                $lotNo = $dataLot->LOT_STOCK_NO;
                $lotId = $dataLot->LOT_STOCK_ID_INT;
                $sqm = $dataLot->LOT_STOCK_SQM;
                $lotLevel = $dataLevel->LOT_LEVEL_DESC;
            }
        }

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        $dataLead = SpkAssign::where('PROJECT_NO_CHAR','=',$project_no)
            ->where('LEAD_PROJECT','=',1)->first();

        $page = 'IN';

        if ($TYPE == 'INV')
        {
            if ($dataInvoice->TGL_SCHEDULE_DATE <= '2022-09-30')
            {
                return View::make('page.accountreceivable.pdfCetakInvoice',
                    ['dataInvoice'=>$dataInvoice,'dataInvoiceDetail'=>$dataInvoiceDetail,'printDate'=>$printDate,
                        'printBy'=>$printBy,'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'page'=>$page,
                        'noVA'=>$noVA,'dataProject'=>$dataProject,'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup,
                        'noPSM'=>$noPSM,'tenantName'=>$tenantName,'tenantId'=>$tenantId,'lotNo'=>$lotNo,'lotId'=>$lotId,
                        'sqm'=>$sqm,'tenantAddres'=>$tenantAddres,'tenantCity'=>$tenantCity,'tenantPosCode'=>$tenantPosCode,
                        'shopName'=>$shopName,'lotLevel'=>$lotLevel,'dataCompany'=>$dataCompany,
                        'dataLead'=>$dataLead,'kodeBulan'=>$kodeBulan]);
            }
            else
            {
                return View::make('page.accountreceivable.pdfCetakKwitansi_Inv',
                    ['dataInvoice'=>$dataInvoice,'dataInvoiceDetail'=>$dataInvoiceDetail,'printDate'=>$printDate,
                        'printBy'=>$printBy,'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'page'=>$page,
                        'noVA'=>$noVA,'dataProject'=>$dataProject,'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup,
                        'noPSM'=>$noPSM,'tenantName'=>$tenantName,'tenantId'=>$tenantId,'lotNo'=>$lotNo,'lotId'=>$lotId,
                        'sqm'=>$sqm,'tenantAddres'=>$tenantAddres,'tenantCity'=>$tenantCity,'tenantPosCode'=>$tenantPosCode,
                        'shopName'=>$shopName,'lotLevel'=>$lotLevel,'dataCompany'=>$dataCompany,
                        'dataLead'=>$dataLead,'kodeBulan'=>$kodeBulan]);
            }
        }
        else
        {
            return View::make('page.accountreceivable.pdfCetakKwitansi',
                ['dataInvoice'=>$dataInvoice,'dataInvoiceDetail'=>$dataInvoiceDetail,'printDate'=>$printDate,
                'printBy'=>$printBy,'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,
                'noVA'=>$noVA,'dataProject'=>$dataProject,'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup,
                'noKwitansi'=>$noKwitansi,'dataLead'=>$dataLead,'page'=>$page,'noPSM'=>$noPSM,'tenantName'=>$tenantName,
                'tenantId'=>$tenantId,'lotNo'=>$lotNo,'lotId'=>$lotId,'sqm'=>$sqm,'tenantAddres'=>$tenantAddres,
                'tenantCity'=>$tenantCity,'tenantPosCode'=>$tenantPosCode,'shopName'=>$shopName,
                'lotLevel'=>$lotLevel,'dataCompany'=>$dataCompany,'kodeBulan'=>$kodeBulan]);
        }
    }

    public function PrintInvoiceKwitansiPDF($TYPE,$INVOICE_TRANS_ID_INT){
        $generator = new utilGenerator();
        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $printBy = trim(session('first_name') . ' ' . session('last_name'));
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $project_logo = $dataProject['logo'];
        
        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
            ->first();

        if ($dataInvoice->KWITANSI_NOCHAR == '')
        {
            $noKwitansi = $generator->KwitansiCicilanInvGenerator(ERROR_ROUTE_KWT_INV);

            DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
                ->update([
                    'KWITANSI_NOCHAR'=>$noKwitansi,
                    'updated_at'=>$dateNow
                ]);
        }
        else
        {
            $noKwitansi = $dataInvoice->KWITANSI_NOCHAR;
        }

        $printDate = $converter->indonesian_date($dataInvoice->TGL_SCHEDULE_DATE, 'd F Y');

        if($dataInvoice->INVOICE_TRANS_TYPE == 'UT' && $dataInvoice->INVOICE_AUTOMATION_INT == 1)
        {
            $dataInvoiceDetail = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.UTILS_TYPE_NAME,b.BILLING_METER_START_LWBP,b.BILLING_METER_END_LWBP,b.BILLING_METER_LWBP_DIFF,c.UTILS_LOW_RATE,b.BILLING_AMOUNT_LWBP,
                                                    b.BILLING_METER_START_WBP,b.BILLING_METER_END_WBP,BILLING_METER_WBP_DIFF,c.UTILS_HIGH_RATE,b.BILLING_AMOUNT_WBP,
                                                    b.BILLING_METER_BILLBOARD_DAY,b.BILLING_METER_BILLBOARD_HOUR,b.BILLING_BILLBOARD_NUM,c.UTILS_BILLBOARD_RATE,
                                                    b.IS_HANDLING,b.BILLING_HANDLING_FEE_NUM,b.IS_BPJU,b.BILLING_BPJU_NUM,b.IS_LOST_FACTOR,b.BILLING_LOST_FACTOR_NUM,
                                                    a.INVOICE_TRANS_DTL_DPP,a.INVOICE_TRANS_DTL_PPN,a.INVOICE_TRANS_DTL_TOTAL,
                                                    c.UTILS_KVA_RATE,c.UTILS_BILLBOARD_RATE,c.UTILS_BPJU_RATE,c.UTILS_LOST_FACTOR_RATE,c.UTILS_LOST_FACTOR_FIXAMT,
                                                    c.UTILS_HANDLING_FEE_FIXAMT,c.UTILS_HANDLING_FEE_FIXAMT,d.UTILS_METER_MULTIPLIER
                                            From INVOICE_TRANS_DETAIL as a INNER JOIN UTILS_BILLING as b ON a.ID_BILLING = b.ID_BILLING
                                            INNER JOIN UTILS_FORMULA as c ON b.ID_FORMULA = c.ID_U_FORMULA
                                            INNER JOIN UTILS_METER as d ON b.ID_METER = d.ID_METER
                                            where a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'");
        }
        else
        {
            if ($dataInvoice->INVOICE_AUTOMATION_INT == 0)
            {
                $dataInvoiceDetail = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
                    ->get();
            }
            else
            {
                $dataInvoiceDetail = array(
                    array('NONE','OH',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)
                );
            }
        }

        $dateSchedule =  $converter->indonesian_date(Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE), 'd F Y');
        $periodSchedule =  $converter->indonesian_date(Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE), 'F Y');

        if($dataInvoice->MD_TENANT_PPH_INT == 1 && $TYPE == 'KWT')
        {
            $terbilangAmount = $converter->terbilang((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH));
        }
        else
        {
            $terbilangAmount = $converter->terbilang($dataInvoice->INVOICE_TRANS_TOTAL);
        }

        if ($dataInvoice->PSM_TRANS_NOCHAR == '')
        {
            $noPSM = '';
            $shopName = '';
            $noVA = '';
        }
        else
        {
            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                ->first();

            $noPSM = $dataPSM->PSM_TRANS_NOCHAR;
            $shopName = $dataPSM->SHOP_NAME_CHAR;
            $noVA = $dataPSM->PSM_TRANS_VA;
        }

        if ($dataInvoice->MD_TENANT_ID_INT == 0)
        {
            $tenantName = '';
            $tenantId = 0;
            $tenantAddres = '';
            $tenantCity = '';
            $tenantPosCode = '';
        }
        else
        {
            $dataTenant = DB::table('MD_TENANT')
                ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                ->first();

            $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
            $tenantId = $dataTenant->MD_TENANT_ID_INT;
            $tenantAddres = $dataTenant->MD_TENANT_ADDRESS1;
            $tenantCity = $dataTenant->MD_TENANT_CITY_CHAR;
            $tenantPosCode = $dataTenant->MD_TENANT_POSCODE;
        }

        if ($dataInvoice->LOT_STOCK_NO == '')
        {
            $lotNo = '';
            $lotId = 0;
            $sqm = 0;
            $lotLevel = '';
        }
        else
        {
            $dataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_NO','=',$dataInvoice->LOT_STOCK_NO)
                ->where('PROJECT_NO_CHAR','=',$project_no)
                ->first();

            $dataLevel = DB::table('LOT_LEVEL')
                ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                ->first();

            $lotNo = $dataLot->LOT_STOCK_NO;
            $lotId = $dataLot->LOT_STOCK_ID_INT;
            $sqm = $dataLot->LOT_STOCK_SQM;
            $lotLevel = $dataLevel->LOT_LEVEL_DESC;
        }

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        $dataLead = SpkAssign::where('PROJECT_NO_CHAR','=',$project_no)
            ->where('LEAD_PROJECT','=',1)->first();

        $page = 'IN';

        $noInvoice = str_replace('/','#',$dataInvoice->INVOICE_TRANS_NOCHAR);

        $nameFile = $tenantName.' ('.$noInvoice.')';

        if ($TYPE == 'INV')
        {
            $pdf = \PDF::loadView('page.accountreceivable.pdfCetakInvoiceWithLogo',
                ['dataInvoice'=>$dataInvoice,'dataInvoiceDetail'=>$dataInvoiceDetail,'printDate'=>$printDate,
                'printBy'=>$printBy,'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,'page'=>$page,
                'noVA'=>$noVA,'dataProject'=>$dataProject,'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup,
                'noPSM'=>$noPSM,'tenantName'=>$tenantName,'tenantId'=>$tenantId,'lotNo'=>$lotNo,'lotId'=>$lotId,
                'sqm'=>$sqm,'tenantAddres'=>$tenantAddres,'tenantCity'=>$tenantCity,'tenantPosCode'=>$tenantPosCode,
                'shopName'=>$shopName,'lotLevel'=>$lotLevel, 'project_logo'=>$project_logo]);

            $originalName = '/INVOICE/'.$dataProject['PROJECT_CODE'].'/'.$nameFile.'.pdf';

            Storage::disk('ftp')->put($originalName, $pdf->output(), 'r+');
        }
        else
        {
            return View::make('page.accountreceivable.pdfCetakKwitansi',
                ['dataInvoice'=>$dataInvoice,'dataInvoiceDetail'=>$dataInvoiceDetail,'printDate'=>$printDate,
                    'printBy'=>$printBy,'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,
                    'noVA'=>$noVA,'dataProject'=>$dataProject,'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup,
                    'noKwitansi'=>$noKwitansi,'dataLead'=>$dataLead,'page'=>$page,'noPSM'=>$noPSM,'tenantName'=>$tenantName,
                    'tenantId'=>$tenantId,'lotNo'=>$lotNo,'lotId'=>$lotId,'sqm'=>$sqm,'tenantAddres'=>$tenantAddres,
                    'tenantCity'=>$tenantCity,'tenantPosCode'=>$tenantPosCode,'shopName'=>$shopName,
                    'lotLevel'=>$lotLevel]);
        }
    }

    public function PrintKwitansiReceipt($INVOICE_PAYMENT_ID_INT){
        $generator = new utilGenerator();
        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $printBy = trim(session('first_name') . ' ' . session('last_name'));
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataInvPayment = DB::table('INVOICE_PAYMENT')
            ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
            ->first();

        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_NOCHAR','=',$dataInvPayment->INVOICE_TRANS_NOCHAR)
            ->first();

        if ($dataInvPayment->INVOICE_RECEIPT_NOCHAR == '')
        {
            $noKwitansi = $generator->KwitansiCicilanInvGenerator(ERROR_ROUTE_KWT_INV);

            DB::table('INVOICE_PAYMENT')
                ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                ->update([
                    'INVOICE_RECEIPT_NOCHAR'=>$noKwitansi,
                    'updated_at'=>$dateNow
                ]);
        }
        else
        {
            $noKwitansi = $dataInvPayment->INVOICE_RECEIPT_NOCHAR;
        }

        if ($dataInvoice->PSM_TRANS_NOCHAR == '')
        {
            $noPSM = '';
            $shopName = '';
            $noVA = '';
        }
        else
        {
            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                ->first();

            $noPSM = $dataPSM->PSM_TRANS_NOCHAR;
            $shopName = $dataPSM->SHOP_NAME_CHAR;
            $noVA = $dataPSM->PSM_TRANS_VA;
        }

        if ($dataInvoice->MD_TENANT_ID_INT == 0)
        {
            $tenantName = '';
            $tenantId = 0;
            $tenantAddres = '';
            $tenantCity = '';
            $tenantPosCode = '';
        }
        else
        {
            $dataTenant = DB::table('MD_TENANT')
                ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                ->first();

            $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
            $tenantId = $dataTenant->MD_TENANT_ID_INT;
            $tenantAddres = $dataTenant->MD_TENANT_ADDRESS1;
            $tenantCity = $dataTenant->MD_TENANT_CITY_CHAR;
            $tenantPosCode = $dataTenant->MD_TENANT_POSCODE;
        }

        if ($dataInvoice->LOT_STOCK_NO == '')
        {
            $lotNo = '';
            $lotId = 0;
            $sqm = 0;
            $lotLevel = '';
        }
        else
        {
            if(empty($dataPSM->LOT_STOCK_ID_INT)) {
                $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();

                $lotSqmSum = 0;
                $arrLot = array();
                $arrLevel = array();
                foreach($dataPSMLot as $data) {
                    $dataLotCurrent = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
                    $dataPSMLevel = \DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotCurrent->LOT_LEVEL_ID_INT)->first();
                    $lotSqmSum += $data->LOT_STOCK_SQM;
                    array_push($arrLot, $data->LOT_STOCK_NO);
                    array_push($arrLevel, $dataPSMLevel->LOT_LEVEL_DESC);
                }

                $lotNo = implode(',', $arrLot);
                $lotId = NULL;
                $sqm = $lotSqmSum;
                $lotLevel = implode(',', $arrLevel);
            }
            else {
                $dataLot = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_NO','=',$dataInvoice->LOT_STOCK_NO)
                    ->where('PROJECT_NO_CHAR','=',$project_no)
                    ->first();

                $dataLevel = DB::table('LOT_LEVEL')
                    ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                    ->first();

                $lotNo = $dataLot->LOT_STOCK_NO;
                $lotId = $dataLot->LOT_STOCK_ID_INT;
                $sqm = $dataLot->LOT_STOCK_SQM;
                $lotLevel = $dataLevel->LOT_LEVEL_DESC;
            }
        }

        $printDate = $converter->indonesian_date($dataInvPayment->TGL_BAYAR_DATE, 'd F Y');

        if($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
        {

            $dataInvoiceDetail = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.UTILS_TYPE_NAME,a.INVOICE_TRANS_DTL_DPP,
                                                   a.INVOICE_TRANS_DTL_PPN,a.INVOICE_TRANS_DTL_TOTAL
                                            from INVOICE_TRANS_DETAIL as a INNER JOIN UTILS_BILLING as b ON a.ID_BILLING = b.ID_BILLING
                                            where a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'");
        }
        else
        {
            $dataInvoiceDetail = array(
                array('NONE','OH',0,0,0,0,0)
            );
        }

        $dateSchedule =  $converter->indonesian_date(Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE), 'd F Y');
        $periodSchedule =  $converter->indonesian_date(Carbon::parse($dataInvoice->TGL_SCHEDULE_DATE), 'F Y');

        $terbilangAmount = $converter->terbilang($dataInvPayment->PAID_BILL_AMOUNT);

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataLot = NULL;
            $dataLevel = NULL;
        }
        else {
            $dataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();

            $dataLevel = DB::table('LOT_LEVEL')
                ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                ->first();
        }

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        $dataLead = SpkAssign::where('PROJECT_NO_CHAR','=',$project_no)
            ->where('LEAD_PROJECT','=',1)->first();

        $page = "BM";

        return View::make('page.accountreceivable.pdfCetakKwitansi',
            ['dataInvoice'=>$dataInvoice,'dataInvoiceDetail'=>$dataInvoiceDetail,'printDate'=>$printDate,
                'printBy'=>$printBy,'dateSchedule'=>$dateSchedule,'periodSchedule'=>$periodSchedule,
                'dataTenant'=>$dataTenant,'dataLot'=>$dataLot,'dataLevel'=>$dataLevel,'dataPSM'=>$dataPSM,
                'dataProject'=>$dataProject,'terbilangAmount'=>$terbilangAmount,'dataFinSetup'=>$dataFinSetup,
                'noKwitansi'=>$noKwitansi,'dataLead'=>$dataLead,'page'=>$page,'noPSM'=>$noPSM,'tenantName'=>$tenantName,
                'tenantId'=>$tenantId,'lotNo'=>$lotNo,'lotId'=>$lotId,'sqm'=>$sqm,'tenantAddres'=>$tenantAddres,
                'tenantCity'=>$tenantCity,'tenantPosCode'=>$tenantPosCode,'shopName'=>$shopName,
                'lotLevel'=>$lotLevel,'dataInvPayment'=>$dataInvPayment]);
    }

    public function viewAddDataInvoiceManual(){
        $project_no = session('current_project');

        $billingType = DB::table('INVOICE_TRANS_TYPE')
            ->where('INVOICE_TRANS_TYPE_STATUS','=',1)
            ->get();

        $secureDepType = DB::table('PSM_SECURE_DEP_TYPE')
            ->where('IS_DELETE','=',0)
            ->get();

        $dataLot = DB::select("Select a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT,SUM(c.LOT_STOCK_SQM) AS LOT_STOCK_SQM
                                from PSM_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                LEFT JOIN PSM_TRANS_LOT as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                WHERE a.PSM_TRANS_STATUS_INT = 2
                                AND a.PROJECT_NO_CHAR = '".$project_no."'
                                GROUP BY a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT");

        $tenant = DB::select("SELECT *
                            FROM MD_TENANT
                            WHERE PROJECT_NO_CHAR = '".$project_no."'");

        return View::make('page.accountreceivable.addDataInvoiceManual',
            ['billingType'=>$billingType,'dataLot'=>$dataLot,'tenant'=>$tenant,'secureDepType'=>$secureDepType]);
    }

    public function viewAddDataRevenueSharing(){
        $date = Carbon::parse(Carbon::now());

        $project_no = session('current_project');

        $billingType = DB::table('INVOICE_TRANS_TYPE')
            ->where('INVOICE_TRANS_TYPE_STATUS','=',1)
            ->whereIn('INVOICE_TRANS_TYPE',['RS'])
            ->get();

        $dataLot = DB::select("exec sp_unit_revenue_sharing2 '".$date."','".$project_no."'");

        return View::make('page.accountreceivable.addDataInvoiceRevenueSharing',
            ['billingType'=>$billingType,'dataLot'=>$dataLot]);
    }

    public function saveInvoiceManual(Requests\AccountReceivable\AddDataInvoiceManual $requestInv){
        try {
            \DB::beginTransaction();

            $inputDataInvManual = $requestInv->all();
            $converter = new utilConverter();
            $docDate = Carbon::parse($inputDataInvManual['TGL_SCHEDULE_DATE']);

            $project_no = session('current_project');
            $userName = trim(session('first_name') . ' ' . session('last_name'));
            $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

            $date = Carbon::parse(Carbon::now());

            $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

            $dataTenant = DB::table("MD_TENANT")
                ->where('MD_TENANT_ID_INT','=',$inputDataInvManual['MD_TENANT_ID_INT'])
                ->first();

            if($inputDataInvManual['INVOICE_TRANS_TYPE'] == "UT") {
                $Counter = str_pad($counter->inv_util_count, 5, "0", STR_PAD_LEFT);
                Counter::where('PROJECT_NO_CHAR', '=', $project_no)->update(['inv_util_count'=>$counter->inv_util_count + 1]);
            }
            else if($inputDataInvManual['INVOICE_TRANS_TYPE'] == "CL") {
                $Counter = str_pad($counter->inv_casual_count, 5, "0", STR_PAD_LEFT);
                Counter::where('PROJECT_NO_CHAR', '=', $project_no)->update(['inv_casual_count'=>$counter->inv_casual_count + 1]);
            }
            else if($inputDataInvManual['INVOICE_TRANS_TYPE'] == "OT") {
                $Counter = str_pad($counter->inv_ot_count, 5, "0", STR_PAD_LEFT);
                Counter::where('PROJECT_NO_CHAR', '=', $project_no)->update(['inv_ot_count'=>$counter->inv_ot_count + 1]);
            }
            else if($inputDataInvManual['INVOICE_TRANS_TYPE'] == "RT") {
                $Counter = str_pad($counter->inv_rent_count, 5, "0", STR_PAD_LEFT);
                Counter::where('PROJECT_NO_CHAR', '=', $project_no)->update(['inv_rent_count'=>$counter->inv_rent_count + 1]);
            }
            else if($inputDataInvManual['INVOICE_TRANS_TYPE'] == "SC") {
                $Counter = str_pad($counter->inv_sc_count, 5, "0", STR_PAD_LEFT);
                Counter::where('PROJECT_NO_CHAR', '=', $project_no)->update(['inv_sc_count'=>$counter->inv_sc_count + 1]);
            }
            else if($inputDataInvManual['INVOICE_TRANS_TYPE'] == "DCL" ||
                $inputDataInvManual['INVOICE_TRANS_TYPE'] == "DEL" ||
                $inputDataInvManual['INVOICE_TRANS_TYPE'] == "DFO" ||
                $inputDataInvManual['INVOICE_TRANS_TYPE'] == "DRT" ||
                $inputDataInvManual['INVOICE_TRANS_TYPE'] == "DRV" ||
                $inputDataInvManual['INVOICE_TRANS_TYPE'] == "DSC" ||
                $inputDataInvManual['INVOICE_TRANS_TYPE'] == "DTLP") {

                $Counter = str_pad($counter->inv_securedep_count, 5, "0", STR_PAD_LEFT);
                Counter::where('PROJECT_NO_CHAR', '=', $project_no)->update(['inv_securedep_count'=>$counter->inv_securedep_count + 1]);
            }
            else {
                $Counter = str_pad($counter->inv_manual_count, 5, "0", STR_PAD_LEFT);
                Counter::where('PROJECT_NO_CHAR', '=', $project_no)->update(['inv_manual_count'=>$counter->inv_manual_count + 1]);
            }

            $Year = substr($docDate->year, 2);
            $Month = $docDate->month;
            $monthRomawi = $converter->getRomawi($Month);

            $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$inputDataInvManual['INVOICE_TRANS_TYPE'].'/'.$monthRomawi.'/'.$Year;

            DB::table('INVOICE_TRANS')
                ->insert([
                    'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                    //'INVOICE_FP_NOCHAR'=>'0',
                    'PSM_SCHEDULE_ID_INT'=>0,
                    'PSM_TRANS_NOCHAR'=>$inputDataInvManual['PSM_TRANS_NOCHAR'],
                    'MD_TENANT_ID_INT'=>$inputDataInvManual['MD_TENANT_ID_INT'],
                    'LOT_STOCK_NO'=>$inputDataInvManual['LOT_STOCK_NO'],
                    'INVOICE_TRANS_TYPE'=>$inputDataInvManual['INVOICE_TRANS_TYPE'],
                    'TRANS_CODE'=>$inputDataInvManual['TRANS_CODE'],
                    'DOC_TYPE'=>$inputDataInvManual['DOC_TYPE'],
                    'MD_TENANT_PPH_INT'=>$dataTenant->MD_TENANT_PPH_INT,
                    'INVOICE_TRANS_DESC_CHAR'=>$inputDataInvManual['INVOICE_TRANS_DESC_CHAR'],
                    'TGL_SCHEDULE_DATE'=>$inputDataInvManual['TGL_SCHEDULE_DATE'],
                    'TGL_SCHEDULE_DUE_DATE'=>$inputDataInvManual['TGL_SCHEDULE_DUE_DATE'],
                    'INVOICE_TRANS_DPP'=>0,
                    'INVOICE_TRANS_PPN'=>0,
                    'INVOICE_TRANS_TOTAL'=>0,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'INVOICE_CREATE_CHAR'=>$userName,
                    'INVOICE_CREATE_DATE'=>$date,
                    'INVOICE_AUTOMATION_INT'=>0,
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);

            $action = "INSERT DATA INV MANUAL";
            $description = 'Saving Invoice Manual '.$noInvoice.' Lease Doc :. '.$inputDataInvManual['PSM_TRANS_NOCHAR'];
            $this->saveToLog($action, $description);

            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                ->first();

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect('invoice.vieweditdatainvoicemanual', [$dataInvoice->INVOICE_TRANS_ID_INT,$dataInvoice->DOC_TYPE])->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.vieweditdatainvoicemanual',[$dataInvoice->INVOICE_TRANS_ID_INT,$dataInvoice->DOC_TYPE])
            ->with('success',$description.' Successfully');
    }

    public function saveInvoiceRevenueSharing(Requests\AccountReceivable\AddDataInvoiceRevenueSharing $requestInv){
        $inputDataInvManual = $requestInv->all();
        $converter = new utilConverter();
        
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

            $date = Carbon::parse(Carbon::now());

            $docDate = Carbon::parse($inputDataInvManual['TGL_SCHEDULE_DATE']);

            $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

            $Counter = str_pad($counter->inv_manual_count, 5, "0", STR_PAD_LEFT);
            $Year = substr($docDate->year, 2);
            $Month = $docDate->month;
            $monthRomawi = $converter->getRomawi($Month);

            Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                ->update(['inv_manual_count'=>$counter->inv_manual_count + 1]);

            $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$inputDataInvManual['INVOICE_TRANS_TYPE'].'/'.$monthRomawi.'/'.$Year;

            DB::table('INVOICE_TRANS')
                ->insert([
                    'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                    //'INVOICE_FP_NOCHAR'=>'0',
                    'PSM_SCHEDULE_ID_INT'=>0,
                    'PSM_TRANS_NOCHAR'=>$inputDataInvManual['PSM_TRANS_NOCHAR'],
                    'LOT_STOCK_NO'=>$inputDataInvManual['LOT_STOCK_NO'],
                    'INVOICE_TRANS_TYPE'=>$inputDataInvManual['INVOICE_TRANS_TYPE'],
                    'INVOICE_TRANS_DESC_CHAR'=>$inputDataInvManual['INVOICE_TRANS_DESC_CHAR'],
                    'TGL_SCHEDULE_DATE'=>$inputDataInvManual['TGL_SCHEDULE_DATE'],
                    'TGL_SCHEDULE_DUE_DATE'=>$inputDataInvManual['TGL_SCHEDULE_DUE_DATE'],
                    'INVOICE_TRANS_DPP'=>0,
                    'INVOICE_TRANS_PPN'=>0,
                    'INVOICE_TRANS_TOTAL'=>0,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'INVOICE_CREATE_CHAR'=>$userName,
                    'INVOICE_CREATE_DATE'=>$date,
                    'INVOICE_AUTOMATION_INT'=>0,
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);

            $action = "INSERT DATA INV REVENUE SHARING";
            $description = 'Saving Invoice Revenue Sharing '.$noInvoice.' Lease Doc :. '.$inputDataInvManual['PSM_TRANS_NOCHAR'];
            $this->saveToLog($action, $description);

            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                ->first();

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('invoice.viewadddatarevenuesharing')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.vieweditdatainvoicerevenuesharing',[$dataInvoice->INVOICE_TRANS_ID_INT])
            ->with('success',$description.' Successfully');
    }

    public function saveEditInvoiceManual(Requests\AccountReceivable\AddDataInvoiceManual $requestInv){
        $inputDataInvManual = $requestInv->all();
        $converter = new utilConverter();
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_NOCHAR','=',$inputDataInvManual['INVOICE_TRANS_NOCHAR'])
            ->first();

        if ($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
        {
            // if (($dataInvoice->INVOICE_TRANS_TOTAL) >= 5000000)
            // {
            //     $dutyStamp = 10000;
            // }
            // else
            // {
                $dutyStamp = 0;
            // }
        }
        else
        {
            $dutyStamp = 0;
        }

        $date = Carbon::parse(Carbon::now());

        DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_NOCHAR','=',$inputDataInvManual['INVOICE_TRANS_NOCHAR'])
            ->update([
                //'INVOICE_FP_NOCHAR'=>'0',
                'PSM_TRANS_NOCHAR'=>$inputDataInvManual['PSM_TRANS_NOCHAR'],
                'MD_TENANT_ID_INT'=>$inputDataInvManual['MD_TENANT_ID_INT'],
                'LOT_STOCK_NO'=>$inputDataInvManual['LOT_STOCK_NO'],
                'INVOICE_TRANS_TYPE'=>$inputDataInvManual['INVOICE_TRANS_TYPE'],
                'TRANS_CODE'=>$inputDataInvManual['TRANS_CODE'],
                'INVOICE_TRANS_DESC_CHAR'=>$inputDataInvManual['INVOICE_TRANS_DESC_CHAR'],
                'TGL_SCHEDULE_DATE'=>$inputDataInvManual['TGL_SCHEDULE_DATE'],
                'TGL_SCHEDULE_DUE_DATE'=>$inputDataInvManual['TGL_SCHEDULE_DUE_DATE'],
                'INVOICE_CREATE_CHAR'=>$userName,
                'DUTY_STAMP'=>$dutyStamp,
                'INVOICE_CREATE_DATE'=>$date,
                // 'INVOICE_AUTOMATION_INT'=>0,
                'created_at'=>$date,
                'updated_at'=>$date
            ]);

        $action = "UPDATE DATA INV MANUAL";
        $description = 'update Invoice Manual '.$inputDataInvManual['INVOICE_TRANS_NOCHAR'].' Lease Doc :. '.$inputDataInvManual['PSM_TRANS_NOCHAR'];
        $this->saveToLog($action, $description);

        return redirect()->route('invoice.listdatainvoice')
            ->with('success',$description.' Successfully');
    }

    public function viewEditDataInvoiceManual($INVOICE_TRANS_ID_INT,$DOC_TYPE){
        $project_no = session('current_project');

        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
            ->first();

        if ($dataInvoice->PSM_TRANS_NOCHAR == '')
        {
            $dataTenant = DB::table('MD_TENANT')
                ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                ->first();

            $lotNo = '';
            $lotId = 0;
            $noPSM = '';
            $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
            $tenantId = $dataTenant->MD_TENANT_ID_INT;
            $sqm = 0;
        }
        else
        {
            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                ->first();

            if(empty($dataPSM->LOT_STOCK_ID_INT)) {
                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                    ->first();

                $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();
                $lotSqmSum = 0;
                $arrLot = array();
                foreach($dataPSMLot as $data) {
                    $lotSqmSum += $data->LOT_STOCK_SQM;
                    array_push($arrLot, $data->LOT_STOCK_NO);
                }

                $lotNo = implode(',', $arrLot);
                $lotId = NULL;
                $noPSM = $dataInvoice->PSM_TRANS_NOCHAR;
                $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
                $tenantId = $dataTenant->MD_TENANT_ID_INT;
                $sqm = $lotSqmSum;
            }
            else {
                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataInvoice->MD_TENANT_ID_INT)
                    ->first();

                $lotData = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_NO','=',$dataInvoice->LOT_STOCK_NO)
                    ->first();

                $lotNo = $lotData->LOT_STOCK_NO;
                $lotId = $lotData->LOT_STOCK_ID_INT;
                $noPSM = $dataInvoice->PSM_TRANS_NOCHAR;
                $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
                $tenantId = $dataTenant->MD_TENANT_ID_INT;
                $sqm = $lotData->LOT_STOCK_SQM;
            }
        }

        $dataInvoiceDetail = DB::select("Select *
                                        from INVOICE_TRANS_DETAIL as a LEFT JOIN UTILS_TYPE as b ON a.BILLING_TYPE = b.id
                                        where a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'");

        if ($DOC_TYPE == 'B')
        {
            $dataBillingType = DB::table('INVOICE_TRANS_TYPE')
                ->where('INVOICE_TRANS_TYPE','=',$dataInvoice->INVOICE_TRANS_TYPE)
                ->first();

            $billingCode = $dataBillingType->INVOICE_TRANS_TYPE;
            $billingDesc = $dataBillingType->INVOICE_TRANS_TYPE_DESC;
        }
        else
        {
            $dataBillingType = DB::table('PSM_SECURE_DEP_TYPE')
                ->where('PSM_SECURE_DEP_TYPE_CODE','=',$dataInvoice->INVOICE_TRANS_TYPE)
                ->first();

            $billingCode = $dataBillingType->PSM_SECURE_DEP_TYPE_CODE;
            $billingDesc = $dataBillingType->PSM_SECURE_DEP_TYPE_DESC;
        }

        $billingType = DB::table('INVOICE_TRANS_TYPE')
            ->where('INVOICE_TRANS_TYPE_STATUS','=',1)
            ->get();

        $utilType = DB::table('UTILS_TYPE')
            ->where('UTILS_TYPE_STATUS','=',1)
            ->get();

        $dataLot = DB::select("Select a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT,SUM(c.LOT_STOCK_SQM) AS LOT_STOCK_SQM
                                from PSM_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                LEFT JOIN PSM_TRANS_LOT as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                WHERE a.PSM_TRANS_STATUS_INT = 2
                                AND a.PROJECT_NO_CHAR = '".$project_no."'
                                GROUP BY a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT");

        $tenant = DB::select("SELECT *
                            FROM MD_TENANT
                            WHERE PROJECT_NO_CHAR = '".$project_no."'");

        return View::make('page.accountreceivable.editDataInvoiceManual',
            ['billingType'=>$billingType,'dataLot'=>$dataLot,'tenant'=>$tenant,
             'dataInvoiceDetail'=>$dataInvoiceDetail,'tenantId'=>$tenantId,
             'dataBillingType'=>$dataBillingType,'dataInvoice'=>$dataInvoice,
             'utilType'=>$utilType,'lotNo'=>$lotNo,'lotId'=>$lotId,'noPSM'=>$noPSM,
             'tenantName'=>$tenantName,'sqm'=>$sqm,'billingCode'=>$billingCode,
             'billingDesc'=>$billingDesc]);
    }

    public function viewEditDataInvoiceRevenueSharing($INVOICE_TRANS_ID_INT){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        $dataInvoice = DB::table('INVOICE_TRANS')
            ->where('INVOICE_TRANS_ID_INT','=',$INVOICE_TRANS_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();
            $lotSqmSum = 0;
            $arrLot = array();
            foreach($dataPSMLot as $data) {
                $lotSqmSum += $data->LOT_STOCK_SQM;
                array_push($arrLot, $data->LOT_STOCK_NO);
            }

            $lotNo = implode(',', $arrLot);
            $lotId = NULL;
            $sqm = $lotSqmSum;
        }
        else {
            $lotData = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_NO','=',$dataInvoice->LOT_STOCK_NO)
                ->first();

            $lotNo = $lotData->LOT_STOCK_NO;
            $lotId = $lotData->LOT_STOCK_ID_INT;
            $sqm = $lotData->LOT_STOCK_SQM;
        }

        $dataInvoiceDetail = DB::table('INVOICE_TRANS_DETAIL')
            ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoice->INVOICE_TRANS_NOCHAR)
            ->get();


        $dataBillingType = DB::table('INVOICE_TRANS_TYPE')
            ->where('INVOICE_TRANS_TYPE','=',$dataInvoice->INVOICE_TRANS_TYPE)
            ->first();

        $billingType = DB::table('INVOICE_TRANS_TYPE')
            ->where('INVOICE_TRANS_TYPE_STATUS','=',1)
            ->get();

        $dataLot = DB::select("exec sp_unit_revenue_sharing '".$dateNow->format('Y-m-d')."','".$project_no."'");

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.accountreceivable.editDataInvoiceRevenueSharing2',
                ['billingType'=>$billingType,'dataLot'=>$dataLot,'lotData'=>$lotData,
                    'dataTenant'=>$dataTenant,'dataInvoiceDetail'=>$dataInvoiceDetail,
                    'dataBillingType'=>$dataBillingType,'dataInvoice'=>$dataInvoice,
                    'lotNo'=>$lotNo,'lotId'=>$lotId,'sqm'=>$sqm]);
        }
        else {
            return View::make('page.accountreceivable.editDataInvoiceRevenueSharing',
                ['billingType'=>$billingType,'dataLot'=>$dataLot,'lotData'=>$lotData,
                    'dataTenant'=>$dataTenant,'dataInvoiceDetail'=>$dataInvoiceDetail,
                    'dataBillingType'=>$dataBillingType,'dataInvoice'=>$dataInvoice]);
        }
    }

    public function insertUpdateItemInvoice(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        if ($request->insert_id == '0') // update
        {
            $dataInvDetail = DB::table('INVOICE_TRANS_DETAIL')
                ->where('INVOICE_TRANS_DTL_ID_INT','=',$request->INVOICE_TRANS_DTL_ID_INT)
                ->update([
                    'BILLING_TYPE'=>$request->BILLING_TYPE,
                    'UTILS_TYPE_NAME'=>$request->UTILS_TYPE_NAME,
                    'INVOICE_TRANS_DTL_DESC'=>$request->INVOICE_TRANS_DTL_DESC,
                    'INVOICE_TRANS_DTL_DPP'=>$request->INVOICE_TRANS_DTL_DPP,
                    'INVOICE_TRANS_DTL_PPN'=>$request->INVOICE_TRANS_DTL_PPN,
                    'INVOICE_TRANS_DTL_PPH'=>$request->INVOICE_TRANS_DTL_DPP * 0.1,
                    'INVOICE_TRANS_DTL_TOTAL'=>$request->INVOICE_TRANS_DTL_TOTAL,
                    'updated_at'=>$dateNow
                ]);

            if ($dataInvDetail)
            {
                $dataSumInvDPP = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_DPP');

                $dataSumInvPPN = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_PPN');

                $dataSumInvPPH = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_PPH');

                $dataSumInv = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_TOTAL');

                DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->update([
                        'INVOICE_TRANS_DPP'=>$dataSumInvDPP,
                        'INVOICE_TRANS_PPN'=>$dataSumInvPPN,
                        'INVOICE_TRANS_PPH'=>$dataSumInvPPH,
                        'INVOICE_TRANS_TOTAL'=>$dataSumInv,
                        'updated_at'=>$dateNow
                    ]);

                $action = "UPDATE DATA DETAIL INV";
                $description = 'Update data detail invoice : ' .$request->INVOICE_TRANS_NOCHAR.'('.$request->INVOICE_TRANS_DTL_DESC.')' ;
                $this->saveToLog($action, $description);
                return response()->json(['Success' => 'Data Has Been Updated']);
            }
            else
            {
                return response()->json(['Error' => 'Gagal Update Item']);
            }
        }
        else
        {
            $dataInvDetail = DB::table('INVOICE_TRANS_DETAIL')
                ->insert([
                    'INVOICE_TRANS_NOCHAR'=>$request->INVOICE_TRANS_NOCHAR,
                    'ID_BILLING'=>0,
                    'BILLING_TYPE'=>$request->BILLING_TYPE,
                    'UTILS_TYPE_NAME'=>$request->UTILS_TYPE_NAME,
                    'INVOICE_TRANS_DTL_DESC'=>$request->INVOICE_TRANS_DTL_DESC,
                    'INVOICE_TRANS_DTL_DPP'=>$request->INVOICE_TRANS_DTL_DPP,
                    'INVOICE_TRANS_DTL_PPN'=>$request->INVOICE_TRANS_DTL_PPN,
                    'INVOICE_TRANS_DTL_PPH'=>$request->INVOICE_TRANS_DTL_DPP * 0.1,
                    'INVOICE_TRANS_DTL_TOTAL'=>$request->INVOICE_TRANS_DTL_TOTAL,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'updated_at'=>$dateNow
                ]);

            if ($dataInvDetail)
            {
                $dataSumInvDPP = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_DPP');

                $dataSumInvPPN = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_PPN');

                $dataSumInvPPH = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_PPH');

                $dataSumInv = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_TOTAL');

                DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->update([
                        'INVOICE_TRANS_DPP'=>$dataSumInvDPP,
                        'INVOICE_TRANS_PPN'=>$dataSumInvPPN,
                        'INVOICE_TRANS_PPH'=>$dataSumInvPPH,
                        'INVOICE_TRANS_TOTAL'=>$dataSumInv,
                        'updated_at'=>$dateNow
                    ]);

                $action = "INSERT DATA DETAIL INV";
                $description = 'unsert data detail invoice : ' .$request->INVOICE_TRANS_NOCHAR.'('.$request->INVOICE_TRANS_DTL_DESC.')' ;
                $this->saveToLog($action, $description);
                return response()->json(['Success' => 'Data Has Been Updated']);
            }
            else
            {
                return response()->json(['Error' => 'Gagal Update Item']);
            }
        }
    }

    public function insertUpdateItemInvoiceRS(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        if ($request->insert_id == '0') //update
        {
            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                ->first();

            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                ->first();

            if($request->INVOICE_TRANS_REVENUE_AMT < $dataPSM->PSM_REVENUE_HIGH_NUM)
            {
                $rate = $dataPSM->PSM_REVENUE_LOW_RATE;
                if ($rate == 0 || $rate == '')
                {
                    return response()->json(['Error' => 'Low Rate Revenue Not Defined, Please Contact Marketing Team']);
                }
                else
                {
                    $rateAmount = ($rate / 100) * $request->INVOICE_TRANS_REVENUE_AMT;

                    if ($rateAmount < $dataPSM->PSM_MIN_AMT)
                    {
                        $baseAmount = $dataPSM->PSM_MIN_AMT;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                    else
                    {
                        $baseAmount = $rateAmount;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                }
            }
            else
            {
                $rate = $dataPSM->PSM_REVENUE_HIGH_RATE;
                if ($rate == 0 || $rate == '')
                {
                    return response()->json(['Error' => 'High Rate Revenue Not Defined, Please Contact Marketing Team']);
                }
                else
                {
                    $rateAmount = ($rate / 100) * $request->INVOICE_TRANS_REVENUE_AMT;

                    if ($rateAmount < $dataPSM->PSM_MIN_AMT)
                    {
                        $baseAmount = $dataPSM->PSM_MIN_AMT;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                    else
                    {
                        $baseAmount = $rateAmount;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                }
            }

            $dataInvDetail = DB::table('INVOICE_TRANS_DETAIL')
                ->where('INVOICE_TRANS_DTL_ID_INT','=',$request->INVOICE_TRANS_DTL_ID_INT)
                ->update([
                    'INVOICE_TRANS_REVENUE_AMT'=>$request->INVOICE_TRANS_REVENUE_AMT,
                    'INVOICE_TRANS_RATE_INT'=>$rate,
                    'INVOICE_TRANS_DTL_DESC'=>$request->INVOICE_TRANS_DTL_DESC,
                    'INVOICE_TRANS_DTL_DPP'=>$request->INVOICE_TRANS_DTL_DPP,
                    'INVOICE_TRANS_DTL_PPN'=>$request->INVOICE_TRANS_DTL_PPN,
                    'INVOICE_TRANS_DTL_TOTAL'=>$request->INVOICE_TRANS_DTL_TOTAL,
                    'updated_at'=>$dateNow
                ]);

            if ($dataInvDetail)
            {
                $dataSumInvDPP = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_DPP');

                $dataSumInvPPN = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_PPN');

                $dataSumInv = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_TOTAL');

                DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->update([
                        'INVOICE_TRANS_DPP'=>$dataSumInvDPP,
                        'INVOICE_TRANS_PPN'=>$dataSumInvPPN,
                        'INVOICE_TRANS_TOTAL'=>$dataSumInv,
                        'updated_at'=>$dateNow
                    ]);

                $action = "UPDATE DATA DETAIL INV";
                $description = 'Update data detail invoice : ' .$request->INVOICE_TRANS_NOCHAR.'('.$request->INVOICE_TRANS_DTL_DESC.')' ;
                $this->saveToLog($action, $description);
                return response()->json(['Success' => 'Data Has Been Updated']);
            }
            else
            {
                return response()->json(['Error' => 'Gagal Update Item']);
            }
        }
        else
        {
            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                ->first();

            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                ->first();

            if($request->INVOICE_TRANS_REVENUE_AMT < $dataPSM->PSM_REVENUE_HIGH_NUM)
            {
                $rate = $dataPSM->PSM_REVENUE_LOW_RATE;
                if ($rate == 0 || $rate == '')
                {
                    return response()->json(['Error' => 'Low Rate Revenue Not Defined, Please Contact Marketing Team']);
                }
                else
                {
                    $rateAmount = ($rate / 100) * $request->INVOICE_TRANS_REVENUE_AMT;

                    if ($rateAmount < $dataPSM->PSM_MIN_AMT)
                    {
                        $baseAmount = $dataPSM->PSM_MIN_AMT;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                    else
                    {
                        $baseAmount = $rateAmount;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                }
            }
            else
            {
                $rate = $dataPSM->PSM_REVENUE_HIGH_RATE;
                if ($rate == 0 || $rate == '')
                {
                    return response()->json(['Error' => 'High Rate Revenue Not Defined, Please Contact Marketing Team']);
                }
                else
                {
                    $rateAmount = ($rate / 100) * $request->INVOICE_TRANS_REVENUE_AMT;

                    if ($rateAmount < $dataPSM->PSM_MIN_AMT)
                    {
                        $baseAmount = $dataPSM->PSM_MIN_AMT;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                    else
                    {
                        $baseAmount = $rateAmount;
                        $ppn = $baseAmount * $dataProject['PPNBM_NUM'];
                        $total = $baseAmount + $ppn;
                    }
                }
            }

            $dataInvDetail = DB::table('INVOICE_TRANS_DETAIL')
                ->insert([
                    'INVOICE_TRANS_NOCHAR'=>$request->INVOICE_TRANS_NOCHAR,
                    'ID_BILLING'=>0,
                    'BILLING_TYPE'=>0,
                    'UTILS_TYPE_NAME'=>'',
                    'INVOICE_TRANS_REVENUE_AMT'=>$request->INVOICE_TRANS_REVENUE_AMT,
                    'INVOICE_TRANS_RATE_INT'=>$rate,
                    'INVOICE_TRANS_DTL_DESC'=>$request->INVOICE_TRANS_DTL_DESC,
                    'INVOICE_TRANS_DTL_DPP'=>$baseAmount,
                    'INVOICE_TRANS_DTL_PPN'=>$ppn,
                    'INVOICE_TRANS_DTL_TOTAL'=>$total,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'updated_at'=>$dateNow
                ]);

            if ($dataInvDetail)
            {
                $dataSumInvDPP = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_DPP');

                $dataSumInvPPN = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_PPN');

                $dataSumInv = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->SUM('INVOICE_TRANS_DTL_TOTAL');

                DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_NOCHAR','=',$request->INVOICE_TRANS_NOCHAR)
                    ->update([
                        'INVOICE_TRANS_DPP'=>$dataSumInvDPP,
                        'INVOICE_TRANS_PPN'=>$dataSumInvPPN,
                        'INVOICE_TRANS_TOTAL'=>$dataSumInv,
                        'updated_at'=>$dateNow
                    ]);

                $action = "INSERT DATA DETAIL INV RS";
                $description = 'unsert data detail invoice : ' .$request->INVOICE_TRANS_NOCHAR.'('.$request->INVOICE_TRANS_DTL_DESC.')' ;
                $this->saveToLog($action, $description);
                return response()->json(['Success' => 'Data Has Been Updated']);
            }
            else
            {
                return response()->json(['Error' => 'Gagal Update Item']);
            }
        }
    }

    public function getItemInv(Request $request){
        $invItem = DB::table('INVOICE_TRANS_DETAIL')
            ->where('INVOICE_TRANS_DTL_ID_INT','=',$request->INVOICE_TRANS_DTL_ID_INT)
            ->first();

        if($invItem){
            return response()->json([
                'status' => 'success',
                'INVOICE_TRANS_DTL_ID_INT' => $invItem->INVOICE_TRANS_DTL_ID_INT,
                'BILLING_TYPE' => $invItem->BILLING_TYPE,
                'UTILS_TYPE_NAME' => $invItem->UTILS_TYPE_NAME,
                'INVOICE_TRANS_DTL_DESC' => $invItem->INVOICE_TRANS_DTL_DESC,
                'INVOICE_TRANS_DTL_DPP' => $invItem->INVOICE_TRANS_DTL_DPP,
                'INVOICE_TRANS_DTL_PPN' => $invItem->INVOICE_TRANS_DTL_PPN,
                'INVOICE_TRANS_DTL_TOTAL' => $invItem->INVOICE_TRANS_DTL_TOTAL
            ]);
        }else{
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function getItemInvRS(Request $request){
        $invItem = DB::table('INVOICE_TRANS_DETAIL')
            ->where('INVOICE_TRANS_DTL_ID_INT','=',$request->INVOICE_TRANS_DTL_ID_INT)
            ->first();

        if($invItem){
            return response()->json([
                'status' => 'success',
                'INVOICE_TRANS_DTL_ID_INT' => $invItem->INVOICE_TRANS_DTL_ID_INT,
                'INVOICE_TRANS_REVENUE_AMT' => $invItem->INVOICE_TRANS_REVENUE_AMT,
            ]);
        }else{
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function deleteItemInv(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $invItem = DB::table('INVOICE_TRANS_DETAIL')
            ->where('INVOICE_TRANS_DTL_ID_INT','=',$request->INVOICE_TRANS_DTL_ID_INT)
            ->first();

        $deleteItem = DB::table('INVOICE_TRANS_DETAIL')
            ->where('INVOICE_TRANS_DTL_ID_INT','=',$request->INVOICE_TRANS_DTL_ID_INT)
            ->delete();

        if ($deleteItem)
        {
            $dataSumInvDPP = DB::table('INVOICE_TRANS_DETAIL')
                ->where('INVOICE_TRANS_NOCHAR','=',$invItem->INVOICE_TRANS_NOCHAR)
                ->SUM('INVOICE_TRANS_DTL_DPP');

            $dataSumInvPPN = DB::table('INVOICE_TRANS_DETAIL')
                ->where('INVOICE_TRANS_NOCHAR','=',$invItem->INVOICE_TRANS_NOCHAR)
                ->SUM('INVOICE_TRANS_DTL_PPN');

            $dataSumInv = DB::table('INVOICE_TRANS_DETAIL')
                ->where('INVOICE_TRANS_NOCHAR','=',$invItem->INVOICE_TRANS_NOCHAR)
                ->SUM('INVOICE_TRANS_DTL_TOTAL');

            DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$invItem->INVOICE_TRANS_NOCHAR)
                ->update([
                    'INVOICE_TRANS_DPP'=>$dataSumInvDPP,
                    'INVOICE_TRANS_PPN'=>$dataSumInvPPN,
                    'INVOICE_TRANS_TOTAL'=>$dataSumInv,
                    'updated_at'=>$dateNow
                ]);

            $action = "DELETE DETAIL INV";
            $description = 'Delete detail invoice : ' .$invItem->INVOICE_TRANS_NOCHAR." (".$invItem->INVOICE_TRANS_DTL_DESC.")";
            $this->saveToLog($action, $description);
            return response()->json(['Success' => 'Berhasil Menghapus Item']);
        }
        else
        {
            return response()->json(['Error' => 'Gagal Menghapus Item']);
        }
    }

    public function creditPPH(){
        $project_no = Session::get('PROJECT_NO_CHAR');

        $dataInvoicePT = DB::select("SELECT *, (INVOICE_TRANS_PPH - INVOICE_TRANS_BP_AMOUNT) AS SISA_BAYAR FROM (
                                        select a.INVOICE_TRANS_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,c.MD_TENANT_NAME_CHAR,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                            a.INVOICE_TRANS_DESC_CHAR,a.INVOICE_TRANS_DPP,a.INVOICE_TRANS_PPN,a.INVOICE_TRANS_PPH,a.INVOICE_TRANS_TOTAL,
                                            a.INVOICE_AUTOMATION_INT,a.INVOICE_TRANS_TYPE,a.JOURNAL_STATUS_INT,a.INVOICE_FP_NOCHAR,
                                            (
                                                SELECT ISNULL(SUM(INVOICE_TRANS_BP_AMOUNT), 0) FROM INVOICE_TRANS_BP WHERE INVOICE_TRANS_NOCHAR = a.INVOICE_TRANS_NOCHAR AND INVOICE_TRANS_BP_STATUS = 2
                                            )
                                            AS INVOICE_TRANS_BP_AMOUNT
                                        from INVOICE_TRANS as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                        INNER JOIN MD_TENANT as c ON b.MD_TENANT_ID_INT = c.MD_TENANT_ID_INT
                                        where a.MD_TENANT_PPH_INT = 1
                                        and a.INVOICE_STATUS_INT IN (4)
                                        and a.IS_HIDE = 0
                                        and a.PROJECT_NO_CHAR = '".$project_no."'
                                    ) AS a
                                    WHERE INVOICE_TRANS_BP_AMOUNT < INVOICE_TRANS_PPH
                                    ORDER BY TGL_SCHEDULE_DATE");

        return View::make('page.accountreceivable.listDataCreditPPH2',
            ['project_no'=>$project_no,'dataInvoicePT'=>$dataInvoicePT]);
    }

    public function processCreditPPH(){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dataProcessCP = \Request::all();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            if ($dataProcessCP['INVOICE_TRANS_BP_DATE'] == '' || $dataProcessCP['INVOICE_TRANS_BP_NOCHAR'] == '')
            {
                return redirect()->route('invoice.creditpph')
                    ->with('error', 'Credit PPh Date / No. Credit PPh Cannot be Empty, Process CP Invoice '.$dataProcessCP['INVOICE_TRANS_NOCHAR'].' Fail....');
            }

            DB::table("INVOICE_TRANS_BP")
                ->insert([
                    'INVOICE_TRANS_NOCHAR'=>$dataProcessCP['INVOICE_TRANS_NOCHAR'],
                    'INVOICE_TRANS_BP_NOCHAR'=>$dataProcessCP['INVOICE_TRANS_BP_NOCHAR'],
                    'INVOICE_TRANS_BP_DATE'=>$dataProcessCP['INVOICE_TRANS_BP_DATE'],
                    'INVOICE_TRANS_BP_AMOUNT'=>empty($dataProcessCP['AMOUNT']) ? 0 : $dataProcessCP['AMOUNT'],
                    'INVOICE_TRANS_BP_STATUS'=>2, //Approve
                    'INVOICE_TRANS_BP_REQ_BY'=>$userName,
                    'INVOICE_TRANS_BP_REQ_DATE'=>$dateNow,
                    'INVOICE_TRANS_BP_APPR_BY'=>$userName,
                    'INVOICE_TRANS_BP_APPR_DATE'=>$dateNow,
                    'created_at'=>$dateNow,
                    'updated_at'=>$dateNow
                ]);

            $action = "PROCESS CREDIT PPH";
            $description = 'Generate CP Invoice '.$dataProcessCP['INVOICE_TRANS_NOCHAR'].' No CP: '.$dataProcessCP['INVOICE_TRANS_BP_NOCHAR'];
            $this->saveToLogInvBP($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('invoice.creditpph')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.creditpph')->with('success',$description.' Successfully');
    }

    public function generateInvoiceSecurityDesposit(){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $generator = new utilGenerator;
            $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $converter = new utilConverter();
            $dataRentSC = \Request::all();
            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $yearTaxPeriod = substr($dateNow->year,2,4);

            if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error', 'Document Date or Due Date Cannot be Empty');
            }

            if($dataRentSC['backdate'] == "")
            {
                return redirect()->route('invoice.listgenerateinvoice')
                    ->with('error','You Cannot Create Transaction In Closed Month');
            }

            $docDate = Carbon::parse($dataRentSC['docDate']);
            $dueDate = Carbon::parse($dataRentSC['dueDate']);

            if($dataRentSC['selectall'] == 'all')
            {
                $dataInvRentSC = DB::select("exec sp_invoice_secure_dep '".$dataRentSC['cutoff']."','".$project_no."'");

                foreach($dataInvRentSC as $data)
                {
                    $cekDataInvoice = DB::table('INVOICE_TRANS')
                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                        ->whereIn('INVOICE_TRANS_TYPE',[$data->TRX_CODE])
                        ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                        ->count();

                    if ($cekDataInvoice <= 0)
                    {
                        $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                        $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                        $Counter = str_pad($counter->inv_securedep_count, 5, "0", STR_PAD_LEFT);
                        $Year = substr($docDate->year, 2);
                        $Month = $docDate->month;
                        $monthRomawi = $converter->getRomawi($Month);

                        Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                            ->update(['inv_securedep_count'=>$counter->inv_securedep_count + 1]);

                        $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                        DB::table('INVOICE_TRANS')
                            ->insert([
                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                'INVOICE_FP_NOCHAR'=>'0',
                                'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                'DOC_TYPE'=>'D',
                                'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                'TGL_SCHEDULE_DATE'=>$docDate,
                                'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                'INVOICE_TRANS_DPP'=>$data->BILL_AMOUNT,
                                'INVOICE_TRANS_PPN'=>0,
                                'INVOICE_TRANS_PPH'=>0,
                                'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                'PROJECT_NO_CHAR'=>$project_no,
                                'INVOICE_CREATE_CHAR'=>$userName,
                                'INVOICE_CREATE_DATE'=>$dateNow,
                                'FROM_SCHEDULE'=>1,
                                'JOURNAL_STATUS_INT'=>1,
                                'created_at'=>$dateNow,
                                'updated_at'=>$dateNow
                            ]);

                        DB::table('PSM_SECURE_DEP')
                            ->where('PSM_SECURE_DEP_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                            ->update([
                                'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                'INVOICE_DEPOSIT_DATE'=>$docDate,
                                'INVOICE_STATUS_INT'=>1, // generate invoice
                                'updated_at'=>$dateNow
                            ]);


                        //Create Journal
                        $Year = substr($dateNow->year, 2);
                        $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                        $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                        $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                        $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                        try {
                            $countTable->save();
                        } catch (QueryException $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $sourcode = 'JM';

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        $BillAmount = $data->BILL_AMOUNT;

                        $dataSecureType = DB::table('PSM_SECURE_DEP_TYPE')
                            ->where('PSM_SECURE_DEP_TYPE_CODE','=',$data->TRX_CODE)
                            ->first();

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = '150000000';
                        $inputGlTrans['ACC_NO_CHAR'] = '150003006';
                        $inputGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalDebit += $BillAmount;

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = $dataSecureType->ACC_NOP_CHAR;
                        $inputGlTrans['ACC_NO_CHAR'] = $dataSecureType->ACC_NO_CHAR;
                        $inputGlTrans['ACC_NAME_CHAR'] = $dataSecureType->ACC_NAME_CHAR;
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount * -1;
                        $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        $totalKredit += $BillAmount;

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }

                        GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                            ->where('ACC_AMOUNT_INT','=',0)->delete();

                        $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                        $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                        $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                        $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                        $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                        $inputJournal['PROJECT_NO_CHAR']=$project_no;
                        $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                        $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                        $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                        $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                        $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                        $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                        $inputJournal['ACC_JOURNAL_FP_CHAR']='';
                        $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                        try {
                            Journal::create($inputJournal);
                        } catch (QueryException $ex) {
                            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                        }

                        DB::table('PSM_SECURE_DEP')
                            ->where('PSM_SECURE_DEP_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                            ->update([
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'updated_at'=>$dateNow
                            ]);

                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                            ->update([
                                'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                'updated_at'=>$dateNow
                            ]);
                    }
                }
            }
            else
            {
                if (count($dataRentSC['billing']) > 0)
                {
                    for($i=0;  $i < count($dataRentSC['billing']); $i++){
                        if ($dataRentSC['billing'][$i] <> 0)
                        {
                            $dataInvRentSC = DB::select("exec sp_invoice_secure_dep_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");

                            foreach($dataInvRentSC as $data)
                            {
                                $cekDataInvoice = DB::table('INVOICE_TRANS')
                                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                    ->whereIn('INVOICE_TRANS_TYPE',[$data->TRX_CODE])
                                    ->whereNotIn('INVOICE_STATUS_INT',[0]) // 0 = void
                                    ->count();

                                if ($cekDataInvoice <= 0)
                                {
                                    $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                                    $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                                    $Counter = str_pad($counter->inv_securedep_count, 5, "0", STR_PAD_LEFT);
                                    $Year = substr($docDate->year, 2);
                                    $Month = $docDate->month;
                                    $monthRomawi = $converter->getRomawi($Month);

                                    Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                                        ->update(['inv_securedep_count'=>$counter->inv_securedep_count + 1]);

                                    $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                                    DB::table('INVOICE_TRANS')
                                        ->insert([
                                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                            'INVOICE_FP_NOCHAR'=>'0',
                                            'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                                            'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                                            'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                                            'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                                            'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                                            'DOC_TYPE'=>'D',
                                            'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                                            'TGL_SCHEDULE_DATE'=>$docDate,
                                            'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                                            'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                                            'INVOICE_TRANS_DPP'=>$data->BILL_AMOUNT,
                                            'INVOICE_TRANS_PPN'=>0,
                                            'INVOICE_TRANS_PPH'=>0,
                                            'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                                            'PROJECT_NO_CHAR'=>$project_no,
                                            'INVOICE_CREATE_CHAR'=>$userName,
                                            'INVOICE_CREATE_DATE'=>$dateNow,
                                            'FROM_SCHEDULE'=>1,
                                            'JOURNAL_STATUS_INT'=>1,
                                            'created_at'=>$dateNow,
                                            'updated_at'=>$dateNow
                                        ]);

                                    DB::table('PSM_SECURE_DEP')
                                        ->where('PSM_SECURE_DEP_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                        ->update([
                                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                                            'INVOICE_DEPOSIT_DATE'=>$docDate,
                                            'INVOICE_STATUS_INT'=>1, // generate invoice
                                            'updated_at'=>$dateNow
                                        ]);

                                    //Create Journal
                                    $Year = substr($dateNow->year, 2);
                                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                                    try {
                                        $countTable->save();
                                    } catch (QueryException $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                    }

                                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                    $tahunRK = $docDate->year;

                                    $period_no = $tahunRK.''.$bulanRK;

                                    $sourcode = 'JM';

                                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                    $totalDebit = 0;
                                    $totalKredit = 0;

                                    $BillAmount = $data->BILL_AMOUNT;

                                    $dataSecureType = DB::table('PSM_SECURE_DEP_TYPE')
                                        ->where('PSM_SECURE_DEP_TYPE_CODE','=',$data->TRX_CODE)
                                        ->first();

                                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                    $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                    $inputGlTrans['ACC_NOP_CHAR'] = '150000000';
                                    $inputGlTrans['ACC_NO_CHAR'] = '150003006';
                                    $inputGlTrans['ACC_NAME_CHAR'] = 'Piutang Usaha Lain-lain';
                                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO;
                                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount;
                                    $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                    $totalDebit += $BillAmount;

                                    try{
                                        GlTrans::create($inputGlTrans);
                                    } catch (Exception $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')
                                            ->with('error','Failed update counter table, errmsg : '.$ex);
                                    }

                                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                                    $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                                    $inputGlTrans['ACC_NOP_CHAR'] = $dataSecureType->ACC_NOP_CHAR;
                                    $inputGlTrans['ACC_NO_CHAR'] = $dataSecureType->ACC_NO_CHAR;
                                    $inputGlTrans['ACC_NAME_CHAR'] = $dataSecureType->ACC_NAME_CHAR;
                                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO;
                                    $inputGlTrans['ACC_AMOUNT_INT'] = $BillAmount * -1;
                                    $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                                    $totalKredit += $BillAmount;

                                    try{
                                        GlTrans::create($inputGlTrans);
                                    } catch (Exception $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')
                                            ->with('error','Failed update counter table, errmsg : '.$ex);
                                    }

                                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                                    $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                    $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO;
                                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$userName;
                                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                                    $inputJournal['ACC_JOURNAL_FP_CHAR']='0';
                                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                                    try {
                                        Journal::create($inputJournal);
                                    } catch (QueryException $ex) {
                                        return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed insert data, errmsg : ' . $ex);
                                    }

                                    DB::table('PSM_SECURE_DEP')
                                        ->where('PSM_SECURE_DEP_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                        ->update([
                                            'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                            'updated_at'=>$dateNow
                                        ]);

                                    DB::table('INVOICE_TRANS')
                                        ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                                        ->update([
                                            'ACC_JOURNAL_NOCHAR'=>$nojournal,
                                            'updated_at'=>$dateNow
                                        ]);
                                }
                            }
                        }
                    }
                }
            }

            \Session::flash('message', 'Generate Invoice Security Deposit Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME']);
            $action = "GENERATE INV SECURE DEP DATA";
            $description = 'Generate Invoice Security Deposit Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('invoice.listgenerateinvoice')->with('error', 'Failed generate data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listgenerateinvoice')->with('success',$description.' Successfully');
    }

    public function listDataInvoiceUnappr(){
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataInvoiceRENT = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                                a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                        AND b.INVOICE_TRANS_TYPE IN ('DP','RT')
                                        AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                        ORDER BY a.TGL_BAYAR_DATE");

        $dataInvoiceSC = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                                a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                        AND b.INVOICE_TRANS_TYPE IN ('SC')
                                        AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                        ORDER BY a.TGL_BAYAR_DATE");

        $dataInvoiceUT = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                                a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                                c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                        from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                        INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                        INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                        AND b.INVOICE_TRANS_TYPE IN ('UT')
                                        AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                        ORDER BY a.TGL_BAYAR_DATE");

        $dataInvoiceCL = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                            a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                            c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                    from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                    INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                    INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                    where a.PROJECT_NO_CHAR = '".$project_no."'
                                    AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                    AND b.INVOICE_TRANS_TYPE IN ('CL')
                                    AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                    ORDER BY a.TGL_BAYAR_DATE");

        $dataInvoiceRS = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                            a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                            c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                    from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                    INNER JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                    INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                    where a.PROJECT_NO_CHAR = '".$project_no."'
                                    AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                    AND b.INVOICE_TRANS_TYPE IN ('RS')
                                    AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                    ORDER BY a.TGL_BAYAR_DATE");

        $dataInvoiceOT = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                        a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                        c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                    from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                    LEFT JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                    INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                    where a.PROJECT_NO_CHAR = '".$project_no."'
                                    AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                    AND b.INVOICE_TRANS_TYPE IN ('OT')
                                    AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                    ORDER BY a.TGL_BAYAR_DATE");

        $dataInvoiceSD = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                            a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                            c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                    from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                    LEFT JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                    INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                    where a.PROJECT_NO_CHAR = '".$project_no."'
                                    AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                    AND b.INVOICE_TRANS_TYPE IN (
                                        Select PSM_SECURE_DEP_TYPE_CODE
                                        from PSM_SECURE_DEP_TYPE
                                        where IS_DELETE = 0
                                    )
                                    AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                    ORDER BY a.TGL_BAYAR_DATE");

        $dataInvoiceRB = DB::select("SELECT a.INVOICE_PAYMENT_ID_INT,a.INVOICE_TRANS_NOCHAR,a.LOT_STOCK_NO,FORMAT(a.TGL_BAYAR_DATE,'dd-MM-yyyy') as TGL_BAYAR_DATE,
                                        a.PAYMENT_METHOD,a.ACC_NAME_CHAR,a.INVOICE_TRANS_TOTAL,a.PAID_BILL_DENDA,a.PAID_BILL_AMOUNT,
                                        c.SHOP_NAME_CHAR,b.INVOICE_TRANS_DESC_CHAR,a.ACC_JOURNAL_NOCHAR,d.ACC_JOURNAL_APPROVED_INT
                                    from INVOICE_PAYMENT as a INNER JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                    LEFT JOIN PSM_TRANS as c ON a.PSM_TRANS_NOCHAR = c.PSM_TRANS_NOCHAR
                                    INNER JOIN ACC_JOURNAL as d ON a.ACC_JOURNAL_NOCHAR = d.ACC_JOURNAL_RNOCHAR
                                    where a.PROJECT_NO_CHAR = '".$project_no."'
                                    AND a.INVOICE_PAYMENT_STATUS_INT = 2
                                    AND b.INVOICE_TRANS_TYPE IN ('RB')
                                    AND a.TGL_BAYAR_DATE >= '".$dataProject['YEAR_PERIOD']."-".$dataProject['MONTH_PERIOD']."-01'
                                    ORDER BY a.TGL_BAYAR_DATE");

        return View::make('page.accountreceivable.listDataInvoiceUnappr',
            ['project_no'=>$project_no,'dataInvoiceRENT'=>$dataInvoiceRENT,
                'dataInvoiceSC'=>$dataInvoiceSC,'dataInvoiceUT'=>$dataInvoiceUT,
                'dataInvoiceCL'=>$dataInvoiceCL,'dataInvoiceRS'=>$dataInvoiceRS,
                'dataInvoiceOT'=>$dataInvoiceOT,'dataInvoiceSD'=>$dataInvoiceSD,
                'dataInvoiceRB'=>$dataInvoiceRB]);
    }

    public function unapproveInvoicePayment($INVOICE_PAYMENT_ID_INT){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            $dataInvoicePayment = DB::table('INVOICE_PAYMENT')
                ->where('INVOICE_PAYMENT_ID_INT','=',$INVOICE_PAYMENT_ID_INT)
                ->first();

            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                ->first();

            $sumCN = 0;

            $dataCNDetail = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.CN_TRANS_DTL_AMOUNT
                                from CN_TRANS_DETAIL as a INNER JOIN CN_TRANS as b ON a.CN_TRANS_NOCHAR = b.CN_TRANS_NOCHAR
                                where a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'
                                and a.IS_DELETE = 0
                                and b.CN_TRANS_STATUS_INT = 2");

            foreach ($dataCNDetail as $cn)
            {
                $sumCN += $cn->CN_TRANS_DTL_AMOUNT;
            }

            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31' && $dataInvoice->MD_TENANT_PPH_INT == 1)
            {
                $nilaiInvoice = $dataInvoice->INVOICE_TRANS_DPP - $sumCN;
            }
            elseif($dataInvoice->TGL_SCHEDULE_DATE > '2022-03-31' && $dataInvoice->MD_TENANT_PPH_INT == 1)
            {
                $nilaiInvoice = (($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH) - $sumCN;
            }
            else
            {
                $nilaiInvoice = $dataInvoice->INVOICE_TRANS_TOTAL - $sumCN;
            }

            $tanggalBayar = Carbon::parse($dataInvoicePayment->TGL_BAYAR_DATE);
            $bulanBayar = $tanggalBayar->month;
            $tahunBayar = $tanggalBayar->year;

            $cekJournalBM = DB::table('ACC_JOURNAL')
                ->where('ACC_JOURNAL_RNOCHAR','=',$dataInvoicePayment->ACC_JOURNAL_NOCHAR)
                ->where('ACC_JOURNAL_APPROVED_INT','=',0)
                ->count();

            if ($cekJournalBM > 0)
            {
                DB::table('ACC_JOURNAL')
                    ->where('ACC_JOURNAL_RNOCHAR','=',$dataInvoicePayment->ACC_JOURNAL_NOCHAR)
                    ->where('ACC_JOURNAL_APPROVED_INT','=',0)
                    ->update([
                        'ACC_JOURNAL_APPROVED_INT'=>2,
                        'ACC_JOURNAL_AUDITOR_CHAR'=>$userName,
                        'ACC_JOURNAL_AUDITOR_DTTIME'=>$dateNow
                    ]);
            }

            DB::table('INVOICE_PAYMENT_DETAIL')
                ->where('INVOICE_PAYMENT_ID_INT','=',$dataInvoicePayment->INVOICE_PAYMENT_ID_INT)
                ->delete();

            DB::table('INVOICE_PAYMENT')
                ->where('INVOICE_PAYMENT_ID_INT','=',$dataInvoicePayment->INVOICE_PAYMENT_ID_INT)
                ->update([
                'INVOICE_PAYMENT_STATUS_INT'=>0,
                'INVOICE_PAYMENT_APPR_CHAR'=>$userName,
                'INVOICE_PAYMENT_APPR_DATE'=>$dateNow
                ]);

            $cekInvoicePayment = DB::table('INVOICE_PAYMENT')
                ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                ->where('INVOICE_PAYMENT_STATUS_INT','=',1)
                ->count();

            if($cekInvoicePayment > 0)
            {
                DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                    ->update([
                        'INVOICE_STATUS_INT'=>2
                    ]);
            }
            else
            {
                $sumPayment = DB::table('INVOICE_PAYMENT')
                    ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                    ->where('INVOICE_PAYMENT_STATUS_INT','=',2)
                    ->SUM('PAID_BILL_AMOUNT');

                if ($nilaiInvoice > $sumPayment)
                {
                    if ($sumPayment > 0)
                    {
                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                            ->update([
                                'INVOICE_STATUS_INT'=>3 //partial
                            ]);
                    }
                    else
                    {
                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                            ->update([
                                'INVOICE_STATUS_INT'=>1 //invoice
                            ]);
                    }
                }
                else
                {
                    DB::table('INVOICE_TRANS')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataInvoicePayment->INVOICE_TRANS_NOCHAR)
                        ->update([
                            'INVOICE_STATUS_INT'=>4 //paid
                        ]);
                }
            }

            $action = "UNAPPROVE INV PAYMENT DATA";
            $description = 'Unapprove Inv Payment Data : '.$dataInvoice->INVOICE_TRANS_NOCHAR.' succesfully';
            $this->saveToLog1($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('invoice.listdatainvoiceunappr')->with('error', 'Failed unapprove data, errmsg : ' . $ex);
        }

        return redirect()->route('invoice.listdatainvoiceunappr')
            ->with('success',$description);
    }

    public function tapingInvoiceRental(){
        if(Session::get('id') == ''){
            Session::flush();
            return redirect('/login');
        }

        //$generator = new utilGenerator();
        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $converter = new utilConverter();

        $dataRentSC = \Request::all();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $yearTaxPeriod = substr($dateNow->year,2,4);
        //dd($dataRentSC);

        //dd($dataRentSC['docDate']);
        if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
            //|| $dataRentSC['INVOICE_FP_NOCHAR'] == ''
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error', 'Document Date or Due Date Cannot be Empty');
        }

        if($dataRentSC['backdate'] == "")
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error','You Cannot Create Transaction In Closed Month');
        }

        //dd($numberTax);

        $docDate = Carbon::parse($dataRentSC['docDate']);
        $dueDate = Carbon::parse($dataRentSC['dueDate']);

        if($dataRentSC['selectall'] == 'all')
        {
            $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_rent '".$dataRentSC['cutoff']."','".$project_no."'");
            //dd($dataInvRentSC);

            foreach($dataInvRentSC as $data)
            {
                DB::table('PSM_SCHEDULE')
                ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                ->update([
                    'IS_TAPING_INV'=>1,
                    'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                    'TAPING_DATE'=>$dateNow,
                    'DOC_DATE_INV'=>$docDate,
                    'DUE_DATE_INV'=>$dueDate,
                    'updated_at'=>$dateNow
                ]);

                $action = "TAPING DATA";
                $descriptionLog = 'Taping Schedule RT ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                $this->saveToLog($action, $descriptionLog);
            }
        }
        else
        {
            //dd(count($updateSetSertifikat['billing']));
            if (count($dataRentSC['billing']) > 0)
            {
                for($i=0;  $i < count($dataRentSC['billing']); $i++){
                    if ($dataRentSC['billing'][$i] <> 0)
                    {
                        $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_rent_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");
                        //dd($dataInvRentSC);

                        foreach($dataInvRentSC as $data)
                        {
                            DB::table('PSM_SCHEDULE')
                                ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'IS_TAPING_INV'=>1,
                                    'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                    'TAPING_DATE'=>$dateNow,
                                    'DOC_DATE_INV'=>$docDate,
                                    'DUE_DATE_INV'=>$dueDate,
                                    'updated_at'=>$dateNow
                                ]);

                            $action = "TAPING DATA";
                            $descriptionLog = 'Taping Schedule RT ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                            $this->saveToLog($action, $descriptionLog);
                        }
                    }
                }
            }
        }

        $description = 'Taping Schedule Rental Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
        return redirect()->route('invoice.listtapinginvoice')->with('success',$description.' Successfully');
    }

    public function tapingInvoiceSecurityDesposit(){
        if(Session::get('id') == ''){
            Session::flush();
            return redirect('/login');
        }

        $generator = new utilGenerator;
        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $converter = new utilConverter();
        $dataRentSC = \Request::all();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        //$yearTaxPeriod = substr($dateNow->year,2,4);

        //dd($dataRentSC['docDate']);
        if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error', 'Document Date or Due Date Cannot be Empty');
        }

        if($dataRentSC['backdate'] == "")
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error','You Cannot Create Transaction In Closed Month');
        }

        $docDate = Carbon::parse($dataRentSC['docDate']);
        $dueDate = Carbon::parse($dataRentSC['dueDate']);

        if($dataRentSC['selectall'] == 'all')
        {
            $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_secure_dep '".$dataRentSC['cutoff']."','".$project_no."'");
            //dd($dataInvRentSC);

            foreach($dataInvRentSC as $data)
            {
                DB::table('PSM_SECURE_DEP')
                    ->where('PSM_SECURE_DEP_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                    ->update([
                        'IS_TAPING_INV'=>1,
                        'TAPING_DATE'=>$dateNow,
                        'DOC_DATE_INV'=>$docDate,
                        'DUE_DATE_INV'=>$dueDate,
                        'updated_at'=>$dateNow
                    ]);

                $action = "TAPING DATA";
                $descriptionLog = 'Taping Schedule Security Deposit ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                $this->saveToLog($action, $descriptionLog);
            }
        }
        else
        {
            //dd(count($updateSetSertifikat['billing']));
            if (count($dataRentSC['billing']) > 0)
            {
                for($i=0;  $i < count($dataRentSC['billing']); $i++){
                    if ($dataRentSC['billing'][$i] <> 0)
                    {
                        $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_secure_dep_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");
                        //dd($dataInvRentSC);

                        foreach($dataInvRentSC as $data)
                        {
                            DB::table('PSM_SECURE_DEP')
                                ->where('PSM_SECURE_DEP_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'IS_TAPING_INV'=>1,
                                    'TAPING_DATE'=>$dateNow,
                                    'DOC_DATE_INV'=>$docDate,
                                    'DUE_DATE_INV'=>$dueDate,
                                    'updated_at'=>$dateNow
                                ]);

                            $action = "TAPING DATA";
                            $descriptionLog = 'Taping Schedule Security Deposit ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                            $this->saveToLog($action, $descriptionLog);
                        }
                    }
                }
            }
        }

        $description = 'Taping Schedule Security Deposit Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
        return redirect()->route('invoice.listtapinginvoice')->with('success',$description.' Successfully');
    }

    public function tapingInvoiceServiceCharge(){
        if(Session::get('id') == ''){
            Session::flush();
            return redirect('/login');
        }

        $generator = new utilGenerator;
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $converter = new utilConverter();
        $dataRentSC = \Request::all();
        $date = Carbon::parse(Carbon::now());
        $yearTaxPeriod = substr($dateNow->year,2,4);

        //dd($dataRentSC['docDate']);
        if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error', 'Document Date or Due Date Cannot be Empty');
        }

        if($dataRentSC['backdate'] == "")
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error','You Cannot Create Transaction In Closed Month');
        }

        $docDate = Carbon::parse($dataRentSC['docDate']);
        $dueDate = Carbon::parse($dataRentSC['dueDate']);

        if($dataRentSC['selectall'] == 'all')
        {
            $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_sc '".$dataRentSC['cutoff']."','".$project_no."'");
            //dd($dataInvRentSC);

            foreach($dataInvRentSC as $data)
            {
                DB::table('PSM_SCHEDULE')
                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                    ->update([
                        'IS_TAPING_INV'=>1,
                        'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                        'TAPING_DATE'=>$dateNow,
                        'DOC_DATE_INV'=>$docDate,
                        'DUE_DATE_INV'=>$dueDate,
                        'updated_at'=>$dateNow
                    ]);

                $action = "TAPING DATA";
                $descriptionLog = 'Taping Schedule SC ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                $this->saveToLog($action, $descriptionLog);
            }
        }
        else
        {
            //dd(count($dataRentSC['billing']));
            if (count($dataRentSC['billing']) > 0)
            {
                for($i=0;  $i < count($dataRentSC['billing']); $i++){
                    if ($dataRentSC['billing'][$i] <> 0)
                    {
                        $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_sc_byID '".$dataRentSC['cutoff']."','".$project_no."',".$dataRentSC['billing'][$i]);
                        //dd($dataInvRentSC);

                        foreach($dataInvRentSC as $data)
                        {
                            DB::table('PSM_SCHEDULE')
                                ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'IS_TAPING_INV'=>1,
                                    'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                    'TAPING_DATE'=>$dateNow,
                                    'DOC_DATE_INV'=>$docDate,
                                    'DUE_DATE_INV'=>$dueDate,
                                    'updated_at'=>$dateNow
                                ]);

                            $action = "TAPING DATA";
                            $descriptionLog = 'Taping Schedule SC ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                            $this->saveToLog($action, $descriptionLog);
                        }
                    }
                }
            }
        }

        $description = 'Taping Schedule Service Charge Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
        return redirect()->route('invoice.listtapinginvoice')
            ->with('success',$description.' Successfully');
    }

    public function tapingInvoiceUtility(){
        if(Session::get('id') == ''){
            Session::flush();
            return redirect('/login');
        }

        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $converter = new utilConverter();
        $dataUtility = \Request::all();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $yearTaxPeriod = substr($dateNow->year,2,4);

        $date = Carbon::parse(Carbon::now());

        if ($dataUtility['docDate'] == '' || $dataUtility['dueDate'] == '')
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error', 'Document Date or Due Date Cannot be Empty');
        }

        if($dataUtility['backdate'] == "")
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error','You Cannot Create Transaction In Closed Month');
        }


        $docDate = Carbon::parse($dataUtility['docDate']);
        $dueDate = Carbon::parse($dataUtility['dueDate']);

        if($dataUtility['selectall'] == 'all')
        {
            $dataInvUtility = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_utility '".$dataUtility['cutoff']."','".$project_no."'");

            foreach($dataInvUtility as $data)
            {
                DB::table('UTILS_BILLING')
                    ->where('ID_BILLING','=',$data->ID_BILLING)
                    ->update([
                        'IS_TAPING_INV'=>1,
                        'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                        'TAPING_DATE'=>$dateNow,
                        'DOC_DATE_INV'=>$docDate,
                        'DUE_DATE_INV'=>$dueDate,
                        'updated_at'=>$dateNow
                    ]);

                $action = "TAPING DATA";
                $descriptionLog = 'Taping Schedule Utility ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                $this->saveToLog($action, $descriptionLog);
            }
        }
        else
        {
            //dd(count($updateSetSertifikat['billing']));
            if (count($dataUtility['billing']) > 0)
            {
                for($i=0;  $i < count($dataUtility['billing']); $i++){
                    if ($dataUtility['billing'][$i] <> 0)
                    {
                        $dataInvUtility = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_utility_ByID '".$dataUtility['cutoff']."','".$project_no."',".$dataUtility['billing'][$i]);

                        foreach($dataInvUtility as $data)
                        {
                            DB::table('UTILS_BILLING')
                                ->where('ID_BILLING','=',$data->ID_BILLING)
                                ->update([
                                    'IS_TAPING_INV'=>1,
                                    'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                    'TAPING_DATE'=>$dateNow,
                                    'DOC_DATE_INV'=>$docDate,
                                    'DUE_DATE_INV'=>$dueDate,
                                    'updated_at'=>$dateNow
                                ]);

                            $action = "TAPING DATA";
                            $descriptionLog = 'Taping Schedule Utility ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                            $this->saveToLog($action, $descriptionLog);
                        }
                    }
                }
            }
        }

        $description = 'Taping Schedule Utility ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
        return redirect()->route('invoice.listtapinginvoice')->with('success',$description.' Successfully');
    }

    public function tapingInvoiceOthers(){
        if(Session::get('id') == ''){
            Session::flush();
            return redirect('/login');
        }

        $generator = new utilGenerator;
        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $converter = new utilConverter();
        $dataRentSC = \Request::all();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $yearTaxPeriod = substr($dateNow->year,2,4);

        //dd($dataRentSC['docDate']);
        if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error', 'Document Date or Due Date Cannot be Empty');
        }

        if($dataRentSC['backdate'] == "")
        {
            return redirect()->route('invoice.listtapinginvoice')
                ->with('error','You Cannot Create Transaction In Closed Month');
        }

        $docDate = Carbon::parse($dataRentSC['docDate']);
        $dueDate = Carbon::parse($dataRentSC['dueDate']);

        if($dataRentSC['selectall'] == 'all')
        {
            $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_others '".$dataRentSC['cutoff']."','".$project_no."'");
            //dd($dataInvRentSC);

            foreach($dataInvRentSC as $data)
            {
                DB::table('PSM_SCHEDULE')
                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                    ->update([
                        'IS_TAPING_INV'=>1,
                        'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                        'TAPING_DATE'=>$dateNow,
                        'DOC_DATE_INV'=>$docDate,
                        'DUE_DATE_INV'=>$dueDate,
                        'updated_at'=>$dateNow
                    ]);

                $action = "TAPING DATA";
                $descriptionLog = 'Taping Schedule Others ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                $this->saveToLog($action, $descriptionLog);
            }
        }
        else
        {
            //dd(count($updateSetSertifikat['billing']));
            if (count($dataRentSC['billing']) > 0)
            {
                for($i=0;  $i < count($dataRentSC['billing']); $i++){
                    if ($dataRentSC['billing'][$i] <> 0)
                    {
                        $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_others_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");
                        //dd($dataInvRentSC);

                        foreach($dataInvRentSC as $data)
                        {
                            DB::table('PSM_SCHEDULE')
                                ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'IS_TAPING_INV'=>1,
                                    'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                    'TAPING_DATE'=>$dateNow,
                                    'DOC_DATE_INV'=>$docDate,
                                    'DUE_DATE_INV'=>$dueDate,
                                    'updated_at'=>$dateNow
                                ]);

                            $action = "TAPING DATA";
                            $descriptionLog = 'Taping Schedule Others ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                            $this->saveToLog($action, $descriptionLog);
                        }
                    }
                }
            }
        }

        $description = 'Taping Schedule Others ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
        return redirect()->route('invoice.listtapinginvoice')->with('success',$description.' Successfully');
    }

    public function tapingInvoiceCasual(){
        if(Session::get('id') == ''){
            Session::flush();
            return redirect('/login');
        }

        $generator = new utilGenerator;
        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $converter = new utilConverter();
        $dataRentSC = \Request::all();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $yearTaxPeriod = substr($dateNow->year,2,4);

        //dd($dataRentSC['docDate']);
        if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '' )
        {
            return redirect()->route('invoice.listgenerateinvoice')
                ->with('error', 'Document Date or Due Date Cannot be Empty');
        }

        if($dataRentSC['backdate'] == "")
        {
            return redirect()->route('invoice.listgenerateinvoice')
                ->with('error','You Cannot Create Transaction In Closed Month');
        }

        $docDate = Carbon::parse($dataRentSC['docDate']);
        $dueDate = Carbon::parse($dataRentSC['dueDate']);

        if($dataRentSC['selectall'] == 'all')
        {
            $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_casual '".$dataRentSC['cutoff']."','".$project_no."'");
            //dd($dataInvRentSC);

            foreach($dataInvRentSC as $data)
            {
                DB::table('PSM_SCHEDULE')
                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                    ->update([
                        'IS_TAPING_INV'=>1,
                        'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                        'TAPING_DATE'=>$dateNow,
                        'DOC_DATE_INV'=>$docDate,
                        'DUE_DATE_INV'=>$dueDate,
                        'updated_at'=>$dateNow
                    ]);

                $action = "TAPING DATA";
                $descriptionLog = 'Taping Schedule Casual ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                $this->saveToLog($action, $descriptionLog);
            }
        }
        else
        {
            //dd(count($updateSetSertifikat['billing']));
            if (count($dataRentSC['billing']) > 0)
            {
                for($i=0;  $i < count($dataRentSC['billing']); $i++){
                    if ($dataRentSC['billing'][$i] <> 0)
                    {
                        $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_tap_invoice_casual_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");
                        //dd($dataInvRentSC);

                        foreach($dataInvRentSC as $data)
                        {
                            DB::table('PSM_SCHEDULE')
                                ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                                ->update([
                                    'IS_TAPING_INV'=>1,
                                    'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
                                    'TAPING_DATE'=>$dateNow,
                                    'DOC_DATE_INV'=>$docDate,
                                    'DUE_DATE_INV'=>$dueDate,
                                    'updated_at'=>$dateNow
                                ]);

                            $action = "TAPING DATA";
                            $descriptionLog = 'Taping Schedule Casual ID'.$data->PSM_SCHEDULE_ID_INT.' Project '.$dataProject['PROJECT_NAME'];
                            $this->saveToLog($action, $descriptionLog);
                        }
                    }
                }
            }
        }

        $description = 'Taping Schedule Casual Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
        return redirect()->route('invoice.listgenerateinvoice')->with('success',$description.' Successfully');
    }

    public function untapingInvoice(){
        if(Session::get('id') == ''){
            Session::flush();
            return redirect('/login');
        }

        //$generator = new utilGenerator();
        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

        $dataUntaping = \Request::all();
        //dd($dataUntaping);
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        //dd(count($updateSetSertifikat['billing']));
        if (count($dataUntaping['billing']) > 0)
        {
            for($i=0;  $i < count($dataUntaping['billing']); $i++){
                if ($dataUntaping['billing'][$i] <> 0)
                {
                    DB::table('PSM_SCHEDULE')
                        ->where('PSM_SCHEDULE_ID_INT','=',$dataUntaping['billing'][$i])
                        ->update([
                            'IS_TAPING_INV'=>0,
                            'TAPING_DATE'=>'',
                            'DOC_DATE_INV'=>'',
                            'DUE_DATE_INV'=>'',
                            'updated_at'=>$dateNow
                        ]);

                    $action = "UNTAPING DATA";
                    $descriptionLog = 'Untaping Schedule ID '.$dataUntaping['billing'][$i].' Project '.$dataProject['PROJECT_NAME'];
                    $this->saveToLog($action, $descriptionLog);
                }
            }
        }

        $description = 'Untaping Schedule Project '.$dataProject['PROJECT_NAME'];
        return redirect()->route('invoice.listtapinginvoiceschedule')->with('success',$description.' Successfully');
    }

//    public function generateInvoiceRentalPDF(){
//        if(Session::get('id') == ''){
//            Session::flush();
//            return redirect('/login');
//        }
//
//        //$generator = new utilGenerator();
//        $project_no = Session::get('PROJECT_NO_CHAR');
//        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
//        $converter = new utilConverter();
//
//
//        $dataRentSC = \Request::all();
//        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
//        $yearTaxPeriod = substr($dateNow->year,2,4);
//        //dd($dataRentSC);
//
//        //dd($dataRentSC['docDate']);
//        if ($dataRentSC['docDate'] == '' || $dataRentSC['dueDate'] == '')
//            //|| $dataRentSC['INVOICE_FP_NOCHAR'] == ''
//        {
//            return redirect()->route('invoice.listgenerateinvoice')
//                ->with('error', 'Document Date or Due Date Cannot be Empty');
//        }
//
//        if($dataRentSC['backdate'] == "")
//        {
//            return redirect()->route('invoice.listgenerateinvoice')
//                ->with('error','You Cannot Create Transaction In Closed Month');
//        }
//
//        //dd($numberTax);
//
//        $docDate = Carbon::parse($dataRentSC['docDate']);
//        $dueDate = Carbon::parse($dataRentSC['dueDate']);
//
//        if($dataRentSC['selectall'] == 'all')
//        {
//            $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_invoice_rent '".$dataRentSC['cutoff']."','".$project_no."'");
//            //dd($dataInvRentSC);
//
//            foreach($dataInvRentSC as $data)
//            {
////                $cekDataTax = DB::table('TAX_MD_FP')
////                    ->where('PROJECT_NO_CHAR','=',$project_no)
////                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
////                    ->where('IS_TAKEN','=',0)
////                    ->where('IS_DELETE','=',0)
////                    ->count();
////
////                if ($cekDataTax <= 0)
////                {
////                    return redirect()->route('invoice.listgenerateinvoice')
////                        ->with('error','Tax Number not found, contact yout tax department ');
////                }
////                else
////                {
////                    $taxNumber = DB::table('TAX_MD_FP')
////                        ->where('PROJECT_NO_CHAR','=',$project_no)
////                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
////                        ->where('IS_TAKEN','=',0)
////                        ->where('IS_DELETE','=',0)
////                        ->first();
////
////                    $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.$taxNumber->TAX_MD_FP_NOCHAR;
////
////                    DB::table('TAX_MD_FP')
////                        ->where('PROJECT_NO_CHAR','=',$project_no)
////                        ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
////                        ->where('IS_TAKEN','=',0)
////                        ->where('IS_DELETE','=',0)
////                        ->where('TAX_MD_FP_NOCHAR','=',$taxNumber->TAX_MD_FP_NOCHAR)
////                        ->update([
////                            'IS_TAKEN'=>1,
////                            'UPDATED_BY'=>Session::get('name'),
////                            'updated_at'=>$dateNow,
////                        ]);
////                }
//
//                $cekDataInvoice = DB::table('INVOICE_TRANS')
//                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
//                    ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
//                    ->whereNotIn('INVOICE_STATUS_INT',[0]) //0 = void
//                    ->count();
//                //dd($cekDataInvoice);
//
//                if ($cekDataInvoice <= 0)
//                {
//                    $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
//                    $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();
//
//                    $Counter = str_pad($counter->inv_rent_count, 5, "0", STR_PAD_LEFT);
//                    $Year = substr($docDate->year, 2);
//                    $Month = $docDate->month;
//                    $monthRomawi = $converter->getRomawi($Month);
//
//                    Counter::where('PROJECT_NO_CHAR', '=', $project_no)
//                        ->update(['inv_rent_count'=>$counter->inv_rent_count + 1]);
//
//                    $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;
//
//                    DB::table('INVOICE_TRANS')
//                        ->insert([
//                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
//                            'INVOICE_FP_NOCHAR'=>'',
//                            'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
//                            'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
//                            'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
//                            'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
//                            'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
//                            'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
//                            'DOC_TYPE'=>'B',
//                            'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
//                            'TGL_SCHEDULE_DATE'=>$docDate,
//                            'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
//                            'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
//                            'INVOICE_TRANS_DPP'=>($data->BASE_AMOUNT_NUM - $data->DISC_NUM),
//                            'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
//                            'INVOICE_TRANS_PPH'=>($data->BASE_AMOUNT_NUM * 0.1),
//                            'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
//                            'PROJECT_NO_CHAR'=>$project_no,
//                            'INVOICE_CREATE_CHAR'=>Session::get('name'),
//                            'INVOICE_CREATE_DATE'=>$dateNow,
//                            'FROM_SCHEDULE'=>1,
//                            'JOURNAL_STATUS_INT'=>0,
//                            'created_at'=>$dateNow,
//                            'updated_at'=>$dateNow
//                        ]);
//
//                    DB::table('PSM_SCHEDULE')
//                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
//                        ->update([
//                            'INVOICE_NUMBER_CHAR'=>$noInvoice,
//                            'SCHEDULE_STATUS_INT'=>2, //generate invoice
//                            'updated_at'=>$dateNow
//                        ]);
//
//                    $dataInvoice = DB::table('INVOICE_TRANS')
//                        ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
//                        ->first();
//
//                    $this->PrintInvoiceKwitansiPDF('INV',$dataInvoice->INVOICE_TRANS_ID_INT);
//                }
//            }
//        }
//        else
//        {
//            //dd(count($updateSetSertifikat['billing']));
//            if (count($dataRentSC['billing']) > 0)
//            {
//                for($i=0;  $i < count($dataRentSC['billing']); $i++){
//                    if ($dataRentSC['billing'][$i] <> 0)
//                    {
//                        $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_invoice_rent_byID '".$dataRentSC['cutoff']."','".$project_no."','".$dataRentSC['billing'][$i]."'");
//                        //dd($dataInvRentSC);
//
//                        foreach($dataInvRentSC as $data)
//                        {
////                            $cekDataTax = DB::table('TAX_MD_FP')
////                                ->where('PROJECT_NO_CHAR','=',$project_no)
////                                ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
////                                ->where('IS_TAKEN','=',0)
////                                ->where('IS_DELETE','=',0)
////                                ->count();
////
////                            if ($cekDataTax <= 0)
////                            {
////                                return redirect()->route('invoice.listgenerateinvoice')
////                                    ->with('error','Tax Number not found, contact yout tax department ');
////                            }
////                            else
////                            {
////                                $taxNumber = DB::table('TAX_MD_FP')
////                                    ->where('PROJECT_NO_CHAR','=',$project_no)
////                                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
////                                    ->where('IS_TAKEN','=',0)
////                                    ->where('IS_DELETE','=',0)
////                                    ->first();
////
////                                $numberTax = $dataRentSC['TRANS_CODE'].'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.$taxNumber->TAX_MD_FP_NOCHAR;
////
////                                DB::table('TAX_MD_FP')
////                                    ->where('PROJECT_NO_CHAR','=',$project_no)
////                                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
////                                    ->where('IS_TAKEN','=',0)
////                                    ->where('IS_DELETE','=',0)
////                                    ->where('TAX_MD_FP_NOCHAR','=',$taxNumber->TAX_MD_FP_NOCHAR)
////                                    ->update([
////                                        'IS_TAKEN'=>1,
////                                        'UPDATED_BY'=>Session::get('name'),
////                                        'updated_at'=>$dateNow,
////                                    ]);
////                            }
//
//                            $cekDataInvoice = DB::table('INVOICE_TRANS')
//                                ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
//                                ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
//                                ->whereNotIn('INVOICE_STATUS_INT',[0]) //0 = void
//                                ->count();
//                            //dd($cekDataInvoice);
//
//                            if ($cekDataInvoice <= 0)
//                            {
//                                //dd($docDate <= Carbon::parse('2022-03-31'));
//                                $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
//                                $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();
//
//                                $Counter = str_pad($counter->inv_rent_count, 5, "0", STR_PAD_LEFT);
//                                $Year = substr($docDate->year, 2);
//                                $Month = $docDate->month;
//                                $monthRomawi = $converter->getRomawi($Month);
//
//                                Counter::where('PROJECT_NO_CHAR', '=', $project_no)
//                                    ->update(['inv_rent_count'=>$counter->inv_rent_count + 1]);
//
//                                $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;
//
//                                DB::table('INVOICE_TRANS')
//                                    ->insert([
//                                        'INVOICE_TRANS_NOCHAR'=>$noInvoice,
//                                        'INVOICE_FP_NOCHAR'=>'',
//                                        'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
//                                        'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
//                                        'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
//                                        'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
//                                        'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
//                                        'TRANS_CODE'=>$dataRentSC['TRANS_CODE'],
//                                        'DOC_TYPE'=>'B',
//                                        'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
//                                        'TGL_SCHEDULE_DATE'=>$docDate,
//                                        'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
//                                        'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
//                                        'INVOICE_TRANS_DPP'=>($data->BASE_AMOUNT_NUM - $data->DISC_NUM),
//                                        'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
//                                        'INVOICE_TRANS_PPH'=>($data->BASE_AMOUNT_NUM * 0.1),
//                                        'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
//                                        'PROJECT_NO_CHAR'=>$project_no,
//                                        'INVOICE_CREATE_CHAR'=>Session::get('name'),
//                                        'INVOICE_CREATE_DATE'=>$dateNow,
//                                        'FROM_SCHEDULE'=>1,
//                                        'JOURNAL_STATUS_INT'=>0,
//                                        'created_at'=>$dateNow,
//                                        'updated_at'=>$dateNow
//                                    ]);
//
//                                DB::table('PSM_SCHEDULE')
//                                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
//                                    ->update([
//                                        'INVOICE_NUMBER_CHAR'=>$noInvoice,
//                                        'SCHEDULE_STATUS_INT'=>2, //generate invoice
//                                        'updated_at'=>$dateNow
//                                    ]);
//
//                                $dataInvoice = DB::table('INVOICE_TRANS')
//                                    ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
//                                    ->first();
//
//                                $this->PrintInvoiceKwitansiPDF('INV',$dataInvoice->INVOICE_TRANS_ID_INT);
//
////                                DB::table('INVOICE_TRANS')
////                                    ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
////                                    ->update([
////                                        'ACC_JOURNAL_NOCHAR'=>$nojournal,
////                                        'updated_at'=>$dateNow
////                                    ]);
//                            }
//                        }
//                    }
//                }
//            }
//        }
//
//        \Session::flash('message', 'Generate Invoice Rental Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME']);
//        $action = "GENERATE RT DATA";
//        $description = 'Generate Invoice Rental Cut Off '.$dataRentSC['cutoff'].' Project '.$dataProject['PROJECT_NAME'];
//        $this->saveToLog($action, $description);
//
//        return redirect()->route('invoice.listgenerateinvoice')->with('success',$description.' Successfully');
//    }

    public function generateInvoiceRentalTap(){
        $generator = new utilGenerator;
        $project_no = Session::get('PROJECT_NO_CHAR');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $converter = new utilConverter();

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $yearTaxPeriod = substr($dateNow->year,2,4);
        //dd($yearTaxPeriod);

        //dd("exec MTLA_MALL.dbo.sp_invoice_rent_01 '".$dateNow."','".$project_no."'");

        $dataInvRentSC = DB::select("exec MTLA_MALL.dbo.sp_invoice_rent_01 '".$dateNow."','".$project_no."'");


        $cekDataTax = DB::table('TAX_MD_FP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
            ->where('IS_TAKEN','=',0)
            ->where('IS_DELETE','=',0)
            ->count();

        $countDataInv = count($dataInvRentSC);

        if ($countDataInv > $cekDataTax)
        {
            $action = "GENERATE INV ".$dataProject['PROJECT_CODE'];
            $description = 'Tax Number is not enough';
            $this->saveToLog($action, $description);
        }
        else
        {
            foreach($dataInvRentSC as $data)
            {
                $taxNumber = DB::table('TAX_MD_FP')
                    ->where('PROJECT_NO_CHAR','=',$project_no)
                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                    ->where('IS_TAKEN','=',0)
                    ->where('IS_DELETE','=',0)
                    ->first();

                $numberTax = $data->TRANS_CODE.'0.'.$taxNumber->TAX_MD_FP_KODE_CHAR.'-'.$taxNumber->TAX_MD_FP_YEAR_CHAR.'.'.$taxNumber->TAX_MD_FP_NOCHAR;

                DB::table('TAX_MD_FP')
                    ->where('TAX_MD_FP_ID_INT','=',$taxNumber->TAX_MD_FP_ID_INT)
                    ->update([
                        'IS_TAKEN'=>1,
                        'UPDATED_BY'=>Session::get('name'),
                        'updated_at'=>$dateNow,
                    ]);

                $cekDataInvoice = DB::table('INVOICE_TRANS')
                    ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                    ->whereIn('INVOICE_TRANS_TYPE',['DP','RT'])
                    ->whereNotIn('INVOICE_STATUS_INT',[0]) //0 = void
                    ->count();
                //dd($cekDataInvoice);

                $docDate = Carbon::parse($data->DOC_DATE_INV);
                $dueDate = Carbon::parse($data->DUE_DATE_INV);

                if ($cekDataInvoice <= 0)
                {
                    $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
                    $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

                    $Counter = str_pad($counter->inv_rent_count, 5, "0", STR_PAD_LEFT);
                    $Year = substr($docDate->year, 2);
                    $Month = $docDate->month;
                    $monthRomawi = $converter->getRomawi($Month);

                    Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                        ->update(['inv_rent_count'=>$counter->inv_rent_count + 1]);

                    $noInvoice = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/INV-'.$data->TRX_CODE.'/'.$monthRomawi.'/'.$Year;

                    DB::table('INVOICE_TRANS')
                        ->insert([
                            'INVOICE_TRANS_NOCHAR'=>$noInvoice,
                            'INVOICE_FP_NOCHAR'=>$numberTax,
                            'PSM_SCHEDULE_ID_INT'=>$data->PSM_SCHEDULE_ID_INT,
                            'PSM_TRANS_NOCHAR'=>$data->PSM_TRANS_NOCHAR,
                            'MD_TENANT_ID_INT'=>$data->MD_TENANT_ID_INT,
                            'LOT_STOCK_NO'=>$data->LOT_STOCK_NO,
                            'INVOICE_TRANS_TYPE'=>$data->TRX_CODE,
                            'TRANS_CODE'=>$data->TRANS_CODE,
                            'DOC_TYPE'=>'B',
                            'INVOICE_TRANS_DESC_CHAR'=>$data->DESC_CHAR,
                            'TGL_SCHEDULE_DATE'=>$docDate,
                            'TGL_SCHEDULE_DUE_DATE'=>$dueDate,
                            'MD_TENANT_PPH_INT'=>$data->MD_TENANT_PPH_INT,
                            'INVOICE_TRANS_DPP'=>($data->DPP_AMOUNT),
                            'INVOICE_TRANS_PPN'=>$data->PPN_PRICE_NUM,
                            'INVOICE_TRANS_PPH'=>($data->DPP_AMOUNT * 0.1),
                            'INVOICE_TRANS_TOTAL'=>$data->BILL_AMOUNT,
                            'PROJECT_NO_CHAR'=>$project_no,
                            'INVOICE_CREATE_CHAR'=>Session::get('name'),
                            'INVOICE_CREATE_DATE'=>$dateNow,
                            'FROM_SCHEDULE'=>1,
                            'JOURNAL_STATUS_INT'=>1,
                            'created_at'=>$dateNow,
                            'updated_at'=>$dateNow
                        ]);

                    DB::table('PSM_SCHEDULE')
                        ->where('PSM_SCHEDULE_ID_INT','=',$data->PSM_SCHEDULE_ID_INT)
                        ->update([
                            'INVOICE_NUMBER_CHAR'=>$noInvoice,
                            'SCHEDULE_STATUS_INT'=>2, //generate invoice
                            'updated_at'=>$dateNow,
                            'IS_GENERATE_INV'=>1
                        ]);

                    //Create Journal
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    //dd($countTable);
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;
                    //$project_code = $dataProject['PROJECT_CODE'];

                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->first();
                    //dd($trxtype);
                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  'invoice.listdatainvoice');
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $BillAmount = $data->BILL_AMOUNT;

                    if ($docDate <= Carbon::parse('2022-03-31'))
                    {
                        $UNEARNED = round($BillAmount / 1.1);
                        $PPN = round($UNEARNED * 0.1);
                    }
                    else
                    {
                        $UNEARNED = round($BillAmount / $dataProject['DPPBM_NUM']);
                        $PPN = round($UNEARNED * $dataProject['PPNBM_NUM']);
                    }


                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'INVRT')->get();
                    //dd($dataTrxtype);
                    foreach($dataTrxtype as $trx)
                    {
                        if ($trx->MD_TRX_MODE == 'Debit')
                        {
                            if($trx->ACC_NO_CHAR == '150003001')
                            {
                                $nilaiAmount = $BillAmount;
                                $totalDebit += $BillAmount;
                            }
                        }elseif($trx->MD_TRX_MODE == 'Kredit')
                        {
                            if($trx->ACC_NO_CHAR == '650005001')
                            {
                                $nilaiAmount = $UNEARNED * -1;
                                $totalKredit += $UNEARNED;
                            }
                            elseif($trx->ACC_NO_CHAR == '630002012')
                            {
                                $nilaiAmount = $PPN * -1;
                                $totalKredit += $PPN;
                            }
                        }

                        //dd($nilaiAmount);

                        $datacoa = DB::table('ACC_MD_COA_NEW')
                            ->where('ACC_NO_CHAR', '=',$trx->ACC_NO_CHAR)
                            ->first();

                        $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                        $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                        $inputGlTrans['ACC_SOURCODE_CHAR'] = $trx->ACC_SOURCODE_CHAR;
                        $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                        $inputGlTrans['PSM_TRANS_NOCHAR'] = $data->PSM_TRANS_NOCHAR;
                        $inputGlTrans['MD_TENANT_ID_INT'] = $data->MD_TENANT_ID_INT;
                        $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                        $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                        $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                        $inputGlTrans['ACC_NO_CHAR'] = $trx->ACC_NO_CHAR;
                        $inputGlTrans['ACC_NAME_CHAR'] = $trx->ACC_NAME_CHAR;
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $data->LOT_STOCK_NO;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                        //dd($inputGlTrans);
                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('invoice.listdatainvoice')
                                ->with('error','Failed update counter table, errmsg : '.$ex);
                        }
                    }

//                    $cekDataGL = GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
//                                ->count();
                    //dd($noBankVoucher.'-'.$cekDataGL);

                    GlTrans::where('ACC_JOURNAL_NOCHAR', '=', $noBankVoucher)
                        ->where('ACC_AMOUNT_INT','=',0)->delete();

                    $inputJournal['ACC_JOURNAL_NOCHAR']=$noBankVoucher;
                    $inputJournal['ACC_JOURNAL_RNOCHAR']=$nojournal;
                    $inputJournal['INVOICE_NUMBER_NUM']=$noInvoice;
                    $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                    $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                    $inputJournal['PROJECT_NO_CHAR']=$project_no;
                    $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                    $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                    $inputJournal['ACC_JOURNAL_REF_DESC']="Tagihan ".$data->DESC_CHAR.', '.$data->MD_TENANT_NAME_CHAR.', LOT '.$data->LOT_STOCK_NO.', Faktur '.$numberTax;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=Session::get('name');
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_CHAR']='';
                    $inputJournal['ACC_JOURNAL_MODIFIEDBY_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_AUDITOR_DTTIME']='';
                    $inputJournal['ACC_JOURNAL_PERIOD']=$period_no;
                    $inputJournal['ACC_JOURNAL_VENDOR_CHAR']='';
                    $inputJournal['ACC_JOURNAL_FP_CHAR']= $numberTax;
                    $inputJournal['ACC_JOURNAL_AUTOMATION']=1;

                    try {
                        Journal::create($inputJournal);
                    } catch (QueryException $ex) {
                        return redirect()->route('accounting.journal.viewlistjournalar')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    DB::table('INVOICE_TRANS')
                        ->where('INVOICE_TRANS_NOCHAR','=',$noInvoice)
                        ->update([
                            'ACC_JOURNAL_NOCHAR'=>$nojournal,
                            'updated_at'=>$dateNow
                        ]);
                }

                $action = "GENERATE INV ".$dataProject['PROJECT_CODE'];
                $description = 'Generate Invoice Schdule ID '.$data->PSM_SCHEDULE_ID_INT.' ('.$data->PSM_TRANS_NOCHAR.')';
                $this->saveToLog($action, $description);
            }
        }
    }
}