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
use Maatwebsite\Excel;

use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\BTP;
use Carbon\Carbon;
use View;
use Session;
use App\Http\Controllers\LogActivity\LogActivityController;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use App\Http\Controllers\Util\utilArray;
use App\Http\Controllers\Util\utilConverter;
use App\Http\Controllers\Util\utilGenerator;
use App\Http\Controllers\Util\utilSession;

define("ERROR_ROUTE_KWT_INV", "invoice.listcreeditnotes");

class CreditNotes extends Controller {

    public function __construct()
    {

    }

    public function listDataCreditNotes(){
        $project_no = session('current_project');

        $listDataCreditNotes = DB::select("Select a.CN_TRANS_ID_INT,a.CN_TRANS_NOCHAR,a.PSM_TRANS_NOCHAR,b.LOT_STOCK_NO,c.MD_TENANT_NAME_CHAR,b.SHOP_NAME_CHAR,
                                                   a.CN_TRANS_DESC,
                                                   (CASE
                                                        WHEN a.CN_TRANS_STATUS_INT = 1 THEN 'REQUEST'
                                                        WHEN a.CN_TRANS_STATUS_INT = 2 THEN 'APPROVE'
                                                   END) as CN_TRANS_STATUS_INT,a.CN_TRANS_AMOUNT,a.DOC_TYPE
                                            from CN_TRANS as a LEFT JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                            LEFT JOIN MD_TENANT as c ON a.MD_TENANT_ID_INT = c.MD_TENANT_ID_INT
                                            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
                                            AND a.CN_TRANS_STATUS_INT NOT IN (0)");

        return View::make('page.accountreceivable.creditnotes.listDataCreditNotes',
            ['project_no'=>$project_no,'listDataCreditNotes'=>$listDataCreditNotes]);
    }

    public function viewAddDataCreditNotes(){
        $project_no = session('current_project');

        $billingType = DB::table('INVOICE_TRANS_TYPE')
            ->where('INVOICE_TRANS_TYPE_STATUS','=',1)
            ->get();

       $secureDepType = DB::table('PSM_SECURE_DEP_TYPE')
           ->where('IS_DELETE','=',0)
           ->get();

        $dataLot = DB::select("Select a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT,SUM(c.LOT_STOCK_SQM) AS LOT_STOCK_SQM
            from PSM_TRANS as a LEFT JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
            LEFT JOIN PSM_TRANS_LOT AS c ON c.PSM_TRANS_NOCHAR = a.PSM_TRANS_NOCHAR
            WHERE a.PSM_TRANS_STATUS_INT = 2
            AND a.PROJECT_NO_CHAR = '".$project_no."'
            GROUP BY a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT");

        $tenant = DB::select("SELECT *
                            FROM MD_TENANT
                            WHERE PROJECT_NO_CHAR = '".$project_no."'");

        return View::make('page.accountreceivable.creditnotes.addDataCreditNotes',
            ['billingType'=>$billingType,'dataLot'=>$dataLot,'secureDepType'=>$secureDepType,
             'tenant'=>$tenant]);
    }

    public function saveCreditNotes(Requests\AccountReceivable\AddDataCreditNotes $requestInv){
        $inputDataCN = $requestInv->all();
        $converter = new utilConverter();
        
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

            if ($inputDataCN['INVOICE_TRANS_TYPE'] <> 'OT' && $inputDataCN['PSM_TRANS_NOCHAR'] == '')
            {
                return redirect()->route('creditnotes.listdatacreeditnotes')
                    ->with('error','Your Lease Agreement is empty, Process Fail..');
            }

            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

            $counter = Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataCompany = Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

            $Counter = str_pad($counter->cn_count, 5, "0", STR_PAD_LEFT);
            $Year = substr($dateNow->year, 2);
            $Month = $dateNow->month;
            $monthRomawi = $converter->getRomawi($Month);

            Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                ->update(['cn_count'=>$counter->cn_count + 1]);

            $noCN = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/CN-'.$inputDataCN['INVOICE_TRANS_TYPE'].'/'.$monthRomawi.'/'.$Year;

            DB::table('CN_TRANS')
                ->insert([
                    'CN_TRANS_NOCHAR'=>$noCN,
                    'PSM_TRANS_NOCHAR'=>$inputDataCN['PSM_TRANS_NOCHAR'],
                    'MD_TENANT_ID_INT'=>$inputDataCN['MD_TENANT_ID_INT'],
                    'INVOICE_TRANS_TYPE'=>$inputDataCN['INVOICE_TRANS_TYPE'],
                    'DOC_TYPE'=>$inputDataCN['DOC_TYPE'],
                    'CN_TRANS_DESC'=>$inputDataCN['CN_TRANS_DESC'],
                    'CN_TRANS_TRX_DATE'=>$inputDataCN['CN_TRANS_TRX_DATE'],
                    'CN_TRANS_AMOUNT'=>$inputDataCN['CN_TRANS_AMOUNT'],
                    'PROJECT_NO_CHAR'=>$project_no,
                    'CN_TRANS_REQUEST_DATE'=>$dateNow,
                    'CN_TRANS_REQUEST_CHAR'=>$userName,
                    'created_at'=>$dateNow,
                    'updated_at'=>$dateNow
                ]);

            $action = "INSERT DATA CREDIT NOTES";
            $description = 'Saving Credit Notes '.$noCN.' Lease Doc :. '.$inputDataCN['PSM_TRANS_NOCHAR'];
            $this->saveToLog($action, $description);

            $dataCN = DB::table('CN_TRANS')
                ->where('CN_TRANS_NOCHAR','=',$noCN)
                ->first();

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('creditnotes.viewadddatacreditnotes')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('creditnotes.vieweditdatacreditnotes',[$dataCN->CN_TRANS_ID_INT,$dataCN->DOC_TYPE])
            ->with('success',$description.' Successfully');
    }

    public function saveEditCreditNotes(Requests\AccountReceivable\AddDataCreditNotes $requestInv){
        $inputDataCN = $requestInv->all();
        $converter = new utilConverter();
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataCNTrans = DB::table('CN_TRANS')
            ->where('CN_TRANS_NOCHAR','=',$inputDataCN['CN_TRANS_NOCHAR'])
            ->first();

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        DB::table('CN_TRANS')
            ->where('CN_TRANS_NOCHAR','=',$dataCNTrans->CN_TRANS_NOCHAR)
            ->update([
                'CN_TRANS_DESC'=>$inputDataCN['CN_TRANS_DESC'],
                'CN_TRANS_TRX_DATE'=>$inputDataCN['CN_TRANS_TRX_DATE'],
                'updated_at'=>$dateNow
            ]);

        $action = "UPDATE DATA CREDIT NOTES";
        $description = 'Update Credit Notes '.$dataCNTrans->CN_TRANS_NOCHAR.' Lease Doc :. '.$dataCNTrans->PSM_TRANS_NOCHAR.' Tenant ID : '.$dataCNTrans->MD_TENANT_ID_INT;
        $this->saveToLog($action, $description);

        return redirect()->route('creditnotes.listdatacreeditnotes')
            ->with('success',$description.' Successfully');
    }

    public function viewEditDataCreditNotes($CN_TRANS_ID_INT,$DOC_TYPE){
        $project_no = session('current_project');

        $roles = session('level');

        $dataCN = DB::table('CN_TRANS')
            ->where('CN_TRANS_ID_INT','=',$CN_TRANS_ID_INT)
            ->first();

        if ($dataCN->PSM_TRANS_NOCHAR == '')
        {
            $dataTenant = DB::table('MD_TENANT')
                ->where('MD_TENANT_ID_INT','=',$dataCN->MD_TENANT_ID_INT)
                ->first();

            $lotNo = '';
            $lotId = 0;
            $noPSM = '';
            $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
            $tenantId = $dataTenant->MD_TENANT_ID_INT;
            $sqm = 0;

            $whereInvoice = " AND a.MD_TENANT_ID_INT = ".$tenantId." ";
        }
        else
        {

            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$dataCN->PSM_TRANS_NOCHAR)
                ->first();

            if(empty($dataPSM->LOT_STOCK_ID_INT)) {
                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataCN->MD_TENANT_ID_INT)
                    ->first();

                $dataLotSqmPSMSum = DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->sum('LOT_STOCK_SQM');

                $lotData = NULL;

                $lotNo = $dataPSM->LOT_STOCK_NO;
                $lotId = NULL;
                $noPSM = $dataPSM->PSM_TRANS_NOCHAR;
                $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
                $tenantId = $dataTenant->MD_TENANT_ID_INT;
                $sqm = $dataLotSqmPSMSum;
            }
            else {
                $dataTenant = DB::table('MD_TENANT')
                    ->where('MD_TENANT_ID_INT','=',$dataCN->MD_TENANT_ID_INT)
                    ->first();
    
                $lotData = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_NO','=',$dataPSM->LOT_STOCK_NO)
                    ->first();
    
                $lotNo = $lotData->LOT_STOCK_NO;
                $lotId = $lotData->LOT_STOCK_ID_INT;
                $noPSM = $dataPSM->PSM_TRANS_NOCHAR;
                $tenantName = $dataTenant->MD_TENANT_NAME_CHAR;
                $tenantId = $dataTenant->MD_TENANT_ID_INT;
                $sqm = $lotData->LOT_STOCK_SQM;
            }


            if($DOC_TYPE == 'D') {
                $whereInvoice = " AND a.PSM_TRANS_NOCHAR = '".$noPSM."'";
            }
            else {
                $whereInvoice = " AND a.PSM_TRANS_NOCHAR = '".$noPSM."' AND a.MD_TENANT_ID_INT = ".$tenantId." ";
            }
        }

        $dataCNDetail = DB::select("Select a.CN_TRANS_DTL_ID,a.CN_TRANS_DTL_TYPE,a.INVOICE_TRANS_NOCHAR,b.INVOICE_TRANS_DESC_CHAR,
                                           a.INVOICE_TRANS_AMOUNT,a.CN_TRANS_DTL_AMOUNT,a.UTILS_TYPE_NAME,a.INVOICE_AMOUNT
                                    from CN_TRANS_DETAIL as a LEFT JOIN INVOICE_TRANS as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                    where a.CN_TRANS_NOCHAR = '".$dataCN->CN_TRANS_NOCHAR."'
                                    and a.IS_DELETE = 0");

        $countCNDetail = DB::table('CN_TRANS_DETAIL')
            ->where('CN_TRANS_NOCHAR','=', $dataCN->CN_TRANS_NOCHAR)
            ->where('IS_DELETE','=',0)
            ->count();

        if ($DOC_TYPE == 'B')
        {
            $dataBillingType = DB::table('INVOICE_TRANS_TYPE')
                ->where('INVOICE_TRANS_TYPE','=',$dataCN->INVOICE_TRANS_TYPE)
                ->first();

            $billingCode = $dataBillingType->INVOICE_TRANS_TYPE;
            $billingDesc = $dataBillingType->INVOICE_TRANS_TYPE_DESC;
        }
        else
        {
            $dataBillingType = DB::table('PSM_SECURE_DEP_TYPE')
                ->where('PSM_SECURE_DEP_TYPE_CODE','=',$dataCN->INVOICE_TRANS_TYPE)
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

        if(empty($dataPSM->LOT_STOCK_ID_INT) && $dataPSM->PSM_TRANS_NOCHAR != "") {
            $dataLot = DB::select("Select a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT,SUM(c.LOT_STOCK_SQM) AS LOT_STOCK_SQM
                from PSM_TRANS as a LEFT JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                LEFT JOIN PSM_TRANS_LOT AS c ON c.PSM_TRANS_NOCHAR = a.PSM_TRANS_NOCHAR
                WHERE a.PSM_TRANS_STATUS_INT = 2
                AND a.PROJECT_NO_CHAR = '".$project_no."'
                GROUP BY a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT");
        }
        else {
            $dataLot = DB::select("Select a.LOT_STOCK_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.MD_TENANT_ID_INT,c.LOT_STOCK_SQM
                from PSM_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                INNER JOIN LOT_STOCK as c ON a.LOT_STOCK_ID_INT = c.LOT_STOCK_ID_INT
                WHERE a.PSM_TRANS_STATUS_INT = 2
                AND a.PROJECT_NO_CHAR = '".$project_no."'");
        }

        $tenant = DB::select("SELECT *
                            FROM MD_TENANT
                            WHERE PROJECT_NO_CHAR = '".$project_no."'");

        if ($dataCN->INVOICE_TRANS_TYPE == 'UT')
        {
            $dataListInvoice = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.INVOICE_TRANS_DESC_CHAR,
                                           ISNULL((a.INVOICE_TRANS_DPP + a.INVOICE_TRANS_PPN) -
		                                    SUM(ISNULL(CASE WHEN b.INVOICE_PAYMENT_STATUS_INT = 0 THEN 0 ELSE b.PAID_BILL_AMOUNT END,0)),0) as INVOICE_TRANS_AMOUNT
                                    from INVOICE_TRANS as a LEFT JOIN INVOICE_PAYMENT as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                    WHERE a.INVOICE_STATUS_INT NOT IN (0,4)
                                    AND a.INVOICE_TRANS_NOCHAR NOT IN (
                                        Select a.INVOICE_TRANS_NOCHAR
                                        from CN_TRANS_DETAIL as a INNER JOIN CN_TRANS as b ON a.CN_TRANS_NOCHAR = b.CN_TRANS_NOCHAR
                                        where a.IS_DELETE = 0
                                        and b.CN_TRANS_STATUS_INT NOT IN (0)
                                        AND a.CN_TRANS_DTL_TYPE = 'INVOICE'
                                    )
                                    AND a.INVOICE_TRANS_TYPE = 'UT' ".$whereInvoice."
                                    GROUP BY a.INVOICE_TRANS_NOCHAR,a.INVOICE_TRANS_DESC_CHAR,a.MD_TENANT_PPH_INT,a.TGL_SCHEDULE_DATE,
                                            a.INVOICE_TRANS_DPP,a.INVOICE_TRANS_PPN,a.INVOICE_TRANS_PPH,a.INVOICE_TRANS_TOTAL
                                    HAVING ISNULL((a.INVOICE_TRANS_DPP + a.INVOICE_TRANS_PPN) -
		                                    SUM(ISNULL(CASE WHEN b.INVOICE_PAYMENT_STATUS_INT = 0 THEN 0 ELSE b.PAID_BILL_AMOUNT END,0)),0) > 0");
        }
        else if($DOC_TYPE == 'D') {
            $dataListInvoice = DB::select("SELECT a.INVOICE_TRANS_NOCHAR, a.PSM_TRANS_DEPOSIT_DESC AS INVOICE_TRANS_DESC_CHAR,
                ISNULL(SUM(a.PSM_TRANS_DEPOSIT_NUM), 0) - ISNULL(SUM(a.PSM_SECURE_REFUND_NUM), 0) AS INVOICE_TRANS_AMOUNT
                FROM PSM_SECURE_DEP as a
                WHERE a.INVOICE_STATUS_INT NOT IN (0)
                AND a.INVOICE_TRANS_NOCHAR NOT IN (
                    SELECT a.INVOICE_TRANS_NOCHAR
                    FROM CN_TRANS_DETAIL AS a INNER JOIN CN_TRANS AS b ON a.CN_TRANS_NOCHAR = b.CN_TRANS_NOCHAR
                    WHERE a.IS_DELETE = 0
                    AND b.CN_TRANS_STATUS_INT NOT IN (0)
                    AND a.CN_TRANS_DTL_TYPE = 'INVOICE'
                )
                AND a.PSM_TRANS_DEPOSIT_TYPE = '".$dataCN->INVOICE_TRANS_TYPE."' ".$whereInvoice."
                GROUP BY a.INVOICE_TRANS_NOCHAR, a.PSM_TRANS_DEPOSIT_DESC
                HAVING (ISNULL(SUM(a.PSM_TRANS_DEPOSIT_NUM), 0) - ISNULL(SUM(a.PSM_SECURE_REFUND_NUM), 0)) > 0");
        }
        else
        {
            $dataListInvoice = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.INVOICE_TRANS_DESC_CHAR,
                                                   ISNULL((a.INVOICE_TRANS_DPP + a.INVOICE_TRANS_PPN) -
		                                            SUM(ISNULL(CASE WHEN b.INVOICE_PAYMENT_STATUS_INT = 0 THEN 0 ELSE b.PAID_BILL_AMOUNT END,0)),0) as INVOICE_TRANS_AMOUNT
                                            from INVOICE_TRANS as a LEFT JOIN INVOICE_PAYMENT as b ON a.INVOICE_TRANS_NOCHAR = b.INVOICE_TRANS_NOCHAR
                                            WHERE a.INVOICE_STATUS_INT NOT IN (0,4)
                                            AND a.INVOICE_TRANS_NOCHAR NOT IN (
                                                Select a.INVOICE_TRANS_NOCHAR
                                                from CN_TRANS_DETAIL as a INNER JOIN CN_TRANS as b ON a.CN_TRANS_NOCHAR = b.CN_TRANS_NOCHAR
                                                where a.IS_DELETE = 0
                                                and b.CN_TRANS_STATUS_INT NOT IN (0)
                                                AND a.CN_TRANS_DTL_TYPE = 'INVOICE'
                                            )
                                            AND a.INVOICE_TRANS_TYPE = '".$dataCN->INVOICE_TRANS_TYPE."' ".$whereInvoice."
                                            GROUP BY a.INVOICE_TRANS_NOCHAR,a.INVOICE_TRANS_DESC_CHAR,a.MD_TENANT_PPH_INT,a.TGL_SCHEDULE_DATE,
                                                     a.INVOICE_TRANS_DPP,a.INVOICE_TRANS_PPN,a.INVOICE_TRANS_PPH,a.INVOICE_TRANS_TOTAL
                                            HAVING ISNULL((a.INVOICE_TRANS_DPP + a.INVOICE_TRANS_PPN) -
		                                            SUM(ISNULL(CASE WHEN b.INVOICE_PAYMENT_STATUS_INT = 0 THEN 0 ELSE b.PAID_BILL_AMOUNT END,0)),0) > 0");
        }

        return View::make('page.accountreceivable.creditnotes.editDataCreditNotes',
            ['billingType'=>$billingType,'dataLot'=>$dataLot,'tenant'=>$tenant,
            'dataCNDetail'=>$dataCNDetail,'tenantId'=>$tenantId,'roles'=>$roles,
            'dataBillingType'=>$dataBillingType,'dataCN'=>$dataCN,'countCNDetail'=>$countCNDetail,
            'utilType'=>$utilType,'lotNo'=>$lotNo,'lotId'=>$lotId,'noPSM'=>$noPSM,
            'tenantName'=>$tenantName,'sqm'=>$sqm,'billingCode'=>$billingCode,
            'billingDesc'=>$billingDesc,'dataListInvoice'=>$dataListInvoice]);
    }

    public function saveToLog($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Invoice';
        $module = 'Finance In Flow';
        $by = $userName;
        $table = 'CN_TRANS';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function insertUpdateItemCreditNotes(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            if ($request->INVOICE_TRANS_TYPE == 'UT')
            {
                if ($request->insert_id == '0') //update
                {
                    $dataCNDetail = DB::table('CN_TRANS_DETAIL')
                        ->where('CN_TRANS_DTL_ID','=',$request->CN_TRANS_DTL_ID)
                        ->update([
                            // 'BILLING_TYPE'=>$request->BILLING_TYPE,
                            // 'UTILS_TYPE_NAME'=>$request->UTILS_TYPE_NAME,
                            'CN_TRANS_DTL_TYPE'=>$request->CN_TRANS_DTL_TYPE,
                            'INVOICE_TRANS_NOCHAR'=>$request->INVOICE_TRANS_NOCHAR,
                            'INVOICE_AMOUNT'=>$request->INVOICE_AMOUNT,
                            'INVOICE_TRANS_AMOUNT'=>$request->INVOICE_TRANS_AMOUNT,
                            'CN_TRANS_DTL_AMOUNT'=>$request->CN_TRANS_DTL_AMOUNT,
                            'updated_at'=>$dateNow
                        ]);

                    if ($dataCNDetail)
                    {
                        $dataSumCN = DB::table('CN_TRANS_DETAIL')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->where('IS_DELETE','=',0)
                            ->SUM('CN_TRANS_DTL_AMOUNT');

                        DB::table('CN_TRANS')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->update([
                                'CN_TRANS_AMOUNT'=>$dataSumCN,
                                'updated_at'=>$dateNow
                            ]);

                        $action = "UPDATE DATA DETAIL CN";
                        $description = 'Update data detail CN : ' .$request->CN_TRANS_NOCHAR.'('.$request->CN_TRANS_DTL_ID.')' ;
                        $this->saveToLog($action, $description);
                        \DB::commit();
                        return response()->json(['Success' => 'Data Has Been Updated']);
                    }
                    else
                    {
                        \DB::rollback();
                        return response()->json(['Error' => 'Gagal Update Item']);
                    }
                }
                else
                {
                    $dataCNDetail = DB::table('CN_TRANS_DETAIL')
                        ->insert([
                            'CN_TRANS_NOCHAR'=>$request->CN_TRANS_NOCHAR,
                            'CN_TRANS_DTL_TYPE'=>$request->CN_TRANS_DTL_TYPE,
                            'INVOICE_TRANS_NOCHAR'=>$request->INVOICE_TRANS_NOCHAR,
                            // 'BILLING_TYPE'=>$request->BILLING_TYPE,
                            // 'UTILS_TYPE_NAME'=>$request->UTILS_TYPE_NAME,
                            'INVOICE_AMOUNT'=>$request->INVOICE_AMOUNT,
                            'INVOICE_TRANS_AMOUNT'=>$request->INVOICE_TRANS_AMOUNT,
                            'CN_TRANS_DTL_AMOUNT'=>$request->CN_TRANS_DTL_AMOUNT,
                            'IS_DELETE'=>0,
                            'PROJECT_NO_CHAR'=>$project_no,
                            'created_at'=>$dateNow,
                            'updated_at'=>$dateNow
                        ]);

                    if ($dataCNDetail)
                    {
                        $dataSumCN = DB::table('CN_TRANS_DETAIL')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->where('IS_DELETE','=',0)
                            ->SUM('CN_TRANS_DTL_AMOUNT');

                        DB::table('CN_TRANS')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->update([
                                'CN_TRANS_AMOUNT'=>$dataSumCN,
                                'updated_at'=>$dateNow
                            ]);

                        $action = "INSERT DATA DETAIL CN";
                        $description = 'insert data detail CN : ' .$request->CN_TRANS_NOCHAR ;
                        $this->saveToLog($action, $description);
                        \DB::commit();
                        return response()->json(['Success' => 'Data Has Been Updated']);
                    }
                    else
                    {
                        \DB::rollback();
                        return response()->json(['Error' => 'Gagal Update Item']);
                    }
                }
            }
            else
            {
                if ($request->insert_id == '0') //update
                {
                    $dataCNDetail = DB::table('CN_TRANS_DETAIL')
                        ->where('CN_TRANS_DTL_ID','=',$request->CN_TRANS_DTL_ID)
                        ->update([
                            'CN_TRANS_DTL_TYPE'=>$request->CN_TRANS_DTL_TYPE,
                            'INVOICE_TRANS_NOCHAR'=>$request->INVOICE_TRANS_NOCHAR,
                            'INVOICE_AMOUNT'=>$request->INVOICE_AMOUNT,
                            'INVOICE_TRANS_AMOUNT'=>$request->INVOICE_TRANS_AMOUNT,
                            'CN_TRANS_DTL_AMOUNT'=>$request->CN_TRANS_DTL_AMOUNT,
                            'updated_at'=>$dateNow
                        ]);

                    if ($dataCNDetail)
                    {
                        $dataSumCN = DB::table('CN_TRANS_DETAIL')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->where('IS_DELETE','=',0)
                            ->SUM('CN_TRANS_DTL_AMOUNT');

                        DB::table('CN_TRANS')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->update([
                                'CN_TRANS_AMOUNT'=>$dataSumCN,
                                'updated_at'=>$dateNow
                            ]);

                        $action = "UPDATE DATA DETAIL CN";
                        $description = 'Update data detail CN : ' .$request->CN_TRANS_NOCHAR.'('.$request->CN_TRANS_DTL_ID.')' ;
                        $this->saveToLog($action, $description);
                        \DB::commit();
                        return response()->json(['Success' => 'Data Has Been Updated']);
                    }
                    else
                    {
                        \DB::rollback();
                        return response()->json(['Error' => 'Gagal Update Item']);
                    }
                }
                else
                {
                    $dataCNDetail = DB::table('CN_TRANS_DETAIL')
                        ->insert([
                            'CN_TRANS_NOCHAR'=>$request->CN_TRANS_NOCHAR,
                            'CN_TRANS_DTL_TYPE'=>$request->CN_TRANS_DTL_TYPE,
                            'INVOICE_TRANS_NOCHAR'=>$request->INVOICE_TRANS_NOCHAR,
                            'INVOICE_AMOUNT'=>$request->INVOICE_AMOUNT,
                            'INVOICE_TRANS_AMOUNT'=>$request->INVOICE_TRANS_AMOUNT,
                            'CN_TRANS_DTL_AMOUNT'=>$request->CN_TRANS_DTL_AMOUNT,
                            'IS_DELETE'=>0,
                            'PROJECT_NO_CHAR'=>$project_no,
                            'created_at'=>$dateNow,
                            'updated_at'=>$dateNow
                        ]);

                    if ($dataCNDetail)
                    {
                        $dataSumCN = DB::table('CN_TRANS_DETAIL')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->where('IS_DELETE','=',0)
                            ->SUM('CN_TRANS_DTL_AMOUNT');

                        DB::table('CN_TRANS')
                            ->where('CN_TRANS_NOCHAR','=',$request->CN_TRANS_NOCHAR)
                            ->update([
                                'CN_TRANS_AMOUNT'=>$dataSumCN,
                                'updated_at'=>$dateNow
                            ]);

                        $action = "INSERT DATA DETAIL CN";
                        $description = 'insert data detail CN : ' .$request->CN_TRANS_NOCHAR ;
                        $this->saveToLog($action, $description);
                        \DB::commit();
                        return response()->json(['Success' => 'Data Has Been Updated']);
                    }
                    else
                    {
                        \DB::rollback();
                        return response()->json(['Error' => 'Gagal Update Item']);
                    }
                }
            }

        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Update Item']);
        }
    }

    public function getItemCreditNotes(Request $request){
        $cnItem = DB::table('CN_TRANS_DETAIL')
            ->where('CN_TRANS_DTL_ID','=',$request->CN_TRANS_DTL_ID)
            ->first();

        if ($cnItem->CN_TRANS_DTL_TYPE == 'INVOICE' && $cnItem->INVOICE_TRANS_NOCHAR <> '')
        {
            $dataInvoice = DB::table('INVOICE_TRANS')
                ->where('INVOICE_TRANS_NOCHAR','=',$cnItem->INVOICE_TRANS_NOCHAR)
                ->first();
            $invDesc = $dataInvoice->INVOICE_TRANS_NOCHAR;
        }
        else
        {
            $invDesc = '';
        }

        if($cnItem){
            return response()->json([
                'status' => 'success',
                'CN_TRANS_DTL_ID'=>$cnItem->CN_TRANS_DTL_ID,
                'CN_TRANS_NOCHAR'=>$cnItem->CN_TRANS_NOCHAR,
                'CN_TRANS_DTL_TYPE'=>$cnItem->CN_TRANS_DTL_TYPE,
                'INVOICE_TRANS_NOCHAR'=>$cnItem->INVOICE_TRANS_NOCHAR,
                'BILLING_TYPE'=>$cnItem->BILLING_TYPE,
                'UTILS_TYPE_NAME'=>$cnItem->UTILS_TYPE_NAME,
                'INVOICE_TRANS_DESC_CHAR'=>$invDesc,
                'INVOICE_AMOUNT'=>$cnItem->INVOICE_AMOUNT,
                'INVOICE_TRANS_AMOUNT'=>$cnItem->INVOICE_TRANS_AMOUNT,
                'CN_TRANS_DTL_AMOUNT'=>$cnItem->CN_TRANS_DTL_AMOUNT
            ]);
        }else{
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function deleteItemCreditNotes(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $CNDetail = DB::table('CN_TRANS_DETAIL')
            ->where('CN_TRANS_DTL_ID','=',$request->CN_TRANS_DTL_ID)
            ->first();

        $dataCNDetail = DB::table('CN_TRANS_DETAIL')
            ->where('CN_TRANS_DTL_ID','=',$request->CN_TRANS_DTL_ID)
            ->update([
                'IS_DELETE'=>1,
                'updated_at'=>$dateNow
            ]);

        if ($dataCNDetail)
        {
            $dataCNTrans = DB::table('CN_TRANS')
                ->where('CN_TRANS_NOCHAR','=',$CNDetail->CN_TRANS_NOCHAR)
                ->first();

            $dataSumCN = DB::table('CN_TRANS_DETAIL')
                ->where('CN_TRANS_NOCHAR','=',$dataCNTrans->CN_TRANS_NOCHAR)
                ->where('IS_DELETE','=',0)
                ->SUM('CN_TRANS_DTL_AMOUNT');

            DB::table('CN_TRANS')
                ->where('CN_TRANS_NOCHAR','=',$dataCNTrans->CN_TRANS_NOCHAR)
                ->update([
                    'CN_TRANS_AMOUNT'=>$dataSumCN,
                    'updated_at'=>$dateNow
                ]);

            $action = "DELETE DATA DETAIL CN";
            $description = 'Delete data detail CN : ' .$dataCNTrans->CN_TRANS_NOCHAR.'('.$request->CN_TRANS_DTL_ID.')' ;
            $this->saveToLog($action, $description);
            return response()->json(['Success' => 'Data Has Been Updated']);
        }
        else
        {
            return response()->json(['Error' => 'Gagal Delete Item']);
        }
    }

    public function cancelDatacreditNotes($CN_TRANS_ID_INT){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $dataCNTrans = DB::table('CN_TRANS')
            ->where('CN_TRANS_ID_INT','=',$CN_TRANS_ID_INT)
            ->first();


        DB::table('CN_TRANS')
            ->where('CN_TRANS_ID_INT','=',$CN_TRANS_ID_INT)
            ->update([
                'CN_TRANS_STATUS_INT'=>0,
                'CN_TRANS_APPR_CHAR'=>$userName,
                'CN_TRANS_APPR_DATE'=>$dateNow,
                'updated_at'=>$dateNow
            ]);

        $action = "CANCEL CREDIT NOTES DATA";
        $description = 'Cancel Credit Notes Data : '.$dataCNTrans->CN_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);

        return redirect()->route('creditnotes.listdatacreeditnotes')
            ->with('success',$description);
    }

    public function approveDataCreditNotes($CN_TRANS_ID_INT){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        \DB::beginTransaction();

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $generator = new utilGenerator;

        $dataCreditNotes = DB::table('CN_TRANS')
            ->where('CN_TRANS_ID_INT','=',$CN_TRANS_ID_INT)
            ->first();

        $cekDataSecDepo = DB::table('PSM_SECURE_DEP_TYPE')
            ->where('PSM_SECURE_DEP_TYPE_CODE', $dataCreditNotes->INVOICE_TRANS_TYPE)
            ->count();

        $docDate = Carbon::parse($dataCreditNotes->CN_TRANS_TRX_DATE);

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

        $dataCNDetail = DB::table('CN_TRANS_DETAIL')
            ->where('CN_TRANS_NOCHAR','=',$dataCreditNotes->CN_TRANS_NOCHAR)
            ->where('IS_DELETE','=',0)
            ->get();

        foreach ($dataCNDetail as $cndetail) {
            // Untuk Yang CN Security Deposit
            if($cekDataSecDepo > 0) {
                try {
                    \DB::beginTransaction();

                    $cekDataInvoice = DB::table('PSM_SECURE_DEP')
                        ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                        ->count();

                    $dataInvoice = DB::table('PSM_SECURE_DEP')
                        ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                        ->first();

                    $dataInvoicePayment = DB::table('PSM_SECURE_DEP')
                        ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                        ->SUM('PSM_SECURE_REFUND_NUM');

                    $dataPSM = DB::table('PSM_TRANS')
                        ->where('PSM_TRANS_NOCHAR','=',$dataInvoice->PSM_TRANS_NOCHAR)
                        ->first();

                    $dataTenant = DB::table('MD_TENANT')
                        ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
                        ->first();

                    $TenantId = $dataTenant->MD_TENANT_ID_INT;
                    $lotNo = $dataPSM->LOT_STOCK_NO;

                    $nilaiInvoice = $dataInvoice->PSM_TRANS_DEPOSIT_NUM - $dataInvoicePayment;

                    $nilaiPayment = $cndetail->CN_TRANS_DTL_AMOUNT;

                    // GENERATE JOURNAL & GL (START)
                    $Year = substr($dateNow->year, 2);
                    $Month = str_pad($dateNow->month, 2, "0", STR_PAD_LEFT);
                    $countTable = Counter::where('PROJECT_NO_CHAR','=',$project_no)->first();
                    $Counter = str_pad($countTable->bank_voucher_int, 4, "0", STR_PAD_LEFT);
                    $countTable->bank_voucher_int = $countTable->bank_voucher_int + 1;

                    try {
                        $countTable->save();
                    } catch (QueryException $ex) {
                        return redirect()->route('creditnotes.listdatacreeditnotes')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }

                    $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                    $tahunRK = $docDate->year;

                    $period_no = $tahunRK.''.$bulanRK;

                    $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNSD')->first();
                    $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                    $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                    $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                    $totalDebit = 0;
                    $totalKredit = 0;

                    $dataSecDepType = DB::table('PSM_SECURE_DEP_TYPE')->where('PSM_SECURE_DEP_TYPE_CODE', $dataCreditNotes->INVOICE_TRANS_TYPE)->first();
                    $datacoa = DB::table('ACC_MD_COA')->where('ACC_NO_CHAR', '=',$dataSecDepType->ACC_NO_CHAR)->first();
                    $inputGlTrans['ACC_JOURNAL_NOCHAR'] = $noBankVoucher;
                    $inputGlTrans['PROJECT_CODE'] = $dataProject['PROJECT_CODE'];
                    $inputGlTrans['ACC_SOURCODE_CHAR'] = $sourcode;
                    $inputGlTrans['PROJECT_NO_CHAR'] = $project_no;
                    $inputGlTrans['PSM_TRANS_NOCHAR'] = $dataInvoice->PSM_TRANS_NOCHAR;
                    $inputGlTrans['MD_TENANT_ID_INT'] = $TenantId;
                    $inputGlTrans['ACC_JOURNAL_DTTIME'] = $dateNow;
                    $inputGlTrans['ACC_JOURNAL_TRX_DATE'] = $docDate;
                    $inputGlTrans['ACC_NOP_CHAR'] = $datacoa->ACC_NOP_CHAR;
                    $inputGlTrans['ACC_NO_CHAR'] = $dataSecDepType->ACC_NO_CHAR;
                    $inputGlTrans['ACC_NAME_CHAR'] = $datacoa->ACC_NAME_CHAR;
                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes ".$dataInvoice->PSM_TRANS_DEPOSIT_DESC.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiPayment;
                    $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                    $inputGlTrans['ACC_GLTRANS_REFNO'] = '';
                    $totalDebit += $nilaiPayment;
                    GlTrans::create($inputGlTrans);

                    $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNSD')->get();

                    foreach($dataTrxtype as $trx)
                    {
                        if($trx->MD_TRX_MODE == 'Kredit') {
                            if($trx->ACC_NO_CHAR == '150003006') // Piutang Usaha Lain-lain
                            {
                                $nilaiAmount = $nilaiPayment * -1;
                                $totalKredit += $nilaiPayment;
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
                        $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes ".$dataInvoice->PSM_TRANS_DEPOSIT_DESC.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                        $inputGlTrans['ACC_AMOUNT_INT'] = $nilaiAmount;
                        $inputGlTrans['LOT_STOCK_NO'] = $lotNo;
                        $inputGlTrans['ACC_GLTRANS_REFNO'] = '';

                        try{
                            GlTrans::create($inputGlTrans);
                        } catch (Exception $ex) {
                            return redirect()->route('creditnotes.listdatacreeditnotes')->with('error','Failed update counter table, errmsg : '.$ex);
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
                    $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                    $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes ".$dataInvoice->PSM_TRANS_DEPOSIT_DESC.', '.$dataTenant->MD_TENANT_NAME_CHAR;
                    $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                    $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                    $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                    $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                    $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                    $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                    $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                        return redirect()->route('creditnotes.listdatacreeditnotes')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                    }
                    // GENERATE JOURNAL & GL (START)

                    $sumCN = 0;

                    $dataCNDetail = DB::select("Select a.INVOICE_TRANS_NOCHAR,a.CN_TRANS_DTL_AMOUNT
                                from CN_TRANS_DETAIL as a INNER JOIN CN_TRANS as b ON a.CN_TRANS_NOCHAR = b.CN_TRANS_NOCHAR
                                where a.INVOICE_TRANS_NOCHAR = '".$dataInvoice->INVOICE_TRANS_NOCHAR."'
                                and a.IS_DELETE = 0
                                and b.CN_TRANS_STATUS_INT = 2");

                    foreach ($dataCNDetail as $cn) {
                        $sumCN += $cn->CN_TRANS_DTL_AMOUNT;
                    }

                    if (($dataInvoicePayment + $nilaiPayment + $sumCN) >= $nilaiInvoice) {
                        DB::table('PSM_SECURE_DEP')
                            ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                            ->update([
                                'INVOICE_STATUS_INT'=>1, // Paid
                                'REFUND_STATUS_INT'=>1, // Paid
                                'updated_at'=>$dateNow
                            ]);
                    }
                    else {
                        DB::table('PSM_SECURE_DEP')
                            ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                            ->update([
                                'INVOICE_STATUS_INT'=>1, // Paid
                                'updated_at'=>$dateNow
                            ]);
                    }

                    \DB::commit();
                } catch (QueryException $ex) {
                    \DB::rollback();
                    return redirect()->route('creditnotes.listdatacreeditnotes')->with('error', 'Failed approve data, errmsg : ' . $ex);
                }
            }
            else {
                $cekDataInvoice = DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                    ->count();

                $dataInvoice = DB::table('INVOICE_TRANS')
                    ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                    ->first();

                $dataInvDetails = DB::table('INVOICE_TRANS_DETAIL')
                    ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                    ->get();

                $dataInvoicePayment = DB::table('INVOICE_PAYMENT')
                    ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                    ->where('INVOICE_PAYMENT_STATUS_INT','=',2)
                    ->SUM('PAID_BILL_AMOUNT');

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

                if ($dataInvoice->MD_TENANT_PPH_INT == 1 && $dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                {
                    $nilaiInvoice = $dataInvoice->INVOICE_TRANS_DPP - $dataInvoicePayment;
                }
                elseif ($dataInvoice->MD_TENANT_PPH_INT == 1 && $dataInvoice->TGL_SCHEDULE_DATE > '2022-03-31')
                {
                    $nilaiInvoice = (($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH) - $dataInvoicePayment;
                }
                else
                {
                    $nilaiInvoice = $dataInvoice->INVOICE_TRANS_TOTAL - $dataInvoicePayment;
                }

                $nilaiPayment = $cndetail->CN_TRANS_DTL_AMOUNT;

                if ($cekDataInvoice > 0)
                {
                    if($dataInvoice->PSM_TRANS_NOCHAR == '')
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
                            return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                        }

                        $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                        $tahunRK = $docDate->year;

                        $period_no = $tahunRK.''.$bulanRK;

                        $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNOT')->first();
                        $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                        $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                        $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                        $totalDebit = 0;
                        $totalKredit = 0;

                        if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                        {
                            $pendapatan = ($nilaiPayment / 1.1) ;
                            $ppn = $pendapatan * 0.1;
                        }
                        else
                        {
                            $pendapatan = ($nilaiPayment / $dataProject['DPPBM_NUM']);
                            $ppn = $pendapatan * $dataProject['PPNBM_NUM'];
                        }

                        if ($dataInvoice->INVOICE_TRANS_TYPE == 'UT' && $dataInvoicePayment->PAYMENT_STAMP == 1)
                        {
                            $dutyStamp = 10000;
                        }
                        else
                        {
                            $dutyStamp = 0;
                        }

                        if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                        {
                            $uangMukaPPH = $pendapatan * 0.1;
                        }
                        else
                        {
                            $uangMukaPPH = $pendapatan * 0.1;
                        }

                        $pph = $uangMukaPPH;

                        $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNOT')->get();

                        foreach($dataTrxtype as $trx)
                        {
                            if ($trx->MD_TRX_MODE == 'Debit')
                            {
                                if($trx->ACC_NO_CHAR == '912301999') //Pendapatan Lainnya
                                {
                                    $nilaiAmount = $pendapatan;
                                    $totalDebit += $pendapatan;
                                }
                                elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                {
                                    $nilaiAmount = $ppn;
                                    $totalDebit += $ppn;
                                }
                                elseif($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL4 (2)
                                {
                                    $nilaiAmount = $uangMukaPPH;
                                    $totalDebit += $uangMukaPPH;
                                }
                            }
                            elseif($trx->MD_TRX_MODE == 'Kredit')
                            {
                                if($trx->ACC_NO_CHAR == '980100002') //BEBAN PAJAK FINAL SEWA
                                {
                                    $nilaiAmount = $pph * -1;
                                    $totalKredit += $pph;
                                }
                                elseif($trx->ACC_NO_CHAR == '150003006') // Piutang Usaha Lain-lain
                                {
                                    $nilaiAmount = $nilaiPayment * -1;
                                    $totalKredit += $nilaiPayment;
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
                            $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.' Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                        $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                        $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.' Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                        $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                        $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                        $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                        $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                        $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                        $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                        $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                    else
                    {
                        //Create Journal
                        if($dataInvoice->INVOICE_TRANS_TYPE == 'RS')
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
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNRS')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;

                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $bagiHasil = ($bank/1.1);
                                $PPN = $bagiHasil * 0.1;
                                $uangMukaPPH = $bagiHasil * 0.1;
                            }
                            else
                            {
                                $bagiHasil = ($bank/$dataProject['DPPBM_NUM']);
                                $PPN = $bagiHasil * $dataProject['PPNBM_NUM'];
                                $uangMukaPPH = $bagiHasil * 0.1;
                            }

                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNRS')->get();

                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '912301024') //Bagi Hasil Lainnya
                                    {
                                        $nilaiAmount = $bagiHasil;
                                        $totalDebit += $bagiHasil;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                    {
                                        $nilaiAmount = $PPN;
                                        $totalDebit += $PPN;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL 4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '980100002') //BEBAN PAJAK FINAL SEWA
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '150003006') //Hutang 4 (2) Lain-Lain
                                    {
                                        $nilaiAmount = $bank * -1;
                                        $totalKredit += $bank;
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
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Bagi Hasil ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                        elseif($dataInvoice->INVOICE_TRANS_TYPE == 'DP' || $dataInvoice->INVOICE_TRANS_TYPE == 'RT')
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
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNRT')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $Unearn = ($bank / 1.1);
                                $PPN = $Unearn * 0.1;
                                $uangMukaPPH = $Unearn * 0.1;
                            }
                            else
                            {
                                $Unearn = ($bank / $dataProject['DPPBM_NUM']);
                                $PPN = $Unearn * $dataProject['PPNBM_NUM'];
                                $uangMukaPPH =  $Unearn * 0.1;
                            }

                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNRT')->get();

                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '650005001') //UNEARNED RUPIAH
                                    {
                                        $nilaiAmount = $Unearn;
                                        $totalDebit += $Unearn;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                    {
                                        $nilaiAmount = $PPN;
                                        $totalDebit += $PPN;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003001') //Piutang Sewa dan Service Charges
                                    {
                                        $nilaiAmount = $bank * -1;
                                        $totalKredit += $bank;
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
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                            $inputJournal['ACC_JOURNAL_REF_DESC']= " Credit Notes Sewa ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                        elseif($dataInvoice->INVOICE_TRANS_TYPE == 'SC')
                        {
                            if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
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
                                    return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                                }

                                $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                $tahunRK = $docDate->year;

                                $period_no = $tahunRK.''.$bulanRK;

                                $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNSCP')->first();
                                $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                $totalDebit = 0;
                                $totalKredit = 0;

                                $bank = $nilaiPayment;
                                if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                                {
                                    $serviceCharge = ($bank / 1.1);
                                    $PPN = $serviceCharge * 0.1;
                                    $uangMukaPPH = $serviceCharge * 0.1;
                                }
                                else
                                {
                                    $serviceCharge = ($bank / $dataProject['DPPBM_NUM']);
                                    $PPN = $serviceCharge * $dataProject['PPNBM_NUM'];
                                    $uangMukaPPH =  $serviceCharge * 0.1;
                                }

                                $pph = $uangMukaPPH;

                                $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNSCP')->get();

                                foreach($dataTrxtype as $trx)
                                {
                                    if ($trx->MD_TRX_MODE == 'Debit')
                                    {
                                        if($trx->ACC_NO_CHAR == '912200002') //SERVICE CHARGE (Rp)
                                        {
                                            $nilaiAmount = $serviceCharge;
                                            $totalDebit += $serviceCharge;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                        {
                                            $nilaiAmount = $PPN;
                                            $totalDebit += $PPN;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL 4 (2)
                                        {
                                            $nilaiAmount = $uangMukaPPH;
                                            $totalDebit += $uangMukaPPH;
                                        }
                                    }
                                    elseif($trx->MD_TRX_MODE == 'Kredit')
                                    {
                                        if($trx->ACC_NO_CHAR == '980100002') //BEBAN PAJAK FINAL SEWA
                                        {
                                            $nilaiAmount = $pph * -1;
                                            $totalKredit += $pph;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '150003001') //Piutang Sewa dan Service Charges
                                        {
                                            $nilaiAmount = $bank * -1;
                                            $totalKredit += $bank;
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
                                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                                $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                                $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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

                                $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNSCBU')->first();
                                $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                $totalDebit = 0;
                                $totalKredit = 0;

                                $bank = $nilaiPayment;
                                if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                                {
                                    $serviceCharge = ($bank / 1.1);
                                    $PPN = $serviceCharge * 0.1;
                                    $uangMukaPPH = $serviceCharge * 0.1;
                                }
                                else
                                {
                                    $serviceCharge = ($bank / $dataProject['DPPBM_NUM']);
                                    $PPN = $serviceCharge * $dataProject['PPNBM_NUM'];
                                    $uangMukaPPH =  $serviceCharge * 0.1;
                                }

                                $pph = $uangMukaPPH;

                                $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNSCBU')->get();

                                foreach($dataTrxtype as $trx)
                                {
                                    if ($trx->MD_TRX_MODE == 'Debit')
                                    {
                                        if($trx->ACC_NO_CHAR == '912200002') //SERVICE CHARGE (Rp)
                                        {
                                            $nilaiAmount = $serviceCharge;
                                            $totalDebit += $serviceCharge;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                        {
                                            $nilaiAmount = $PPN;
                                            $totalDebit += $PPN;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '170002007') //PIUTANG PPH FINAL (4 AY 2)
                                        {
                                            $nilaiAmount = $uangMukaPPH;
                                            $totalDebit += $uangMukaPPH;
                                        }
                                    }
                                    elseif($trx->MD_TRX_MODE == 'Kredit')
                                    {
                                        if($trx->ACC_NO_CHAR == '980100002') //BEBAN PAJAK FINAL SEWA
                                        {
                                            $nilaiAmount = $pph * -1;
                                            $totalKredit += $pph;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '150003001') //Piutang Sewa dan Service Charges
                                        {
                                            $nilaiAmount = $bank * -1;
                                            $totalKredit += $bank;
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
                                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                                $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                                $inputJournal['PROJECT_CODE']=$dataProject['PROJECT_CODE'];
                                $inputJournal['ACC_SOURCODE_CHAR']=$sourcode;
                                $inputJournal['PROJECT_NO_CHAR']=$project_no;
                                $inputJournal['ACC_JOURNAL_DTTIME']=$dateNow;
                                $inputJournal['ACC_JOURNAL_TRX_DATE']=$docDate;
                                $inputJournal['ACC_JOURNAL_REF_NOCHAR']='';
                                $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Service Charge ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                            //Create Journal
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

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNCL')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $casual = ($bank / 1.1);
                                $PPN = $casual * 0.1;
                                $uangMukaPPH = $casual * 0.1;
                            }
                            else
                            {
                                $casual = ($bank / $dataProject['DPPBM_NUM']);
                                $PPN = $casual * $dataProject['PPNBM_NUM'];
                                $uangMukaPPH =  $casual* 0.1;
                            }

                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNCL')->get();

                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '650001002') //Uang Muka Pameran
                                    {
                                        $nilaiAmount = $casual;
                                        $totalDebit += $casual;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                    {
                                        $nilaiAmount = $PPN;
                                        $totalDebit += $PPN;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003004') //Piutang Pameran
                                    {
                                        $nilaiAmount = $bank * -1;
                                        $totalKredit += $bank;
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
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Casual Leasing  ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Casual Leasing ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                        elseif($dataInvoice->INVOICE_TRANS_TYPE == 'OT')
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
                                return redirect()->route('btp.listdatabtprequest')->with('errorFailed', 'Failed insert data, errmsg : ' . $ex);
                            }

                            $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                            $tahunRK = $docDate->year;

                            $period_no = $tahunRK.''.$bulanRK;

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNOT')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                            {
                                $others = ($bank / 1.1);
                                $PPN = $others * 0.1;
                                $uangMukaPPH = $others * 0.1;
                            }
                            else
                            {
                                $others = ($bank / $dataProject['DPPBM_NUM']);
                                $PPN = $others * $dataProject['PPNBM_NUM'];
                                $uangMukaPPH = $others * 0.1;
                            }

                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNOT')->get();

                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '912301999') // Pendapatan Lainnya
                                    {
                                        $nilaiAmount = $others;
                                        $totalDebit += $others;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                    {
                                        $nilaiAmount = $PPN;
                                        $totalDebit += $PPN;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '170002009') // UANG MUKA PPH PASAL 4 (2)
                                    {
                                        $nilaiAmount = $uangMukaPPH;
                                        $totalDebit += $uangMukaPPH;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '980100002') //BEBAN PAJAK FINAL SEWA
                                    {
                                        $nilaiAmount = $pph * -1;
                                        $totalKredit += $pph;
                                    }
                                    elseif($trx->ACC_NO_CHAR == '150003006') //Piutang Usaha Lain-lain
                                    {
                                        $nilaiAmount = $bank * -1;
                                        $totalKredit += $bank;
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
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Biaya Lain-lain ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                        elseif($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
                        {
                            if($dataInvoice->MD_TENANT_PPH_INT == 0) //Perorangan (Potong Sendiri)
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
                                    return redirect()->route('btp.listdatabtprequest')->with('Failed', 'Failed insert data, errmsg : ' . $ex);
                                }

                                $bulanRK = str_pad($docDate->month, 2, "0", STR_PAD_LEFT);
                                $tahunRK = $docDate->year;

                                $period_no = $tahunRK.''.$bulanRK;

                                $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNUTP')->first();
                                $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                $totalDebit = 0;
                                $totalKredit = 0;

                                $BillAmount = $dataInvoice->INVOICE_TRANS_TOTAL;

                                if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                                {
                                    $ELECTRIC = 0; $PPN_ELECTRIC = 0; $uangMukaPPH_ELECTRIC = 0;
                                    $WATER = 0; $PPN_WATER = 0; $uangMukaPPH_WATER = 0;
                                    $GAS = 0; $PPN_GAS = 0; $uangMukaPPH_GAS = 0;
                                    foreach($dataInvDetails as $dataInvDetail) {
                                        if ($dataInvDetail->BILLING_TYPE == 2)
                                        {
                                            $ELECTRIC += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / 1.1;
                                            $PPN_ELECTRIC += $ELECTRIC * 0.1;
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }
                                        else
                                        {
                                            $ELECTRIC += 0 / 1.1;
                                            $PPN_ELECTRIC += $ELECTRIC * 0.1;
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 3)
                                        {
                                            $WATER += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / 1.1;
                                            $PPN_WATER += $WATER * 0.1;
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }
                                        else
                                        {
                                            $WATER += 0 / 1.1;
                                            $PPN_WATER += $WATER * 0.1;
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 5)
                                        {
                                            $GAS += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / 1.1;
                                            $PPN_GAS += $GAS * 0.1;
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                        else
                                        {
                                            $GAS += 0 / 1.1;
                                            $PPN_GAS += $GAS * 0.1;
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                    }
                                }
                                else
                                {
                                    $ELECTRIC = 0; $PPN_ELECTRIC = 0; $uangMukaPPH_ELECTRIC = 0;
                                    $WATER = 0; $PPN_WATER = 0; $uangMukaPPH_WATER = 0;
                                    $GAS = 0; $PPN_GAS = 0; $uangMukaPPH_GAS = 0;
                                    foreach($dataInvDetails as $dataInvDetail) {
                                        if ($dataInvDetail->BILLING_TYPE == 2)
                                        {
                                            $ELECTRIC += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / $dataProject['DPPBM_NUM'];
                                            $PPN_ELECTRIC += $ELECTRIC * $dataProject['PPNBM_NUM'];
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }
                                        else
                                        {
                                            $ELECTRIC += 0 / $dataProject['DPPBM_NUM'];
                                            $PPN_ELECTRIC += $ELECTRIC * $dataProject['PPNBM_NUM'];
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 3)
                                        {
                                            $WATER += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / $dataProject['DPPBM_NUM'];
                                            $PPN_WATER += $WATER * $dataProject['PPNBM_NUM'];
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }
                                        else
                                        {
                                            $WATER += 0 / $dataProject['DPPBM_NUM'];
                                            $PPN_WATER += $WATER * $dataProject['PPNBM_NUM'];
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 5)
                                        {
                                            $GAS += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / $dataProject['DPPBM_NUM'];
                                            $PPN_GAS += $GAS * $dataProject['PPNBM_NUM'];
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                        else
                                        {
                                            $GAS += 0 / $dataProject['DPPBM_NUM'];
                                            $PPN_GAS += $GAS * $dataProject['PPNBM_NUM'];
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                    }
                                }

                                $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNUTP')->get();
                                foreach($dataTrxtype as $trx)
                                {
                                    if ($trx->MD_TRX_MODE == 'Debit')
                                    {
                                        if($trx->ACC_NO_CHAR == '912301001') // PENDAPATAN LISTRIK & DAYA LSTR
                                        {
                                            $nilaiAmount = $ELECTRIC;
                                            $totalDebit += $ELECTRIC;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '912301002') //PENDAPATAN AIR
                                        {
                                            $nilaiAmount = $WATER;
                                            $totalDebit += $WATER;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '912301003') //PENDAPATAN GAS
                                        {
                                            $nilaiAmount = $GAS;
                                            $totalDebit += $GAS;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                        {
                                            $nilaiAmount = $PPN_ELECTRIC + $PPN_WATER + $PPN_GAS;
                                            $totalDebit += ($PPN_ELECTRIC + $PPN_WATER + $PPN_GAS);
                                        }
                                        elseif($trx->ACC_NO_CHAR == '170002009') //UANG MUKA PPH PASAL 4 (2)
                                        {
                                            $nilaiAmount = $uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS;
                                            $totalDebit += ($uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS);
                                        }
                                    }
                                    elseif($trx->MD_TRX_MODE == 'Kredit')
                                    {
                                        if($trx->ACC_NO_CHAR == '980100002') //BEBAN PAJAK FINAL SEWA
                                        {
                                            $nilaiAmount = ($uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS) * -1;
                                            $totalKredit += ($uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS);
                                        }
                                        elseif($trx->ACC_NO_CHAR == '150003002') //Piutang Listrik, Air dan Gas
                                        {
                                            $nilaiAmount = ($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) * -1;
                                            $totalKredit += ($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN);
                                        }
                                    }

                                    $datacoa = DB::table('ACC_MD_COA_NEW')
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
                                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Utility ".$cndetail->UTILS_TYPE_NAME.', '.$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                                $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                                $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Utility ".$cndetail->UTILS_TYPE_NAME.', '.$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                                $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                                $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                                $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                                $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                                $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                                $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                                $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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

                                $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNUTBU')->first();
                                $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                                $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                                $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                                $totalDebit = 0;
                                $totalKredit = 0;

                                if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                                {
                                    $ELECTRIC = 0; $PPN_ELECTRIC = 0; $uangMukaPPH_ELECTRIC = 0;
                                    $WATER = 0; $PPN_WATER = 0; $uangMukaPPH_WATER = 0;
                                    $GAS = 0; $PPN_GAS = 0; $uangMukaPPH_GAS = 0;
                                    foreach($dataInvDetails as $dataInvDetail) {
                                        if ($dataInvDetail->BILLING_TYPE == 2)
                                        {
                                            $ELECTRIC += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / 1.1;
                                            $PPN_ELECTRIC += $ELECTRIC * 0.1;
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }
                                        else
                                        {
                                            $ELECTRIC += 0 / 1.1;
                                            $PPN_ELECTRIC += $ELECTRIC * 0.1;
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 3)
                                        {
                                            $WATER += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / 1.1;
                                            $PPN_WATER += $WATER * 0.1;
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }
                                        else
                                        {
                                            $WATER += 0 / 1.1;
                                            $PPN_WATER += $WATER * 0.1;
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 5)
                                        {
                                            $GAS += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / 1.1;
                                            $PPN_GAS += $GAS * 0.1;
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                        else
                                        {
                                            $GAS += 0 / 1.1;
                                            $PPN_GAS += $GAS * 0.1;
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                    }
                                }
                                else
                                {
                                    $ELECTRIC = 0; $PPN_ELECTRIC = 0; $uangMukaPPH_ELECTRIC = 0;
                                    $WATER = 0; $PPN_WATER = 0; $uangMukaPPH_WATER = 0;
                                    $GAS = 0; $PPN_GAS = 0; $uangMukaPPH_GAS = 0;
                                    foreach($dataInvDetails as $dataInvDetail) {
                                        if ($dataInvDetail->BILLING_TYPE == 2)
                                        {
                                            $ELECTRIC += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / $dataProject['DPPBM_NUM'];
                                            $PPN_ELECTRIC += $ELECTRIC * ((float) $dataProject['PPNBM_NUM']);
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }
                                        else
                                        {
                                            $ELECTRIC += 0 / $dataProject['DPPBM_NUM'];
                                            $PPN_ELECTRIC += $ELECTRIC * ((float) $dataProject['PPNBM_NUM']);
                                            $uangMukaPPH_ELECTRIC += $ELECTRIC * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 3)
                                        {
                                            $WATER += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / $dataProject['DPPBM_NUM'];
                                            $PPN_WATER += $WATER * ((float) $dataProject['PPNBM_NUM']);
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }
                                        else
                                        {
                                            $WATER += 0 / $dataProject['DPPBM_NUM'];
                                            $PPN_WATER += $WATER * ((float) $dataProject['PPNBM_NUM']);
                                            $uangMukaPPH_WATER += $WATER * 0.1;
                                        }

                                        if ($dataInvDetail->BILLING_TYPE == 5)
                                        {
                                            $GAS += ($dataInvDetail->INVOICE_TRANS_DTL_DPP + $dataInvDetail->INVOICE_TRANS_DTL_PPN) / $dataProject['DPPBM_NUM'];
                                            $PPN_GAS += $GAS * ((float) $dataProject['PPNBM_NUM']);
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                        else
                                        {
                                            $GAS += 0 / $dataProject['DPPBM_NUM'];
                                            $PPN_GAS += $GAS * ((float) $dataProject['PPNBM_NUM']);
                                            $uangMukaPPH_GAS += $GAS * 0.1;
                                        }
                                    }
                                }

                                $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNUTBU')->get();
                                foreach($dataTrxtype as $trx)
                                {
                                    if ($trx->MD_TRX_MODE == 'Debit')
                                    {
                                        if($trx->ACC_NO_CHAR == '912301001') // PENDAPATAN LISTRIK & DAYA LSTR
                                        {
                                            $nilaiAmount = $ELECTRIC;
                                            $totalDebit += $ELECTRIC;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '912301002') //PENDAPATAN AIR
                                        {
                                            $nilaiAmount = $WATER;
                                            $totalDebit += $WATER;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '912301003') //PENDAPATAN GAS
                                        {
                                            $nilaiAmount = $GAS;
                                            $totalDebit += $GAS;
                                        }
                                        elseif($trx->ACC_NO_CHAR == '630002012') //PPN KELUARAN
                                        {
                                            $nilaiAmount = $PPN_ELECTRIC + $PPN_WATER + $PPN_GAS;
                                            $totalDebit += ($PPN_ELECTRIC + $PPN_WATER + $PPN_GAS);
                                        }
                                        elseif($trx->ACC_NO_CHAR == '170002007') //PIUTANG PPH FINAL (4 AY 2)
                                        {
                                            $nilaiAmount = $uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS;
                                            $totalDebit += ($uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS);
                                        }
                                    }
                                    elseif($trx->MD_TRX_MODE == 'Kredit')
                                    {
                                        if($trx->ACC_NO_CHAR == '980100002') //BEBAN PAJAK FINAL SEWA
                                        {
                                            $nilaiAmount = ($uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS) * -1;
                                            $totalKredit += ($uangMukaPPH_ELECTRIC + $uangMukaPPH_WATER + $uangMukaPPH_GAS);
                                        }
                                        elseif($trx->ACC_NO_CHAR == '150003002') //Piutang Listrik, Air dan Gas
                                        {
                                            $nilaiAmount = ($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) * -1;
                                            $totalKredit += ($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN);
                                        }
                                    }

                                    $datacoa = DB::table('ACC_MD_COA_NEW')
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
                                    $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Utility ".$cndetail->UTILS_TYPE_NAME.', '.$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                                $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                                $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Utility ".$cndetail->UTILS_TYPE_NAME.', '.$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                                $inputJournal['ACC_JOURNAL_FP_CHAR']=$dataInvoice->INVOICE_FP_NOCHAR;
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
                            //Create Journal
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

                            $trxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNRB')->first();
                            $sourcode = $trxtype->ACC_SOURCODE_CHAR;

                            $noBankVoucher = $dataProject['PROJECT_CODE'].$sourcode.'BV'.$Year.$Month.$Counter;

                            $nojournal = $generator->JournalGenerator($sourcode,  ERROR_ROUTE_KWT_INV);
                            $totalDebit = 0;
                            $totalKredit = 0;

                            $bank = $nilaiPayment;
                            if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31') {
                                $reim = ($bank / 1.1);
                                $PPN = $reim * 0.1;
                                $uangMukaPPH = $reim * 0.1;
                            }
                            else {
                                $reim = ($bank / $dataProject['DPPBM_NUM']);
                                $PPN = $reim * $dataProject['PPNBM_NUM'];
                                $uangMukaPPH = $reim * 0.1;
                            }

                            $pph = $uangMukaPPH;

                            $dataTrxtype = DB::table('MD_TRX_TYPE')->where('MD_TRX_NAME','=', 'CNRB')->get();

                            foreach($dataTrxtype as $trx)
                            {
                                if ($trx->MD_TRX_MODE == 'Debit')
                                {
                                    if($trx->ACC_NO_CHAR == '160012999') { // Lain-lain Piutang
                                        $nilaiAmount = $bank;
                                        $totalDebit += $bank;
                                    }
                                }
                                elseif($trx->MD_TRX_MODE == 'Kredit')
                                {
                                    if($trx->ACC_NO_CHAR == '150003006') // Piutang Usaha Lain-lain
                                    {
                                        $nilaiAmount = $bank * -1;
                                        $totalKredit += $bank;
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
                                $inputGlTrans['ACC_GLTRANS_DESC_CHAR'] = "Credit Notes Reimbursement ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$lotNo.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
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
                            $inputJournal['ACC_JOURNAL_REF_NOCHAR']=$cndetail->CN_TRANS_NOCHAR;
                            $inputJournal['ACC_JOURNAL_REF_DESC']= "Credit Notes Reimbursement ".$dataInvoice->INVOICE_TRANS_DESC_CHAR.', '.$dataTenant->MD_TENANT_NAME_CHAR.', LOT '.$dataPSM->LOT_STOCK_NO.', Faktur '.$dataInvoice->INVOICE_FP_NOCHAR;
                            $inputJournal['ACC_JOURNAL_CURR_CHAR']='IDR';
                            $inputJournal['ACC_JOURNAL_DEBIT_INT']=$totalDebit;
                            $inputJournal['ACC_JOURNAL_CREDIT_INT']=$totalKredit;
                            $inputJournal['ACC_JOURNAL_RATE_INT']=1;
                            $inputJournal['ACC_JOURNAL_FIN_APPROVED']=0;
                            $inputJournal['ACC_JOURNAL_APPROVED_INT']=0;
                            $inputJournal['ACC_JOURNAL_CREATEDBY_CHAR']=$dataCreditNotes->CN_TRANS_REQUEST_CHAR;
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
                        else
                        {
                            return redirect()->route('invoice.listdatainvoiceappr')
                                ->with('error','Invoice Type Not Found. Create Journal Fail....');
                        }
                    }

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

                    if (($dataInvoicePayment + $nilaiPayment + $sumCN) >= $nilaiInvoice)
                    {
                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                            ->update([
                                'INVOICE_STATUS_INT'=>4, //paid
                                'updated_at'=>$dateNow
                            ]);
                    }
                    else
                    {
                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$cndetail->INVOICE_TRANS_NOCHAR)
                            ->update([
                                'INVOICE_STATUS_INT'=>3, //paid
                                'updated_at'=>$dateNow
                            ]);
                    }
                }
            }
        }

        DB::table('CN_TRANS')
            ->where('CN_TRANS_ID_INT','=',$CN_TRANS_ID_INT)
            ->update([
                'CN_TRANS_STATUS_INT'=>2,
                'CN_TRANS_APPR_CHAR'=>$userName,
                'CN_TRANS_APPR_DATE'=>$dateNow
            ]);

        $action = "APPROVE CREDIT NOTES DATA";
        $description = 'Approve Credit Notes Data : '.$dataInvoice->INVOICE_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);

        \DB::commit();

        return redirect()->route('creditnotes.listdatacreeditnotes')
            ->with('success',$description);
    }
}
