<?php namespace App\Http\Controllers\Marketing;

use App\Http\Requests;
use App\Model\BillingScheduleModel;
use App\Model\Engineerings\UtilsMeter;
use App\Model\Engineerings\UtilsTenant;
use App\Model\ProjectModel;
use App\User;
use App\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Else_;
use View;
use Carbon\Carbon;
use Session;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Requests\MasterData\RequestValidation;
use Requests\Masterdata\RequestEditCustValidation;
use App\Http\Controllers\LogActivity\LogActivityController;
use Illuminate\Database\QueryException;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Util\utilArray;
use App\Http\Controllers\Util\utilConverter;
use App\Http\Controllers\Util\utilGenerator;
use App\Http\Controllers\Util\utilSession;
use Illuminate\Support\Str;

// define("DATABASE","MTLA_WATERGROUP");

class LeaseAgreement extends Controller {

    public function viewListData(){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if($isLogged == FALSE){
            //dd($isLogged);
            return redirect('/login');
        }

        $project_no = \Session::get('PROJECT_NO_CHAR');
        //dd($project_no);

        $PSMData = DB::select("select a.PSM_TRANS_ID_INT,a.PSM_TRANS_NOCHAR,a.LOI_TRANS_NOCHAR,a.SKS_TRANS_NOCHAR,b.MD_TENANT_NAME_CHAR,a.LOT_STOCK_NO,
                                        FORMAT(a.PSM_TRANS_START_DATE,'dd-MM-yyyy') as PSM_TRANS_START_DATE,a.SHOP_NAME_CHAR,
                                        FORMAT(a.PSM_TRANS_END_DATE,'dd-MM-yyyy') as PSM_TRANS_END_DATE,a.PSM_TRANS_PRICE,
                                        CASE
                                            WHEN a.PSM_TRANS_STATUS_INT = 1 THEN 'REQUEST'
                                            WHEN a.PSM_TRANS_STATUS_INT = 2 THEN 'APPROVE'
                                            WHEN a.PSM_TRANS_STATUS_INT = 3 THEN 'IN-ACTIVE'
                                        ELSE 'NONE'
                                        END as PSM_TRANS_STATUS_INT
                                from PSM_TRANS as a
                                INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                LEFT JOIN LOT_STOCK as c ON a.LOT_STOCK_ID_INT = c.LOT_STOCK_ID_INT
                                where a.PROJECT_NO_CHAR = '".$project_no."'
                                AND a.PSM_TRANS_STATUS_INT NOT IN (0)
                                ORDER BY a.PSM_TRANS_NOCHAR DESC");

        return View::make('page.leaseagreement.listDataLeaseAgreement',
            ['PSMData'=>$PSMData]);
    }

    public function viewListDataNew(){
        $project_no = session('current_project');

        $PSMData = DB::select("select a.PSM_TRANS_ID_INT,a.PSM_TRANS_NOCHAR,a.NO_BAST_NOCHAR,a.NO_KONTRAK_NOCHAR,a.SKS_TRANS_NOCHAR,b.MD_TENANT_NAME_CHAR,a.LOT_STOCK_NO,
                                        FORMAT(a.PSM_TRANS_START_DATE,'dd-MM-yyyy') as PSM_TRANS_START_DATE,a.SHOP_NAME_CHAR,
                                        FORMAT(a.PSM_TRANS_END_DATE,'dd-MM-yyyy') as PSM_TRANS_END_DATE,a.PSM_TRANS_PRICE,a.PSM_TRANS_NET_BEFORE_TAX,
                                        a.PSM_TRANS_PPN,
                                        CASE
                                            WHEN a.PSM_TRANS_STATUS_INT = 1 THEN 'REQUEST'
                                            WHEN a.PSM_TRANS_STATUS_INT = 2 THEN 'APPROVE'
                                            WHEN a.PSM_TRANS_STATUS_INT = 3 THEN 'INACTIVE'
                                        ELSE 'NONE'
                                        END as PSM_TRANS_STATUS_INT
                                from PSM_TRANS as a
                                INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                LEFT JOIN LOT_STOCK as c ON a.LOT_STOCK_ID_INT = c.LOT_STOCK_ID_INT
                                where a.PROJECT_NO_CHAR = '".$project_no."'
                                AND a.PSM_TRANS_STATUS_INT NOT IN (0)
                                ORDER BY a.PSM_TRANS_NOCHAR DESC");

        return View::make('page.leaseagreement.listDataLeaseAgreementNew',
            ['PSMData'=>$PSMData]);
    }

    public function viewAddDataLeaseAgreement(){
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataLot = DB::table('LOT_STOCK')->where('ON_RELEASE_STAT_INT','=',1)
            ->where('ON_RENT_STAT_INT','=',0)
            ->where('IS_DELETE','=',0)
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->get();

        $dataTenant = DB::table('MD_TENANT')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->get();

        $dataSalesType = DB::table("MD_SALES_TYPE")
            ->where('IS_ACTIVE','=',1)
            ->get();

        $dataCategory = DB::table('PSM_CATEGORY')
            ->where('IS_DELETE','=',0)
            ->get();

        return View::make('page.leaseagreement.addDataLeaseAgreement2',
            ['dataLot'=>$dataLot,'dataTenant'=>$dataTenant,'dataSalesType'=>$dataSalesType,
            'dataCategory'=>$dataCategory,'dataProject'=>$dataProject]);
    }

    public function saveToLog($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogAddendum($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS_ADDENDUM';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogRentSCLot($action,$description) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogRentSCLotAdd($action,$description) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS_ADDENDUM';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogRentSCAmt($action,$description) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS_PRICE';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogRentSCAmtAdd($action,$description) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS_ADDENDUM_PRICE';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogRentSCDiscAdd($action,$description) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS_ADDENDUM';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogRentSCDisc($action,$description) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_TRANS';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogSecureDep($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Lease Agreement';
        $module = 'Marketing';
        $by = $userName;
        $table = 'PSM_SECURE_DEP';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function generateLeaseAgreement($typedoc,$ID)
    {
        $isLogged = (bool)Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $project_no = Session::get('PROJECT_NO_CHAR');
        //$arrayPushVendor = new utilArray();
        $date = Carbon::parse(Carbon::now());
        //$monthYear = $date->year.' '.$date->month;
        $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

        $converter = new utilConverter();

        $Counter = str_pad($counter->psm_count, 5, "0", STR_PAD_LEFT);
        $Year = substr($date->year, 2);
        $Month = $date->month;
        $monthRomawi = $converter->getRomawi($Month);

        Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
            ->update(['psm_count' => $counter->psm_count + 1]);

        $noPSM = $Counter . '/' . $dataCompany['COMPANY_CODE'] . '/' . $dataProject['PROJECT_CODE'] . '/MKT.RF/' . $monthRomawi . '/' . $Year;

        if ($typedoc == 'LOI') {
            $cekLOI = DB::table('LOI_TRANS')
                ->where('LOI_TRANS_ID_INT', '=', $ID)
                ->count();

            if ($cekLOI > 0) {
                $dataLOI = DB::table('LOI_TRANS')
                    ->where('LOI_TRANS_ID_INT', '=', $ID)
                    ->first();

                $dataLot = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT', '=', $dataLOI->LOT_STOCK_ID_INT)
                    ->first();

                DB::table('PSM_TRANS')
                    ->insert([
                        'PSM_TRANS_NOCHAR' => $noPSM,
                        'LOI_TRANS_NOCHAR' => $dataLOI->LOI_TRANS_NOCHAR,
                        'SKS_TRANS_NOCHAR' => $dataLOI->SKS_TRANS_NOCHAR,
                        'LOT_STOCK_ID_INT' => $dataLOI->LOT_STOCK_ID_INT,
                        'LOT_STOCK_NO' => $dataLOI->LOT_STOCK_NO,
                        'DEBTOR_ACCT_CHAR' => $dataProject['PREFIX_DEBTOR'] . '#' . $dataLOI->LOT_STOCK_NO . '#' . $dataLot->COUNT_DEBTOR_NUM,
                        'SHOP_NAME_CHAR' => $dataLOI->SHOP_NAME_CHAR,
                        'PSM_CATEGORY_ID_INT' => $dataLot->PSM_CATEGORY_ID_INT,
                        'MD_TENANT_ID_INT' => $dataLOI->MD_TENANT_ID_INT,
                        //'PSM_TRANS_TYPE' => $dataLOI->LOI_TRANS_TYPE,
                        'MD_SALES_TYPE_ID_INT'=>$dataLOI->MD_SALES_TYPE_ID_INT,
                        'PSM_TRANS_BOOKING_DATE' => $dataLOI->LOI_TRANS_BOOKING_DATE,
                        'PSM_TRANS_START_DATE' => $dataLOI->LOI_TRANS_OCCU_START_DATE,
                        'PSM_TRANS_END_DATE' => $dataLOI->LOI_TRANS_OCCU_END_DATE,
                        'PSM_TRANS_FREQ_NUM' => $dataLOI->LOI_TRANS_OCCU_FREQ_NUM,
                        'PSM_TRANS_FREQ_DAY_NUM' => $dataLOI->LOI_TRANS_OCCU_FREQ_DAY_NUM,
                        'PSM_TRANS_TIME_PERIOD_SCHED' => $dataLOI->LOI_TRANS_TIME_PERIOD_SCHED,
                        'PSM_TRANS_RENT_NUM' => $dataLOI->LOI_TRANS_RENT_NUM,
                        'PSM_TRANS_SC_NUM' => $dataLOI->LOI_TRANS_SC_NUM,
                        'PSM_TRANS_DESCRIPTION' => $dataLOI->LOI_TRANS_DESCRIPTION,
                        'PSM_TRANS_NET_BEFORE_TAX' => $dataLOI->LOI_TRANS_NET_BEFORE_TAX,
                        'PSM_TRANS_PPN' => $dataLOI->LOI_TRANS_PPN,
                        'PSM_TRANS_PRICE' => $dataLOI->LOI_TRANS_PRICE,
                        'PSM_TRANS_UNEARN' => 0,
                        'PSM_TRANS_DP_PERSEN' => $dataLOI->LOI_TRANS_DP_PERSEN,
                        'PSM_TRANS_DP_NUM' => $dataLOI->LOI_TRANS_DP_NUM,
                        'PSM_TRANS_DP_PERIOD' => $dataLOI->LOI_TRANS_DP_PERIOD,
                        'PSM_TRANS_DEPOSIT_MONTH' => $dataLOI->LOI_TRANS_DEPOSIT_MONTH,
                        'PSM_TRANS_DEPOSIT_TYPE' => $dataLOI->LOI_TRANS_DEPOSIT_TYPE,
                        'PSM_TRANS_DEPOSIT_NUM' => $dataLOI->LOI_TRANS_DEPOSIT_NUM,
                        'PSM_TRANS_DEPOSIT_DATE' => $dataLOI->LOI_TRANS_DEPOSIT_DATE,
                        'PSM_INVEST_NUM' => $dataLOI->LOI_TRANS_INVEST_NUM,
                        'PSM_INVEST_RATE' => $dataLOI->LOI_TRANS_INVEST_RATE,
                        'PSM_REVENUE_LOW_NUM' => $dataLOI->LOI_TRANS_REVENUE_LOW_NUM,
                        'PSM_REVENUE_LOW_RATE' => $dataLOI->LOI_TRANS_REVENUE_LOW_RATE,
                        'PSM_REVENUE_HIGH_NUM' => $dataLOI->LOI_TRANS_REVENUE_HIGH_NUM,
                        'PSM_REVENUE_HIGH_RATE' => $dataLOI->LOI_TRANS_REVENUE_HIGH_RATE,
                        'PSM_TRANS_GRASS_TYPE' => $dataLOI->LOI_TRANS_GRASS_TYPE,
                        'PSM_TRANS_GRASS_PERIOD' => $dataLOI->LOI_TRANS_GRASS_PERIOD,
                        'PSM_TRANS_GRASS_DATE' => $dataLOI->LOI_TRANS_GRASS_DATE,
                        'PSM_TRANS_VA' => $dataLOI->LOI_TRANS_VA,
                        'PSM_TRANS_REQUEST_CHAR' => Session::get('name'),
                        'PSM_TRANS_REQUEST_DATE' => $date,
                        'PROJECT_NO_CHAR' => $project_no,
                        'PSM_TRANS_STATUS_INT'=>2,
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);

                DB::table('PSM_TRANS_HIST')
                    ->insert([
                        'PSM_TRANS_NOCHAR' => $noPSM,
                        'LOI_TRANS_NOCHAR' => $dataLOI->LOI_TRANS_NOCHAR,
                        'SKS_TRANS_NOCHAR' => $dataLOI->SKS_TRANS_NOCHAR,
                        'LOT_STOCK_ID_INT' => $dataLOI->LOT_STOCK_ID_INT,
                        'LOT_STOCK_NO' => $dataLOI->LOT_STOCK_NO,
                        'DEBTOR_ACCT_CHAR' => $dataProject['PREFIX_DEBTOR'] . '#' . $dataLOI->LOT_STOCK_NO . '#' . $dataLot->COUNT_DEBTOR_NUM,
                        'SHOP_NAME_CHAR' => $dataLOI->SHOP_NAME_CHAR,
                        'PSM_CATEGORY_ID_INT' => $dataLot->PSM_CATEGORY_ID_INT,
                        'MD_TENANT_ID_INT' => $dataLOI->MD_TENANT_ID_INT,
                        //'PSM_TRANS_TYPE' => $dataLOI->LOI_TRANS_TYPE,
                        'MD_SALES_TYPE_ID_INT'=>$dataLOI->MD_SALES_TYPE_ID_INT,
                        'PSM_TRANS_BOOKING_DATE' => $dataLOI->LOI_TRANS_BOOKING_DATE,
                        'PSM_TRANS_START_DATE' => $dataLOI->LOI_TRANS_OCCU_START_DATE,
                        'PSM_TRANS_END_DATE' => $dataLOI->LOI_TRANS_OCCU_END_DATE,
                        'PSM_TRANS_FREQ_NUM' => $dataLOI->LOI_TRANS_OCCU_FREQ_NUM,
                        'PSM_TRANS_FREQ_DAY_NUM' => $dataLOI->LOI_TRANS_OCCU_FREQ_DAY_NUM,
                        'PSM_TRANS_TIME_PERIOD_SCHED' => $dataLOI->LOI_TRANS_TIME_PERIOD_SCHED,
                        'PSM_TRANS_RENT_NUM' => $dataLOI->LOI_TRANS_RENT_NUM,
                        'PSM_TRANS_SC_NUM' => $dataLOI->LOI_TRANS_SC_NUM,
                        'PSM_TRANS_DESCRIPTION' => $dataLOI->LOI_TRANS_DESCRIPTION,
                        'PSM_TRANS_NET_BEFORE_TAX' => $dataLOI->LOI_TRANS_NET_BEFORE_TAX,
                        'PSM_TRANS_PPN' => $dataLOI->LOI_TRANS_PPN,
                        'PSM_TRANS_PRICE' => $dataLOI->LOI_TRANS_PRICE,
                        'PSM_TRANS_DP_PERSEN' => $dataLOI->LOI_TRANS_DP_PERSEN,
                        'PSM_TRANS_DP_NUM' => $dataLOI->LOI_TRANS_DP_NUM,
                        'PSM_TRANS_DP_PERIOD' => $dataLOI->LOI_TRANS_DP_PERIOD,
                        'PSM_TRANS_DEPOSIT_MONTH' => $dataLOI->LOI_TRANS_DEPOSIT_MONTH,
                        'PSM_TRANS_DEPOSIT_TYPE' => $dataLOI->LOI_TRANS_DEPOSIT_TYPE,
                        'PSM_TRANS_DEPOSIT_NUM' => $dataLOI->LOI_TRANS_DEPOSIT_NUM,
                        'PSM_TRANS_DEPOSIT_DATE' => $dataLOI->LOI_TRANS_DEPOSIT_DATE,
                        'PSM_INVEST_NUM' => $dataLOI->LOI_TRANS_INVEST_NUM,
                        'PSM_INVEST_RATE' => $dataLOI->LOI_TRANS_INVEST_RATE,
                        'PSM_REVENUE_LOW_NUM' => $dataLOI->LOI_TRANS_REVENUE_LOW_NUM,
                        'PSM_REVENUE_LOW_RATE' => $dataLOI->LOI_TRANS_REVENUE_LOW_RATE,
                        'PSM_REVENUE_HIGH_NUM' => $dataLOI->LOI_TRANS_REVENUE_HIGH_NUM,
                        'PSM_REVENUE_HIGH_RATE' => $dataLOI->LOI_TRANS_REVENUE_HIGH_RATE,
                        'PSM_TRANS_GRASS_TYPE' => $dataLOI->LOI_TRANS_GRASS_TYPE,
                        'PSM_TRANS_GRASS_PERIOD' => $dataLOI->LOI_TRANS_GRASS_PERIOD,
                        'PSM_TRANS_GRASS_DATE' => $dataLOI->LOI_TRANS_GRASS_DATE,
                        'PSM_TRANS_VA' => $dataLOI->LOI_TRANS_VA,
                        'PSM_TRANS_REQUEST_CHAR' => Session::get('name'),
                        'PSM_TRANS_REQUEST_DATE' => $date,
                        'PROJECT_NO_CHAR' => $project_no,
                        'ACTION' => 'GENERATE',
                        'USER_BY' => Session::get('name'),
                        'created_at' => $date,
                        'updated_at' => $date,
                        'PSM_TRANS_STATUS_INT' => 2,
                        'PSM_TRANS_GENERATE_BILLING' => 0,
                        'PSM_TRANS_BILLING_INT' => 0
                    ]);

                DB::table('LOI_TRANS')
                    ->where('LOI_TRANS_ID_INT', '=', $ID)
                    ->update([
                        'LOI_TRANS_STATUS_INT' => 3,
                        'updated_at' => $date
                    ]);

                DB::table('SKS_TRANS')
                    ->where('SKS_TRANS_NOCHAR', '=', $dataLOI->SKS_TRANS_NOCHAR)
                    ->update([
                        'SKS_STATUS_INT' => 4,
                        'updated_at' => $date
                    ]);

                DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT', '=', $dataLOI->LOT_STOCK_ID_INT)
                    ->update([
                        'COUNT_DEBTOR_NUM' => $dataLot->COUNT_DEBTOR_NUM + 1,
                        'updated_at' => $date
                    ]);
            } else {
                return redirect()->route('marketing.letterofintent.viewlistdata')
                    ->with('error', 'Letter Of Intent Document Not Found');
            }
        } elseif ($typedoc == 'SKS') {
            $cekSKS = DB::table('SKS_TRANS')
                ->where('SKS_TRANS_ID_INT', '=', $ID)
                ->count();

            if ($cekSKS > 0) {
                $dataSKS = DB::table('SKS_TRANS')
                    ->where('SKS_TRANS_ID_INT', '=', $ID)
                    ->first();

                $dataLot = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT', '=', $dataSKS->LOT_STOCK_ID_INT)
                    ->first();

                DB::table('PSM_TRANS')
                    ->insert([
                        'PSM_TRANS_NOCHAR' => $noPSM,
                        'LOI_TRANS_NOCHAR' => 'NONE',
                        'SKS_TRANS_NOCHAR' => $dataSKS->SKS_TRANS_NOCHAR,
                        'LOT_STOCK_ID_INT' => $dataSKS->LOT_STOCK_ID_INT,
                        'LOT_STOCK_NO' => $dataSKS->LOT_STOCK_NO,
                        'DEBTOR_ACCT_CHAR' => $dataProject['PREFIX_DEBTOR'] . '#' . $dataSKS->LOT_STOCK_NO . '#' . $dataLot->COUNT_DEBTOR_NUM,
                        'SHOP_NAME_CHAR' => $dataSKS->SHOP_NAME_CHAR,
                        'MD_TENANT_ID_INT' => $dataSKS->MD_TENANT_ID_INT,
                        //'PSM_TRANS_TYPE' => $dataSKS->SKS_TRANS_TYPE,
                        'MD_SALES_TYPE_ID_INT'=>$dataSKS->MD_SALES_TYPE_ID_INT,
                        'PSM_TRANS_BOOKING_DATE' => $dataSKS->SKS_TRANS_BOOKING_DATE,
                        'PSM_TRANS_START_DATE' => $dataSKS->SKS_TRANS_START_DATE,
                        'PSM_TRANS_END_DATE' => $dataSKS->SKS_TRANS_END_DATE,
                        'PSM_TRANS_FREQ_NUM' => $dataSKS->SKS_TRANS_FREQ_NUM,
                        'PSM_TRANS_FREQ_DAY_NUM' => $dataSKS->SKS_TRANS_FREQ_DAY_NUM,
                        'PSM_TRANS_TIME_PERIOD_SCHED' => $dataSKS->SKS_TRANS_TIME_PERIOD_SCHED,
                        'PSM_TRANS_RENT_NUM' => $dataSKS->SKS_TRANS_RENT_NUM,
                        'PSM_TRANS_SC_NUM' => $dataSKS->SKS_TRANS_SC_NUM,
                        'PSM_TRANS_DESCRIPTION' => $dataSKS->SKS_TRANS_DESCRIPTION,
                        'PSM_TRANS_NET_BEFORE_TAX' => $dataSKS->SKS_TRANS_NET_BEFORE_TAX,
                        'PSM_TRANS_PPN' => $dataSKS->SKS_TRANS_PPN,
                        'PSM_TRANS_PRICE' => $dataSKS->SKS_TRANS_PRICE,
                        'PSM_TRANS_UNEARN' => 0,
                        'PSM_TRANS_DP_PERSEN' => $dataSKS->SKS_TRANS_DP_PERSEN,
                        'PSM_TRANS_DP_NUM' => $dataSKS->SKS_TRANS_DP_NUM,
                        'PSM_TRANS_DP_PERIOD' => $dataSKS->SKS_TRANS_DP_PERIOD,
                        'PSM_TRANS_DEPOSIT_MONTH' => $dataSKS->SKS_DEPOSIT_MONTH,
                        'PSM_TRANS_DEPOSIT_TYPE' => $dataSKS->SKS_DEPOSIT_TYPE,
                        'PSM_TRANS_DEPOSIT_NUM' => $dataSKS->SKS_DEPOSIT_NUM,
                        'PSM_TRANS_DEPOSIT_DATE' => $dataSKS->SKS_DEPOSIT_DATE,
                        'PSM_INVEST_NUM' => $dataSKS->SKS_INVEST_NUM,
                        'PSM_INVEST_RATE' => $dataSKS->SKS_INVEST_RATE,
                        'PSM_REVENUE_LOW_NUM' => $dataSKS->SKS_REVENUE_LOW_NUM,
                        'PSM_REVENUE_LOW_RATE' => $dataSKS->SKS_REVENUE_LOW_RATE,
                        'PSM_REVENUE_HIGH_NUM' => $dataSKS->SKS_REVENUE_HIGH_NUM,
                        'PSM_REVENUE_HIGH_RATE' => $dataSKS->SKS_REVENUE_HIGH_RATE,
                        'PSM_TRANS_GRASS_TYPE' => $dataSKS->SKS_TRANS_GRASS_TYPE,
                        'PSM_TRANS_GRASS_PERIOD' => $dataSKS->SKS_TRANS_GRASS_PERIOD,
                        'PSM_TRANS_GRASS_DATE' => $dataSKS->SKS_TRANS_GRASS_DATE,
                        'PSM_TRANS_VA' => '',
                        'PSM_TRANS_REQUEST_CHAR' => Session::get('name'),
                        'PSM_TRANS_REQUEST_DATE' => $date,
                        'PROJECT_NO_CHAR' => $project_no,
                        'PSM_TRANS_STATUS_INT'=>2,
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);

                DB::table('PSM_TRANS_HIST')
                    ->insert([
                        'PSM_TRANS_NOCHAR' => $noPSM,
                        'LOI_TRANS_NOCHAR' => 'NONE',
                        'SKS_TRANS_NOCHAR' => $dataSKS->SKS_TRANS_NOCHAR,
                        'LOT_STOCK_ID_INT' => $dataSKS->LOT_STOCK_ID_INT,
                        'LOT_STOCK_NO' => $dataSKS->LOT_STOCK_NO,
                        'DEBTOR_ACCT_CHAR' => $dataProject['PREFIX_DEBTOR'] . '#' . $dataSKS->LOT_STOCK_NO . '#' . $dataLot->COUNT_DEBTOR_NUM,
                        'SHOP_NAME_CHAR' => $dataSKS->SHOP_NAME_CHAR,
                        'MD_TENANT_ID_INT' => $dataSKS->MD_TENANT_ID_INT,
                        //'PSM_TRANS_TYPE' => $dataSKS->SKS_TRANS_TYPE,
                        'MD_SALES_TYPE_ID_INT'=>$dataSKS->MD_SALES_TYPE_ID_INT,
                        'PSM_TRANS_BOOKING_DATE' => $dataSKS->SKS_TRANS_BOOKING_DATE,
                        'PSM_TRANS_START_DATE' => $dataSKS->SKS_TRANS_START_DATE,
                        'PSM_TRANS_END_DATE' => $dataSKS->SKS_TRANS_END_DATE,
                        'PSM_TRANS_FREQ_NUM' => $dataSKS->SKS_TRANS_FREQ_NUM,
                        'PSM_TRANS_FREQ_DAY_NUM' => $dataSKS->SKS_TRANS_FREQ_DAY_NUM,
                        'PSM_TRANS_TIME_PERIOD_SCHED' => $dataSKS->SKS_TRANS_TIME_PERIOD_SCHED,
                        'PSM_TRANS_RENT_NUM' => $dataSKS->SKS_TRANS_RENT_NUM,
                        'PSM_TRANS_SC_NUM' => $dataSKS->SKS_TRANS_SC_NUM,
                        'PSM_TRANS_DESCRIPTION' => $dataSKS->SKS_TRANS_DESCRIPTION,
                        'PSM_TRANS_NET_BEFORE_TAX' => $dataSKS->SKS_TRANS_NET_BEFORE_TAX,
                        'PSM_TRANS_PPN' => $dataSKS->SKS_TRANS_PPN,
                        'PSM_TRANS_PRICE' => $dataSKS->SKS_TRANS_PRICE,
                        'PSM_TRANS_DP_PERSEN' => $dataSKS->SKS_TRANS_DP_PERSEN,
                        'PSM_TRANS_DP_NUM' => $dataSKS->SKS_TRANS_DP_NUM,
                        'PSM_TRANS_DP_PERIOD' => $dataSKS->SKS_TRANS_DP_PERIOD,
                        'PSM_TRANS_DEPOSIT_MONTH' => $dataSKS->SKS_DEPOSIT_MONTH,
                        'PSM_TRANS_DEPOSIT_TYPE' => $dataSKS->SKS_DEPOSIT_TYPE,
                        'PSM_TRANS_DEPOSIT_NUM' => $dataSKS->SKS_DEPOSIT_NUM,
                        'PSM_TRANS_DEPOSIT_DATE' => $dataSKS->SKS_DEPOSIT_DATE,
                        'PSM_INVEST_NUM' => $dataSKS->SKS_INVEST_NUM,
                        'PSM_INVEST_RATE' => $dataSKS->SKS_INVEST_RATE,
                        'PSM_REVENUE_LOW_NUM' => $dataSKS->SKS_REVENUE_LOW_NUM,
                        'PSM_REVENUE_LOW_RATE' => $dataSKS->SKS_REVENUE_LOW_RATE,
                        'PSM_REVENUE_HIGH_NUM' => $dataSKS->SKS_REVENUE_HIGH_NUM,
                        'PSM_REVENUE_HIGH_RATE' => $dataSKS->SKS_REVENUE_HIGH_RATE,
                        'PSM_TRANS_STATUS_INT'=>2,
                        'PSM_TRANS_GENERATE_BILLING' => 0,
                        'PSM_TRANS_BILLING_INT' => 0,
                        'PSM_TRANS_GRASS_TYPE' => $dataSKS->SKS_TRANS_GRASS_TYPE,
                        'PSM_TRANS_GRASS_PERIOD' => $dataSKS->SKS_TRANS_GRASS_PERIOD,
                        'PSM_TRANS_GRASS_DATE' => $dataSKS->SKS_TRANS_GRASS_DATE,
                        'PSM_TRANS_VA' => '',
                        'PSM_TRANS_REQUEST_CHAR' => Session::get('name'),
                        'PSM_TRANS_REQUEST_DATE' => $date,
                        'PROJECT_NO_CHAR' => $project_no,
                        'ACTION' => 'GENERATE',
                        'USER_BY' => Session::get('name'),
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);

                DB::table('SKS_TRANS')
                    ->where('SKS_TRANS_ID_INT', '=', $ID)
                    ->update([
                        'SKS_STATUS_INT' => 4,
                        'updated_at' => $date
                    ]);

                DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT', '=', $dataSKS->LOT_STOCK_ID_INT)
                    ->update([
                        'COUNT_DEBTOR_NUM' => $dataLot->COUNT_DEBTOR_NUM + 1,
                        'updated_at' => $date
                    ]);
            } else {
                return redirect()->route('marketing.confirmationletter.viewlistdata')
                    ->with('error', 'Conf. Letter Document Not Found');
            }
        } else {
            return redirect()->route('marketing.leaseagreement.viewlistdata')
                ->with('error', 'Document Not Found');
        }

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR', '=', $noPSM)
            ->first();

        $action = "INSERT DATA";
        $description = 'Insert Lease Agreement: ' . $noPSM . ' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.vieweditdata', ['id' => $dataPSM->PSM_TRANS_ID_INT])
            ->with('success', $description);
    }
    public function cancelDataPSM($PSM_TRANS_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        if($isLogged == FALSE){
            return redirect('/login');
        }

        $date = Carbon::parse(Carbon::now());

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();


        if ($dataPSM->LOI_TRANS_NOCHAR == 'NONE')
        {
            DB::table('SKS_TRANS')
                ->where('SKS_TRANS_NOCHAR','=',$dataPSM->SKS_TRANS_NOCHAR)
                ->update([
                    'SKS_STATUS_INT'=>2,
                    'updated_at'=>$date
                ]);
        }
        else
        {
            DB::table('LOI_TRANS')
                ->where('LOI_TRANS_NOCHAR','=',$dataPSM->LOI_TRANS_NOCHAR)
                ->update([
                    'LOI_TRANS_STATUS_INT'=>2,
                    'updated_at'=>$date
                ]);

            DB::table('SKS_TRANS')
                ->where('SKS_TRANS_NOCHAR','=',$dataPSM->SKS_TRANS_NOCHAR)
                ->update([
                    'SKS_STATUS_INT'=>3,
                    'updated_at'=>$date
                ]);
        }

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->update([
                'PSM_TRANS_STATUS_INT'=>0,
                'updated_at'=>$date
            ]);

        $action = "CANCEL DATA";
        $description = 'Cancel Lease Agreement : '.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.viewlistdata')
            ->with('success','Cancel Lease Agreement '.$dataPSM->PSM_TRANS_NOCHAR.' Succesfully');
    }

    public function inactiveDataPSM($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $date = Carbon::parse(Carbon::now());

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->update([
                'IS_INACTIVE'=>1,
                'PSM_TRANS_STATUS_INT'=>3,
                'updated_at'=>$date
            ]);

        $dataLotPSM = DB::table('PSM_TRANS_LOT')
            ->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)
            ->get();

        foreach($dataLotPSM as $data) {
            DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)
                ->update([
                    'ON_RENT_STAT_INT'=>0
                ]);
        }

        $action = "INACTIVE DATA";
        $description = 'In-Active Lease Agreement : '.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.viewlistdata')
            ->with('success','In-Active Lease Agreement '.$dataPSM->PSM_TRANS_NOCHAR.' Succesfully');
    }

    public function viewFormEditDataPSM($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        $addressTaxData = DB::table('MD_TENANT_ADDRESS_TAX')
            ->where('MD_TENANT_TAX_ID_INT','=',$dataPSM->MD_TENANT_TAX_ID_INT)
            ->first();

        $categoryData = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_NO) == false) {
            $isMultipleLot = \Str::contains($dataPSM->LOT_STOCK_NO, ',');
            if($isMultipleLot) {
                $dataMultipleLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->get();
                $dataMultipleLotArr = array();
                foreach($dataMultipleLot as $data) {
                    array_push($dataMultipleLotArr, $data->LOT_STOCK_ID_INT);
                }

                $lotData = DB::table('LOT_STOCK')
                    ->whereIn('LOT_STOCK_ID_INT', $dataMultipleLotArr)
                    ->get();
            }
            else if(empty($dataPSM->LOT_STOCK_ID_INT)) {
                $dataSingleLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();
                $lotData = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$dataSingleLot->LOT_STOCK_ID_INT)
                    ->get();
            }
            else {
                $lotData = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                    ->first();
            }
        }
        else {
            $lotData = DB::table('LOT_STOCK')->where('ON_RELEASE_STAT_INT','=',1)
                ->where('ON_RENT_STAT_INT','=',0)
                ->where('IS_DELETE','=',0)
                ->where('PROJECT_NO_CHAR','=',$project_no)
                ->get();
        }

        $scheduleData = DB::select("Select a.PSM_SCHEDULE_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                           a.TRX_CODE,a.DESC_CHAR,a.BASE_AMOUNT_NUM,a.DISC_NUM,a.PPN_PRICE_NUM,a.BILL_AMOUNT,
                                           a.SCHEDULE_STATUS_INT
                                    FROM PSM_SCHEDULE as a
                                    WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                                    AND a.SCHEDULE_STATUS_INT NOT IN (0)
                                    AND a.BASE_AMOUNT_NUM > 0
                                    ORDER BY a.TGL_SCHEDULE_DATE");

        $scheduleDataCL = DB::select("Select a.PSM_SCHEDULE_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                    a.TRX_CODE,a.DESC_CHAR,a.BASE_AMOUNT_NUM,a.DISC_NUM,a.PPN_PRICE_NUM,a.BILL_AMOUNT,
                                    a.SCHEDULE_STATUS_INT
                             FROM PSM_SCHEDULE as a
                             WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                             AND a.SCHEDULE_STATUS_INT NOT IN (0)
                             AND a.BASE_AMOUNT_NUM > 0
                             AND a.TRX_CODE = 'CL'
                             ORDER BY a.TGL_SCHEDULE_DATE");

        $scheduleDataDP = DB::select("Select a.PSM_SCHEDULE_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                    a.TRX_CODE,a.DESC_CHAR,a.BASE_AMOUNT_NUM,a.DISC_NUM,a.PPN_PRICE_NUM,a.BILL_AMOUNT,
                                    a.SCHEDULE_STATUS_INT
                            FROM PSM_SCHEDULE as a
                            WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                            AND a.SCHEDULE_STATUS_INT NOT IN (0)
                            AND a.BASE_AMOUNT_NUM > 0
                            AND a.TRX_CODE = 'DP'
                            ORDER BY a.TGL_SCHEDULE_DATE");

        $scheduleDataRT = DB::select("Select a.PSM_SCHEDULE_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                    a.TRX_CODE,a.DESC_CHAR,a.BASE_AMOUNT_NUM,a.DISC_NUM,a.PPN_PRICE_NUM,a.BILL_AMOUNT,
                                    a.SCHEDULE_STATUS_INT
                            FROM PSM_SCHEDULE as a
                            WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                            AND a.SCHEDULE_STATUS_INT NOT IN (0)
                            AND a.BASE_AMOUNT_NUM > 0
                            AND a.TRX_CODE = 'RT'
                            ORDER BY a.TGL_SCHEDULE_DATE");

        $scheduleDataSC = DB::select("Select a.PSM_SCHEDULE_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                    a.TRX_CODE,a.DESC_CHAR,a.BASE_AMOUNT_NUM,a.DISC_NUM,a.PPN_PRICE_NUM,a.BILL_AMOUNT,
                                    a.SCHEDULE_STATUS_INT
                            FROM PSM_SCHEDULE as a
                            WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                            AND a.SCHEDULE_STATUS_INT NOT IN (0)
                            AND a.BASE_AMOUNT_NUM > 0
                            AND a.TRX_CODE = 'SC'
                            ORDER BY a.TGL_SCHEDULE_DATE");

        $dataRentSCAmt = DB::select("SELECT a.* FROM PSM_TRANS_PRICE AS a WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."' AND a.PROJECT_NO_CHAR = '".$project_no."'");

        $dataSecureDep = DB::select("Select a.PSM_SECURE_DEP_ID_INT,b.PSM_SECURE_DEP_TYPE_DESC,a.PSM_TRANS_DEPOSIT_DATE,a.PSM_TRANS_DEPOSIT_NUM,a.INVOICE_STATUS_INT
                                    from PSM_SECURE_DEP as a INNER JOIN PSM_SECURE_DEP_TYPE as b ON a.PSM_TRANS_DEPOSIT_TYPE = b.PSM_SECURE_DEP_TYPE_CODE
                                    where a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'");

        $secureType  = DB::table('PSM_SECURE_DEP_TYPE')
            ->where('IS_DELETE','=',0)
            ->get();

        $schedDPCount = DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->where('TRX_CODE','=','DP')
            ->count();

        $schedRentCount = DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->where('TRX_CODE','=','RT')
            ->count();

        $cekBayarSchedDP = DB::table('PSM_SCHEDULE')
            ->whereNotIn('SCHEDULE_STATUS_INT',[1])
            ->whereNotNull('INVOICE_NUMBER_CHAR')
            ->where('TRX_CODE','=','DP')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->count();

        $cekBayarSched= DB::table('PSM_SCHEDULE')
            ->whereNotIn('SCHEDULE_STATUS_INT',[1])
            ->whereNotNull('INVOICE_NUMBER_CHAR')
            ->whereNotIn('TRX_CODE',['DP'])
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->count();

        $dataInvType = DB::table('INVOICE_TRANS_TYPE')
            ->whereIn('INVOICE_TRANS_TYPE',['CL','DP','RT','SC'])
            ->get();

        $salesTypedata = DB::table("MD_SALES_TYPE")
            ->where('MD_SALES_TYPE_ID_INT','=',$dataPSM->MD_SALES_TYPE_ID_INT)
            ->first();

        $dataSalesType = DB::table("MD_SALES_TYPE")
            ->where('IS_ACTIVE','=',1)
            ->get();

        $dataReqRevenueSharing = DB::select("select *
                                    from PSM_REVENUE_SHARING as a
                                    where PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                                    AND (a.PSM_RS_START_DATE >= GETDATE() OR GETDATE() <= a.PSM_RS_END_DATE)
                                    AND a.PSM_RS_STATUS_INT NOT IN (0)");

        $dataListAddendum = DB::select("Select a.PSM_TRANS_ADD_ID_INT,a.PSM_TRANS_ADD_NOCHAR,c.MD_TENANT_NOCHAR,a.SHOP_NAME_CHAR,b.MD_ADD_TYPE,a.PSM_TRANS_ADD_STATUS_INT,a.PSM_ADD_DOC_TYPE
                                        from PSM_TRANS_ADDENDUM as a INNER JOIN MD_ADD_TYPE as b ON a.MD_ADD_TYPE_ID_INT = b.MD_ADD_TYPE_ID_INT
                                        INNER JOIN MD_TENANT as c ON a.MD_TENANT_ID_INT = c.MD_TENANT_ID_INT
                                        where a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                                        AND a.PSM_TRANS_ADD_STATUS_INT NOT IN (0)");

        $dataCategory = DB::table('PSM_CATEGORY')
            ->where('IS_DELETE','=',0)
            ->get();

        $dataAddressTax = DB::table('MD_TENANT_ADDRESS_TAX')
            ->where('MD_TENANT_NOCHAR','=',$tenantData->MD_TENANT_NOCHAR)
            ->get();

        return View::make('page.leaseagreement.viewDataLeaseAgreement2',
            ['dataPSM'=>$dataPSM,'tenantData'=>$tenantData,'lotData'=>$lotData,
                'scheduleData'=>$scheduleData,'categoryData'=>$categoryData,
                'cekBayarSchedDP'=>$cekBayarSchedDP,'dataListAddendum'=>$dataListAddendum,
                'schedDPCount'=>$schedDPCount,'cekBayarSched'=>$cekBayarSched,
                'schedRentCount'=>$schedRentCount,'dataInvType'=>$dataInvType,
                'salesTypedata'=>$salesTypedata,'dataSalesType'=>$dataSalesType,
                'dataReqRevenueSharing'=>$dataReqRevenueSharing,'dataCategory'=>$dataCategory,
                'dataSecureDep'=>$dataSecureDep,'secureType'=>$secureType,'scheduleDataCL'=>$scheduleDataCL,'scheduleDataDP'=>$scheduleDataDP,
                'scheduleDataRT'=>$scheduleDataRT,'scheduleDataSC'=>$scheduleDataSC,
                'addressTaxData'=>$addressTaxData,'dataAddressTax'=>$dataAddressTax,
                'dataRentSCAmt'=>$dataRentSCAmt
            ]);
    }

    public function saveEditDescSchedule(Request $request) {
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $date = Carbon::parse(Carbon::now());

        try {
            DB::beginTransaction();

            DB::table('PSM_SCHEDULE')->where('PSM_SCHEDULE_ID_INT', $request->SCHEDULE_ID_EDIT)->update([
                'DESC_CHAR' => $request->DESCRIPTION_EDIT
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed update data, errmsg : ' . $ex);
        }

        \Session::flash('success', 'Description has been updated...');
        return \Redirect::back();
    }

    public function saveDataPSM(Requests\Marketing\AddDataPSMRequest $requestPSM){
        $project_no = session('current_project');

        $inputDataPSM = $requestPSM->all();
        $date = Carbon::parse(Carbon::now());
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

            $converter = new utilConverter();
            
            $Counter = str_pad($counter->psm_count, 5, "0", STR_PAD_LEFT);
            $Year = substr($date->year, 2);
            $Month = $date->month;
            $monthRomawi = $converter->getRomawi($Month);

            Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                ->update(['psm_count' => $counter->psm_count + 1]);

            $noPSM = $Counter . '/' . $dataCompany['COMPANY_CODE'] . '/' . $dataProject['PROJECT_CODE'] . '/MKT.RF/' . $monthRomawi . '/' . $Year;

            $bookingDate = date_create($inputDataPSM['PSM_TRANS_BOOKING_DATE']);
            $startDate = date_create($inputDataPSM['PSM_TRANS_START_DATE']);
            $endDate = date_create($inputDataPSM['PSM_TRANS_END_DATE']);

            $freq_num = $endDate->diff($startDate);
            $difMonth = (int)($freq_num->days / 30);
            $freq_day_num = $difMonth * 30;
            $difDays = (int)($freq_num->days) - (int)($freq_day_num);

            $netbeforetax = str_replace('.','',empty($inputDataPSM['PSM_TRANS_NET_BEFORE_TAX']) ? 0 : $inputDataPSM['PSM_TRANS_NET_BEFORE_TAX']);
            $ppn = str_replace('.','',empty($inputDataPSM['PSM_TRANS_PPN']) ? 0 : $inputDataPSM['PSM_TRANS_PPN']);
            $total = str_replace('.','',empty($inputDataPSM['PSM_TRANS_PRICE']) ? 0 : $inputDataPSM['PSM_TRANS_PRICE']);

            $downPayment = (empty($inputDataPSM['PSM_TRANS_DP_PERSEN']) ? 0 : $inputDataPSM['PSM_TRANS_DP_PERSEN'] / 100) * $netbeforetax;

            DB::table('PSM_TRANS')
                ->insert([
                    'PSM_TRANS_NOCHAR'=>$noPSM,
                    'LOT_STOCK_NO'=>empty($inputDataPSM['LOT_STOCK_NO']) ? NULL : $inputDataPSM['LOT_STOCK_NO'],
                    'LOT_STOCK_ID_INT'=>empty($inputDataPSM['LOT_STOCK_ID_INT']) ? NULL : $inputDataPSM['LOT_STOCK_ID_INT'],
                    'MD_TENANT_ID_INT'=>$inputDataPSM['MD_TENANT_ID_INT'],
                    'MD_TENANT_TAX_ID_INT'=>0,
                    'SHOP_NAME_CHAR'=>$inputDataPSM['SHOP_NAME_CHAR'],
                    'MD_SALES_TYPE_ID_INT'=>$inputDataPSM['MD_SALES_TYPE_ID_INT'],
                    'PSM_CATEGORY_ID_INT'=>$inputDataPSM['PSM_CATEGORY_ID_INT'],
                    'PSM_TRANS_BOOKING_DATE'=>$bookingDate,
                    'PSM_TRANS_START_DATE'=>$startDate,
                    'PSM_TRANS_END_DATE'=>$endDate,
                    'PSM_TRANS_FREQ_NUM'=>$difMonth,
                    'PSM_TRANS_FREQ_DAY_NUM'=>$difDays,
                    'PSM_TRANS_TIME_PERIOD_SCHED'=>$inputDataPSM['PSM_TRANS_TIME_PERIOD_SCHED'],
                    'PSM_TRANS_RENT_NUM'=>empty($inputDataPSM['PSM_TRANS_RENT_NUM']) ? 0 : $inputDataPSM['PSM_TRANS_RENT_NUM'],
                    'PSM_TRANS_SC_NUM'=>empty($inputDataPSM['PSM_TRANS_SC_NUM']) ? 0 : $inputDataPSM['PSM_TRANS_SC_NUM'],
                    'PSM_TRANS_DESCRIPTION'=>$inputDataPSM['PSM_TRANS_DESCRIPTION'],
                    'PSM_TRANS_DISKON_NUM'=>empty($inputDataPSM['PSM_TRANS_DISKON_NUM']) ? 0 : $inputDataPSM['PSM_TRANS_DISKON_NUM'],
                    'PSM_TRANS_DISKON_PERSEN'=>empty($inputDataPSM['PSM_TRANS_DISKON_PERSEN']) ? 0 : $inputDataPSM['PSM_TRANS_DISKON_PERSEN'],
                    'PSM_TRANS_NET_BEFORE_TAX'=>$netbeforetax,
                    'PSM_TRANS_PPN'=>$ppn,
                    'PSM_TRANS_PRICE'=>$total,
                    'PSM_TRANS_DP_PERSEN'=>$inputDataPSM['PSM_TRANS_DP_PERSEN'],
                    'PSM_TRANS_DP_NUM'=>$downPayment,
                    'PSM_TRANS_DP_PERIOD'=>$inputDataPSM['PSM_TRANS_DP_PERIOD'],
                    'PSM_TRANS_DEPOSIT_MONTH'=>0,
                    'PSM_INVEST_NUM'=>$inputDataPSM['PSM_INVEST_NUM'],
                    'PSM_INVEST_RATE'=>$inputDataPSM['PSM_INVEST_RATE'],
                    'PSM_MIN_AMT'=>$inputDataPSM['PSM_MIN_AMT'],
                    'PSM_REVENUE_LOW_NUM'=>$inputDataPSM['PSM_REVENUE_LOW_NUM'],
                    'PSM_REVENUE_LOW_RATE'=>$inputDataPSM['PSM_REVENUE_LOW_RATE'],
                    'PSM_REVENUE_HIGH_NUM'=>$inputDataPSM['PSM_REVENUE_HIGH_NUM'],
                    'PSM_REVENUE_HIGH_RATE'=>$inputDataPSM['PSM_REVENUE_HIGH_RATE'],
                    'PSM_TRANS_GRASS_TYPE'=>$inputDataPSM['PSM_TRANS_GRASS_TYPE'],
                    'PSM_TRANS_GRASS_PERIOD'=>$inputDataPSM['PSM_TRANS_GRASS_PERIOD'],
                    'PSM_TRANS_REQUEST_CHAR'=>$userName,
                    'PSM_TRANS_REQUEST_DATE'=>$date,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'PSM_TRANS_GENERATE_BILLING'=>$inputDataPSM['PSM_TRANS_GENERATE_BILLING'],
                    'PSM_TRANS_VA'=>$inputDataPSM['PSM_TRANS_VA'],
                    'PSM_BANK_GARANSI'=>$inputDataPSM['PSM_BANK_GARANSI'],
                    'PSM_BANK_GARANSI_NOCHAR'=>$inputDataPSM['PSM_BANK_GARANSI_NOCHAR'],
                    'PSM_TRANS_STATUS_INT'=>2, //approve
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);

            if(empty($inputDataPSM['LOT_STOCK_ID_INT']) == false) {
                DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$inputDataPSM['LOT_STOCK_ID_INT'])
                    ->update([
                        'ON_RENT_STAT_INT'=>1,
                        'updated_at'=>$date
                    ]);
            }

            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$noPSM)
                ->first();

            DB::table('PSM_TRANS_HIST')
                ->insert([
                    'PSM_TRANS_NOCHAR'=>$dataPSM->PSM_TRANS_NOCHAR,
                    'LOI_TRANS_NOCHAR'=>$dataPSM->LOI_TRANS_NOCHAR,
                    'SKS_TRANS_NOCHAR'=>$dataPSM->SKS_TRANS_NOCHAR,
                    'LOT_STOCK_ID_INT'=>$dataPSM->LOT_STOCK_ID_INT,
                    'LOT_STOCK_NO'=>$dataPSM->LOT_STOCK_NO,
                    'DEBTOR_ACCT_CHAR'=>$dataPSM->DEBTOR_ACCT_CHAR,
                    'SHOP_NAME_CHAR'=>$dataPSM->SHOP_NAME_CHAR,
                    'MD_TENANT_ID_INT'=>$dataPSM->MD_TENANT_ID_INT,
                    'MD_TENANT_TAX_ID_INT'=>$dataPSM->MD_TENANT_TAX_ID_INT,
                    'PSM_TRANS_TYPE'=>$dataPSM->PSM_TRANS_TYPE,
                    'PSM_TRANS_BOOKING_DATE'=>$dataPSM->PSM_TRANS_BOOKING_DATE,
                    'PSM_TRANS_START_DATE'=>$dataPSM->PSM_TRANS_START_DATE,
                    'PSM_TRANS_END_DATE'=>$dataPSM->PSM_TRANS_END_DATE,
                    'PSM_TRANS_FREQ_NUM'=>$dataPSM->PSM_TRANS_FREQ_NUM,
                    'PSM_TRANS_FREQ_DAY_NUM'=>$dataPSM->PSM_TRANS_FREQ_DAY_NUM,
                    'PSM_TRANS_TIME_PERIOD_SCHED'=>$dataPSM->PSM_TRANS_TIME_PERIOD_SCHED,
                    'PSM_TRANS_RENT_NUM'=>$dataPSM->PSM_TRANS_RENT_NUM,
                    'PSM_TRANS_SC_NUM'=>$dataPSM->PSM_TRANS_SC_NUM,
                    'PSM_TRANS_DESCRIPTION'=>$dataPSM->PSM_TRANS_DESCRIPTION,
                    'PSM_TRANS_NET_BEFORE_TAX'=>$dataPSM->PSM_TRANS_NET_BEFORE_TAX,
                    'PSM_TRANS_PPN'=>$dataPSM->PSM_TRANS_PPN,
                    'PSM_TRANS_PRICE'=>$dataPSM->PSM_TRANS_PRICE,
                    'PSM_TRANS_UNEARN'=>$dataPSM->PSM_TRANS_UNEARN,
                    'PSM_TRANS_DP_PERSEN'=>$dataPSM->PSM_TRANS_DP_PERSEN,
                    'PSM_TRANS_DP_NUM'=>$dataPSM->PSM_TRANS_DP_NUM,
                    'PSM_TRANS_DP_PERIOD'=>$dataPSM->PSM_TRANS_DP_PERIOD,
                    'PSM_TRANS_DEPOSIT_MONTH'=>$dataPSM->PSM_TRANS_DEPOSIT_MONTH,
                    'PSM_TRANS_DEPOSIT_TYPE'=>$dataPSM->PSM_TRANS_DEPOSIT_TYPE,
                    'PSM_TRANS_DEPOSIT_NUM'=>$dataPSM->PSM_TRANS_DEPOSIT_NUM,
                    'PSM_TRANS_DEPOSIT_DATE'=>$dataPSM->PSM_TRANS_DEPOSIT_DATE,
                    'PSM_INVEST_NUM'=>$dataPSM->PSM_INVEST_NUM,
                    'PSM_INVEST_RATE'=>$dataPSM->PSM_INVEST_RATE,
                    'PSM_MIN_AMT'=>$dataPSM->PSM_MIN_AMT,
                    'PSM_REVENUE_LOW_NUM'=>$dataPSM->PSM_REVENUE_LOW_NUM,
                    'PSM_REVENUE_LOW_RATE'=>$dataPSM->PSM_REVENUE_LOW_RATE,
                    'PSM_REVENUE_HIGH_NUM'=>$dataPSM->PSM_REVENUE_HIGH_NUM,
                    'PSM_REVENUE_HIGH_RATE'=>$dataPSM->PSM_REVENUE_HIGH_RATE,
                    'PSM_TRANS_STATUS_INT'=>$dataPSM->PSM_TRANS_STATUS_INT,
                    'PSM_TRANS_GENERATE_BILLING'=>$dataPSM->PSM_TRANS_GENERATE_BILLING,
                    'PSM_TRANS_BILLING_INT'=>$dataPSM->PSM_TRANS_BILLING_INT,
                    'PSM_TRANS_DP_BILLING_INT'=>$dataPSM->PSM_TRANS_DP_BILLING_INT,
                    'PSM_TRANS_GRASS_TYPE'=>$dataPSM->PSM_TRANS_GRASS_TYPE,
                    'PSM_TRANS_GRASS_PERIOD'=>$dataPSM->PSM_TRANS_GRASS_PERIOD,
                    'PSM_TRANS_GRASS_DATE'=>$dataPSM->PSM_TRANS_GRASS_DATE,
                    'PSM_TRANS_VA'=>$dataPSM->PSM_TRANS_VA,
                    'PSM_BANK_GARANSI'=>$dataPSM->PSM_BANK_GARANSI,
                    'PSM_BANK_GARANSI_NOCHAR'=>$dataPSM->PSM_BANK_GARANSI_NOCHAR,
                    'PSM_TRANS_REQUEST_CHAR'=>$dataPSM->PSM_TRANS_REQUEST_CHAR,
                    'PSM_TRANS_REQUEST_DATE'=>$dataPSM->PSM_TRANS_REQUEST_DATE,
                    'PROJECT_NO_CHAR'=>$dataPSM->PROJECT_NO_CHAR,
                    'PSM_CATEGORY_ID_INT'=>$dataPSM->PSM_CATEGORY_ID_INT,
                    'ACTION'=>'INSERT DATA',
                    'USER_BY'=>$userName,
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);

            \Session::flash('message', 'Saving Insert Data Letter Of Intent '.$dataPSM->PSM_TRANS_NOCHAR);
            $action = "INSERT DATA";
            $description = 'Saving Insert Data Letter Of Intent '.$dataPSM->PSM_TRANS_NOCHAR;
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('marketing.leaseagreement.viewadddataleaseAgreement')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('marketing.leaseagreement.vieweditdata',[$dataPSM->PSM_TRANS_ID_INT])
            ->with('success',$description.' Successfully');
    }

    public function saveEditDataPSM(Requests\Marketing\AddDataPSMRequest $requestPSM){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $inputDataPSM = $requestPSM->all();
        $date = Carbon::parse(Carbon::now());

        try {
            \DB::beginTransaction();

            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$inputDataPSM['PSM_TRANS_NOCHAR'])
                ->first();

            DB::table('PSM_TRANS_HIST')
                ->insert([
                    'PSM_TRANS_NOCHAR'=>$dataPSM->PSM_TRANS_NOCHAR,
                    'LOI_TRANS_NOCHAR'=>$dataPSM->LOI_TRANS_NOCHAR,
                    'SKS_TRANS_NOCHAR'=>$dataPSM->SKS_TRANS_NOCHAR,
                    'LOT_STOCK_ID_INT'=>$dataPSM->LOT_STOCK_ID_INT,
                    'LOT_STOCK_NO'=>$dataPSM->LOT_STOCK_NO,
                    'DEBTOR_ACCT_CHAR'=>$dataPSM->DEBTOR_ACCT_CHAR,
                    'SHOP_NAME_CHAR'=>$dataPSM->SHOP_NAME_CHAR,
                    'MD_TENANT_ID_INT'=>$dataPSM->MD_TENANT_ID_INT,
                    'MD_TENANT_TAX_ID_INT'=>$dataPSM->MD_TENANT_TAX_ID_INT,
                    'PSM_TRANS_TYPE'=>$dataPSM->PSM_TRANS_TYPE,
                    'PSM_TRANS_BOOKING_DATE'=>$dataPSM->PSM_TRANS_BOOKING_DATE,
                    'PSM_TRANS_START_DATE'=>$dataPSM->PSM_TRANS_START_DATE,
                    'PSM_TRANS_END_DATE'=>$dataPSM->PSM_TRANS_END_DATE,
                    'PSM_TRANS_FREQ_NUM'=>$dataPSM->PSM_TRANS_FREQ_NUM,
                    'PSM_TRANS_FREQ_DAY_NUM'=>$dataPSM->PSM_TRANS_FREQ_DAY_NUM,
                    'PSM_TRANS_TIME_PERIOD_SCHED'=>$dataPSM->PSM_TRANS_TIME_PERIOD_SCHED,
                    'PSM_TRANS_RENT_NUM'=>$dataPSM->PSM_TRANS_RENT_NUM,
                    'PSM_TRANS_SC_NUM'=>$dataPSM->PSM_TRANS_SC_NUM,
                    'PSM_TRANS_DESCRIPTION'=>$dataPSM->PSM_TRANS_DESCRIPTION,
                    'PSM_TRANS_NET_BEFORE_TAX'=>$dataPSM->PSM_TRANS_NET_BEFORE_TAX,
                    'PSM_TRANS_PPN'=>$dataPSM->PSM_TRANS_PPN,
                    'PSM_TRANS_PRICE'=>$dataPSM->PSM_TRANS_PRICE,
                    'PSM_TRANS_UNEARN'=>$dataPSM->PSM_TRANS_UNEARN,
                    'PSM_TRANS_DP_PERSEN'=>$dataPSM->PSM_TRANS_DP_PERSEN,
                    'PSM_TRANS_DP_NUM'=>$dataPSM->PSM_TRANS_DP_NUM,
                    'PSM_TRANS_DP_PERIOD'=>$dataPSM->PSM_TRANS_DP_PERIOD,
                    'PSM_TRANS_DEPOSIT_MONTH'=>$dataPSM->PSM_TRANS_DEPOSIT_MONTH,
                    'PSM_TRANS_DEPOSIT_TYPE'=>$dataPSM->PSM_TRANS_DEPOSIT_TYPE,
                    'PSM_TRANS_DEPOSIT_NUM'=>$dataPSM->PSM_TRANS_DEPOSIT_NUM,
                    'PSM_TRANS_DEPOSIT_DATE'=>$dataPSM->PSM_TRANS_DEPOSIT_DATE,
                    'PSM_INVEST_NUM'=>$dataPSM->PSM_INVEST_NUM,
                    'PSM_INVEST_RATE'=>$dataPSM->PSM_INVEST_RATE,
                    'PSM_MIN_AMT'=>$dataPSM->PSM_MIN_AMT,
                    'PSM_REVENUE_LOW_NUM'=>$dataPSM->PSM_REVENUE_LOW_NUM,
                    'PSM_REVENUE_LOW_RATE'=>$dataPSM->PSM_REVENUE_LOW_RATE,
                    'PSM_REVENUE_HIGH_NUM'=>$dataPSM->PSM_REVENUE_HIGH_NUM,
                    'PSM_REVENUE_HIGH_RATE'=>$dataPSM->PSM_REVENUE_HIGH_RATE,
                    'PSM_TRANS_STATUS_INT'=>$dataPSM->PSM_TRANS_STATUS_INT,
                    'PSM_TRANS_GENERATE_BILLING'=>$dataPSM->PSM_TRANS_GENERATE_BILLING,
                    'PSM_TRANS_BILLING_INT'=>$dataPSM->PSM_TRANS_BILLING_INT,
                    'PSM_TRANS_DP_BILLING_INT'=>$dataPSM->PSM_TRANS_DP_BILLING_INT,
                    'PSM_TRANS_GRASS_TYPE'=>$dataPSM->PSM_TRANS_GRASS_TYPE,
                    'PSM_TRANS_GRASS_PERIOD'=>$dataPSM->PSM_TRANS_GRASS_PERIOD,
                    'PSM_TRANS_GRASS_DATE'=>$dataPSM->PSM_TRANS_GRASS_DATE,
                    'PSM_TRANS_VA'=>$dataPSM->PSM_TRANS_VA,
                    'PSM_BANK_GARANSI'=>$dataPSM->PSM_BANK_GARANSI,
                    'PSM_BANK_GARANSI_NOCHAR'=>$dataPSM->PSM_BANK_GARANSI_NOCHAR,
                    'PSM_TRANS_REQUEST_CHAR'=>$dataPSM->PSM_TRANS_REQUEST_CHAR,
                    'PSM_TRANS_REQUEST_DATE'=>$dataPSM->PSM_TRANS_REQUEST_DATE,
                    'PROJECT_NO_CHAR'=>$dataPSM->PROJECT_NO_CHAR,
                    'PSM_CATEGORY_ID_INT'=>$dataPSM->PSM_CATEGORY_ID_INT,
                    'ACTION'=>'UPDATE DATA',
                    'USER_BY'=>$userName,
                    'created_at'=>$date,
                    'updated_at'=>$date

                ]);

            $bookingDate = date_create($inputDataPSM['PSM_TRANS_BOOKING_DATE']);
            $startDate = date_create($inputDataPSM['PSM_TRANS_START_DATE']);
            $endDate = date_create($inputDataPSM['PSM_TRANS_END_DATE']);

            $freq_num = $endDate->diff($startDate);
            $difMonth = (int)($freq_num->days / 30);
            $freq_day_num = $difMonth * 30;
            $difDays = (int)($freq_num->days) - (int)($freq_day_num);

            $netbeforetax = str_replace('.','',$inputDataPSM['PSM_TRANS_NET_BEFORE_TAX']);
            $ppn = str_replace('.','',$inputDataPSM['PSM_TRANS_PPN']);
            $total = str_replace('.','',$inputDataPSM['PSM_TRANS_PRICE']);

            $downPayment = ($inputDataPSM['PSM_TRANS_DP_PERSEN']/100) * $netbeforetax;

            if ($inputDataPSM['MD_SALES_TYPE_ID_INT'] == 2 && $dataPSM->PSM_TRANS_STATUS_INT == 1)
            {
                $cekDataSchedule = DB::table('PSM_SCHEDULE')
                    ->where('PSM_TRANS_NOCHAR','=',$inputDataPSM['PSM_TRANS_NOCHAR'])
                    ->count();

                if ($cekDataSchedule > 0)
                {
                    return redirect()->route('marketing.leaseagreement.vieweditdata',[$inputDataPSM['PSM_TRANS_ID_INT']])
                        ->with('error','You Cannot Change Type, Please Remove Schedule Payment First.');
                }
            }

            DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$inputDataPSM['PSM_TRANS_NOCHAR'])
                ->update([
                    'MD_TENANT_TAX_ID_INT'=>$inputDataPSM['MD_TENANT_TAX_ID_INT'],
                    'SHOP_NAME_CHAR'=>$inputDataPSM['SHOP_NAME_CHAR'],
                    // 'PSM_TRANS_TYPE'=>$inputDataPSM['PSM_TRANS_TYPE'],
                    'MD_SALES_TYPE_ID_INT'=>$inputDataPSM['MD_SALES_TYPE_ID_INT'],
                    'PSM_CATEGORY_ID_INT'=>$inputDataPSM['PSM_CATEGORY_ID_INT'],
                    'PSM_TRANS_BOOKING_DATE'=>$bookingDate,
                    'PSM_TRANS_START_DATE'=>$startDate,
                    'PSM_TRANS_END_DATE'=>$endDate,
                    'PSM_TRANS_FREQ_NUM'=>$difMonth,
                    'PSM_TRANS_FREQ_DAY_NUM'=>$difDays,
                    'PSM_TRANS_TIME_PERIOD_SCHED'=>$inputDataPSM['PSM_TRANS_TIME_PERIOD_SCHED'],
                    'PSM_TRANS_RENT_NUM'=>empty($inputDataPSM['PSM_TRANS_RENT_NUM']) ? 0 : $inputDataPSM['PSM_TRANS_RENT_NUM'],
                    'PSM_TRANS_SC_NUM'=>empty($inputDataPSM['PSM_TRANS_SC_NUM']) ? 0 : $inputDataPSM['PSM_TRANS_SC_NUM'],
                    'PSM_TRANS_DESCRIPTION'=>$inputDataPSM['PSM_TRANS_DESCRIPTION'],
                    'PSM_TRANS_DISKON_NUM'=>empty($inputDataPSM['PSM_TRANS_DISKON_NUM']) ? $dataPSM->PSM_TRANS_DISKON_NUM : $inputDataPSM['PSM_TRANS_DISKON_NUM'],
                    'PSM_TRANS_DISKON_PERSEN'=>empty($inputDataPSM['PSM_TRANS_DISKON_PERSEN']) ? $dataPSM->PSM_TRANS_DISKON_PERSEN : $inputDataPSM['PSM_TRANS_DISKON_PERSEN'],
                    'PSM_TRANS_NET_BEFORE_TAX'=>$netbeforetax,
                    'PSM_TRANS_PPN'=>$ppn,
                    'PSM_TRANS_PRICE'=>$total,
                    'PSM_TRANS_DP_PERSEN'=>$inputDataPSM['PSM_TRANS_DP_PERSEN'],
                    'PSM_TRANS_DP_NUM'=>$downPayment,
                    'PSM_TRANS_DP_PERIOD'=>$inputDataPSM['PSM_TRANS_DP_PERIOD'],
                    'PSM_TRANS_DEPOSIT_MONTH'=>0,
                    // 'PSM_TRANS_DEPOSIT_TYPE'=>$inputDataPSM['PSM_TRANS_DEPOSIT_TYPE'],
                    // 'PSM_TRANS_DEPOSIT_NUM'=>$inputDataPSM['PSM_TRANS_DEPOSIT_NUM'],
                    // 'PSM_TRANS_DEPOSIT_DATE'=>$inputDataPSM['PSM_TRANS_DEPOSIT_DATE'],
                    'PSM_INVEST_NUM'=>$inputDataPSM['PSM_INVEST_NUM'],
                    'PSM_INVEST_RATE'=>$inputDataPSM['PSM_INVEST_RATE'],
                    'PSM_MIN_AMT'=>$inputDataPSM['PSM_MIN_AMT'],
                    'PSM_REVENUE_LOW_NUM'=>$inputDataPSM['PSM_REVENUE_LOW_NUM'],
                    'PSM_REVENUE_LOW_RATE'=>$inputDataPSM['PSM_REVENUE_LOW_RATE'],
                    'PSM_REVENUE_HIGH_NUM'=>$inputDataPSM['PSM_REVENUE_HIGH_NUM'],
                    'PSM_REVENUE_HIGH_RATE'=>$inputDataPSM['PSM_REVENUE_HIGH_RATE'],
                    'PSM_TRANS_GRASS_TYPE'=>$inputDataPSM['PSM_TRANS_GRASS_TYPE'],
                    'PSM_TRANS_GRASS_PERIOD'=>$inputDataPSM['PSM_TRANS_GRASS_PERIOD'],
                    // 'PSM_TRANS_GRASS_DATE'=>$inputDataPSM['PSM_TRANS_GRASS_DATE'],
                    'PSM_TRANS_REQUEST_CHAR'=>$userName,
                    'PSM_TRANS_REQUEST_DATE'=>$date,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'PSM_TRANS_GENERATE_BILLING'=>$inputDataPSM['PSM_TRANS_GENERATE_BILLING'],
                    'PSM_TRANS_VA'=>$inputDataPSM['PSM_TRANS_VA'],
                    'PSM_BANK_GARANSI'=>$inputDataPSM['PSM_BANK_GARANSI'],
                    'PSM_BANK_GARANSI_NOCHAR'=>$inputDataPSM['PSM_BANK_GARANSI_NOCHAR'],
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);

            \Session::flash('message', 'Saving Edit Data Letter Of Intent '.$inputDataPSM['PSM_TRANS_NOCHAR']);
            $action = "EDIT DATA";
            $description = 'Saving Edit Data Letter Of Intent '.$inputDataPSM['PSM_TRANS_NOCHAR'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('marketing.leaseagreement.vieweditdata', [$inputDataPSM['PSM_TRANS_ID_INT']])->with('error', 'Failed update data, errmsg : ' . $ex);
        }

        return redirect()->route('marketing.leaseagreement.vieweditdata',[$inputDataPSM['PSM_TRANS_ID_INT']])
            ->with('success','Saving Edit Data Lease Agreement '.$inputDataPSM['PSM_TRANS_NOCHAR'].' Successfully');
    }

    public function viewListDataAppr(){
        //$user = User::all();
        $isLogged = (bool) Session::get('isLogin');
        //''
        if($isLogged == FALSE){
            //dd($isLogged);
            return redirect('/login');
        }
        $project_no = \Session::get('PROJECT_NO_CHAR');
        //dd($project_no);

        $PSMData = DB::select("select a.PSM_TRANS_ID_INT,a.PSM_TRANS_NOCHAR,a.LOI_TRANS_NOCHAR,d.SKS_TRANS_NOCHAR,b.MD_TENANT_NAME_CHAR,c.LOT_STOCK_NO,
                                        FORMAT(a.PSM_TRANS_START_DATE,'dd-MM-yyyy') as PSM_TRANS_START_DATE,
                                        FORMAT(a.PSM_TRANS_END_DATE,'dd-MM-yyyy') as PSM_TRANS_END_DATE,a.PSM_TRANS_PRICE
                                from PSM_TRANS as a INNER JOIN SKS_TRANS as d ON a.SKS_TRANS_NOCHAR = d.SKS_TRANS_NOCHAR
                                INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                INNER JOIN LOT_STOCK as c ON a.LOT_STOCK_ID_INT = c.LOT_STOCK_ID_INT
                                where a.PROJECT_NO_CHAR = '".$project_no."'
                                AND a.PSM_TRANS_STATUS_INT IN (1)");

        return View::make('page.leaseagreement.listDataLeaseAgreementAppr',
            ['PSMData'=>$PSMData]);
    }

    public function approveDataPSM($PSM_TRANS_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $date = Carbon::parse(Carbon::now());

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
            ->first();

        $dataSUMDPP = DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->SUM('BASE_AMOUNT_NUM');

        $dataSUMPPN = DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->SUM('PPN_PRICE_NUM');

        $dataSUMTOTAL = DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->SUM('BILL_AMOUNT');

//        if (($dataPSM->PSM_TRANS_NET_BEFORE_TAX <> $dataSUMDPP) || ($dataPSM->PSM_TRANS_PPN <> $dataSUMPPN) ||
//            ($dataPSM->PSM_TRANS_PRICE <> $dataSUMTOTAL))
//        {
//            return redirect()->route('marketing.leaseagreement.viewlistdataappr')
//                ->with('error','Your DPP, PPN or Price Not Match with Schedule '.$dataPSM->PSM_TRANS_NOCHAR.', Approve Fail....');
//        }

        if ($dataPSM->PSM_TRANS_NET_BEFORE_TAX == 0)
        {
            return redirect()->route('marketing.leaseagreement.viewlistdataappr')
                ->with('error','Cannot Approve 0 Value! '.$dataPSM->PSM_TRANS_NOCHAR.', Approve Fail....');
        }

        $startDate = date_create($dataPSM->PSM_TRANS_START_DATE);
        $endDate = date_create($dataPSM->PSM_TRANS_END_DATE);

        $diffDate = date_diff($startDate,$endDate);
        $intDiffdate = (int)$diffDate->format("%a");

        $month = (int)($intDiffdate/30);
        if ($dataPSM->PSM_TRANS_GRASS_TYPE == '')
        {
            $grassPeriod = 0;
        }
        else
        {
            $grassPeriod = (int)$dataPSM->PSM_TRANS_GRASS_PERIOD;
        }


        if ($dataPSM->MD_SALES_TYPE_ID_INT == 3)
        {
            $dataIncome = round(($dataPSM->PSM_TRANS_NET_BEFORE_TAX));
        }
        else
        {
            $dataIncome = round(($dataPSM->PSM_TRANS_NET_BEFORE_TAX / ($month + $grassPeriod)));
        }


//            ($dataLot->LOT_STOCK_SQM * $dataPSM->PSM_TRANS_RENT_NUM) -
//            (($dataPSM->PSM_TRANS_DISKON_PERSEN/100) * ($dataLot->LOT_STOCK_SQM * $dataPSM->PSM_TRANS_RENT_NUM));

        //dd($dataIncome);

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->update([
                'PSM_TRANS_STATUS_INT'=>2,
                'PSM_TRANS_UNEARN'=>$dataIncome,
                'PSM_TRANS_APPR_DATE'=>$date,
                'PSM_TRANS_APPR_CHAR'=>Session::get('name'),
                'updated_at'=>$date
            ]);

        $dataPSM1 = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        DB::table('PSM_TRANS_HIST')
            ->insert([
                'PSM_TRANS_NOCHAR'=>$dataPSM1->PSM_TRANS_NOCHAR,
                'LOI_TRANS_NOCHAR'=>$dataPSM1->LOI_TRANS_NOCHAR,
                'SKS_TRANS_NOCHAR'=>$dataPSM1->SKS_TRANS_NOCHAR,
                'LOT_STOCK_ID_INT'=>$dataPSM1->LOT_STOCK_ID_INT,
                'LOT_STOCK_NO'=>$dataPSM1->LOT_STOCK_NO,
                'DEBTOR_ACCT_CHAR'=>$dataPSM1->DEBTOR_ACCT_CHAR,
                'SHOP_NAME_CHAR'=>$dataPSM1->SHOP_NAME_CHAR,
                'MD_TENANT_ID_INT'=>$dataPSM1->MD_TENANT_ID_INT,
                'PSM_TRANS_TYPE'=>$dataPSM1->PSM_TRANS_TYPE,
                'PSM_TRANS_BOOKING_DATE'=>$dataPSM1->PSM_TRANS_BOOKING_DATE,
                'PSM_TRANS_START_DATE'=>$dataPSM1->PSM_TRANS_START_DATE,
                'PSM_TRANS_END_DATE'=>$dataPSM1->PSM_TRANS_END_DATE,
                'PSM_TRANS_FREQ_NUM'=>$dataPSM1->PSM_TRANS_FREQ_NUM,
                'PSM_TRANS_FREQ_DAY_NUM'=>$dataPSM1->PSM_TRANS_FREQ_DAY_NUM,
                'PSM_TRANS_TIME_PERIOD_SCHED'=>$dataPSM1->PSM_TRANS_TIME_PERIOD_SCHED,
                'PSM_TRANS_RENT_NUM'=>$dataPSM1->PSM_TRANS_RENT_NUM,
                'PSM_TRANS_SC_NUM'=>$dataPSM1->PSM_TRANS_SC_NUM,
                'PSM_TRANS_DESCRIPTION'=>$dataPSM1->PSM_TRANS_DESCRIPTION,
                'PSM_TRANS_NET_BEFORE_TAX'=>$dataPSM1->PSM_TRANS_NET_BEFORE_TAX,
                'PSM_TRANS_PPN'=>$dataPSM1->PSM_TRANS_PPN,
                'PSM_TRANS_PRICE'=>$dataPSM1->PSM_TRANS_PRICE,
                'PSM_TRANS_UNEARN'=>$dataPSM1->PSM_TRANS_UNEARN,
                'PSM_TRANS_DP_PERSEN'=>$dataPSM1->PSM_TRANS_DP_PERSEN,
                'PSM_TRANS_DP_NUM'=>$dataPSM1->PSM_TRANS_DP_NUM,
                'PSM_TRANS_DP_PERIOD'=>$dataPSM1->PSM_TRANS_DP_PERIOD,
                'PSM_TRANS_DEPOSIT_MONTH'=>$dataPSM1->PSM_TRANS_DEPOSIT_MONTH,
                'PSM_TRANS_DEPOSIT_TYPE'=>$dataPSM1->PSM_TRANS_DEPOSIT_TYPE,
                'PSM_TRANS_DEPOSIT_NUM'=>$dataPSM1->PSM_TRANS_DEPOSIT_NUM,
                'PSM_TRANS_DEPOSIT_DATE'=>$dataPSM1->PSM_TRANS_DEPOSIT_DATE,
                'PSM_INVEST_NUM'=>$dataPSM1->PSM_INVEST_NUM,
                'PSM_INVEST_RATE'=>$dataPSM1->PSM_INVEST_RATE,
                'PSM_REVENUE_LOW_NUM'=>$dataPSM1->PSM_REVENUE_LOW_NUM,
                'PSM_REVENUE_LOW_RATE'=>$dataPSM1->PSM_REVENUE_LOW_RATE,
                'PSM_REVENUE_HIGH_NUM'=>$dataPSM1->PSM_REVENUE_HIGH_NUM,
                'PSM_REVENUE_HIGH_RATE'=>$dataPSM1->PSM_REVENUE_HIGH_RATE,
                'PSM_TRANS_STATUS_INT'=>$dataPSM1->PSM_TRANS_STATUS_INT,
                'PSM_TRANS_GENERATE_BILLING'=>$dataPSM1->PSM_TRANS_GENERATE_BILLING,
                'PSM_TRANS_BILLING_INT'=>$dataPSM1->PSM_TRANS_BILLING_INT,
                'PSM_TRANS_DP_BILLING_INT'=>$dataPSM1->PSM_TRANS_DP_BILLING_INT,
                'PSM_TRANS_GRASS_TYPE'=>$dataPSM1->PSM_TRANS_GRASS_TYPE,
                'PSM_TRANS_GRASS_PERIOD'=>$dataPSM1->PSM_TRANS_GRASS_PERIOD,
                'PSM_TRANS_GRASS_DATE'=>$dataPSM1->PSM_TRANS_GRASS_DATE,
                'PSM_TRANS_VA'=>$dataPSM1->PSM_TRANS_VA,
                'PSM_TRANS_REQUEST_CHAR'=>$dataPSM1->PSM_TRANS_REQUEST_CHAR,
                'PSM_TRANS_REQUEST_DATE'=>$dataPSM1->PSM_TRANS_REQUEST_DATE,
                'PROJECT_NO_CHAR'=>$dataPSM1->PROJECT_NO_CHAR,
                'ACTION'=>'APPROVE',
                'USER_BY'=>Session::get('name'),
                'created_at'=>$date,
                'updated_at'=>$date
            ]);

        $action = "APPROVE DATA";
        $description = 'Approve Lease Agreement : '.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.viewlistdataappr')
            ->with('success','Approve Lease Agreement '.$dataPSM->PSM_TRANS_NOCHAR.' Succesfully');
    }

    public function PrintSKS($SKS_TRANS_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

//        $project_no = Session::get('PROJECT_NO_CHAR');
        $converter = new utilConverter();
       $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
////        $email = \Session::get('dataSession.email');
////        $utilGetUserName = new \App\Http\Util\utilGetLoginProfile();
//        $UserNameSales = Session::get('username');
//
//        $dataSKS = DB::table('SKS_TRANS')
//            ->where('SKS_TRANS_ID_INT','=',$SKS_TRANS_ID_INT)
//            ->first();
//
//        $tahun = (int)$dataSKS->SKS_TRANS_FREQ_NUM/12;
//
//        $bulan = $dataSKS->SKS_TRANS_FREQ_NUM - ($tahun * 12);
//
//        if ($tahun > 0){
//            $sewaTahun = $tahun.' Tahun';
//        }else{
//            $sewaTahun = '';
//        }
//
//        if ($bulan > 0){
//            $sewaBulan = $bulan.' Bulan';
//        }else{
//            $sewaBulan = '';
//        }
//
//        if ($dataSKS->SKS_TRANS_FREQ_DAY_NUM > 0){
//            $sewaHari = $dataSKS->SKS_TRANS_FREQ_DAY_NUM.' Hari';
//        }else{
//            $sewaHari = '';
//        }
//
//        $masaSewa = $sewaTahun.' '.$sewaBulan.' '.$sewaHari;
//
//        if($dataSKS->SKS_DEPOSIT_TYPE == 'SC')
//        {
//            $deposit = $dataSKS->SKS_DEPOSIT_MONTH.' Bulan Service Charge';
//        }
//        else
//        {
//            $deposit = '';
//        }
//
//        $dataTenant = DB::table('MD_TENANT')
//            ->where('MD_TENANT_ID_INT','=',$dataSKS->MD_TENANT_ID_INT)
//            ->first();
//
//        $dataLot = DB::table('LOT_STOCK')
//            ->where('LOT_STOCK_ID_INT','=',$dataSKS->LOT_STOCK_ID_INT)
//            ->first();

        $datePrint = $converter->indonesian_date($dateNow, 'd F Y');
        dd($datePrint);
        //$dateDocument = $converter->indonesian_date($dataSKS->SKS_REQUEST_DATE, 'd F Y');

        //$dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

        //dd($dataBPU['BT_ESTIMASI_PTDP']);
        //$terbilangAmount = $converter->terbilang($dataBPU['BT_TRANS_AMOUNT_INT']);

        //$dateEstimasi = $converter->indonesian_date($dataBPU['BT_ESTIMASI_PTDP'], 'd F Y');

//        $dataLead = \App\Model\SuratPerintahKerja\SpkAssign::where('PROJECT_NO_CHAR','=',$project_no)
//            ->where('LEAD_PROJECT','=',1)->first();

//        return View::make('page.confirmationletter.pdfCetakConfirmationLetter',
//            ['dataSKS'=>$dataSKS,'datePrint'=>$datePrint,'project_no'=>$project_no,
//                'dataProject'=>$dataProject,'UserNameSales'=>$UserNameSales,'dataLot'=>$dataLot,
//                'dateDocument'=>$dateDocument,'dataTenant'=>$dataTenant,'masaSewa'=>$masaSewa,
//                'deposit'=>$deposit
//            ]);
    }

    public function viewUploadBillingSchedule(){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }
        $project_no = Session::get('PROJECT_NO_CHAR');
        //dd($project_no);

        $dataUpload = 0;
        $psm = array();
        $dataPSM = DB::select("select a.PSM_TRANS_ID_INT,a.PSM_TRANS_NOCHAR,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR
                                from PSM_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                where a.PSM_TRANS_STATUS_INT = 1
                                and a.PSM_TRANS_BILLING_INT = 0
                                and a.PSM_TRANS_GENERATE_BILLING = 0
                                and a.PROJECT_NO_CHAR = '".$project_no."'");


        //dd($dataBookingEntry);
        return View::make('page.leaseagreement.uploadBillingSchedule',
            ['dataPSM' => $dataPSM,'dataUpload'=>$dataUpload,'psm'=>$psm]);
    }

    public function uploadBillingSchedule(Requests\Marketing\UploadBillingSchedule $request){
        $project_no = session('current_project');

        $fileData = $request->all();

        $PSMData = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT', '=', $fileData['PSM_TRANS_ID_INT_UPLOAD'])
            ->first();

        Excel::load($fileData['sheet'], function ($reader) {
        //Excel\Facades\Excel::load(\Input::file('sheet'), function($reader) {
            $fileData = \Request::all();
            dd($fileData);
            $PSMData = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_ID_INT', '=', $fileData['PSM_TRANS_ID_INT_UPLOAD'])
                ->first();

            //$result = $reader->get();
            //dd($dataBookingEntry['TOTAL_PRICE']);
            if ($PSMData->PSM_TRANS_BILLING_INT <> 0)
            {
                return redirect()->route('marketing.leaseagreement.vieweditdata',[$PSMData->PSM_TRANS_ID_INT])
                    ->with('error', 'You Cant Upload Billing Schedule, Because Unit '.$PSMData->LOT_STOCK_NO.' has been uploaded before');
            }
            else
            {
//                $sumDataSchedule = DB::table('PSM_SCHEDULE')
//                    ->where('PSM_TRANS_NOCHAR','=',$PSMData->PSM_TRANS_NOCHAR)
//                    ->SUM('BILL_AMOUNT');

                $nilaiUploadSched = 0;
                $tableSchedule = new \Illuminate\Support\Collection();
                //dd($reader->toArray());
                $dataNumber = 1;
                foreach ($reader->toArray() as $row) {
                    if ($row['no'] != null) {
//                        if ($dataNumber == 1)
//                        {
//                            if (carbon::parse($row['tanggal_schedule']) <= carbon::parse($PSMData->PSM_TRANS_BOOKING_DATE))
//                            {
//                                return redirect()->route('marketing.leaseagreement.vieweditdata',[$PSMData->PSM_TRANS_ID_INT])
//                                    ->with('error', 'Upload Fail, Your First Schedule is Earlier Than The Booking Date');
//                            }
//                        }

                        if (trim($row['trx_code']) == 'DP')
                        {
                            if ($PSMData->PSM_TRANS_DP_NUM <> $row['base_amount'])
                            {
                                return redirect()->route('marketing.leaseagreement.vieweditdata',[$PSMData->PSM_TRANS_ID_INT])
                                    ->with('error', 'Upload Fail, Your DP Amount Not Match with PSM data...');
                            }
                            else
                            {
                                $listData['DESC_CHAR'] = $row['description'];
                                $listData['TGL_SCHEDULE_DATE'] = $row['tanggal_schedule'];
                                $listData['TRX_CODE'] = $row['trx_code'];
                                $listData['BASE_AMOUNT_NUM'] = $row['base_amount'];
                                $listData['PPN_PRICE_NUM'] = $row['tax_amount'];
                                $listData['BILL_AMOUNT'] = $row['bill_amount'];
                                //$listData['SCHEDULE_STATUS_INT'] = $row['status_bill'];
                                $tableSchedule->push($listData);

                                $nilaiUploadSched += $row['bill_amount'];
                            }
                        }
                        elseif (trim($row['trx_code']) == 'RT')
                        {
                            $listData['DESC_CHAR'] = $row['description'];
                            $listData['TGL_SCHEDULE_DATE'] = $row['tanggal_schedule'];
                            $listData['TRX_CODE'] = $row['trx_code'];
                            $listData['BASE_AMOUNT_NUM'] = $row['base_amount'];
                            $listData['PPN_PRICE_NUM'] = $row['tax_amount'];
                            $listData['BILL_AMOUNT'] = $row['bill_amount'];
                            //$listData['SCHEDULE_STATUS_INT'] = $row['status_bill'];
                            $tableSchedule->push($listData);

                            $nilaiUploadSched += $row['bill_amount'];
                        }
                        else
                        {
                            return redirect()->route('marketing.leaseagreement.vieweditdata',[$PSMData->PSM_TRANS_ID_INT])
                                ->with('error', 'Upload Fail, Transaction Type '.$row['trx_code'].' is Denied');
                        }
                    }

                    $dataNumber += 1;
                }
                dd($tableSchedule);
                $this->uploadScheduleDB($PSMData, $tableSchedule);
            }

            \Session::flash('message', 'Successfully Upload Schedule : ' . $PSMData->PSM_TRANS_NOCHAR);
            $action = "UPLOAD BILLING SCHEDULE";
            $description = 'Upload Billing Schedule : ' . $PSMData->PSM_TRANS_NOCHAR;
            $this->saveToLog($action, $description);
        });

        return redirect()->route('salesadministration.billingschedule.viewtempuploadbs',
            ['BOOKING_ENTRY_CODE_INT'=>$PSMData->LOT_STOCK_NO]);
    }

    public function uploadScheduleDB($PSMData, $tableSchedule){
        $isLogged = (bool) Session::get('dataSession.isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $project_no = Session::get('PROJECT_NO_CHAR');
        //$projectname_options = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

        //$dataTower = Model\TowerApt::where('ID_TOWER_INT','=', $dataBookingEntry['ID_TOWER_INT'])->first();
        $date = Carbon::parse(Carbon::now());

//        $dataPSM = DB::table('PSM_TRANS')
//            ->where('PSM_TRANS_ID_INT', '=', $PSMData->PSM_TRANS_ID_INT)
//            ->first();

        //$invoiceGenerator = new utilGenerator;

        foreach ($tableSchedule as $schedule){
            DB::table('PSM_SCHEDULE')
                ->insert([
                    'PSM_TRANS_NOCHAR'=>$PSMData->PSM_TRANS_NOCHAR,
                    'LOT_STOCK_NO'=>$PSMData->LOT_STOCK_NO,
                    'DEBTOR_ACCT_CHAR'=>$PSMData->DEBTOR_ACCT_CHAR,
                    'TRX_CODE'=>$schedule['TRX_CODE'],
                    'DESC_CHAR'=>$schedule['DESC_CHAR'],
                    'TGL_SCHEDULE_DATE'=>$schedule['TGL_SCHEDULE_DATE'],
                    'BASE_AMOUNT_NUM'=>$schedule['BASE_AMOUNT_NUM'],
                    'PPN_PRICE_NUM'=>$schedule['PPN_PRICE_NUM'],
                    'BILL_AMOUNT'=>$schedule['BILL_AMOUNT'],
                    'PROJECT_NO_CHAR'=>$project_no,
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);
        }
    }

    public function insertUpdateItemSchedule(Request $request){
        $project_no = session('current_project');
        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
            ->first();

        $date = Carbon::parse(Carbon::now());

        if ($request->insert_id == '1')
        {
            $startDate = date_create($request->TGL_SCHEDULE_ST_DATE);
            $endDate = date_create($request->TGL_SCHEDULE_EN_DATE);

            $bfPSM = date_create($dataPSM->PSM_TRANS_BOOKING_DATE);
            $stPSM = date_create($dataPSM->PSM_TRANS_START_DATE);
            $enPSM = date_create($dataPSM->PSM_TRANS_END_DATE);

            $cekSchedDate = DB::table('PSM_SCHEDULE_DATE')
                ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
                ->count();

            if ($request->TRX_CODE == 'DP')
            {
                if (($startDate < $bfPSM) && $cekSchedDate == 0)
                {
                    return response()->json(['Success' => 'Generate Fail, Check Your Schedule Start Date']);
                }
            }
            else
            {
                if (($startDate < $stPSM) && $cekSchedDate == 0)
                {
                    return response()->json(['Success' => 'Generate Fail, Check Your Schedule Start Date']);
                }

                if (($endDate > $enPSM))
                {
                    return response()->json(['Success' => 'Generate Fail, Check Your Schedule End Date']);
                }
            }

            $begin = date_create($request->TGL_SCHEDULE_ST_DATE);
            $end = date_create($request->TGL_SCHEDULE_EN_DATE);

            $maxRowNum = DB::table('PSM_SCHEDULE')
                ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
                ->where('TRX_CODE','=',$request->TRX_CODE)
                ->max('ROW_NUM');

            if ($maxRowNum == 0)
            {
                $rowNum = 1;
            }
            else
            {
                $rowNum = $maxRowNum;
            }

            for($i = $begin; $i <= $end; $i->modify('+1 month'))
            {
                $baseAmount = $request->BASE_AMOUNT_NUM;
                if ($i->format("Y-m-d") <= '2022-03-31')
                {
                    $ppn = round(($request->BASE_AMOUNT_NUM * 0.1));
                }
                else
                {
                    $ppn = round(($request->BASE_AMOUNT_NUM * $dataProject['PPNBM_NUM']));
                }

                $total = $baseAmount + $ppn;

                DB::table('PSM_SCHEDULE')
                    ->insert([
                        'PSM_TRANS_NOCHAR'=>$dataPSM->PSM_TRANS_NOCHAR,
                        'LOT_STOCK_NO'=>$dataPSM->LOT_STOCK_NO,
                        'DEBTOR_ACCT_CHAR'=>$dataPSM->DEBTOR_ACCT_CHAR,
                        'TRX_CODE'=>$request->TRX_CODE,
                        'ROW_NUM'=>$rowNum,
                        'DESC_CHAR'=>$request->DESC_CHAR.' '.$rowNum,
                        'TGL_SCHEDULE_DATE'=>$i->format("Y-m-d"),
                        'BASE_AMOUNT_NUM'=>$baseAmount,
                        'PPN_PRICE_NUM'=>$ppn,
                        'BILL_AMOUNT'=>$total,
                        'PROJECT_NO_CHAR'=>$project_no,
                        'created_at'=>$date,
                        'updated_at'=>$date
                    ]);

                $rowNum += 1;
            }

            if ($request->TRX_CODE == 'DP')
            {
                DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
                    ->update([
                        'PSM_TRANS_DP_BILLING_INT'=>1,
                        'updated_at'=>$date
                    ]);
            }
            elseif ($request->TRX_CODE == 'RT')
            {
                DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
                    ->update([
                        'PSM_TRANS_BILLING_INT'=>1,
                        'updated_at'=>$date
                    ]);
            }

            $action = "INSERT DATA SCHEDULE";
            $description = 'Insert data schedule: '.$dataPSM->PSM_TRANS_NOCHAR;
            $this->saveToLog($action, $description);
            return response()->json(['Success' => 'Berhasil Insert Item']);
        }
    }

    public function deleteItemSchedule(Request $request){
        $project_no = session('current_project');
        $date = Carbon::parse(Carbon::now());
        $dataSchedule = DB::table('PSM_SCHEDULE')
            ->where('PSM_SCHEDULE_ID_INT','=',$request->PSM_SCHEDULE_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataSchedule->PSM_TRANS_NOCHAR)
            ->first();

        $deleteSchedule = DB::table('PSM_SCHEDULE')
            ->where('PSM_SCHEDULE_ID_INT','=',$request->PSM_SCHEDULE_ID_INT)
            ->delete();

        if ($deleteSchedule)
        {
            $countDataSchedule = DB::table('PSM_SCHEDULE')
                ->where('PSM_TRANS_NOCHAR','=',$dataSchedule->PSM_TRANS_NOCHAR)
                ->count();

            if($countDataSchedule <= 0)
            {
                DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$dataSchedule->PSM_TRANS_NOCHAR)
                    ->update([
                        'PSM_TRANS_BILLING_INT'=>0,
                        'updated_at'=>$date
                    ]);
            }

            $action = "DELETE DATA SCHEDULE";
            $description = 'Delete data schedule: '.$dataPSM->PSM_TRANS_NOCHAR;
            $this->saveToLog($action, $description);
            return response()->json(['Success' => 'Berhasil Delete Item']);
        }
        else
        {
            return response()->json(['Error' => 'Gagal Update Item']);
        }
    }

    public function saveViewEditDataPSM(Requests\Marketing\AddDataPSMRequest $requestPSM){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $inputDataPSM = $requestPSM->all();
        //dd($inputDataConf);
        $project_no = Session::get('PROJECT_NO_CHAR');
        $date = Carbon::parse(Carbon::now());

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$inputDataPSM['LOT_STOCK_ID_INT'])
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$inputDataPSM['PSM_TRANS_NOCHAR'])
            ->first();

        $bookingDate = date_create($inputDataPSM['PSM_TRANS_BOOKING_DATE']);
        $startDate = date_create($inputDataPSM['PSM_TRANS_START_DATE']);
        $endDate = date_create($inputDataPSM['PSM_TRANS_END_DATE']);

        $freq_num = $endDate->diff($startDate);
        //$freq_num = date_diff($startDate,$endDate);
        //dd($freq_num);
        $difMonth = (int)($freq_num->days / 30);
        $freq_day_num = $difMonth * 30;
        $difDays = (int)($freq_num->days) - (int)($freq_day_num);

        //dd($difMonth.'-'.$difDays);
        $netbeforetax = str_replace('.','',$inputDataPSM['PSM_TRANS_NET_BEFORE_TAX']);
        $ppn = str_replace('.','',$inputDataPSM['PSM_TRANS_PPN']);
        $total = str_replace('.','',$inputDataPSM['PSM_TRANS_PRICE']);

        $downPayment = ($inputDataPSM['PSM_TRANS_DP_PERSEN']/100) * $netbeforetax;

        if ($inputDataPSM['PSM_TRANS_DEPOSIT_TYPE'] == 'SC')
        {
            $depositNum = ($inputDataPSM['PSM_TRANS_SC_NUM'] * $dataLot->LOT_STOCK_SQM) * $inputDataPSM['PSM_TRANS_DEPOSIT_MONTH'];
        }
        elseif ($inputDataPSM['PSM_TRANS_DEPOSIT_TYPE'] == 'SW')
        {
            $depositNum = ($inputDataPSM['PSM_TRANS_RENT_NUM'] * $dataLot->LOT_STOCK_SQM) * $inputDataPSM['PSM_TRANS_DEPOSIT_MONTH'];
        }
        else
        {
            $depositNum = 0;
        }

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$inputDataPSM['PSM_TRANS_NOCHAR'])
            ->update([
                'SHOP_NAME_CHAR'=>$inputDataPSM['SHOP_NAME_CHAR'],
                'PSM_CATEGORY_ID_INT'=>$inputDataPSM['PSM_CATEGORY_ID_INT'],
                'PSM_TRANS_BOOKING_DATE'=>$bookingDate,
                'PSM_TRANS_START_DATE'=>$startDate,
                'PSM_TRANS_END_DATE'=>$endDate,
                'PSM_TRANS_FREQ_NUM'=>$difMonth,
                'PSM_TRANS_FREQ_DAY_NUM'=>$difDays,
                'PSM_TRANS_TIME_PERIOD_SCHED'=>$inputDataPSM['PSM_TRANS_TIME_PERIOD_SCHED'],
                'PSM_TRANS_RENT_NUM'=>$inputDataPSM['PSM_TRANS_RENT_NUM'],
                'PSM_TRANS_SC_NUM'=>$inputDataPSM['PSM_TRANS_SC_NUM'],
                'PSM_TRANS_DESCRIPTION'=>$inputDataPSM['PSM_TRANS_DESCRIPTION'],
                'PSM_TRANS_DISKON_NUM'=>$inputDataPSM['PSM_TRANS_DISKON_NUM'],
                'PSM_TRANS_DISKON_PERSEN'=>$inputDataPSM['PSM_TRANS_DISKON_PERSEN'],
                'PSM_TRANS_NET_BEFORE_TAX'=>$netbeforetax,
                'PSM_TRANS_PPN'=>$ppn,
                'PSM_TRANS_PRICE'=>$total,
                'PSM_TRANS_DP_PERSEN'=>$inputDataPSM['PSM_TRANS_DP_PERSEN'],
                'PSM_TRANS_DP_NUM'=>$downPayment,
                'PSM_TRANS_DEPOSIT_MONTH'=>$inputDataPSM['PSM_TRANS_DEPOSIT_MONTH'],
                'PSM_TRANS_DEPOSIT_TYPE'=>$inputDataPSM['PSM_TRANS_DEPOSIT_TYPE'],
                'PSM_TRANS_DEPOSIT_NUM'=>$depositNum,
                'PSM_TRANS_REQUEST_CHAR'=>Session::get('name'),
                'PSM_TRANS_REQUEST_DATE'=>$date,
                'PROJECT_NO_CHAR'=>$project_no,
//                'PSM_TRANS_SCHEDULE_DATE'=>$inputDataPSM['PSM_TRANS_SCHEDULE_DATE'],
                'PSM_TRANS_GENERATE_BILLING'=>$inputDataPSM['PSM_TRANS_GENERATE_BILLING'],
//                'PSM_TRANS_GRASS_PERIOD'=>$inputDataPSM['PSM_TRANS_GRASS_PERIOD'],
                'PSM_TRANS_VA'=>$inputDataPSM['PSM_TRANS_VA'],
                'created_at'=>$date,
                'updated_at'=>$date
            ]);

        \Session::flash('message', 'Saving Edit Data Lease Agreement '.$inputDataPSM['PSM_TRANS_NOCHAR']);
        $action = "EDIT DATA";
        $description = 'Saving Edit Data Lease Agreement '.$inputDataPSM['PSM_TRANS_NOCHAR'];
        $this->saveToLog($action, $description);

        return redirect()->route('marketing.leaseagreement.viewlistdata')
            ->with('success','Saving Edit Data Lease Agreement '.$inputDataPSM['PSM_TRANS_NOCHAR'].' Successfully');
    }

    public function generateSchedDataPSM($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        DB::statement("exec sp_generateSchedule ".DB::raw($PSM_TRANS_ID_INT));

        $action = "GENERATE SCHED DATA";
        $description = 'Generate Schedule Lease Agreement : '.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        //return View::make('accounting.addDataGlTrans', ['dataJournal' => $dataJournal], ['dataCoa' => $datacoa]);
        return redirect()->route('marketing.leaseagreement.vieweditdata',['id'=>$PSM_TRANS_ID_INT])
            ->with('success','Generate Schedule Lease Agreement '.$dataPSM->PSM_TRANS_NOCHAR.' Succesfully');
    }

    public function generateSchedDataPSMDP($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        DB::statement("exec sp_generateScheduleDP ".DB::raw($PSM_TRANS_ID_INT));

        $action = "GENERATE SCHED DATA DP";
        $description = 'Generate Schedule DP Lease Agreement : '.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        //return View::make('accounting.addDataGlTrans', ['dataJournal' => $dataJournal], ['dataCoa' => $datacoa]);
        return redirect()->route('marketing.leaseagreement.vieweditdata',['id'=>$PSM_TRANS_ID_INT])
            ->with('success','Generate Schedule DP Lease Agreement '.$dataPSM->PSM_TRANS_NOCHAR.' Succesfully');
    }

    public function deleteSchedDataPSM($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $date = Carbon::parse(Carbon::now());

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->whereNotIn('TRX_CODE',['DP'])
            ->whereIn('SCHEDULE_STATUS_INT',[0,1])
            ->delete();

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->update([
                'PSM_TRANS_BILLING_INT'=>0,
                'updated_at'=>$date
            ]);

        $action = "DELETE SCHED DATA";
        $description = 'Delete Schedule Lease Agreement : '.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        //return View::make('accounting.addDataGlTrans', ['dataJournal' => $dataJournal], ['dataCoa' => $datacoa]);
        return redirect()->route('marketing.leaseagreement.vieweditdata',['id'=>$PSM_TRANS_ID_INT])
            ->with('success','Delete Schedule Lease Agreement '.$dataPSM->PSM_TRANS_NOCHAR.' Succesfully');
    }

    public function deleteSchedDataPSMDP($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $date = Carbon::parse(Carbon::now());

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->where('TRX_CODE','=','DP')
            ->whereIn('SCHEDULE_STATUS_INT',[0,1])
            ->delete();

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->update([
                'PSM_TRANS_DP_BILLING_INT'=>0,
                'updated_at'=>$date
            ]);

        $action = "DELETE SCHED DATA DP";
        $description = 'Delete Schedule DP Lease Agreement : '.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        //return View::make('accounting.addDataGlTrans', ['dataJournal' => $dataJournal], ['dataCoa' => $datacoa]);
        return redirect()->route('marketing.leaseagreement.vieweditdata',['id'=>$PSM_TRANS_ID_INT])
            ->with('success','Delete Schedule DP Lease Agreement '.$dataPSM->PSM_TRANS_NOCHAR.' Succesfully');
    }

    public function voidSchedDataPSM($PSM_SCHEDULE_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if($isLogged == FALSE){
            //dd($isLogged);
            return redirect('/login');
        }

        $date = Carbon::parse(Carbon::now());

        $dataSchedPSM = DB::table('PSM_SCHEDULE')
            ->where('PSM_SCHEDULE_ID_INT','=',$PSM_SCHEDULE_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataSchedPSM->PSM_TRANS_NOCHAR)
            ->first();

        DB::table('PSM_SCHEDULE')
            ->where('PSM_SCHEDULE_ID_INT','=',$PSM_SCHEDULE_ID_INT)
            ->update([
                'SCHEDULE_STATUS_INT'=>0,
                'updated_at'=>$date
            ]);

        $action = "VOID SCHED DATA";
        $description = 'Void Schedule Lease Agreement : '.$dataSchedPSM->PSM_TRANS_NOCHAR.' ('.$dataSchedPSM->PSM_SCHEDULE_ID_INT.') succesfully';
        $this->saveToLog($action, $description);
        //return View::make('accounting.addDataGlTrans', ['dataJournal' => $dataJournal], ['dataCoa' => $datacoa]);
        return redirect()->route('marketing.leaseagreement.vieweditdata',['id'=>$dataPSM->PSM_TRANS_ID_INT])
            ->with('success',$description);
    }

    public function viewSchedDiscount($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataPSMLot = DB::table('PSM_TRANS_LOT')
                ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
                ->get();

            $dataLotArr = array();
            foreach($dataPSMLot as $data) {
                array_push($dataLotArr, $data->LOT_STOCK_NO);
            }

            $lotData = implode(',', $dataLotArr);
        }
        else {
            $lotData = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();
        }

        $categoryData = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        $scheduleData = DB::select("Select a.PSM_SCHEDULE_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                           a.TRX_CODE,a.DESC_CHAR,a.BASE_AMOUNT_NUM,a.DISC_NUM,a.PPN_PRICE_NUM,a.BILL_AMOUNT,
                                           a.SCHEDULE_STATUS_INT
                                    FROM PSM_SCHEDULE as a
                                    WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                                    AND a.SCHEDULE_STATUS_INT = 1 --aktif
                                    AND a.PSM_SCHED_DISC_NOCHAR IS NULL
                                    AND a.BASE_AMOUNT_NUM > 0
                                    AND a.TRX_CODE NOT IN ('EC','WC','PL')");

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.leaseagreement.addDiscountSchedule2',
                ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'scheduleData'=>$scheduleData,
                'tenantData'=>$tenantData,'categoryData'=>$categoryData,
                'lotData'=>$lotData]);
        }
        else {
            return View::make('page.leaseagreement.addDiscountSchedule',
                ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'scheduleData'=>$scheduleData,
                'tenantData'=>$tenantData,'categoryData'=>$categoryData,
                'lotData'=>$lotData]);
        }
    }

    public function viewRequestRevenueSharing($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataPSMLot = DB::table('PSM_TRANS_LOT')
                ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
                ->get();

            $dataLotArr = array();
            foreach($dataPSMLot as $data) {
                array_push($dataLotArr, $data->LOT_STOCK_NO);
            }

            $lotData = implode(',', $dataLotArr);
        }
        else {
            $lotData = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();
        }

        $categoryData = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.leaseagreement.addDataRevenueSharing2',
                ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'tenantData'=>$tenantData,
                'categoryData'=>$categoryData,'lotData'=>$lotData]);
        }
        else {
            return View::make('page.leaseagreement.addDataRevenueSharing',
                ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'tenantData'=>$tenantData,
                'categoryData'=>$categoryData,'lotData'=>$lotData]);
        }
    }

    public function viewEditRequestRevenueSharing($type,$PSM_RS_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $project_no = Session::get('PROJECT_NO_CHAR');

        $dataReqRevenueSharing = DB::table('PSM_REVENUE_SHARING')
            ->where('PSM_RS_ID_INT','=',$PSM_RS_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataReqRevenueSharing->PSM_TRANS_NOCHAR)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        $lotData = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
            ->first();

        $categoryData = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        if($type == 'user')
        {
            $modul = 'Edit';
        }
        else
        {
            $modul = 'Appr';
        }

        return View::make('page.leaseagreement.viewEditDataRevenueSharing',
            ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'tenantData'=>$tenantData,
            'categoryData'=>$categoryData,'lotData'=>$lotData,'modul'=>$modul,
            'dataReqRevenueSharing'=>$dataReqRevenueSharing]);
    }

    public function addSchedDiscount(Requests\Marketing\AddDataSchedDiscountRequest $requestDisc){
        $project_no = session('current_project');
        $inputDataDisc = $requestDisc->all();
        $date = Carbon::parse(Carbon::now());
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR'])
                ->first();

            $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

            $converter = new utilConverter();

            $Counter = str_pad($counter->sc_disc_int, 5, "0", STR_PAD_LEFT);
            $Year = substr($date->year, 2);
            $Month = $date->month;
            $monthRomawi = $converter->getRomawi($Month);

            Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                ->update(['sc_disc_int'=>$counter->sc_disc_int + 1]);

            if ($inputDataDisc['PSM_SCHED_DISC_TYPE'] == 'SCHEDULE')
            {
                $noSchedDisc = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/SCH.DISC/'.$monthRomawi.'/'.$Year;

                DB::table('PSM_SCHED_DISC')
                    ->insert([
                        'PSM_SCHED_DISC_NOCHAR'=>$noSchedDisc,
                        'PSM_TRANS_NOCHAR'=>$inputDataDisc['PSM_TRANS_NOCHAR'],
                        'PSM_TRANS_DISC_TYPE'=>$inputDataDisc['PSM_TRANS_DISC_TYPE'],
                        'PSM_SCHED_DISC_AMT'=>$inputDataDisc['PSM_SCHED_DISC_AMT'],
                        'PSM_SCHED_DISC_TYPE'=>$inputDataDisc['PSM_SCHED_DISC_TYPE'],
                        'PROJECT_NO_CHAR'=>$project_no,
                        'PSM_SCHED_DISC_REQ_DATE'=>$date,
                        'PSM_SCHED_DISC_REQ_CHAR'=>$userName,
                        'created_at'=>$date,
                        'updated_at'=>$date
                    ]);

                if ($inputDataDisc['selectall'] == 'all')
                {
                    DB::table('PSM_SCHEDULE')
                        ->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR'])
                        ->where('SCHEDULE_STATUS_INT','=', 1)
                        ->where('PSM_SCHED_DISC_ID_INT','=', 0)
                        ->update([
                            'PSM_SCHED_DISC_NOCHAR'=>$noSchedDisc,
                            'updated_at'=>$date
                        ]);
                }
                else
                {
                    for($i=0;$i<count($inputDataDisc['billing']);$i++)
                    {
                        DB::table('PSM_SCHEDULE')
                            ->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR'])
                            ->where('PSM_SCHEDULE_ID_INT','=',$inputDataDisc['billing'][$i])
                            ->update([
                                'PSM_SCHED_DISC_NOCHAR'=>$noSchedDisc,
                                'updated_at'=>$date
                            ]);
                    }
                }
            }
            elseif ($inputDataDisc['PSM_SCHED_DISC_TYPE'] == 'SERVICE_CHARGE')
            {
                $cekdataStartServiceCharge = DB::table('PSM_SCHED_DISC')
                    ->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR'])
                    ->where('PSM_SCHED_DISC_TYPE','=','SERVICE_CHARGE')
                    ->where('PSM_SCHED_DISC_START_DATE','<=',$inputDataDisc['PSM_SCHED_DISC_START_DATE'])
                    ->where('PSM_TRANS_DISC_STATUS_INT','=',3)
                    ->count();

                $cekdataEndServiceCharge = DB::table('PSM_SCHED_DISC')
                    ->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR'])
                    ->where('PSM_SCHED_DISC_TYPE','=','SERVICE_CHARGE')
                    ->where('PSM_SCHED_DISC_END_DATE','<=',$inputDataDisc['PSM_SCHED_DISC_END_DATE'])
                    ->where('PSM_TRANS_DISC_STATUS_INT','=',3)
                    ->count();

                $noSchedDisc = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/SVC.DISC/'.$monthRomawi.'/'.$Year;

                DB::table('PSM_SCHED_DISC')
                    ->insert([
                        'PSM_SCHED_DISC_NOCHAR'=>$noSchedDisc,
                        'PSM_TRANS_NOCHAR'=>$inputDataDisc['PSM_TRANS_NOCHAR'],
                        'PSM_TRANS_DISC_TYPE'=>$inputDataDisc['PSM_TRANS_DISC_TYPE'],
                        'PSM_SCHED_DISC_AMT'=>$inputDataDisc['PSM_SCHED_DISC_AMT'],
                        'PSM_SCHED_DISC_TYPE'=>$inputDataDisc['PSM_SCHED_DISC_TYPE'],
                        'PSM_SCHED_DISC_START_DATE'=>$inputDataDisc['PSM_SCHED_DISC_START_DATE'],
                        'PSM_SCHED_DISC_END_DATE'=>$inputDataDisc['PSM_SCHED_DISC_END_DATE'],
                        'PROJECT_NO_CHAR'=>$project_no,
                        'PSM_SCHED_DISC_REQ_DATE'=>$date,
                        'PSM_SCHED_DISC_REQ_CHAR'=>$userName,
                        'created_at'=>$date,
                        'updated_at'=>$date
                    ]);
            }
            else
            {
                return redirect()->route('marketing.leaseagreement.viewlistdatascheddisc')
                    ->with('error','Error Save Discount, Contact Administrator');
            }

            \Session::flash('message', 'Saving Schedule Discount '.$noSchedDisc.' / '.$inputDataDisc['PSM_TRANS_NOCHAR']);
            $action = "ADD SCHED DISCOUNT";
            $description = 'Saving Schedule Discount '.$noSchedDisc.' / '.$inputDataDisc['PSM_TRANS_NOCHAR'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('marketing.leaseagreement.viewlistdatascheddisc')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('marketing.leaseagreement.viewlistdatascheddisc')->with('success',$description);
    }

    public function addRequestRevenueSharing(Requests\Marketing\AddDataRevenueSharingRequest $requestRS){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $inputRevenueSharing = $requestRS->all();
        $date = Carbon::parse(Carbon::now());

        $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

        $converter = new utilConverter();

        $Counter = str_pad($counter->revenue_sharing, 5, "0", STR_PAD_LEFT);
        $Year = substr($date->year, 2);
        $Month = $date->month;
        $monthRomawi = $converter->getRomawi($Month);

        Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
            ->update(['revenue_sharing'=>$counter->revenue_sharing + 1]);

        $noRevenueSharing = $Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'/REV-SHR/'.$monthRomawi.'/'.$Year;

        DB::table('PSM_REVENUE_SHARING')
            ->insert([
                'PSM_RS_NOCHAR'=>$noRevenueSharing,
                'PSM_TRANS_NOCHAR'=>$inputRevenueSharing['PSM_TRANS_NOCHAR'],
                'PSM_RS_START_DATE'=>$inputRevenueSharing['PSM_RS_START_DATE'],
                'PSM_RS_MIN_AMT'=>$inputRevenueSharing['PSM_RS_MIN_AMT'],
                'PSM_RS_END_DATE'=>$inputRevenueSharing['PSM_RS_END_DATE'],
                'PSM_RS_LOW_NUM'=>$inputRevenueSharing['PSM_RS_LOW_NUM'],
                'PSM_RS_LOW_RATE'=>$inputRevenueSharing['PSM_RS_LOW_RATE'],
                'PSM_RS_HIGH_NUM'=>$inputRevenueSharing['PSM_RS_HIGH_NUM'],
                'PSM_RS_HIGH_RATE'=>$inputRevenueSharing['PSM_RS_HIGH_RATE'],
                'PROJECT_NO_CHAR'=>$project_no,
                'PSM_RS_REQUEST_CHAR'=>$userName,
                'PSM_RS_REQUEST_DATE'=>$date,
                'created_at'=>$date,
                'updated_at'=>$date,
            ]);

        if ($inputRevenueSharing['upload_file'] <> 'None')
        {
            $file = $inputRevenueSharing['upload_file'];
            
            $ext = $file->getClientOriginalExtension();
            $originalName = '/REVENUE_SHARING/'.$dataProject['PROJECT_CODE'].'/'.$file->getClientOriginalName();
            
            if ($ext == 'pdf')
            {
                if (Storage::disk('ftp')->exists($originalName,$file))
                {
                    Storage::disk('ftp')->delete($originalName,$file);
                }

                Storage::disk('ftp')->put($originalName, fopen($file, 'r+'));

                DB::table('PSM_REVENUE_SHARING')
                    ->where('PSM_RS_NOCHAR','=',$noRevenueSharing)
                    ->update([
                        'PSM_RS_DOC_FILE'=>$file->getClientOriginalName(),
                        'updated_at'=>$date
                    ]);
            }
            else
            {
                return redirect()->route('marketing.leaseagreement.viewrequestrevenuesharing',[$inputRevenueSharing['PSM_TRANS_ID_INT']])
                    ->with('error','Upload PDF File Only, Upload Failed.');
            }
        }

        $action = "ADD REQ REVENUE SHARING";
        $description = 'Saving REQ Revenue Sharing '.$noRevenueSharing.' / '.$inputRevenueSharing['PSM_TRANS_NOCHAR'];
        $this->saveToLog($action, $description);

        return redirect()->route('marketing.leaseagreement.vieweditdata',[$inputRevenueSharing['PSM_TRANS_ID_INT']])
            ->with('success',$description);
    }

    public function approveDataRevenueSharing($PSM_RS_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $date = Carbon::parse(Carbon::now());

        $dataRevenueSharing = DB::table('PSM_REVENUE_SHARING')
            ->where('PSM_RS_ID_INT','=',$PSM_RS_ID_INT)
            ->first();

        DB::table('PSM_REVENUE_SHARING')
            ->where('PSM_RS_ID_INT','=',$PSM_RS_ID_INT)
            ->update([
                'PSM_RS_STATUS_INT'=>2,
                'updated_at'=>$date
            ]);


        $action = "APPROVE REQ REVENUE SHARING";
        $description = 'Approve Req. Revenue Sharing : '.$dataRevenueSharing->PSM_RS_NOCHAR.' / '.$dataRevenueSharing->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.viewlistdatarevenuesharingappr')
            ->with('success',$description);
    }

    public function cancelDataRevenueSharing($PSM_RS_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $date = Carbon::parse(Carbon::now());

        $dataRevenueSharing = DB::table('PSM_REVENUE_SHARING')
            ->where('PSM_RS_ID_INT','=',$PSM_RS_ID_INT)
            ->first();

        DB::table('PSM_REVENUE_SHARING')
            ->where('PSM_RS_ID_INT','=',$PSM_RS_ID_INT)
            ->update([
                'PSM_RS_STATUS_INT'=>0,
                'updated_at'=>$date
            ]);


        $action = "CANCEL REQ REVENUE SHARING";
        $description = 'Cancel Req. Revenue Sharing : '.$dataRevenueSharing->PSM_RS_NOCHAR.' / '.$dataRevenueSharing->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.viewlistdatarevenuesharingappr')
            ->with('success',$description);
    }

    public function viewListDataSchedDiscAppr(){
        $project_no = session('current_project');

        $schedDisc = DB::select("Select a.PSM_SCHED_DISC_ID_INT,a.PSM_SCHED_DISC_NOCHAR,a.PSM_TRANS_NOCHAR,b.LOT_STOCK_NO,a.PSM_TRANS_DISC_TYPE,
                                        a.PSM_SCHED_DISC_AMT,a.PSM_SCHED_DISC_TYPE,b.SHOP_NAME_CHAR
                                from PSM_SCHED_DISC as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                where a.PROJECT_NO_CHAR = '".$project_no."'
                                and a.PSM_TRANS_DISC_STATUS_INT = 1");

        return View::make('page.leaseagreement.listDataScheduleDiscountAppr',
            ['schedDisc'=>$schedDisc]);
    }

    public function viewListDataSchedDisc() {
        $project_no = session('current_project');

        $schedDisc = DB::select("Select a.PSM_SCHED_DISC_ID_INT,a.PSM_SCHED_DISC_NOCHAR,a.PSM_TRANS_NOCHAR,b.LOT_STOCK_NO,a.PSM_TRANS_DISC_TYPE,
                                        a.PSM_SCHED_DISC_AMT,a.PSM_SCHED_DISC_TYPE,
                                        CASE
                                            WHEN PSM_TRANS_DISC_STATUS_INT = 1 THEN 'REQUEST'
                                            WHEN PSM_TRANS_DISC_STATUS_INT = 2 THEN 'APPROVE'
                                            WHEN PSM_TRANS_DISC_STATUS_INT = 3 THEN 'PROCESS'
                                        ELSE 'CANCEL' END PSM_TRANS_DISC_STATUS_INT,b.SHOP_NAME_CHAR
                                from PSM_SCHED_DISC as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                where a.PROJECT_NO_CHAR = '".$project_no."'
                                and a.PSM_TRANS_DISC_STATUS_INT NOT IN (0)");

        return View::make('page.leaseagreement.listDataScheduleDiscount',
            ['schedDisc'=>$schedDisc]);
    }

    public function viewListDataRevenueSharing(){
        $project_no = session('current_project');

        $revenueSharing = DB::select("Select a.PSM_RS_ID_INT,a.PSM_RS_NOCHAR,a.PSM_TRANS_NOCHAR,b.LOT_STOCK_NO,FORMAT(a.PSM_RS_START_DATE,'dd-MM-yyyy') as PSM_RS_START_DATE,
                                               FORMAT(a.PSM_RS_END_DATE,'dd-MM-yyyy') as PSM_RS_END_DATE,
                                                CASE
                                                    WHEN PSM_RS_STATUS_INT = 1 THEN 'REQUEST'
                                                    WHEN PSM_RS_STATUS_INT = 2 THEN 'APPROVE'
                                                ELSE 'CANCEL' END PSM_TRANS_DISC_STATUS_INT
                                        from PSM_REVENUE_SHARING as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        and a.PSM_RS_STATUS_INT NOT IN (0)");

        return View::make('page.leaseagreement.listDataRevenueSharing',
            ['revenueSharing'=>$revenueSharing]);
    }

    public function viewListDataRevenueSharingAppr(){
        $project_no = session('current_project');

        $revenueSharing = DB::select("Select a.PSM_RS_ID_INT,a.PSM_RS_NOCHAR,a.PSM_TRANS_NOCHAR,b.LOT_STOCK_NO,FORMAT(a.PSM_RS_START_DATE,'dd-MM-yyyy') as PSM_RS_START_DATE,
                                               FORMAT(a.PSM_RS_END_DATE,'dd-MM-yyyy') as PSM_RS_END_DATE,
                                                CASE
                                                    WHEN PSM_RS_STATUS_INT = 1 THEN 'REQUEST'
                                                    WHEN PSM_RS_STATUS_INT = 2 THEN 'APPROVE'
                                                ELSE 'CANCEL' END PSM_TRANS_DISC_STATUS_INT
                                        from PSM_REVENUE_SHARING as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                                        where a.PROJECT_NO_CHAR = '".$project_no."'
                                        and a.PSM_RS_STATUS_INT IN (1)");

        return View::make('page.leaseagreement.listDataRevenueSharingAppr',
            ['revenueSharing'=>$revenueSharing]);
    }

    public function approveDataSchedDisc($PSM_SCHED_DISC_ID_INT){
        $date = Carbon::parse(Carbon::now());
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $dataSchedDisc = DB::table('PSM_SCHED_DISC')
            ->where('PSM_SCHED_DISC_ID_INT','=',$PSM_SCHED_DISC_ID_INT)
            ->first();

        DB::table('PSM_SCHED_DISC')
            ->where('PSM_SCHED_DISC_ID_INT','=',$PSM_SCHED_DISC_ID_INT)
            ->update([
                'PSM_TRANS_DISC_STATUS_INT'=>2,
                'PSM_SCHED_DISC_APPR_CHAR'=>$userName,
                'PSM_SCHED_DISC_APPR_DATE'=>$date,
                'updated_at'=>$date
            ]);

        $action = "APPROVE SCHED DISC DATA";
        $description = 'Approve Sched Disc : '.$dataSchedDisc->PSM_SCHED_DISC_NOCHAR.' / '.$dataSchedDisc->PSM_TRANS_NOCHAR.' Succesfully';
        $this->saveToLog($action, $description);

        return redirect()->route('marketing.leaseagreement.viewlistdatascheddiscappr')
            ->with('success',$description);
    }

    public function cancelDataSchedDisc($PSM_SCHED_DISC_ID_INT){
        $date = Carbon::parse(Carbon::now());
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $dataSchedDisc = DB::table('PSM_SCHED_DISC')
            ->where('PSM_SCHED_DISC_ID_INT','=',$PSM_SCHED_DISC_ID_INT)
            ->first();

        DB::table('PSM_SCHED_DISC')
            ->where('PSM_SCHED_DISC_ID_INT','=',$PSM_SCHED_DISC_ID_INT)
            ->update([
                'PSM_TRANS_DISC_STATUS_INT'=>0,
                'PSM_SCHED_DISC_APPR_CHAR'=>$userName,
                'PSM_SCHED_DISC_APPR_DATE'=>$date,
                'updated_at'=>$date
            ]);

        DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
            ->where('PSM_SCHED_DISC_NOCHAR','=',$dataSchedDisc->PSM_SCHED_DISC_NOCHAR)
            ->update([
                'PSM_SCHED_DISC_NOCHAR'=>NULL,
                'updated_at'=>$date
            ]);

        $action = "CANCEL SCHED DISC DATA";
        $description = 'Cancel Sched Disc : '.$dataSchedDisc->PSM_SCHED_DISC_NOCHAR.' / '.$dataSchedDisc->PSM_TRANS_NOCHAR.' Succesfully';
        $this->saveToLog($action, $description);

        return redirect()->route('marketing.leaseagreement.viewlistdatascheddiscappr')
            ->with('success',$description);
    }

    public function viewProcessDiscount($PSM_SCHED_DISC_ID_INT) {
        $project_no = session('current_project');

        $dataSchedDiscount = DB::table('PSM_SCHED_DISC')
            ->where('PSM_SCHED_DISC_ID_INT','=',$PSM_SCHED_DISC_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataSchedDiscount->PSM_TRANS_NOCHAR)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        $categoryData = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            $dataPSMLot = DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)->get();

            $dataLotArr = array();
            foreach($dataPSMLot as $data) {
                array_push($dataLotArr, $data->LOT_STOCK_NO);
            }

            $lotData = implode(',', $dataLotArr);
        }
        else {
            $lotData = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();
        }

        $dataCategory = DB::table('PSM_CATEGORY')
            ->where('IS_DELETE','=',0)
            ->get();

        $scheduleData = DB::select("Select a.PSM_SCHEDULE_ID_INT,FORMAT(a.TGL_SCHEDULE_DATE,'dd-MM-yyyy') as TGL_SCHEDULE_DATE,
                                           a.TRX_CODE,a.DESC_CHAR,a.BASE_AMOUNT_NUM,a.DISC_NUM,a.PPN_PRICE_NUM,a.BILL_AMOUNT,
                                           a.SCHEDULE_STATUS_INT
                                    FROM PSM_SCHEDULE as a
                                    WHERE a.PSM_SCHED_DISC_NOCHAR = '".$dataSchedDiscount->PSM_SCHED_DISC_NOCHAR."'");

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.leaseagreement.viewDataApproveDiscount2',
                ['dataPSM'=>$dataPSM,'tenantData'=>$tenantData,'lotData'=>$lotData,
                'scheduleData'=>$scheduleData,'categoryData'=>$categoryData,
                'dataCategory'=>$dataCategory,'dataSchedDiscount'=>$dataSchedDiscount]);
        }
        else {
            return View::make('page.leaseagreement.viewDataApproveDiscount',
                ['dataPSM'=>$dataPSM,'tenantData'=>$tenantData,'lotData'=>$lotData,
                'scheduleData'=>$scheduleData,'categoryData'=>$categoryData,
                'dataCategory'=>$dataCategory,'dataSchedDiscount'=>$dataSchedDiscount]);
        }
    }

    public function uploadFileDiscount(Request $param) {
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $inputDataDiscount = $param->all();

        $date = Carbon::parse(Carbon::now());
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataSchedDisc = DB::table('PSM_SCHED_DISC')
            ->where('PSM_SCHED_DISC_ID_INT','=',$inputDataDiscount['PSM_SCHED_DISC_ID_INT'])
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
            ->first();

        if($dataSchedDisc->PSM_SCHED_DISC_TYPE == 'SCHEDULE')
        {
            $dataSchedDetail = DB::table('PSM_SCHEDULE')
                ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
                ->where('PSM_SCHED_DISC_NOCHAR','=',$dataSchedDisc->PSM_SCHED_DISC_NOCHAR)
                ->count();

            $tanggalMinSchedDisc = DB::table('PSM_SCHEDULE')
                ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
                ->where('PSM_SCHED_DISC_NOCHAR','=',$dataSchedDisc->PSM_SCHED_DISC_NOCHAR)
                ->min('TGL_SCHEDULE_DATE');

            $jumlahSchedAfterDisc = DB::table('PSM_SCHEDULE')
                ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
                ->where('TGL_SCHEDULE_DATE','>=',$tanggalMinSchedDisc)
                ->count();
        }

        if ($inputDataDiscount['upload_file'] <> 'None')
        {
            $file = $inputDataDiscount['upload_file'];
            
            $ext = $file->getClientOriginalExtension();
            $originalName = '/DISCOUNT/'.$dataProject['PROJECT_CODE'].'/'.$file->getClientOriginalName();
            
            if ($ext == 'pdf')
            {
                if (Storage::disk('ftp')->exists($originalName,$file))
                {
                    Storage::disk('ftp')->delete($originalName,$file);
                }

                Storage::disk('ftp')->put($originalName, fopen($file, 'r+'));

                $totalNilaiDisc = 0;
                if ($dataSchedDisc->PSM_TRANS_DISC_TYPE == 'Percentation' &&
                    $dataSchedDisc->PSM_SCHED_DISC_TYPE == 'SCHEDULE')
                {
                    $dataSchedule = DB::table('PSM_SCHEDULE')
                        ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
                        ->where('PSM_SCHED_DISC_NOCHAR','=',$dataSchedDisc->PSM_SCHED_DISC_NOCHAR)
                        ->get();

                    foreach ($dataSchedule as $schedule)
                    {
                        $nilaiBaseAmount = $schedule->BASE_AMOUNT_NUM;
                        $disc_persen = $dataSchedDisc->PSM_SCHED_DISC_AMT;
                        $disc_num = ($dataSchedDisc->PSM_SCHED_DISC_AMT/100) * $nilaiBaseAmount;

                        if ($schedule->TGL_SCHEDULE_DATE <= '2022-03-31')
                        {
                            $ppn_num = round((($nilaiBaseAmount - $disc_num) * 0.1));
                            $total_bill = ($nilaiBaseAmount - $disc_num) + $ppn_num;
                        }
                        else
                        {
                            $ppn_num = round((($nilaiBaseAmount - $disc_num) * $dataProject['PPNBM_NUM']));
                            $total_bill = ($nilaiBaseAmount - $disc_num) + $ppn_num;
                        }

                        DB::table('PSM_SCHEDULE')
                            ->where('PSM_SCHEDULE_ID_INT','=',$schedule->PSM_SCHEDULE_ID_INT)
                            ->update([
                                'DISC_PERSEN'=>$disc_persen,
                                'DISC_NUM'=>$disc_num,
                                'PPN_PRICE_NUM'=>$ppn_num,
                                'BILL_AMOUNT'=>$total_bill,
                                'PSM_SCHED_DISC_NOCHAR'=>NULL,
                                'updated_at'=>$date
                            ]);
                        $totalNilaiDisc += $disc_num;
                    }

                    $totalIncomeBeforeDisc = ($dataPSM->PSM_TRANS_UNEARN * $jumlahSchedAfterDisc);
                    $totalIncomeAfterDisc = $totalIncomeBeforeDisc - $totalNilaiDisc;
                    $incomeNew = round(($totalIncomeAfterDisc / $jumlahSchedAfterDisc));
                }
                elseif ($dataSchedDisc->PSM_TRANS_DISC_TYPE == 'Amount' &&
                    $dataSchedDisc->PSM_SCHED_DISC_TYPE == 'SCHEDULE')
                {
                    $dataSchedule = DB::table('PSM_SCHEDULE')
                        ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
                        ->where('PSM_SCHED_DISC_NOCHAR','=',$dataSchedDisc->PSM_SCHED_DISC_NOCHAR)
                        ->get();

                    foreach ($dataSchedule as $schedule)
                    {
                        $nilaiBaseAmount = $schedule->BASE_AMOUNT_NUM;
                        $disc_persen = ($dataSchedDisc->PSM_SCHED_DISC_AMT / $nilaiBaseAmount) * 100;
                        $disc_num = ($dataSchedDisc->PSM_SCHED_DISC_AMT);

                        if ($schedule->TGL_SCHEDULE_DATE <= '2022-03-31')
                        {
                            $ppn_num = ($nilaiBaseAmount - $disc_num) * 0.1;
                            $total_bill = ($nilaiBaseAmount - $disc_num) + (($nilaiBaseAmount - $disc_num) * 0.1);
                        }
                        else
                        {
                            $ppn_num = ($nilaiBaseAmount - $disc_num) * $dataProject['PPNBM_NUM'];
                            $total_bill = ($nilaiBaseAmount - $disc_num) + (($nilaiBaseAmount - $disc_num) * $dataProject['PPNBM_NUM']);
                        }

                        DB::table('PSM_SCHEDULE')
                            ->where('PSM_SCHEDULE_ID_INT','=',$schedule->PSM_SCHEDULE_ID_INT)
                            ->update([
                                'DISC_PERSEN'=>$disc_persen,
                                'DISC_NUM'=>$disc_num,
                                'PPN_PRICE_NUM'=>$ppn_num,
                                'BILL_AMOUNT'=>$total_bill,
                                'PSM_SCHED_DISC_NOCHAR'=>NULL,
                                'updated_at'=>$date
                            ]);
                        $totalNilaiDisc += $disc_num;

                        $totalIncomeBeforeDisc = ($dataPSM->PSM_TRANS_UNEARN * $jumlahSchedAfterDisc);
                        $totalIncomeAfterDisc = $totalIncomeBeforeDisc - $totalNilaiDisc;
                        $incomeNew = round(($totalIncomeAfterDisc / $jumlahSchedAfterDisc));
                    }
                }

                DB::table('PSM_SCHED_DISC')
                    ->where('PSM_SCHED_DISC_ID_INT','=',$inputDataDiscount['PSM_SCHED_DISC_ID_INT'])
                    ->update([
                        'PSM_TRANS_DISC_STATUS_INT' => 3,
                        'PSM_SCHED_DISC_FILE'=>$file->getClientOriginalName(),
                        'updated_at'=>$date
                    ]);

                DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
                    ->update([
                        'PSM_TRANS_UNEARN'=>$incomeNew,
                        'updated_at'=>$date
                    ]);

                $dataPSM1 = DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$dataSchedDisc->PSM_TRANS_NOCHAR)
                    ->first();

                DB::table('PSM_TRANS_HIST')
                    ->insert([
                        'PSM_TRANS_NOCHAR'=>$dataPSM1->PSM_TRANS_NOCHAR,
                        'LOI_TRANS_NOCHAR'=>$dataPSM1->LOI_TRANS_NOCHAR,
                        'SKS_TRANS_NOCHAR'=>$dataPSM1->SKS_TRANS_NOCHAR,
                        'LOT_STOCK_ID_INT'=>$dataPSM1->LOT_STOCK_ID_INT,
                        'LOT_STOCK_NO'=>$dataPSM1->LOT_STOCK_NO,
                        'DEBTOR_ACCT_CHAR'=>$dataPSM1->DEBTOR_ACCT_CHAR,
                        'SHOP_NAME_CHAR'=>$dataPSM1->SHOP_NAME_CHAR,
                        'MD_TENANT_ID_INT'=>$dataPSM1->MD_TENANT_ID_INT,
                        'PSM_TRANS_TYPE'=>$dataPSM1->PSM_TRANS_TYPE,
                        'PSM_TRANS_BOOKING_DATE'=>$dataPSM1->PSM_TRANS_BOOKING_DATE,
                        'PSM_TRANS_START_DATE'=>$dataPSM1->PSM_TRANS_START_DATE,
                        'PSM_TRANS_END_DATE'=>$dataPSM1->PSM_TRANS_END_DATE,
                        'PSM_TRANS_FREQ_NUM'=>$dataPSM1->PSM_TRANS_FREQ_NUM,
                        'PSM_TRANS_FREQ_DAY_NUM'=>$dataPSM1->PSM_TRANS_FREQ_DAY_NUM,
                        'PSM_TRANS_TIME_PERIOD_SCHED'=>$dataPSM1->PSM_TRANS_TIME_PERIOD_SCHED,
                        'PSM_TRANS_RENT_NUM'=>$dataPSM1->PSM_TRANS_RENT_NUM,
                        'PSM_TRANS_SC_NUM'=>$dataPSM1->PSM_TRANS_SC_NUM,
                        'PSM_TRANS_DESCRIPTION'=>$dataPSM1->PSM_TRANS_DESCRIPTION,
                        'PSM_TRANS_NET_BEFORE_TAX'=>$dataPSM1->PSM_TRANS_NET_BEFORE_TAX,
                        'PSM_TRANS_PPN'=>$dataPSM1->PSM_TRANS_PPN,
                        'PSM_TRANS_PRICE'=>$dataPSM1->PSM_TRANS_PRICE,
                        'PSM_TRANS_UNEARN'=>$dataPSM1->PSM_TRANS_UNEARN,
                        'PSM_TRANS_DP_PERSEN'=>$dataPSM1->PSM_TRANS_DP_PERSEN,
                        'PSM_TRANS_DP_NUM'=>$dataPSM1->PSM_TRANS_DP_NUM,
                        'PSM_TRANS_DP_PERIOD'=>$dataPSM1->PSM_TRANS_DP_PERIOD,
                        'PSM_TRANS_DEPOSIT_MONTH'=>$dataPSM1->PSM_TRANS_DEPOSIT_MONTH,
                        'PSM_TRANS_DEPOSIT_TYPE'=>$dataPSM1->PSM_TRANS_DEPOSIT_TYPE,
                        'PSM_TRANS_DEPOSIT_NUM'=>$dataPSM1->PSM_TRANS_DEPOSIT_NUM,
                        'PSM_TRANS_DEPOSIT_DATE'=>$dataPSM1->PSM_TRANS_DEPOSIT_DATE,
                        'PSM_INVEST_NUM'=>$dataPSM1->PSM_INVEST_NUM,
                        'PSM_INVEST_RATE'=>$dataPSM1->PSM_INVEST_RATE,
                        'PSM_REVENUE_LOW_NUM'=>$dataPSM1->PSM_REVENUE_LOW_NUM,
                        'PSM_REVENUE_LOW_RATE'=>$dataPSM1->PSM_REVENUE_LOW_RATE,
                        'PSM_REVENUE_HIGH_NUM'=>$dataPSM1->PSM_REVENUE_HIGH_NUM,
                        'PSM_REVENUE_HIGH_RATE'=>$dataPSM1->PSM_REVENUE_HIGH_RATE,
                        'PSM_TRANS_STATUS_INT'=>$dataPSM1->PSM_TRANS_STATUS_INT,
                        'PSM_TRANS_GENERATE_BILLING'=>$dataPSM1->PSM_TRANS_GENERATE_BILLING,
                        'PSM_TRANS_BILLING_INT'=>$dataPSM1->PSM_TRANS_BILLING_INT,
                        'PSM_TRANS_DP_BILLING_INT'=>$dataPSM1->PSM_TRANS_DP_BILLING_INT,
                        'PSM_TRANS_GRASS_TYPE'=>$dataPSM1->PSM_TRANS_GRASS_TYPE,
                        'PSM_TRANS_GRASS_PERIOD'=>$dataPSM1->PSM_TRANS_GRASS_PERIOD,
                        'PSM_TRANS_GRASS_DATE'=>$dataPSM1->PSM_TRANS_GRASS_DATE,
                        'PSM_TRANS_VA'=>$dataPSM1->PSM_TRANS_VA,
                        'PSM_TRANS_REQUEST_CHAR'=>$dataPSM1->PSM_TRANS_REQUEST_CHAR,
                        'PSM_TRANS_REQUEST_DATE'=>$dataPSM1->PSM_TRANS_REQUEST_DATE,
                        'PROJECT_NO_CHAR'=>$dataPSM1->PROJECT_NO_CHAR,
                        'ACTION'=>'UPDATE UNEARN',
                        'USER_BY'=>$userName,
                        'created_at'=>$date,
                        'updated_at'=>$date
                    ]);
            }
            else
            {
                return redirect()->route('marketing.leaseagreement.viewprocessdiscount',[$inputDataDiscount['PSM_SCHED_DISC_ID_INT']])
                    ->with('error','Upload PDF File Only, Upload Failed.');
            }
        }
        else
        {
            return redirect()->route('marketing.leaseagreement.viewprocessdiscount',[$inputDataDiscount['PSM_SCHED_DISC_ID_INT']])
                ->with('error','Upload Form Discount Approval, Process Failed.');
        }

        $action = "UPLOAD DISCOUNT APPROVE";
        $description = 'Upload Form Discount Approved File : ' . $dataSchedDisc->PSM_SCHED_DISC_NOCHAR;
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.viewlistdatascheddisc')
            ->with('success',$description);
    }

    public function voidSchedule(Request $requestDisc){
        $project_no = session('current_project');

        $inputDataDisc = $requestDisc->all();
        
        $date = Carbon::parse(Carbon::now());

        $dataPSM = DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR1'])->first();

        try {
            \DB::beginTransaction();

            $idSched = '';
            for($i=0;$i<count($inputDataDisc['billing']);$i++)
            {
                DB::table('PSM_SCHEDULE')
                    ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
                    ->where('PSM_SCHEDULE_ID_INT','=',$inputDataDisc['billing'][$i])
                    ->update([
                        'SCHEDULE_STATUS_INT'=>0,
                        'updated_at'=>$date
                    ]);

                $idSched .= $inputDataDisc['billing'][$i].',';
            }

            $cekDataDP = DB::table('PSM_SCHEDULE')
                ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
                ->where('TRX_CODE','=','DP')
                ->whereNotIn('SCHEDULE_STATUS_INT',[0])
                ->count();

            $cekDataRental = DB::table('PSM_SCHEDULE')
                ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
                ->whereIn('TRX_CODE',['RT'])
                ->whereNotIn('SCHEDULE_STATUS_INT',[0])
                ->count();

            if($cekDataDP == 0)
            {
                DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR1'])
                    ->update([
                        'PSM_TRANS_DP_BILLING_INT'=>0,
                        'updated_at'=>$date
                    ]);
            }

            if($cekDataRental == 0)
            {
                DB::table('PSM_TRANS')
                    ->where('PSM_TRANS_NOCHAR','=',$inputDataDisc['PSM_TRANS_NOCHAR1'])
                    ->update([
                        'PSM_TRANS_BILLING_INT'=>0,
                        'updated_at'=>$date
                    ]);
            }

            $action = "VOID SCHEDULE";
            $description = 'Void Schedule '.$dataPSM->PSM_TRANS_NOCHAR.' ('.$idSched.')';
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('marketing.leaseagreement.vieweditdata', [$dataPSM->PSM_TRANS_ID_INT])->with('error', "Gagal Void Schedule");
        }

        return redirect()->route('marketing.leaseagreement.vieweditdata', [$dataPSM->PSM_TRANS_ID_INT])->with('success', $description);
    }

    public function viewAddAddendum($ADD_TYPE,$PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->get();

        $categoryData = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_NO) == false) {
            $isMultipleLot = \Str::contains($dataPSM->LOT_STOCK_NO, ',');
            if($isMultipleLot) {
                $dataMultipleLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->get();
                $dataMultipleLotArr = array();
                foreach($dataMultipleLot as $data) {
                    array_push($dataMultipleLotArr, $data->LOT_STOCK_ID_INT);
                }

                $lotData = DB::table('LOT_STOCK')
                    ->whereIn('LOT_STOCK_ID_INT', $dataMultipleLotArr)
                    ->get();
            }
            else if(empty($dataPSM->LOT_STOCK_ID_INT)) {
                $dataSingleLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();
                $lotData = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$dataSingleLot->LOT_STOCK_ID_INT)
                    ->get();
            }
            else {
                $lotData = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                    ->first();
            }
        }
        else {
            $lotData = DB::table('LOT_STOCK')->where('ON_RELEASE_STAT_INT','=',1)
                ->where('ON_RENT_STAT_INT','=',0)
                ->where('IS_DELETE','=',0)
                ->where('PROJECT_NO_CHAR','=',$project_no)
                ->get();
        }

        $salesTypedata = DB::table("MD_SALES_TYPE")
            ->where('MD_SALES_TYPE_ID_INT','=',$dataPSM->MD_SALES_TYPE_ID_INT)
            ->first();

        $dataSalesType = DB::table("MD_SALES_TYPE")
            ->where('IS_ACTIVE','=',1)
            ->get();

        return View::make('page.leaseagreement.addDataAddendum2',
            ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'salesTypedata'=>$salesTypedata,
            'tenantData'=>$tenantData,'categoryData'=>$categoryData,'ADD_TYPE'=>$ADD_TYPE,
            'lotData'=>$lotData,'dataSalesType'=>$dataSalesType,'dataTenant'=>$dataTenant]);
    }

    public function saveDataAddendum(Requests\Marketing\AddDataAddendumRequest $requestPSM){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            $inputDataAddendum = $requestPSM->all();

            $date = Carbon::parse(Carbon::now());
            $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

            $dataPSM = DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$inputDataAddendum['PSM_TRANS_NOCHAR'])
                ->first();

            if(empty($inputDataAddendum['LOT_STOCK_ID_INT'])) {
                $dataPSMLotCount = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $inputDataAddendum['PSM_TRANS_NOCHAR'])->where('PROJECT_NO_CHAR', $project_no)->count();
                if ($dataPSMLotCount <= 0)
                {
                    return redirect()->route('marketing.leaseagreement.viewaddaddendum',[$inputDataAddendum['ADD_TYPE'],$inputDataAddendum['PSM_TRANS_ID_INT']])
                        ->with('error','Your Lot Data not Found, Process Fail...');
                }
            }
            else {
                $cekDataLot = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$inputDataAddendum['LOT_STOCK_ID_INT'])
                    ->count();

                if ($cekDataLot > 0)
                {
                    $dataLot = DB::table('LOT_STOCK')
                        ->where('LOT_STOCK_ID_INT','=',$inputDataAddendum['LOT_STOCK_ID_INT'])
                        ->first();
                }
                else
                {
                    return redirect()->route('marketing.leaseagreement.viewaddaddendum',[$inputDataAddendum['ADD_TYPE'],$inputDataAddendum['PSM_TRANS_ID_INT']])
                        ->with('error','Your Lot Data not Found, Process Fail...');
                }
            }

            $cekReqAddendum = DB::table('PSM_TRANS_ADDENDUM')
                ->where('PSM_TRANS_NOCHAR','=',$inputDataAddendum['PSM_TRANS_NOCHAR'])
                ->where('PSM_TRANS_ADD_STATUS_INT','=',1) // Request
                ->count();

            if ($cekReqAddendum > 0 )
            {
                return redirect()->route('marketing.leaseagreement.viewaddaddendum',[$inputDataAddendum['ADD_TYPE'],$inputDataAddendum['PSM_TRANS_ID_INT']])
                    ->with('error','You have Document Pending in List Addendum, Save Data Fail...');

            }

            $cekDataOutstanding = DB::table('INVOICE_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$inputDataAddendum['PSM_TRANS_NOCHAR'])
                ->whereNotIn('INVOICE_STATUS_INT',[0,4])
                ->count();

            if ($cekDataOutstanding > 0 && ($dataPSM->MD_TENANT_ID_INT <> $inputDataAddendum['MD_TENANT_ID_INT']))
            {
                return redirect()->route('marketing.leaseagreement.viewaddaddendum',[$inputDataAddendum['ADD_TYPE'],$inputDataAddendum['PSM_TRANS_ID_INT']])
                    ->with('error',$inputDataAddendum['PSM_TRANS_NOCHAR'].' have outstanding amount, Process Fail...');
            }

            $converter = new utilConverter();
            $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $Counter = str_pad($counter->addendum_count, 5, "0", STR_PAD_LEFT);
            $Year = substr($date->year, 2);
            $Month = $date->month;
            $monthRomawi = $converter->getRomawi($Month);

            Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                ->update(['addendum_count' => $counter->addendum_count + 1]);

            $noAddendum = $Counter . '/' . $dataCompany['COMPANY_CODE'] . '/' . $dataProject['PROJECT_CODE'] . '/'.$inputDataAddendum['ADD_TYPE'].'/' . $monthRomawi . '/' . $Year;

            if ($inputDataAddendum['ADD_TYPE'] == 'RVS')
            {
                $md_add_type = 3;
                $remark = 'REVISION';
            }
            else
            {
                $md_add_type = 1;
                $remark = 'RENEWAL';
            }

            $bookingDate = date_create($inputDataAddendum['PSM_TRANS_BOOKING_DATE']);
            $startDate = date_create($inputDataAddendum['PSM_TRANS_START_DATE']);
            $endDate = date_create($inputDataAddendum['PSM_TRANS_END_DATE']);

            $freq_num = $endDate->diff($startDate);
            $difMonth = (int)($freq_num->days / 30);
            $freq_day_num = $difMonth * 30;
            $difDays = (int)($freq_num->days) - (int)($freq_day_num);

            $netbeforetax = str_replace('.','',empty($inputDataAddendum['PSM_TRANS_NET_BEFORE_TAX']) ? 0 : $inputDataAddendum['PSM_TRANS_NET_BEFORE_TAX']);
            $ppn = str_replace('.','',empty($inputDataAddendum['PSM_TRANS_PPN']) ? 0 : $inputDataAddendum['PSM_TRANS_PPN']);
            $total = str_replace('.','',empty($inputDataAddendum['PSM_TRANS_PRICE']) ? 0 : $inputDataAddendum['PSM_TRANS_PRICE']);

            $downPayment = ($inputDataAddendum['PSM_TRANS_DP_PERSEN']/100) * $netbeforetax;

            $lastIdAddendum = DB::table('PSM_TRANS_ADDENDUM')
                ->insertGetId([
                    'PSM_TRANS_ADD_NOCHAR'=>$noAddendum,
                    'MD_ADD_TYPE_ID_INT'=>$md_add_type,
                    'PSM_ADD_DOC_TYPE'=>$inputDataAddendum['ADD_TYPE'],
                    'PSM_TRANS_NOCHAR' => $inputDataAddendum['PSM_TRANS_NOCHAR'],
                    'LOI_TRANS_NOCHAR' => $dataPSM->LOI_TRANS_NOCHAR,
                    'SKS_TRANS_NOCHAR' => $dataPSM->SKS_TRANS_NOCHAR,
                    'LOT_STOCK_ID_INT' => empty($inputDataAddendum['LOT_STOCK_ID_INT']) ? NULL : $dataPSM->LOT_STOCK_ID_INT,
                    'LOT_STOCK_NO' => empty($inputDataAddendum['LOT_STOCK_ID_INT']) ? NULL : $dataPSM->LOT_STOCK_NO,
                    'DEBTOR_ACCT_CHAR' => $dataPSM->DEBTOR_ACCT_CHAR,
                    // 'SHOP_NAME_CHAR' => $dataPSM->SHOP_NAME_CHAR,
                    'SHOP_NAME_CHAR' => $inputDataAddendum['SHOP_NAME_CHAR'],
                    'PSM_CATEGORY_ID_INT' => empty($dataLot->PSM_CATEGORY_ID_INT) ? $dataPSM->PSM_CATEGORY_ID_INT : $dataLot->PSM_CATEGORY_ID_INT,
                    // 'MD_TENANT_ID_INT' => $dataPSM->MD_TENANT_ID_INT,
                    'MD_TENANT_ID_INT' => $inputDataAddendum['MD_TENANT_ID_INT'],
                    'MD_SALES_TYPE_ID_INT'=>$inputDataAddendum['MD_SALES_TYPE_ID_INT'],
                    'PSM_TRANS_BOOKING_DATE' => $bookingDate,
                    'PSM_TRANS_START_DATE' => $startDate,
                    'PSM_TRANS_END_DATE' => $endDate,
                    'PSM_TRANS_FREQ_NUM' => $difMonth,
                    'PSM_TRANS_FREQ_DAY_NUM' => $difDays,
                    'PSM_TRANS_TIME_PERIOD_SCHED' => $inputDataAddendum['PSM_TRANS_TIME_PERIOD_SCHED'],
                    'PSM_TRANS_RENT_NUM' => empty($inputDataAddendum['PSM_TRANS_RENT_NUM']) ? 0 : $inputDataAddendum['PSM_TRANS_RENT_NUM'],
                    'PSM_TRANS_SC_NUM' => empty($inputDataAddendum['PSM_TRANS_SC_NUM']) ? 0 : $inputDataAddendum['PSM_TRANS_SC_NUM'],
                    'PSM_TRANS_DESCRIPTION' => $inputDataAddendum['PSM_TRANS_DESCRIPTION'],
                    'PSM_TRANS_NET_BEFORE_TAX' => $netbeforetax,
                    'PSM_TRANS_PPN' => $ppn,
                    'PSM_TRANS_PRICE' => $total,
                    'PSM_TRANS_UNEARN' => 0,
                    'PSM_TRANS_DP_PERSEN' => $inputDataAddendum['PSM_TRANS_DP_PERSEN'],
                    'PSM_TRANS_DP_NUM' => $downPayment,
                    'PSM_TRANS_DP_PERIOD' => $inputDataAddendum['PSM_TRANS_DP_PERIOD'],
                    'PSM_TRANS_DEPOSIT_MONTH' => 0,
                    // 'PSM_TRANS_DEPOSIT_TYPE' => $inputDataAddendum['PSM_TRANS_DEPOSIT_TYPE'],
                    // 'PSM_TRANS_DEPOSIT_NUM' => $inputDataAddendum['PSM_TRANS_DEPOSIT_NUM'],
                    // 'PSM_TRANS_DEPOSIT_DATE' => $inputDataAddendum['PSM_TRANS_DEPOSIT_DATE'],
                    'PSM_INVEST_NUM' => $inputDataAddendum['PSM_INVEST_NUM'],
                    'PSM_INVEST_RATE' => $inputDataAddendum['PSM_INVEST_RATE'],
                    'PSM_REVENUE_LOW_NUM' => $inputDataAddendum['PSM_REVENUE_LOW_NUM'],
                    'PSM_REVENUE_LOW_RATE' => $inputDataAddendum['PSM_REVENUE_LOW_RATE'],
                    'PSM_REVENUE_HIGH_NUM' => $inputDataAddendum['PSM_REVENUE_HIGH_NUM'],
                    'PSM_REVENUE_HIGH_RATE' => $inputDataAddendum['PSM_REVENUE_HIGH_RATE'],
                    'PSM_TRANS_GRASS_TYPE' => $inputDataAddendum['PSM_TRANS_GRASS_TYPE'],
                    'PSM_TRANS_GRASS_PERIOD' => $inputDataAddendum['PSM_TRANS_GRASS_PERIOD'],
                    // 'PSM_TRANS_GRASS_DATE' => ,
                    'PSM_TRANS_VA' => $inputDataAddendum['PSM_TRANS_VA'],
                    'INVOICE_UTIL_CHAR'=>$dataPSM->INVOICE_UTIL_CHAR,
                    'PSM_TRANS_EXP_STATUS_INT'=>$dataPSM->PSM_TRANS_EXP_STATUS_INT,
                    'IS_REVENUE_SHARING'=>$dataPSM->IS_REVENUE_SHARING,
                    'IS_AMORTIZATION'=>$dataPSM->IS_AMORTIZATION,
                    'PSM_TRANS_ADD_REMARK'=>$remark,
                    'PSM_TRANS_ADD_REQUEST_CHAR' => $userName,
                    'PSM_TRANS_ADD_REQUEST_DATE' => $date,
                    'PROJECT_NO_CHAR' => $project_no,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);

            \Session::flash('message', 'Saving Data Addendum '.$noAddendum.' Kontrak '.$inputDataAddendum['PSM_TRANS_NOCHAR']);
            $action = "ADD REQ ADDENDUM DATA";
            $description = 'Saving Data Addendum '.$noAddendum.' Kontrak '.$inputDataAddendum['PSM_TRANS_NOCHAR'];
            $this->saveToLogAddendum($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('marketing.leaseagreement.viewaddaddendum', [$inputDataAddendum['ADD_TYPE'], $inputDataAddendum['PSM_TRANS_ID_INT']])->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        if(empty($inputDataAddendum['LOT_STOCK_ID_INT'])) {
            return redirect()->route('marketing.leaseagreement.vieweditaddendum',[$inputDataAddendum['ADD_TYPE'], $lastIdAddendum])
                ->with('success',$description.' Successfully');
        }
        else {
            return redirect()->route('marketing.leaseagreement.vieweditdata',[$inputDataAddendum['PSM_TRANS_ID_INT']])
                ->with('success',$description.' Successfully');
        }
    }

    public function viewEditAddendum($ADD_TYPE,$PSM_TRANS_ADD_ID_INT){
        $project_no = session('current_project');

        $dataAddendum = DB::table('PSM_TRANS_ADDENDUM')
            ->where('PSM_TRANS_ADD_ID_INT','=',$PSM_TRANS_ADD_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataAddendum->PSM_TRANS_NOCHAR)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataAddendum->MD_TENANT_ID_INT)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->get();

        $categoryData = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        if(empty($dataAddendum->LOT_STOCK_NO) == false) {
            $isMultipleLot = \Str::contains($dataAddendum->LOT_STOCK_NO, ',');
            if($isMultipleLot) {
                $dataMultipleLot = \DB::table('PSM_TRANS_ADDENDUM_LOT')->where('PSM_TRANS_ADD_NOCHAR', $dataAddendum->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->get();
                $lotData = \DB::table('PSM_TRANS_ADDENDUM_LOT')->where('PSM_TRANS_ADD_NOCHAR', $dataAddendum->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->get();
            }
            else if(empty($dataAddendum->LOT_STOCK_ID_INT)) {
                $dataSingleLot = \DB::table('PSM_TRANS_ADDENDUM_LOT')->where('PSM_TRANS_ADD_NOCHAR', $dataAddendum->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();
                $lotData = \DB::table('PSM_TRANS_ADDENDUM_LOT')->where('PSM_TRANS_ADD_NOCHAR', $dataAddendum->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->get();
            }
            else {
                $lotData = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$dataAddendum->LOT_STOCK_ID_INT)
                    ->first();
            }

            $lotDataCurr = NULL;
        }
        else {
            if(Session::get('level')==11 || Session::get('level')==9 || Session::get('level')==99) {
                $lotData = DB::select("SELECT b.* FROM PSM_TRANS_ADDENDUM_LOT AS a
                    LEFT JOIN LOT_STOCK AS b ON b.LOT_STOCK_ID_INT = a.LOT_STOCK_ID_INT
                    WHERE a.PSM_TRANS_ADD_NOCHAR = '".$dataAddendum->PSM_TRANS_ADD_NOCHAR."' AND a.PROJECT_NO_CHAR = '".$project_no."'");

                $lotDataCurr = NULL;
            }
            else {
                $lotData = DB::select("SELECT b.* FROM PSM_TRANS_LOT AS a
                    LEFT JOIN LOT_STOCK AS b ON b.LOT_STOCK_ID_INT = a.LOT_STOCK_ID_INT
                    WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."' AND a.PROJECT_NO_CHAR = '".$project_no."'
                    UNION ALL
                    SELECT a.* FROM LOT_STOCK AS a
                    WHERE a.ON_RELEASE_STAT_INT = 1 AND a.ON_RENT_STAT_INT = 0
                    AND a.IS_DELETE = 0 AND a.PROJECT_NO_CHAR = '".$project_no."'");

                $lotDataCurrDB = DB::select("SELECT b.* FROM PSM_TRANS_LOT AS a
                    LEFT JOIN LOT_STOCK AS b ON b.LOT_STOCK_ID_INT = a.LOT_STOCK_ID_INT
                    WHERE a.PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."' AND a.PROJECT_NO_CHAR = '".$project_no."'");

                $lotDataCurr = array();
                foreach($lotDataCurrDB as $data) {
                    array_push($lotDataCurr, $data->LOT_STOCK_ID_INT);
                }
            }
        }

        $salesTypedata = DB::table("MD_SALES_TYPE")
            ->where('MD_SALES_TYPE_ID_INT','=',$dataAddendum->MD_SALES_TYPE_ID_INT)
            ->first();

        $dataSalesType = DB::table("MD_SALES_TYPE")
            ->where('IS_ACTIVE','=',1)
            ->get();

        $dataRentSCAmt = DB::select("SELECT a.* FROM PSM_TRANS_ADDENDUM_PRICE AS a WHERE a.PSM_TRANS_ADD_NOCHAR = '".$dataAddendum->PSM_TRANS_ADD_NOCHAR."' AND a.PROJECT_NO_CHAR = '".$project_no."'");
        
        if(Session::get('level')==11 || Session::get('level')==9 || Session::get('level')==99)
        {
            return View::make('page.leaseagreement.viewDataAddendum2',
                ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'salesTypedata'=>$salesTypedata,
                'tenantData'=>$tenantData,'categoryData'=>$categoryData,'ADD_TYPE'=>$ADD_TYPE,
                'lotData'=>$lotData,'dataSalesType'=>$dataSalesType,'dataAddendum'=>$dataAddendum,
                'dataRentSCAmt'=>$dataRentSCAmt,'dataTenant'=>$dataTenant]);
        }
        else
        {
            return View::make('page.leaseagreement.editDataAddendum2',
                ['dataPSM'=>$dataPSM,'project_no'=>$project_no,'salesTypedata'=>$salesTypedata,
                'tenantData'=>$tenantData,'categoryData'=>$categoryData,'ADD_TYPE'=>$ADD_TYPE,
                'lotData'=>$lotData,'dataSalesType'=>$dataSalesType,'dataAddendum'=>$dataAddendum,
                'dataRentSCAmt'=>$dataRentSCAmt,'lotDataCurr'=>$lotDataCurr,'dataTenant'=>$dataTenant]);
        }
    }

    public function saveEditDataAddendum(Requests\Marketing\AddDataAddendumRequest $requestPSM){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $inputDataAddendum = $requestPSM->all();
        $date = Carbon::parse(Carbon::now());
        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

        if(empty($inputDataAddendum['LOT_STOCK_ID_INT'])) {
            $dataPSMLotCount = \DB::table('PSM_TRANS_ADDENDUM_LOT')->where('PSM_TRANS_ADD_NOCHAR', $inputDataAddendum['PSM_TRANS_ADD_NOCHAR'])->where('PROJECT_NO_CHAR', $project_no)->count();
            if ($dataPSMLotCount <= 0)
            {
                return redirect()->route('marketing.leaseagreement.viewaddaddendum',[$inputDataAddendum['ADD_TYPE'],$inputDataAddendum['PSM_TRANS_ID_INT']])
                    ->with('error','You Lot Data not Found, Process Fail...');
            }
        }
        else {
            $cekDataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$inputDataAddendum['LOT_STOCK_ID_INT'])
                ->count();

            if ($cekDataLot > 0)
            {
                $dataLot = DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$inputDataAddendum['LOT_STOCK_ID_INT'])
                    ->first();
            }
            else
            {
                return redirect()->route('marketing.leaseagreement.vieweditaddendum',[$inputDataAddendum['ADD_TYPE'],$inputDataAddendum['PSM_TRANS_ID_INT']])
                    ->with('error','You Lot Data not Found, Process Fail...');
            }
        }

        if ($inputDataAddendum['ADD_TYPE'] == 'RVS')
        {
            $md_add_type = 3;
            $remark = 'REVISION';
        }
        else
        {
            $md_add_type = 1;
            $remark = 'RENEWAL';
        }

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$inputDataAddendum['PSM_TRANS_NOCHAR'])
            ->first();

        $dataPSMAdd = DB::table('PSM_TRANS_ADDENDUM')
            ->where('PSM_TRANS_ADD_NOCHAR','=',$inputDataAddendum['PSM_TRANS_ADD_NOCHAR'])
            ->first();

        $bookingDate = date_create($inputDataAddendum['PSM_TRANS_BOOKING_DATE']);
        $startDate = date_create($inputDataAddendum['PSM_TRANS_START_DATE']);
        $endDate = date_create($inputDataAddendum['PSM_TRANS_END_DATE']);

        $freq_num = $endDate->diff($startDate);
        $difMonth = (int)($freq_num->days / 30);
        $freq_day_num = $difMonth * 30;
        $difDays = (int)($freq_num->days) - (int)($freq_day_num);

        $netbeforetax = str_replace('.','',$inputDataAddendum['PSM_TRANS_NET_BEFORE_TAX']);
        $ppn = str_replace('.','',$inputDataAddendum['PSM_TRANS_PPN']);
        $total = str_replace('.','',$inputDataAddendum['PSM_TRANS_PRICE']);

        $downPayment = ($inputDataAddendum['PSM_TRANS_DP_PERSEN']/100) * $netbeforetax;

        DB::table('PSM_TRANS_ADDENDUM')
            ->where('PSM_TRANS_ADD_NOCHAR','=',$inputDataAddendum['PSM_TRANS_ADD_NOCHAR'])
            ->update([
                'MD_ADD_TYPE_ID_INT'=>$md_add_type,
                'PSM_ADD_DOC_TYPE'=>$inputDataAddendum['ADD_TYPE'],
                'PSM_TRANS_NOCHAR' => $inputDataAddendum['PSM_TRANS_NOCHAR'],
                'LOI_TRANS_NOCHAR' => $dataPSM->LOI_TRANS_NOCHAR,
                'SKS_TRANS_NOCHAR' => $dataPSM->SKS_TRANS_NOCHAR,
                'LOT_STOCK_ID_INT' => empty($dataPSM->LOT_STOCK_ID_INT) ? $dataPSMAdd->LOT_STOCK_ID_INT : $dataPSM->LOT_STOCK_ID_INT,
                'LOT_STOCK_NO' => empty($dataPSM->LOT_STOCK_ID_INT) ? $dataPSMAdd->LOT_STOCK_NO : $dataPSM->LOT_STOCK_NO,
                'DEBTOR_ACCT_CHAR' => $dataPSM->DEBTOR_ACCT_CHAR,
                // 'SHOP_NAME_CHAR' => $dataPSM->SHOP_NAME_CHAR,
                'SHOP_NAME_CHAR' => $inputDataAddendum['SHOP_NAME_CHAR'],
                'PSM_CATEGORY_ID_INT' => empty($dataLot->PSM_CATEGORY_ID_INT) ? $dataPSMAdd->PSM_CATEGORY_ID_INT : $dataLot->PSM_CATEGORY_ID_INT,
                // 'MD_TENANT_ID_INT' => $dataPSM->MD_TENANT_ID_INT,
                'MD_TENANT_ID_INT' => $inputDataAddendum['MD_TENANT_ID_INT'],
                'MD_SALES_TYPE_ID_INT'=>$inputDataAddendum['MD_SALES_TYPE_ID_INT'],
                'PSM_TRANS_BOOKING_DATE' => $bookingDate,
                'PSM_TRANS_START_DATE' => $startDate,
                'PSM_TRANS_END_DATE' => $endDate,
                'PSM_TRANS_FREQ_NUM' => $difMonth,
                'PSM_TRANS_FREQ_DAY_NUM' => $difDays,
                'PSM_TRANS_TIME_PERIOD_SCHED' => $inputDataAddendum['PSM_TRANS_TIME_PERIOD_SCHED'],
                'PSM_TRANS_RENT_NUM' => empty($inputDataAddendum['PSM_TRANS_RENT_NUM']) ? 0 : $inputDataAddendum['PSM_TRANS_RENT_NUM'],
                'PSM_TRANS_SC_NUM' => empty($inputDataAddendum['PSM_TRANS_SC_NUM']) ? 0 : $inputDataAddendum['PSM_TRANS_SC_NUM'],
                'PSM_TRANS_DESCRIPTION' => $inputDataAddendum['PSM_TRANS_DESCRIPTION'],
                'PSM_TRANS_NET_BEFORE_TAX' => $netbeforetax,
                'PSM_TRANS_PPN' => $ppn,
                'PSM_TRANS_PRICE' => $total,
                'PSM_TRANS_UNEARN' => 0,
                'PSM_TRANS_DP_PERSEN' => $inputDataAddendum['PSM_TRANS_DP_PERSEN'],
                'PSM_TRANS_DP_NUM' => $downPayment,
                'PSM_TRANS_DP_PERIOD' => $inputDataAddendum['PSM_TRANS_DP_PERIOD'],
                'PSM_TRANS_DEPOSIT_MONTH' => 0,
                // 'PSM_TRANS_DEPOSIT_TYPE' => $inputDataAddendum['PSM_TRANS_DEPOSIT_TYPE'],
                // 'PSM_TRANS_DEPOSIT_NUM' => $inputDataAddendum['PSM_TRANS_DEPOSIT_NUM'],
                // 'PSM_TRANS_DEPOSIT_DATE' => $inputDataAddendum['PSM_TRANS_DEPOSIT_DATE'],
                'PSM_INVEST_NUM' => $inputDataAddendum['PSM_INVEST_NUM'],
                'PSM_INVEST_RATE' => $inputDataAddendum['PSM_INVEST_RATE'],
                'PSM_REVENUE_LOW_NUM' => $inputDataAddendum['PSM_REVENUE_LOW_NUM'],
                'PSM_REVENUE_LOW_RATE' => $inputDataAddendum['PSM_REVENUE_LOW_RATE'],
                'PSM_REVENUE_HIGH_NUM' => $inputDataAddendum['PSM_REVENUE_HIGH_NUM'],
                'PSM_REVENUE_HIGH_RATE' => $inputDataAddendum['PSM_REVENUE_HIGH_RATE'],
                'PSM_TRANS_GRASS_TYPE' => $inputDataAddendum['PSM_TRANS_GRASS_TYPE'],
                'PSM_TRANS_GRASS_PERIOD' => $inputDataAddendum['PSM_TRANS_GRASS_PERIOD'],
                //'PSM_TRANS_GRASS_DATE' => ,
                'PSM_TRANS_VA' => $inputDataAddendum['PSM_TRANS_VA'],
                'INVOICE_UTIL_CHAR'=>$dataPSM->INVOICE_UTIL_CHAR,
                'PSM_TRANS_EXP_STATUS_INT'=>$dataPSM->PSM_TRANS_EXP_STATUS_INT,
                'IS_REVENUE_SHARING'=>$dataPSM->IS_REVENUE_SHARING,
                'IS_AMORTIZATION'=>$dataPSM->IS_AMORTIZATION,
                'PSM_TRANS_ADD_REMARK'=>$remark,
                'PSM_TRANS_ADD_REQUEST_CHAR' => $userName,
                'PSM_TRANS_ADD_REQUEST_DATE' => $date,
                'PROJECT_NO_CHAR' => $project_no,
                'created_at' => $date,
                'updated_at' => $date
            ]);

        \Session::flash('message', 'Update Data Addendum '.$inputDataAddendum['PSM_TRANS_ADD_NOCHAR'].' Kontrak '.$inputDataAddendum['PSM_TRANS_NOCHAR']);
        $action = "UPDATE REQ ADDENDUM DATA";
        $description = 'Update Data Addendum '.$inputDataAddendum['PSM_TRANS_ADD_NOCHAR'].' Kontrak '.$inputDataAddendum['PSM_TRANS_NOCHAR'];
        $this->saveToLogAddendum($action, $description);

        return redirect()->route('marketing.leaseagreement.vieweditdata',[$inputDataAddendum['PSM_TRANS_ID_INT']])
            ->with('success',$description.' Successfully');
    }

    public function approveDataAddendum($PSM_TRANS_ADD_ID_INT){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $date = Carbon::parse(Carbon::now());

        $dataAddendum = DB::table('PSM_TRANS_ADDENDUM')
            ->where('PSM_TRANS_ADD_ID_INT','=',$PSM_TRANS_ADD_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataAddendum->PSM_TRANS_NOCHAR)
            ->first();

        if ($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
        {
            $ACTION = 'REVISION';
        }
        else
        {
            $ACTION = 'RENEWAL';
        }

        DB::table('PSM_TRANS_HIST')
            ->insert([
                'PSM_TRANS_NOCHAR'=>$dataPSM->PSM_TRANS_NOCHAR,
                'LOI_TRANS_NOCHAR'=>$dataPSM->LOI_TRANS_NOCHAR,
                'SKS_TRANS_NOCHAR'=>$dataPSM->SKS_TRANS_NOCHAR,
                'LOT_STOCK_ID_INT'=>$dataPSM->LOT_STOCK_ID_INT,
                'LOT_STOCK_NO'=>$dataPSM->LOT_STOCK_NO,
                'DEBTOR_ACCT_CHAR'=>$dataPSM->DEBTOR_ACCT_CHAR,
                'SHOP_NAME_CHAR'=>$dataPSM->SHOP_NAME_CHAR,
                'MD_TENANT_ID_INT'=>$dataPSM->MD_TENANT_ID_INT,
                'PSM_TRANS_TYPE'=>$dataPSM->PSM_TRANS_TYPE,
                'PSM_TRANS_BOOKING_DATE'=>$dataPSM->PSM_TRANS_BOOKING_DATE,
                'PSM_TRANS_START_DATE'=>$dataPSM->PSM_TRANS_START_DATE,
                'PSM_TRANS_END_DATE'=>$dataPSM->PSM_TRANS_END_DATE,
                'PSM_TRANS_FREQ_NUM'=>$dataPSM->PSM_TRANS_FREQ_NUM,
                'PSM_TRANS_FREQ_DAY_NUM'=>$dataPSM->PSM_TRANS_FREQ_DAY_NUM,
                'PSM_TRANS_TIME_PERIOD_SCHED'=>$dataPSM->PSM_TRANS_TIME_PERIOD_SCHED,
                'PSM_TRANS_RENT_NUM'=>$dataPSM->PSM_TRANS_RENT_NUM,
                'PSM_TRANS_SC_NUM'=>$dataPSM->PSM_TRANS_SC_NUM,
                'PSM_TRANS_DESCRIPTION'=>$dataPSM->PSM_TRANS_DESCRIPTION,
                'PSM_TRANS_NET_BEFORE_TAX'=>$dataPSM->PSM_TRANS_NET_BEFORE_TAX,
                'PSM_TRANS_PPN'=>$dataPSM->PSM_TRANS_PPN,
                'PSM_TRANS_PRICE'=>$dataPSM->PSM_TRANS_PRICE,
                'PSM_TRANS_UNEARN'=>$dataPSM->PSM_TRANS_UNEARN,
                'PSM_TRANS_DP_PERSEN'=>$dataPSM->PSM_TRANS_DP_PERSEN,
                'PSM_TRANS_DP_NUM'=>$dataPSM->PSM_TRANS_DP_NUM,
                'PSM_TRANS_DP_PERIOD'=>$dataPSM->PSM_TRANS_DP_PERIOD,
                'PSM_TRANS_DEPOSIT_MONTH'=>$dataPSM->PSM_TRANS_DEPOSIT_MONTH,
                'PSM_TRANS_DEPOSIT_TYPE'=>$dataPSM->PSM_TRANS_DEPOSIT_TYPE,
                'PSM_TRANS_DEPOSIT_NUM'=>$dataPSM->PSM_TRANS_DEPOSIT_NUM,
                'PSM_TRANS_DEPOSIT_DATE'=>$dataPSM->PSM_TRANS_DEPOSIT_DATE,
                'PSM_INVEST_NUM'=>$dataPSM->PSM_INVEST_NUM,
                'PSM_INVEST_RATE'=>$dataPSM->PSM_INVEST_RATE,
                'PSM_REVENUE_LOW_NUM'=>$dataPSM->PSM_REVENUE_LOW_NUM,
                'PSM_REVENUE_LOW_RATE'=>$dataPSM->PSM_REVENUE_LOW_RATE,
                'PSM_REVENUE_HIGH_NUM'=>$dataPSM->PSM_REVENUE_HIGH_NUM,
                'PSM_REVENUE_HIGH_RATE'=>$dataPSM->PSM_REVENUE_HIGH_RATE,
                'PSM_TRANS_STATUS_INT'=>$dataPSM->PSM_TRANS_STATUS_INT,
                'PSM_TRANS_GENERATE_BILLING'=>$dataPSM->PSM_TRANS_GENERATE_BILLING,
                'PSM_TRANS_BILLING_INT'=>$dataPSM->PSM_TRANS_BILLING_INT,
                'PSM_TRANS_DP_BILLING_INT'=>$dataPSM->PSM_TRANS_DP_BILLING_INT,
                'PSM_TRANS_GRASS_TYPE'=>$dataPSM->PSM_TRANS_GRASS_TYPE,
                'PSM_TRANS_GRASS_PERIOD'=>$dataPSM->PSM_TRANS_GRASS_PERIOD,
                'PSM_TRANS_GRASS_DATE'=>$dataPSM->PSM_TRANS_GRASS_DATE,
                'PSM_TRANS_VA'=>$dataPSM->PSM_TRANS_VA,
                'PSM_TRANS_REQUEST_CHAR'=>$dataPSM->PSM_TRANS_REQUEST_CHAR,
                'PSM_TRANS_REQUEST_DATE'=>$dataPSM->PSM_TRANS_REQUEST_DATE,
                'PROJECT_NO_CHAR'=>$dataPSM->PROJECT_NO_CHAR,
                'ACTION'=>$ACTION,
                'USER_BY'=>$userName,
                'created_at'=>$date,
                'updated_at'=>$date
            ]);

        $startDate = date_create($dataAddendum->PSM_TRANS_START_DATE);
        $endDate = date_create($dataAddendum->PSM_TRANS_END_DATE);

        $diffDate = date_diff($startDate,$endDate);
        $intDiffdate = (int)$diffDate->format("%a");

        $month = (int)($intDiffdate/30);
        if ($dataAddendum->PSM_TRANS_GRASS_TYPE == '' || $dataAddendum->PSM_TRANS_GRASS_TYPE == 'NONE')
        {
            $grassPeriod = 0;
        }
        else
        {
            $grassPeriod = (int)$dataAddendum->PSM_TRANS_GRASS_PERIOD;
        }

        if ($month == 0 && $grassPeriod == 0)
        {
            $dataIncome = round(($dataAddendum->PSM_TRANS_NET_BEFORE_TAX));
        }
        else
        {
            $dataIncome = round(($dataAddendum->PSM_TRANS_NET_BEFORE_TAX / ($month + $grassPeriod)));
        }

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$dataPSM->PSM_TRANS_ID_INT)
            ->update([
                // 'LOI_TRANS_NOCHAR'=>,
                // 'SKS_TRANS_NOCHAR'=>,
               'LOT_STOCK_ID_INT'=>$dataAddendum->LOT_STOCK_ID_INT,
               'LOT_STOCK_NO'=>$dataAddendum->LOT_STOCK_NO,
                // 'DEBTOR_ACCT_CHAR'=>,
                'SHOP_NAME_CHAR'=>$dataAddendum->SHOP_NAME_CHAR,
                // 'PSM_CATEGORY_ID_INT'=>,
                'MD_TENANT_ID_INT'=>$dataAddendum->MD_TENANT_ID_INT,
                // 'PSM_TRANS_TYPE'=>,
                'MD_SALES_TYPE_ID_INT'=>$dataAddendum->MD_SALES_TYPE_ID_INT,
                // 'PSM_TRANS_BOOKING_DATE'=>,
                // 'PSM_TRANS_SCHEDULE_DATE'=>,
                'PSM_TRANS_START_DATE'=>$dataAddendum->PSM_TRANS_START_DATE,
                'PSM_TRANS_END_DATE'=>$dataAddendum->PSM_TRANS_END_DATE,
                'PSM_TRANS_FREQ_NUM'=>$dataAddendum->PSM_TRANS_FREQ_NUM,
                'PSM_TRANS_FREQ_DAY_NUM'=>$dataAddendum->PSM_TRANS_FREQ_DAY_NUM,
                'PSM_TRANS_TIME_PERIOD_SCHED'=>$dataAddendum->PSM_TRANS_TIME_PERIOD_SCHED,
                'PSM_TRANS_RENT_NUM'=>$dataAddendum->PSM_TRANS_RENT_NUM,
                'PSM_TRANS_SC_NUM'=>$dataAddendum->PSM_TRANS_SC_NUM,
                'PSM_TRANS_DESCRIPTION'=>$dataAddendum->PSM_TRANS_DESCRIPTION,
                'PSM_TRANS_DISKON_NUM'=>$dataAddendum->PSM_TRANS_DISKON_NUM,
                'PSM_TRANS_DISKON_PERSEN'=>$dataAddendum->PSM_TRANS_DISKON_PERSEN,
                'PSM_TRANS_NET_BEFORE_TAX'=>$dataAddendum->PSM_TRANS_NET_BEFORE_TAX,
                'PSM_TRANS_PPN'=>$dataAddendum->PSM_TRANS_PPN,
                'PSM_TRANS_PRICE'=>$dataAddendum->PSM_TRANS_PRICE,
                'PSM_TRANS_UNEARN'=>$dataIncome,
                'PSM_TRANS_DP_PERSEN'=>$dataAddendum->PSM_TRANS_DP_PERSEN,
                'PSM_TRANS_DP_NUM'=>$dataAddendum->PSM_TRANS_DP_NUM,
                'PSM_TRANS_DP_PERIOD'=>$dataAddendum->PSM_TRANS_DP_PERIOD,
                // 'PSM_TRANS_DEPOSIT_MONTH'=>$dataAddendum->PSM_TRANS_DEPOSIT_MONTH,
                // 'PSM_TRANS_DEPOSIT_TYPE'=>$dataAddendum->PSM_TRANS_DEPOSIT_TYPE,
                // 'PSM_TRANS_DEPOSIT_NUM'=>$dataAddendum->PSM_TRANS_DEPOSIT_NUM,
                // 'PSM_TRANS_DEPOSIT_DATE'=>$dataAddendum->PSM_TRANS_DEPOSIT_DATE,
                'PSM_INVEST_NUM'=>$dataAddendum->PSM_INVEST_NUM,
                'PSM_INVEST_RATE'=>$dataAddendum->PSM_INVEST_RATE,
                'PSM_MIN_AMT'=>$dataAddendum->PSM_MIN_AMT,
                'PSM_REVENUE_LOW_NUM'=>$dataAddendum->PSM_REVENUE_LOW_NUM,
                'PSM_REVENUE_LOW_RATE'=>$dataAddendum->PSM_REVENUE_LOW_RATE,
                'PSM_REVENUE_HIGH_NUM'=>$dataAddendum->PSM_REVENUE_HIGH_NUM,
                'PSM_REVENUE_HIGH_RATE'=>$dataAddendum->PSM_REVENUE_HIGH_RATE,
                'PSM_TRANS_GENERATE_BILLING'=>$dataAddendum->PSM_TRANS_GENERATE_BILLING,
                'PSM_TRANS_BILLING_INT'=>0,
                'PSM_TRANS_DP_BILLING_INT'=>1,
                'PSM_TRANS_GRASS_TYPE'=>$dataAddendum->PSM_TRANS_GRASS_TYPE,
                'PSM_TRANS_GRASS_PERIOD'=>$dataAddendum->PSM_TRANS_GRASS_PERIOD,
                'PSM_TRANS_GRASS_DATE'=>$dataAddendum->PSM_TRANS_GRASS_DATE,
                'PSM_TRANS_VA'=>$dataAddendum->PSM_TRANS_VA,
                // 'INVOICE_UTIL_CHAR'=>$dataAddendum->INVOICE_UTIL_CHAR,
                'PSM_TRANS_EXP_STATUS_INT'=>0,
                'IS_REVENUE_SHARING'=>0,
                'IS_AMORTIZATION'=>0,
                'PSM_TRANS_REQUEST_CHAR'=>$dataAddendum->PSM_TRANS_ADD_REQUEST_CHAR,
                'PSM_TRANS_REQUEST_DATE'=>$dataAddendum->PSM_TRANS_ADD_REQUEST_DATE,
                'PSM_TRANS_APPR_CHAR'=>$userName,
                'PSM_TRANS_APPR_DATE'=>$date,
                'updated_at'=>$date
            ]);

        DB::statement("INSERT INTO PSM_SCHEDULE_HIST
                        Select *
                        from PSM_SCHEDULE
                        where PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'");

        DB::statement("DELETE from PSM_SCHEDULE
                        where PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'
                        and SCHEDULE_STATUS_INT NOT IN (1)");

        DB::table('PSM_TRANS_ADDENDUM')
            ->where('PSM_TRANS_ADD_ID_INT','=',$PSM_TRANS_ADD_ID_INT)
            ->update([
                'PSM_TRANS_ADD_STATUS_INT'=>2,
                'PSM_TRANS_ADD_APPR_CHAR'=>$userName,
                'PSM_TRANS_ADD_APPR_DATE'=>$date,
                'updated_at'=>$date
            ]);

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            try {
                \DB::beginTransaction();

                // Mengubah Status Lot Dari Addendum Menjadi Disewakan & Mengubah Status Lot Dari Kontrak Real (PSM_TRANS) Menjadi Tidak Disewakan
                $dataLotPSM = \DB::table("PSM_TRANS_LOT")->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $dataPSM->PROJECT_NO_CHAR)->get();
                $dataLotAddendum = \DB::table("PSM_TRANS_ADDENDUM_LOT")->where('PSM_TRANS_ADD_NOCHAR', $dataAddendum->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $dataPSM->PROJECT_NO_CHAR)->get();
                foreach($dataLotPSM as $data) {
                    DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT','=',$data->LOT_STOCK_ID_INT)
                    ->update([
                        'ON_RENT_STAT_INT'=>0
                    ]);
                }
                foreach($dataLotAddendum as $data) {
                    DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT','=',$data->LOT_STOCK_ID_INT)
                    ->update([
                        'ON_RENT_STAT_INT'=>1
                    ]);
                }

                // Memindahkan Lot dari Addendum ke Live
                \DB::statement("INSERT INTO PSM_TRANS_LOT_HIST
                    Select *
                    from PSM_TRANS_LOT
                    where PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'");

                \DB::statement("DELETE from PSM_TRANS_LOT
                    where PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'");

                \DB::statement("
                    SELECT *
                    INTO #PSM_TRANS_ADDENDUM_LOT from PSM_TRANS_ADDENDUM_LOT
                    WHERE PSM_TRANS_ADD_NOCHAR = '".$dataAddendum->PSM_TRANS_ADD_NOCHAR."'
                    ALTER TABLE #PSM_TRANS_ADDENDUM_LOT DROP COLUMN PSM_TRANS_ADD_LOT_ID_INT
                    ALTER TABLE #PSM_TRANS_ADDENDUM_LOT DROP COLUMN PSM_TRANS_ADD_NOCHAR

                    INSERT INTO PSM_TRANS_LOT
                    Select *
                    from #PSM_TRANS_ADDENDUM_LOT
                    DROP TABLE #PSM_TRANS_ADDENDUM_LOT");

                // Memindahkan Price dari Addendum ke Live
                \DB::statement("INSERT INTO PSM_TRANS_PRICE_HIST
                    Select *
                    from PSM_TRANS_PRICE
                    where PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'");

                \DB::statement("DELETE from PSM_TRANS_PRICE
                    where PSM_TRANS_NOCHAR = '".$dataPSM->PSM_TRANS_NOCHAR."'");

                \DB::statement("
                    SELECT *
                    INTO #PSM_TRANS_ADDENDUM_PRICE from PSM_TRANS_ADDENDUM_PRICE
                    WHERE PSM_TRANS_ADD_NOCHAR = '".$dataAddendum->PSM_TRANS_ADD_NOCHAR."'
                    ALTER TABLE #PSM_TRANS_ADDENDUM_PRICE DROP COLUMN PSM_TRANS_ADD_PRICE_ID_INT
                    ALTER TABLE #PSM_TRANS_ADDENDUM_PRICE DROP COLUMN PSM_TRANS_ADD_NOCHAR

                    INSERT INTO PSM_TRANS_PRICE
                    Select *
                    from #PSM_TRANS_ADDENDUM_PRICE
                    DROP TABLE #PSM_TRANS_ADDENDUM_PRICE");

                \DB::commit();
            } catch (QueryException $ex) {
                \DB::rollback();
                return redirect()->route('marketing.leaseagreement.vieweditdata', [$dataPSM->PSM_TRANS_ID_INT])->with('error', 'Failed approve data, errmsg : ' . $ex);
            }
        }

        $action = "APPROVE ADDENDUM DATA";
        $description = 'Approve Addendum Data : '.$dataAddendum->PSM_TRANS_ADD_NOCHAR.' Kontrak'.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.vieweditdata',[$dataPSM->PSM_TRANS_ID_INT])
            ->with('success',$description);
    }

    public function cancelDataAddendum($PSM_TRANS_ADD_ID_INT){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $date = Carbon::parse(Carbon::now());

        $dataAddendum = DB::table('PSM_TRANS_ADDENDUM')
            ->where('PSM_TRANS_ADD_ID_INT','=',$PSM_TRANS_ADD_ID_INT)
            ->first();

        $dataPSM = \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $dataAddendum->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();

        DB::table('PSM_TRANS_ADDENDUM')
            ->where('PSM_TRANS_ADD_ID_INT','=',$PSM_TRANS_ADD_ID_INT)
            ->update([
                'PSM_TRANS_ADD_STATUS_INT'=>0,
                'PSM_TRANS_ADD_APPR_CHAR'=>$userName,
                'PSM_TRANS_ADD_APPR_DATE'=>$date,
                'updated_at'=>$date
            ]);

        $action = "CANCEL ADDENDUM DATA";
        $description = 'Cancel Addendum Data : '.$dataAddendum->PSM_TRANS_ADD_NOCHAR.' Kontrak'.$dataPSM->PSM_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.leaseagreement.vieweditdata',[$dataPSM->PSM_TRANS_ID_INT])
            ->with('success',$description);
    }

    public function insertRentSCDiscAdd(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        try {
            \DB::beginTransaction();

            $dataPSM = \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_NOCHAR', $request->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();
            if($request->PSM_TRANS_DISKON_NUM == 0) {
                $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX + $dataPSM->PSM_TRANS_DISKON_NUM;
                $ppn = $dpp * $dataProject->PPNBM_NUM;
                $total = $dpp + $ppn;

                \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_NOCHAR', $request->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'PSM_TRANS_DISKON_PERSEN' => 0,
                    'PSM_TRANS_DISKON_NUM' => 0,
                    'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                    'PSM_TRANS_PPN' => $ppn,
                    'PSM_TRANS_PRICE' => $total
                ]);
            }
            else {
                $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX - $request->PSM_TRANS_DISKON_NUM;
                $ppn = $dpp * $dataProject->PPNBM_NUM;
                $total = $dpp + $ppn;

                \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_NOCHAR', $request->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'PSM_TRANS_DISKON_PERSEN' => $request->PSM_TRANS_DISKON_PERSEN,
                    'PSM_TRANS_DISKON_NUM' => $request->PSM_TRANS_DISKON_NUM,
                    'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                    'PSM_TRANS_PPN' => $ppn,
                    'PSM_TRANS_PRICE' => $total
                ]);
            }

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Insert Item']);
        }

        $action = "INSERT DATA DISCOUNT ADDENDUM";
        $description = 'Insert Data Discount Addendum with Addendum ' . $request->PSM_TRANS_ADD_NOCHAR . ' Lease '.$request->PSM_TRANS_NOCHAR;
        $this->saveToLogRentSCDiscAdd($action, $description);
        return response()->json(['Success' => 'Berhasil Insert Item']);
    }

    public function insertRentSCDisc(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        try {
            \DB::beginTransaction();

            $dataPSM = \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $request->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();
            if($request->PSM_TRANS_DISKON_NUM == 0) {
                $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX + $dataPSM->PSM_TRANS_DISKON_NUM;
                $ppn = $dpp * $dataProject->PPNBM_NUM;
                $total = $dpp + $ppn;

                \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $request->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'PSM_TRANS_DISKON_PERSEN' => 0,
                    'PSM_TRANS_DISKON_NUM' => 0,
                    'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                    'PSM_TRANS_PPN' => $ppn,
                    'PSM_TRANS_PRICE' => $total
                ]);
            }
            else {
                $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX - $request->PSM_TRANS_DISKON_NUM;
                $ppn = $dpp * $dataProject->PPNBM_NUM;
                $total = $dpp + $ppn;

                \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $request->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'PSM_TRANS_DISKON_PERSEN' => $request->PSM_TRANS_DISKON_PERSEN,
                    'PSM_TRANS_DISKON_NUM' => $request->PSM_TRANS_DISKON_NUM,
                    'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                    'PSM_TRANS_PPN' => $ppn,
                    'PSM_TRANS_PRICE' => $total
                ]);
            }

            $action = "INSERT DATA DISCOUNT";
            $description = 'Insert Data Discount with Lease : '.$request->PSM_TRANS_NOCHAR;
            $this->saveToLogRentSCDisc($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Insert Item']);
        }

        return response()->json(['Success' => 'Berhasil Insert Item']);
    }

    public function deleteItemRentSCAmtAdd(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        try {
            \DB::beginTransaction();

            // Ambil data PSM_TRANS_ADDENDUM_PRICE
            $dataPSMPrice = \DB::table('PSM_TRANS_ADDENDUM_PRICE')
                ->where('PSM_TRANS_ADD_PRICE_ID_INT', $request->PSM_TRANS_ADD_PRICE_ID_INT)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->first();

            // Update data PSM_TRANS_ADDENDUM
            $dataLotSqm = \DB::table('PSM_TRANS_ADDENDUM_LOT')
                ->where('PSM_TRANS_ADD_NOCHAR', $dataPSMPrice->PSM_TRANS_ADD_NOCHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->sum('LOT_STOCK_SQM');

            $priceRent = $dataPSMPrice->PSM_TRANS_PRICE_RENT_NUM * $dataLotSqm;

            $dataPSM = \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_NOCHAR', $dataPSMPrice->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX - $priceRent;
            $ppn = $dpp * $dataProject->PPNBM_NUM;
            $total = $dpp + $ppn;

            \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_NOCHAR', $dataPSMPrice->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                'PSM_TRANS_PPN' => $ppn,
                'PSM_TRANS_PRICE' => $total
            ]);

            // Delete data PSM_TRANS_ADDENDUM_PRICE
            \DB::table('PSM_TRANS_ADDENDUM_PRICE')
                ->where('PSM_TRANS_ADD_PRICE_ID_INT', $request->PSM_TRANS_ADD_PRICE_ID_INT)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->delete();

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Delete Item']);
        }

        $action = "DELETE DATA RENT AMOUNT AND SERVICE CHARGE AMOUNT ADDENDUM";
        $description = 'Delete Data Rent Amount and Service Charge Amount Addendum with Lease : '.$dataPSMPrice->PSM_TRANS_NOCHAR;
        $this->saveToLogRentSCAmtAdd($action, $description);
        return response()->json(['Success' => 'Berhasil Delete Item']);
    }

    public function deleteItemRentSCAmt(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        try {
            \DB::beginTransaction();

            // Ambil data PSM_TRANS_PRICE
            $dataPSMPrice = \DB::table('PSM_TRANS_PRICE')
                ->where('PSM_TRANS_PRICE_ID_INT', $request->PSM_TRANS_PRICE_ID_INT)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->first();

            // Update data PSM_TRANS
            $dataLotSqm = \DB::table('PSM_TRANS_LOT')
                ->where('PSM_TRANS_NOCHAR', $dataPSMPrice->PSM_TRANS_NOCHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->sum('LOT_STOCK_SQM');

            $priceRent = $dataPSMPrice->PSM_TRANS_PRICE_RENT_NUM * $dataLotSqm;

            $dataPSM = \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $dataPSMPrice->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX - $priceRent;
            $ppn = $dpp * $dataProject->PPNBM_NUM;
            $total = $dpp + $ppn;

            \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $dataPSMPrice->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                'PSM_TRANS_PPN' => $ppn,
                'PSM_TRANS_PRICE' => $total
            ]);

            // Delete data PSM_TRANS_PRICE
            \DB::table('PSM_TRANS_PRICE')
                ->where('PSM_TRANS_PRICE_ID_INT', $request->PSM_TRANS_PRICE_ID_INT)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->delete();

            $action = "DELETE DATA RENT AMOUNT AND SERVICE CHARGE AMOUNT";
            $description = 'Delete Data Rent Amount and Service Charge Amount with Lease : '.$dataPSMPrice->PSM_TRANS_NOCHAR;
            $this->saveToLogRentSCAmt($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Delete Item']);
        }

        return response()->json(['Success' => 'Berhasil Delete Item']);
    }

    public function saveEditSqmRtAddendum(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            DB::table('PSM_TRANS_ADDENDUM_LOT')->where('PSM_TRANS_ADD_LOT_ID_INT', $request->SQM_RT_ID)->update([
                'LOT_STOCK_SQM' => $request->SQM_RT_NUM,
                'updated_at' => $dateNow
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('marketing.leaseagreement.vieweditaddendum', [$request->ADD_TYPE_PSM_RT, $request->ID_PSM_RT])->with('error', "Gagal Edit SQM RT");
        }

        $action = "UPDATE DATA SQM RT ADDENDUM";
        $description = 'Update Data SQM RT with Addendum : '.$request->ID_PSM_RT_NOCHAR;
        $this->saveToLogRentSCLotAdd($action, $description);
        return redirect()->route('marketing.leaseagreement.vieweditaddendum', [$request->ADD_TYPE_PSM_RT, $request->ID_PSM_RT]);
    }

    public function saveEditSqmScAddendum(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            DB::table('PSM_TRANS_ADDENDUM_LOT')->where('PSM_TRANS_ADD_LOT_ID_INT', $request->SQM_SC_ID)->update([
                'LOT_STOCK_SQM_SC' => $request->SQM_SC_NUM,
                'updated_at' => $dateNow
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('marketing.leaseagreement.vieweditaddendum', [$request->ADD_TYPE_PSM_SC, $request->ID_PSM_SC])->with('error', "Gagal Edit SQM SC");
        }

        $action = "UPDATE DATA SQM SC ADDENDUM";
        $description = 'Update Data SQM SC with Addendum : '.$request->ID_PSM_SC_NOCHAR;
        $this->saveToLogRentSCLotAdd($action, $description);
        return redirect()->route('marketing.leaseagreement.vieweditaddendum', [$request->ADD_TYPE_PSM_SC, $request->ID_PSM_SC]);
    }

    public function insertRentSCLotAdd(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            $lotArr = explode(',', $request->LOT_STOCK_ID_INT);
            $lotTextArr = array();
            foreach($lotArr as $lot) {
                $dataLot = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $lot)->where('PROJECT_NO_CHAR', $project_no)->first();

                \DB::table('PSM_TRANS_ADDENDUM_LOT')->insert([
                    'PSM_TRANS_ADD_NOCHAR' => $request->PSM_TRANS_ADD_NOCHAR,
                    'PSM_TRANS_NOCHAR' => $request->PSM_TRANS_NOCHAR,
                    'LOT_STOCK_ID_INT' => $dataLot->LOT_STOCK_ID_INT,
                    'LOT_STOCK_NO' => $dataLot->LOT_STOCK_NO,
                    'LOT_STOCK_SQM' => $dataLot->LOT_STOCK_SQM,
                    'LOT_STOCK_SQM_SC' => $dataLot->LOT_STOCK_SQM_SC,
                    'PROJECT_NO_CHAR' => $dataLot->PROJECT_NO_CHAR,
                    'created_at' => $dateNow
                ]);

                array_push($lotTextArr, $dataLot->LOT_STOCK_NO);
            }

            $lotText = implode(',', $lotTextArr);
            \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_ID_INT', $request->PSM_TRANS_ADD_ID_INT)->update([
                'LOT_STOCK_NO' => $lotText
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Insert Item']);
        }

        $action = "INSERT DATA LOT ADDENDUM";
        $description = 'Insert Data Lot Addendum with Lease : '.$request->PSM_TRANS_NOCHAR;
        $this->saveToLogRentSCLotAdd($action, $description);
        return response()->json(['Success' => 'Berhasil Insert Item']);
    }

    public function insertRentSCLot(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            $lotArr = explode(',', $request->LOT_STOCK_ID_INT);
            $lotTextArr = array();
            foreach($lotArr as $lot) {
                $dataLot = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $lot)->where('PROJECT_NO_CHAR', $project_no)->first();

                \DB::table('PSM_TRANS_LOT')->insert([
                    'PSM_TRANS_NOCHAR' => $request->PSM_TRANS_NOCHAR,
                    'LOT_STOCK_ID_INT' => $dataLot->LOT_STOCK_ID_INT,
                    'LOT_STOCK_NO' => $dataLot->LOT_STOCK_NO,
                    'LOT_STOCK_SQM' => $dataLot->LOT_STOCK_SQM,
                    'LOT_STOCK_SQM_SC' => $dataLot->LOT_STOCK_SQM_SC,
                    'PROJECT_NO_CHAR' => $dataLot->PROJECT_NO_CHAR,
                    'created_at' => $dateNow
                ]);

                \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $dataLot->LOT_STOCK_ID_INT)->update([
                    'ON_RENT_STAT_INT' => 1,
                    'updated_at' => $dateNow
                ]);

                array_push($lotTextArr, $dataLot->LOT_STOCK_NO);
            }
            
            $lotText = implode(',', $lotTextArr);

            \DB::table('PSM_TRANS')->where('PSM_TRANS_ID_INT', $request->PSM_TRANS_ID_INT)->update([
                'LOT_STOCK_NO' => $lotText
            ]);

            $action = "INSERT DATA LOT";
            $description = 'Insert Data Lot with Lease : '.$request->PSM_TRANS_NOCHAR;
            $this->saveToLogRentSCLot($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Insert Item']);
        }

        return response()->json(['Success' => 'Berhasil Insert Item']);
    }

    public function insertRentSCAmtAdd(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        try {
            \DB::beginTransaction();

            $dataLotSqm = \DB::table('PSM_TRANS_ADDENDUM_LOT')
                ->where('PSM_TRANS_ADD_NOCHAR', $request->PSM_TRANS_ADD_NOCHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->sum('LOT_STOCK_SQM');

            $arrDiffMonthYear = $this->calculateDateDiffYearMonth($request->PSM_TRANS_START_DATE, $request->PSM_TRANS_END_DATE);
            $diffMonth = $arrDiffMonthYear[$request->PSM_TRANS_PRICE_YEAR];
            $diffMonth = $diffMonth < 1 ? 1 : $diffMonth;

            $priceRent = ($request->PSM_TRANS_PRICE_RENT_NUM * $diffMonth) * $dataLotSqm;

            $dataPSM = \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_NOCHAR', $request->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();

            \DB::table('PSM_TRANS_ADDENDUM_PRICE')->insert([
                'PSM_TRANS_ADD_NOCHAR' => $request->PSM_TRANS_ADD_NOCHAR,
                'PSM_TRANS_NOCHAR' => $request->PSM_TRANS_NOCHAR,
                'PSM_TRANS_PRICE_YEAR' => $request->PSM_TRANS_PRICE_YEAR,
                'COUNT_MONTH' => $diffMonth,
                'PSM_TRANS_PRICE_RENT_NUM' => $request->PSM_TRANS_PRICE_RENT_NUM,
                'PSM_TRANS_PRICE_SC_NUM' => $request->PSM_TRANS_PRICE_SC_NUM,
                'PROJECT_NO_CHAR' => $project_no,
                'created_at' => $dateNow
            ]);

            $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX + $priceRent;
            $ppn = $dpp * $dataProject->PPNBM_NUM;
            $total = $dpp + $ppn;

            \DB::table('PSM_TRANS_ADDENDUM')->where('PSM_TRANS_ADD_NOCHAR', $request->PSM_TRANS_ADD_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                'PSM_TRANS_PPN' => $ppn,
                'PSM_TRANS_PRICE' => $total
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Insert Item']);
        }

        $action = "INSERT DATA RENT AMOUNT AND SERVICE CHARGE AMOUNT ADDENDUM";
        $description = 'Insert Data Rent Amount and Service Charge Amount Addendum with Addendum ' . $request->PSM_TRANS_ADD_NOCHAR . ' and Lease '.$request->PSM_TRANS_NOCHAR;
        $this->saveToLogRentSCAmtAdd($action, $description);
        return response()->json(['Success' => 'Berhasil Insert Item']);
    }

    public function insertRentSCAmt(Request $request) {
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        try {
            \DB::beginTransaction();

            $dataLotSqm = \DB::table('PSM_TRANS_LOT')
                ->where('PSM_TRANS_NOCHAR', $request->PSM_TRANS_NOCHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->sum('LOT_STOCK_SQM');

            $arrDiffMonthYear = $this->calculateDateDiffYearMonth($request->PSM_TRANS_START_DATE, $request->PSM_TRANS_END_DATE);
            $diffMonth = $arrDiffMonthYear[$request->PSM_TRANS_PRICE_YEAR];
            $diffMonth = $diffMonth < 1 ? 1 : $diffMonth;

            $priceRent = ($request->PSM_TRANS_PRICE_RENT_NUM * $diffMonth) * $dataLotSqm;

            $dataPSM = \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $request->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->first();

            \DB::table('PSM_TRANS_PRICE')->insert([
                'PSM_TRANS_NOCHAR' => $request->PSM_TRANS_NOCHAR,
                'PSM_TRANS_PRICE_YEAR' => $request->PSM_TRANS_PRICE_YEAR,
                'COUNT_MONTH' => $diffMonth,
                'PSM_TRANS_PRICE_RENT_NUM' => $request->PSM_TRANS_PRICE_RENT_NUM,
                'PSM_TRANS_PRICE_SC_NUM' => $request->PSM_TRANS_PRICE_SC_NUM,
                'PROJECT_NO_CHAR' => $project_no,
                'created_at' => $dateNow
            ]);

            $dpp = $dataPSM->PSM_TRANS_NET_BEFORE_TAX + $priceRent;
            $ppn = $dpp * $dataProject->PPNBM_NUM;
            $total = $dpp + $ppn;

            \DB::table('PSM_TRANS')->where('PSM_TRANS_NOCHAR', $request->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                'PSM_TRANS_NET_BEFORE_TAX' => $dpp,
                'PSM_TRANS_PPN' => $ppn,
                'PSM_TRANS_PRICE' => $total
            ]);

            $action = "INSERT DATA RENT AMOUNT AND SERVICE CHARGE AMOUNT";
            $description = 'Insert Data Rent Amount and Service Charge Amount with Lease : '.$request->PSM_TRANS_NOCHAR;
            $this->saveToLogRentSCAmt($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Gagal Insert Item']);
        }

        return response()->json(['Success' => 'Berhasil Insert Item']);
    }

    public function insertUpdateSecureDeposit(Request $request){
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR', '=', $request->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT', '=', $dataPSM->MD_TENANT_ID_INT)
            ->first();

        $typeSecure = '';
        $amtSecure = 0;
        if ($request->insert_id == 1) {
            DB::table('PSM_SECURE_DEP')
                ->insert([
                    'PSM_TRANS_NOCHAR'=>$request->PSM_TRANS_NOCHAR,
                    'PSM_TRANS_DEPOSIT_TYPE'=>$request->PSM_TRANS_DEPOSIT_TYPE,
                    'PSM_TRANS_DEPOSIT_DESC'=>$request->PSM_TRANS_DEPOSIT_DESC,
                    'PSM_TRANS_DEPOSIT_NUM'=>$request->PSM_TRANS_DEPOSIT_NUM,
                    'PSM_TRANS_DEPOSIT_DATE'=>$request->PSM_TRANS_DEPOSIT_DATE,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'CREATED_BY'=>$userName,
                    'created_at'=>$dateNow
                ]);

            $listSecureDep = DB::table('PSM_SECURE_DEP')
                ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
                ->get();

            foreach($listSecureDep as $data)
            {
                $typeSecure .= $data->PSM_TRANS_DEPOSIT_TYPE.',';
                $amtSecure += $data->PSM_TRANS_DEPOSIT_NUM;
            }

            DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
                ->update([
                'PSM_TRANS_DEPOSIT_TYPE'=>$typeSecure,
                'PSM_TRANS_DEPOSIT_NUM'=>$amtSecure,
                'updated_at'=>$dateNow
                ]);

            $action = "INSERT DATA SECURE DEPOSIT";
            $description = 'Insert Data Secure Deposit ' . $dataTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR.' Lease : '.$dataPSM->PSM_TRANS_NOCHAR;
            $this->saveToLogSecureDep($action, $description);
            return response()->json(['Success' => 'Berhasil Insert Item']);

        } else {
            DB::table('PSM_SECURE_DEP')
                ->where('PSM_SECURE_DEP_ID_INT','=',$request->PSM_SECURE_DEP_ID_INT)
                ->update([
                    'PSM_TRANS_NOCHAR'=>$request->PSM_TRANS_NOCHAR,
                    'PSM_TRANS_DEPOSIT_TYPE'=>$request->PSM_TRANS_DEPOSIT_TYPE,
                    'PSM_TRANS_DEPOSIT_DESC'=>$request->PSM_TRANS_DEPOSIT_DESC,
                    'PSM_TRANS_DEPOSIT_NUM'=>$request->PSM_TRANS_DEPOSIT_NUM,
                    'PSM_TRANS_DEPOSIT_DATE'=>$request->PSM_TRANS_DEPOSIT_DATE,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'UPDATED_BY'=>$userName,
                    'updated_at'=>$dateNow
                ]);

            $listSecureDep = DB::table('PSM_SECURE_DEP')
                ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
                ->get();

            foreach($listSecureDep as $data)
            {
                $typeSecure .= $data->PSM_TRANS_DEPOSIT_TYPE.',';
                $amtSecure += $data->PSM_TRANS_DEPOSIT_NUM;
            }

            DB::table('PSM_TRANS')
                ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
                ->update([
                    'PSM_TRANS_DEPOSIT_TYPE'=>$typeSecure,
                    'PSM_TRANS_DEPOSIT_NUM'=>$amtSecure,
                    'updated_at'=>$dateNow
                ]);


            $action = "UPDATE DATA SECURE DEPOSIT";
            $description = 'Update Data Secure Deposit ' . $dataTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR.' Lease : '.$dataPSM->PSM_TRANS_NOCHAR;
            $this->saveToLogSecureDep($action, $description);
            return response()->json(['Success' => 'Berhasil Update Item']);
        }
    }

    public function getitemSecureDeposit(Request $request){
        $project_no = session('current_project');

        $itemSecureDep = DB::table('PSM_SECURE_DEP')
            ->where('PSM_SECURE_DEP_ID_INT', '=', $request->PSM_SECURE_DEP_ID_INT)
            ->first();

        if ($itemSecureDep) {
            return response()->json([
                'status' => 'success',
                'PSM_SECURE_DEP_ID_INT'=>$itemSecureDep->PSM_SECURE_DEP_ID_INT,
                'PSM_TRANS_DEPOSIT_TYPE'=>$itemSecureDep->PSM_TRANS_DEPOSIT_TYPE,
                'PSM_TRANS_DEPOSIT_DESC'=>$itemSecureDep->PSM_TRANS_DEPOSIT_DESC,
                'PSM_TRANS_DEPOSIT_NUM'=>number_format($itemSecureDep->PSM_TRANS_DEPOSIT_NUM,0,'.',''),
                'PSM_TRANS_DEPOSIT_DATE'=>$itemSecureDep->PSM_TRANS_DEPOSIT_DATE
            ]);
        } else {
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function deleteItemSecureDeposito(Request $request){
        $project_no = session('current_project');
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $itemSecureDep = DB::table('PSM_SECURE_DEP')
            ->where('PSM_SECURE_DEP_ID_INT', '=', $request->PSM_SECURE_DEP_ID_INT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR', '=', $itemSecureDep->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT', '=', $dataPSM->MD_TENANT_ID_INT)
            ->first();

        DB::table('PSM_SECURE_DEP')
            ->where('PSM_SECURE_DEP_ID_INT', '=', $request->PSM_SECURE_DEP_ID_INT)
            ->delete();

        $listSecureDep = DB::table('PSM_SECURE_DEP')
            ->where('PSM_TRANS_NOCHAR','=',$itemSecureDep->PSM_TRANS_NOCHAR)
            ->get();

        $typeSecure = '';
        $amtSecure = 0;

        foreach($listSecureDep as $data)
        {
            $typeSecure .= $data->PSM_TRANS_DEPOSIT_TYPE.',';
            $amtSecure += $data->PSM_TRANS_DEPOSIT_NUM;
        }

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$request->PSM_TRANS_NOCHAR)
            ->update([
                'PSM_TRANS_DEPOSIT_TYPE'=>$typeSecure,
                'PSM_TRANS_DEPOSIT_NUM'=>$amtSecure,
                'updated_at'=>$dateNow
            ]);

        $action = "DELETE DATA SECURE DEPOSIT";
        $description = 'Delete Data Secure Deposit ' . $dataTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR.' Lease : '.$dataPSM->PSM_TRANS_NOCHAR;
        $this->saveToLogSecureDep($action, $description);
        return response()->json(['Success' => 'Berhasil Delete Item']);
    }

    public function updateExpiredUnit(){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $dataPSM = DB::select("Select a.PSM_TRANS_NOCHAR,a.PSM_TRANS_END_DATE,a.LOT_STOCK_NO,a.LOT_STOCK_ID_INT,
                           DATEDIFF(DAY,GETDATE(),a.PSM_TRANS_END_DATE) as DIFF,PSM_TRANS_EXP_STATUS_INT
                    from PSM_TRANS as a
                    where PSM_TRANS_STATUS_INT = '2'
                    and PSM_TRANS_EXP_STATUS_INT = 0
                    and DATEDIFF(DAY,GETDATE(),a.PSM_TRANS_END_DATE) < 0");

        foreach ($dataPSM as $psm)
        {
            if(empty($psm->LOT_STOCK_ID_INT)) {
                $dataPSMCurr = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $psm->PSM_TRANS_NOCHAR)->get();

                foreach($dataPSMCurr as $data) {
                    DB::table('LOT_STOCK')
                    ->where('LOT_STOCK_ID_INT','=',$data->LOT_STOCK_ID_INT)
                    ->update([
                        'ON_RENT_STAT_INT'=>0,
                        'updated_at'=>$dateNow
                    ]);
                }
            }
            else {
                DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$psm->LOT_STOCK_ID_INT)
                ->update([
                    'ON_RENT_STAT_INT'=>0,
                    'updated_at'=>$dateNow
                ]);
            }

            DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$psm->PSM_TRANS_NOCHAR)
            ->update([
                'PSM_TRANS_EXP_STATUS_INT'=>1,
                'updated_at'=>$dateNow
            ]);
        }
    }

    public function PrintLeaseAgreement($PSM_TRANS_ID_INT) {
        $project_no = session('current_project');

        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataCompany = \DB::table('MD_COMPANY')->where('ID_COMPANY_INT','=',$dataProject->ID_COMPANY_INT)->first();
        $dataFinSetup = \DB::table('MD_FIN_SETUP')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataPSM = \DB::table('PSM_TRANS')->where('PSM_TRANS_ID_INT', $PSM_TRANS_ID_INT)->first();
        $dataTenant = \DB::table('MD_TENANT')->where('MD_TENANT_ID_INT', $dataPSM->MD_TENANT_ID_INT)->first();
        $dataPSMLot = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();
        $dataLotSqm = 0;
        $dataLotSqmSC = 0;
        $dataLotArr = array();
        $dataPSMLevelArr = array();
        foreach($dataPSMLot as $data) {
            $dataLotStock = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
            $dataLevel = \DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotStock->LOT_LEVEL_ID_INT)->first();
            array_push($dataLotArr, "No. " . $dataLotStock->LOT_STOCK_NO);
            array_push($dataPSMLevelArr, $dataLevel->LOT_LEVEL_DESC);
            $dataLotSqm += $data->LOT_STOCK_SQM;
            $dataLotSqmSC += $data->LOT_STOCK_SQM_SC;
        }
        $dataLot = implode(', ', $dataLotArr);
        $dataPSMLevel = implode(', ', $dataPSMLevelArr);
        $dataLotSqmChar = $converter->terbilang($dataLotSqm);
        $dataSecDepTelp = \DB::table('PSM_SECURE_DEP')->where('PSM_TRANS_DEPOSIT_TYPE', 'DTLP')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->sum('PSM_TRANS_DEPOSIT_NUM');
        $dataSecDepTelpChar = $converter->terbilang($dataSecDepTelp);
        $dataSecDepSC = \DB::table('PSM_SECURE_DEP')->where('PSM_TRANS_DEPOSIT_TYPE', 'DSC')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->sum('PSM_TRANS_DEPOSIT_NUM');
        $dataSecDepSCChar = $converter->terbilang($dataSecDepSC);

        // CODINGAN LAMA DESKRIPSI MASA SEWA (START)
        // $tahun = (int)$dataPSM->PSM_TRANS_FREQ_NUM/12;
        // $bulan = $dataPSM->PSM_TRANS_FREQ_NUM - ($tahun * 12);
        // if ($tahun > 0) {
        //     $sewaTahun = number_format($tahun,0,'','').' Tahun';
        // } else {
        //     $sewaTahun = '';
        // }

        // if ($bulan > 0) {
        //     $sewaBulan = number_format($bulan,0,'','').' Bulan';
        // } else {
        //     $sewaBulan = '';
        // }

        // if ($dataPSM->PSM_TRANS_FREQ_DAY_NUM > 0) {
        //     $sewaHari = number_format($dataPSM->PSM_TRANS_FREQ_DAY_NUM,0,'','').' Hari';
        // } else {
        //     $sewaHari = '';
        // }
        // $masaSewa = $sewaTahun.' '.$sewaBulan.' '.$sewaHari;
        // CODINGAN LAMA DESKRIPSI MASA SEWA (END)

        // CODINGAN BARU DESKRIPSI MASA SEWA (START)
        $masaSewa = "";
        $masaSewaStart = date_create($dataPSM->PSM_TRANS_START_DATE);
        $masaSewaEnd = date_create($dataPSM->PSM_TRANS_END_DATE);
        $masaSewaDiff = date_diff($masaSewaStart, $masaSewaEnd);
        if($masaSewaDiff->y > 0) {
            $masaSewa .= $masaSewaDiff->y . " Tahun ";
        }
        if($masaSewaDiff->m > 0) {
            $masaSewa .= $masaSewaDiff->m . " Bulan ";
        }
        if($masaSewaDiff->d > 0) {
            $masaSewa .= $masaSewaDiff->d . " Hari ";
        }
        $masaSewa = trim($masaSewa);
        // CODINGAN BARU DESKRIPSI MASA SEWA (END)

        $dayList = array(
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'
        );
        $hariChar = $dayList[date('D', strtotime($dataPSM->NO_KONTRAK_DATE))];

        if($dataProject->ID_COMPANY_INT == 1) { // Jika Company-nya PT Metropolitan Land Tbk
            $jabatanDir = "Presiden Direktur";
        }
        else {
            $jabatanDir = "Direktur Utama";
        }

        $startDateIndo = $converter->indonesian_date($dataPSM->PSM_TRANS_START_DATE, 'd F Y');
        $endDateIndo = $converter->indonesian_date($dataPSM->PSM_TRANS_END_DATE, 'd F Y');

        // Mengambil data Lot Price Details dari PSM_TRANS_PRICE
        $dataLotPriceDetails = \DB::table('PSM_TRANS_PRICE')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();

        $arrDiffMonthYear = $this->calculateDateDiffYearMonth($dataPSM->PSM_TRANS_START_DATE, $dataPSM->PSM_TRANS_END_DATE);

        return View::make('page.leaseagreement.pdfCetakLeaseAgreement',
        [
            'project_no'=>$project_no,
            'dataProject'=>$dataProject,
            'dataCompany'=>$dataCompany,
            'dataPSM'=>$dataPSM,
            'dataTenant'=>$dataTenant,
            'hariChar'=>$hariChar,
            'jabatanDir'=>$jabatanDir,
            'dataLot'=>$dataLot,
            'dataPSMLevel'=>$dataPSMLevel,
            'dataLotSqm'=>$dataLotSqm,
            'dataLotSqmSC'=>$dataLotSqmSC,
            'dataLotSqmChar'=>$dataLotSqmChar,
            'masaSewa'=>$masaSewa,
            'startDateIndo'=>$startDateIndo,
            'endDateIndo'=>$endDateIndo,
            'dataFinSetup'=>$dataFinSetup,
            'dataSecDepTelp'=>$dataSecDepTelp,
            'dataSecDepTelpChar'=>$dataSecDepTelpChar,
            'dataSecDepSC'=>$dataSecDepSC,
            'dataSecDepSCChar'=>$dataSecDepSCChar,
            'dataLotPriceDetails'=>$dataLotPriceDetails,
            'arrDiffMonthYear'=>$arrDiffMonthYear
        ]);
    }

    public function PrintLOI($PSM_TRANS_ID_INT){
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataCompany = Model\Company::where('ID_COMPANY_INT','=',$dataProject['ID_COMPANY_INT'])->first();

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $UserNameSales = Session::get('name');

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        $dataSecureDep = DB::table('PSM_SECURE_DEP')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->get();

        $TopSchedule = DB::table('PSM_SCHEDULE')
            ->where('PSM_TRANS_NOCHAR','=',$dataPSM->PSM_TRANS_NOCHAR)
            ->where('TRX_CODE','=','RT')
            ->whereNotIn('SCHEDULE_STATUS_INT',[0])
            ->first();

        $terbilangSchedule = $converter->terbilang($dataPSM->PSM_TRANS_TIME_PERIOD_SCHED);

        $dataCategory = DB::table('PSM_CATEGORY')
            ->where('PSM_CATEGORY_ID_INT','=',$dataPSM->PSM_CATEGORY_ID_INT)
            ->first();

        // CODINGAN LAMA DESKRIPSI MASA SEWA (START)
        // $tahun = (int)$dataPSM->PSM_TRANS_FREQ_NUM/12;
        $tahun = 0;

        // $bulan = $dataPSM->PSM_TRANS_FREQ_NUM - ($tahun * 12);
        $bulan = 0;

        if ($tahun > 0){
            $sewaTahun = number_format($tahun,0,'','').' Tahun';
        }else{
            $sewaTahun = '';
        }

        if ($bulan > 0){
            $sewaBulan = number_format($bulan,0,'','').' Bulan';
        }else{
            $sewaBulan = '';
        }

        if ($dataPSM->PSM_TRANS_FREQ_DAY_NUM > 0){
            $sewaHari = number_format($dataPSM->PSM_TRANS_FREQ_DAY_NUM,0,'','').' Hari';
        }else{
            $sewaHari = '';
        }

        $masaSewa = $sewaTahun.' '.$sewaBulan.' '.$sewaHari;
        // CODINGAN LAMA DESKRIPSI MASA SEWA (END)

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            // CODINGAN BARU DESKRIPSI MASA SEWA (START)
            $masaSewa = "";
            $masaSewaStart = date_create($dataPSM->PSM_TRANS_START_DATE);
            $masaSewaEnd = date_create($dataPSM->PSM_TRANS_END_DATE);
            $masaSewaDiff = date_diff($masaSewaStart, $masaSewaEnd);
            if($masaSewaDiff->y > 0) {
                $masaSewa .= $masaSewaDiff->y . " Tahun ";
            }
            if($masaSewaDiff->m > 0) {
                $masaSewa .= $masaSewaDiff->m . " Bulan ";
            }
            if($masaSewaDiff->d > 0) {
                $masaSewa .= $masaSewaDiff->d . " Hari ";
            }
            $masaSewa = trim($masaSewa);
            // CODINGAN BARU DESKRIPSI MASA SEWA (END)

            $dataLotDetails = \DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();
            $arrLotDetails = array();
            foreach($dataLotDetails as $data) {
                $dataLotStock = \DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
                $dataLotLevel = \DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotStock->LOT_LEVEL_ID_INT)->first();
                $arrLot['LOT_STOCK'] = $dataLotStock;
                $arrLot['LOT_LEVEL'] = $dataLotLevel;
                array_push($arrLotDetails, $arrLot);
            }
            $dataLot = $arrLotDetails;
            $dataLotLevel = NULL;
        }
        else {
            $dataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$dataPSM->LOT_STOCK_ID_INT)
                ->first();

            $dataLotLevel = DB::table('LOT_LEVEL')
                ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
                ->first();
        }

        // Mengambil data Lot Price Details dari PSM_TRANS_PRICE
        $dataLotPriceDetails = \DB::table('PSM_TRANS_PRICE')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->get();

        $arrDiffMonthYear = $this->calculateDateDiffYearMonth($dataPSM->PSM_TRANS_START_DATE, $dataPSM->PSM_TRANS_END_DATE);

        $datePrint = $converter->indonesian_date($dateNow, 'd F Y');
        $dateDocument = $converter->indonesian_date($dataPSM->PSM_TRANS_BOOKING_DATE, 'd F Y');

        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

        if(empty($dataPSM->LOT_STOCK_ID_INT)) {
            return View::make('page.leaseagreement.pdfCetakLOI2',
                ['dataPSM'=>$dataPSM,'datePrint'=>$datePrint,'project_no'=>$project_no,
                'dataProject'=>$dataProject,'UserNameSales'=>$UserNameSales,'dataLot'=>$dataLot,
                'dateDocument'=>$dateDocument,'dataTenant'=>$dataTenant,'masaSewa'=>$masaSewa,
                'dataCategory'=>$dataCategory,'dataCompany'=>$dataCompany,'dataLotLevel'=>$dataLotLevel,
                'sewaTahun'=>$sewaTahun,'terbilangSchedule'=>$terbilangSchedule,'TopSchedule'=>$TopSchedule,
                'dataSecureDep'=>$dataSecureDep,'dataFinSetup'=>$dataFinSetup,'dataLotPriceDetails'=>$dataLotPriceDetails,
                'arrDiffMonthYear'=>$arrDiffMonthYear
                ]);
        }
        else {
            return View::make('page.leaseagreement.pdfCetakLOI',
                ['dataPSM'=>$dataPSM,'datePrint'=>$datePrint,'project_no'=>$project_no,
                'dataProject'=>$dataProject,'UserNameSales'=>$UserNameSales,'dataLot'=>$dataLot,
                'dateDocument'=>$dateDocument,'dataTenant'=>$dataTenant,'masaSewa'=>$masaSewa,
                'dataCategory'=>$dataCategory,'dataCompany'=>$dataCompany,'dataLotLevel'=>$dataLotLevel,
                'sewaTahun'=>$sewaTahun,'terbilangSchedule'=>$terbilangSchedule,'TopSchedule'=>$TopSchedule,
                'dataSecureDep'=>$dataSecureDep,'dataFinSetup'=>$dataFinSetup
                ]);
        }
    }

    public function getNumberLeaseAgreement($PSM_TRANS_ID_INT) {
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        
        $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

        $converter = new utilConverter();

        $Counter = str_pad($counter->psm_count, 5, "0", STR_PAD_LEFT);
        $Year = substr($dateNow->year, 2);
        $Month = $dateNow->month;
        $monthRomawi = $converter->getRomawi($Month);

        Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
            ->update(['psm_count' => $counter->psm_count + 1]);

        $noPSM = $Counter . '/' . $dataCompany['COMPANY_CODE'] . '/' . $dataProject['PROJECT_CODE'] . '/MKT.PSM/' . $monthRomawi . '/' . $Year;

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT', '=', $PSM_TRANS_ID_INT)
            ->update([
                'NO_KONTRAK_NOCHAR'=>$noPSM,
                'NO_KONTRAK_DATE'=>$dateNow,
                'updated_at'=>$dateNow
            ]);

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT', '=', $PSM_TRANS_ID_INT)
            ->first();

        DB::table('PSM_TRANS_HIST')
            ->insert([
                'PSM_TRANS_NOCHAR' => $dataPSM->PSM_TRANS_NOCHAR,
                'LOI_TRANS_NOCHAR' => $dataPSM->LOI_TRANS_NOCHAR,
                'SKS_TRANS_NOCHAR' => $dataPSM->SKS_TRANS_NOCHAR,
                'NO_KONTRAK_NOCHAR'=>$dataPSM->NO_KONTRAK_NOCHAR,
                'NO_KONTRAK_DATE'=>$dataPSM->NO_KONTRAK_DATE,
                'LOT_STOCK_ID_INT' => $dataPSM->LOT_STOCK_ID_INT,
                'LOT_STOCK_NO' => $dataPSM->LOT_STOCK_NO,
                'DEBTOR_ACCT_CHAR' => $dataPSM->DEBTOR_ACCT_CHAR,
                'SHOP_NAME_CHAR' => $dataPSM->SHOP_NAME_CHAR,
                'PSM_CATEGORY_ID_INT' => $dataPSM->PSM_CATEGORY_ID_INT,
                'MD_TENANT_ID_INT' => $dataPSM->MD_TENANT_ID_INT,
                //'PSM_TRANS_TYPE' => $dataLOI->LOI_TRANS_TYPE,
                'MD_SALES_TYPE_ID_INT'=>$dataPSM->MD_SALES_TYPE_ID_INT,
                'PSM_TRANS_BOOKING_DATE' => $dataPSM->PSM_TRANS_BOOKING_DATE,
                'PSM_TRANS_START_DATE' => $dataPSM->PSM_TRANS_START_DATE,
                'PSM_TRANS_END_DATE' => $dataPSM->PSM_TRANS_END_DATE,
                'PSM_TRANS_FREQ_NUM' => $dataPSM->PSM_TRANS_FREQ_NUM,
                'PSM_TRANS_FREQ_DAY_NUM' => $dataPSM->PSM_TRANS_FREQ_DAY_NUM,
                'PSM_TRANS_TIME_PERIOD_SCHED' => $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED,
                'PSM_TRANS_RENT_NUM' => $dataPSM->PSM_TRANS_RENT_NUM,
                'PSM_TRANS_SC_NUM' => $dataPSM->PSM_TRANS_SC_NUM,
                'PSM_TRANS_DESCRIPTION' => $dataPSM->PSM_TRANS_DESCRIPTION,
                'PSM_TRANS_NET_BEFORE_TAX' => $dataPSM->PSM_TRANS_NET_BEFORE_TAX,
                'PSM_TRANS_PPN' => $dataPSM->PSM_TRANS_PPN,
                'PSM_TRANS_PRICE' => $dataPSM->PSM_TRANS_PRICE,
                'PSM_TRANS_DP_PERSEN' => $dataPSM->PSM_TRANS_DP_PERSEN,
                'PSM_TRANS_DP_NUM' => $dataPSM->PSM_TRANS_DP_NUM,
                'PSM_TRANS_DP_PERIOD' => $dataPSM->PSM_TRANS_DP_PERIOD,
                'PSM_TRANS_DEPOSIT_MONTH' => $dataPSM->PSM_TRANS_DEPOSIT_MONTH,
                'PSM_TRANS_DEPOSIT_TYPE' => $dataPSM->PSM_TRANS_DEPOSIT_TYPE,
                'PSM_TRANS_DEPOSIT_NUM' => $dataPSM->PSM_TRANS_DEPOSIT_NUM,
                'PSM_TRANS_DEPOSIT_DATE' => $dataPSM->PSM_TRANS_DEPOSIT_DATE,
                'PSM_INVEST_NUM' => $dataPSM->PSM_INVEST_NUM,
                'PSM_INVEST_RATE' => $dataPSM->PSM_INVEST_RATE,
                'PSM_REVENUE_LOW_NUM' => $dataPSM->PSM_REVENUE_LOW_NUM,
                'PSM_REVENUE_LOW_RATE' => $dataPSM->PSM_REVENUE_LOW_RATE,
                'PSM_REVENUE_HIGH_NUM' => $dataPSM->PSM_REVENUE_HIGH_NUM,
                'PSM_REVENUE_HIGH_RATE' => $dataPSM->PSM_REVENUE_HIGH_RATE,
                'PSM_TRANS_GRASS_TYPE' => $dataPSM->PSM_TRANS_GRASS_TYPE,
                'PSM_TRANS_GRASS_PERIOD' => $dataPSM->PSM_TRANS_GRASS_PERIOD,
                'PSM_TRANS_GRASS_DATE' => $dataPSM->PSM_TRANS_GRASS_DATE,
                'PSM_TRANS_VA' => $dataPSM->PSM_TRANS_VA,
                'PSM_TRANS_REQUEST_CHAR' => $userName,
                'PSM_TRANS_REQUEST_DATE' => $dateNow,
                'PROJECT_NO_CHAR' => $project_no,
                'ACTION' => 'UPDATE NO.KONTRAK',
                'USER_BY' => $userName,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
                'PSM_TRANS_STATUS_INT' => $dataPSM->PSM_TRANS_STATUS_INT,
                'PSM_TRANS_GENERATE_BILLING' => $dataPSM->PSM_TRANS_GENERATE_BILLING,
                'PSM_TRANS_BILLING_INT' => $dataPSM->PSM_TRANS_BILLING_INT
            ]);

        $action = "UPDATE NO.KONTRAK LOI";
        $description = 'Update No.Kontrak LOI: ' . $dataPSM->PSM_TRANS_NOCHAR . ' succesfully';
        $this->saveToLog($action,$description);
        return redirect()->route('marketing.leaseagreement.vieweditdata', ['id' => $PSM_TRANS_ID_INT])
            ->with('success', $description);
    }

    public function getNumberBAST($PSM_TRANS_ID_INT) {
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        
        $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

        $converter = new utilConverter();

        $Counter = str_pad($counter->bast_count, 5, "0", STR_PAD_LEFT);
        $Year = substr($dateNow->year, 2);
        $Month = $dateNow->month;
        $monthRomawi = $converter->getRomawi($Month);

        Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
            ->update(['bast_count' => $counter->bast_count + 1]);

        $noBAST = $Counter . '/' . $dataCompany['COMPANY_CODE'] . '/' . $dataProject['PROJECT_CODE'] . '/MKT.BAST/' . $monthRomawi . '/' . $Year;

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT', '=', $PSM_TRANS_ID_INT)
            ->update([
                'NO_BAST_NOCHAR'=>$noBAST,
                'NO_BAST_DATE'=>$dateNow,
                'updated_at'=>$dateNow
            ]);

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT', '=', $PSM_TRANS_ID_INT)
            ->first();

        DB::table('PSM_TRANS_HIST')
            ->insert([
                'PSM_TRANS_NOCHAR' => $dataPSM->PSM_TRANS_NOCHAR,
                'LOI_TRANS_NOCHAR' => $dataPSM->LOI_TRANS_NOCHAR,
                'SKS_TRANS_NOCHAR' => $dataPSM->SKS_TRANS_NOCHAR,
                'NO_KONTRAK_NOCHAR'=>$dataPSM->NO_KONTRAK_NOCHAR,
                'NO_KONTRAK_DATE'=>$dataPSM->NO_KONTRAK_DATE,
                'NO_BAST_NOCHAR'=>$dataPSM->NO_BAST_NOCHAR,
                'NO_BAST_DATE'=>$dataPSM->NO_BAST_DATE,
                'LOT_STOCK_ID_INT' => $dataPSM->LOT_STOCK_ID_INT,
                'LOT_STOCK_NO' => $dataPSM->LOT_STOCK_NO,
                'DEBTOR_ACCT_CHAR' => $dataPSM->DEBTOR_ACCT_CHAR,
                'SHOP_NAME_CHAR' => $dataPSM->SHOP_NAME_CHAR,
                'PSM_CATEGORY_ID_INT' => $dataPSM->PSM_CATEGORY_ID_INT,
                'MD_TENANT_ID_INT' => $dataPSM->MD_TENANT_ID_INT,
                //'PSM_TRANS_TYPE' => $dataLOI->LOI_TRANS_TYPE,
                'MD_SALES_TYPE_ID_INT'=>$dataPSM->MD_SALES_TYPE_ID_INT,
                'PSM_TRANS_BOOKING_DATE' => $dataPSM->PSM_TRANS_BOOKING_DATE,
                'PSM_TRANS_START_DATE' => $dataPSM->PSM_TRANS_START_DATE,
                'PSM_TRANS_END_DATE' => $dataPSM->PSM_TRANS_END_DATE,
                'PSM_TRANS_FREQ_NUM' => $dataPSM->PSM_TRANS_FREQ_NUM,
                'PSM_TRANS_FREQ_DAY_NUM' => $dataPSM->PSM_TRANS_FREQ_DAY_NUM,
                'PSM_TRANS_TIME_PERIOD_SCHED' => $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED,
                'PSM_TRANS_RENT_NUM' => $dataPSM->PSM_TRANS_RENT_NUM,
                'PSM_TRANS_SC_NUM' => $dataPSM->PSM_TRANS_SC_NUM,
                'PSM_TRANS_DESCRIPTION' => $dataPSM->PSM_TRANS_DESCRIPTION,
                'PSM_TRANS_NET_BEFORE_TAX' => $dataPSM->PSM_TRANS_NET_BEFORE_TAX,
                'PSM_TRANS_PPN' => $dataPSM->PSM_TRANS_PPN,
                'PSM_TRANS_PRICE' => $dataPSM->PSM_TRANS_PRICE,
                'PSM_TRANS_DP_PERSEN' => $dataPSM->PSM_TRANS_DP_PERSEN,
                'PSM_TRANS_DP_NUM' => $dataPSM->PSM_TRANS_DP_NUM,
                'PSM_TRANS_DP_PERIOD' => $dataPSM->PSM_TRANS_DP_PERIOD,
                'PSM_TRANS_DEPOSIT_MONTH' => $dataPSM->PSM_TRANS_DEPOSIT_MONTH,
                'PSM_TRANS_DEPOSIT_TYPE' => $dataPSM->PSM_TRANS_DEPOSIT_TYPE,
                'PSM_TRANS_DEPOSIT_NUM' => $dataPSM->PSM_TRANS_DEPOSIT_NUM,
                'PSM_TRANS_DEPOSIT_DATE' => $dataPSM->PSM_TRANS_DEPOSIT_DATE,
                'PSM_INVEST_NUM' => $dataPSM->PSM_INVEST_NUM,
                'PSM_INVEST_RATE' => $dataPSM->PSM_INVEST_RATE,
                'PSM_REVENUE_LOW_NUM' => $dataPSM->PSM_REVENUE_LOW_NUM,
                'PSM_REVENUE_LOW_RATE' => $dataPSM->PSM_REVENUE_LOW_RATE,
                'PSM_REVENUE_HIGH_NUM' => $dataPSM->PSM_REVENUE_HIGH_NUM,
                'PSM_REVENUE_HIGH_RATE' => $dataPSM->PSM_REVENUE_HIGH_RATE,
                'PSM_TRANS_GRASS_TYPE' => $dataPSM->PSM_TRANS_GRASS_TYPE,
                'PSM_TRANS_GRASS_PERIOD' => $dataPSM->PSM_TRANS_GRASS_PERIOD,
                'PSM_TRANS_GRASS_DATE' => $dataPSM->PSM_TRANS_GRASS_DATE,
                'PSM_TRANS_VA' => $dataPSM->PSM_TRANS_VA,
                'PSM_TRANS_REQUEST_CHAR' => $userName,
                'PSM_TRANS_REQUEST_DATE' => $dateNow,
                'PROJECT_NO_CHAR' => $project_no,
                'ACTION' => 'UPDATE NO.BAST',
                'USER_BY' => $userName,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
                'PSM_TRANS_STATUS_INT' => $dataPSM->PSM_TRANS_STATUS_INT,
                'PSM_TRANS_GENERATE_BILLING' => $dataPSM->PSM_TRANS_GENERATE_BILLING,
                'PSM_TRANS_BILLING_INT' => $dataPSM->PSM_TRANS_BILLING_INT
            ]);

        $action = "UPDATE NO.BAST LOI";
        $description = 'Update No.Kontrak LOI: '.$dataPSM->PSM_TRANS_NOCHAR. ' succesfully';
        $this->saveToLog($action,$description);
        return redirect()->route('marketing.leaseagreement.vieweditdata', ['id' => $PSM_TRANS_ID_INT])
            ->with('success', $description);
    }

    public function PrintBAST($PSM_TRANS_ID_INT){
        $project_no = session('current_project');

        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $project_logo = $dataProject['logo'];
        $dataCompany = Model\Company::where('ID_COMPANY_INT','=',$dataProject['ID_COMPANY_INT'])->first();

        $dataFinSetup = DB::table('MD_FIN_SETUP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->first();

        if(empty($dataFinSetup)) {
            return redirect()->route('marketing.leaseagreement.viewlistdata')->with('error', 'Failed Data Fin Setup Not Found!');
        }

        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $UserNameSales = trim(session('first_name') . ' ' . session('last_name'));

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_ID_INT','=',$PSM_TRANS_ID_INT)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataPSM->MD_TENANT_ID_INT)
            ->first();

        $dataLot = DB::table('PSM_TRANS_LOT')->where('PSM_TRANS_NOCHAR', $dataPSM->PSM_TRANS_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

        $dataLotLevelArr = array();
        foreach($dataLot as $data) {
            $dataLotCurr = DB::table('LOT_STOCK')->where('LOT_STOCK_ID_INT', $data->LOT_STOCK_ID_INT)->first();
            $dataLotLevelCurr = DB::table('LOT_LEVEL')->where('LOT_LEVEL_ID_INT', $dataLotCurr->LOT_LEVEL_ID_INT)->first();
            array_push($dataLotLevelArr, $dataLotLevelCurr->LOT_LEVEL_ID_INT);
        }

        $dataLotLevel = DB::table('LOT_LEVEL')->whereIn('LOT_LEVEL_ID_INT', $dataLotLevelArr)->get();

        $datePrint = $converter->indonesian_date($dateNow, 'd F Y');
        $dateFormatHari = $converter->indonesian_date($dataPSM->PSM_TRANS_START_DATE, 'l');
        $dateDocument = $converter->indonesian_date($dataPSM->PSM_TRANS_START_DATE, 'd F Y');

        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

        return View::make('page.leaseagreement.pdfCetakBAST2',
            ['dataPSM'=>$dataPSM,'datePrint'=>$datePrint,'project_no'=>$project_no,
            'dataProject'=>$dataProject,'UserNameSales'=>$UserNameSales,'dataLot'=>$dataLot,
            'dateDocument'=>$dateDocument,'dataTenant'=>$dataTenant,'dataCompany'=>$dataCompany,'dataLotLevel'=>$dataLotLevel,
            'dataFinSetup'=>$dataFinSetup,'project_logo'=>$project_logo,'dateFormatHari'=>$dateFormatHari
            ]);
    }

    public function saveEditDataAdminDoc(Request $requestPSM){
        $project_no = session('current_project');

        $inputDataPSM = $requestPSM->all();
        $date = Carbon::parse(Carbon::now());

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$inputDataPSM['PSM_TRANS_NOCHAR'])
            ->first();

        DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$inputDataPSM['PSM_TRANS_NOCHAR'])
            ->update([
                'NO_KONTRAK_DATE'=>$inputDataPSM['NO_KONTRAK_DATE'],
                'created_at'=>$date,
                'updated_at'=>$date
            ]);

        \Session::flash('message', 'Saving Edit Data Admin Doc LOI '.$inputDataPSM['PSM_TRANS_NOCHAR']);
        $action = "EDIT DATA";
        $description = 'Saving Edit Data Admin Doc LOI '.$inputDataPSM['PSM_TRANS_NOCHAR'];
        $this->saveToLog($action, $description);

        return redirect()->route('marketing.leaseagreement.vieweditdata',[$dataPSM->PSM_TRANS_ID_INT])
            ->with('success',$description.' Successfully');
    }

    public function calculateDateDiffYearMonth($startDate, $endDate) {
        /* Mengambil data berapa bulan pada tahun tersebut dan disimpan di array (Misalnya Start Datenya 2022-11-02 dan End Datenya 2023-11-02
        maka pada tahun 2022 ada 2 bulan yaitu 11 ke 12 dan 12 ke 01 dan juga tahun 2023 ada 10 bulan yaitu 01 ke 02 sampai ke 11 lagi) */
        $startYear = date('Y', strtotime($startDate));
        $startMonth = date('m', strtotime($startDate));
        $endYear = date('Y', strtotime($endDate));
        $endMonth = date('m', strtotime($endDate));
        $arrDiffMonthYear = array();
        for($i = $startYear; $i <= $endYear; $i++) {
            $arrDiffMonthYear[$i] = 0;
        }
        for($i = $startMonth; $i <= 12; $i++) {
            if($i >= $endMonth && $startYear >= $endYear) {
                break;
            }

            if($i == 12) {
                $arrDiffMonthYear[$startYear] += 1;
                $startYear += 1;
                $i = 0;
            }
            else {
                $arrDiffMonthYear[$startYear] += 1;
            }
        }

        return $arrDiffMonthYear;
    }
}
