<?php namespace App\Http\Controllers\Marketing;

use App\Http\Requests;
use App\User;
use App\Model;
use View;
use Carbon\Carbon;
use Session;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Requests\MasterData\RequestValidation;
use Requests\Masterdata\RequestEditCustValidation;
use App\Http\Controllers\LogActivity\LogActivityController;
use Illuminate\Database\QueryException;
use DB;

use App\Http\Controllers\Util\utilArray;
use App\Http\Controllers\Util\utilConverter;
use App\Http\Controllers\Util\utilGenerator;
use App\Http\Controllers\Util\utilSession;

class ConfirmationLetter extends Controller {

    public function viewListData(){
        //$user = User::all();
        $isLogged = (bool) Session::get('isLogin');
        //''
        if($isLogged == FALSE){
            //dd($isLogged);
            return redirect('/login');
        }
        $project_no = \Session::get('PROJECT_NO_CHAR');
        //dd($project_no);

        $confLetter = DB::select("select a.SKS_TRANS_ID_INT,a.SKS_TRANS_NOCHAR,b.MD_TENANT_NAME_CHAR,c.LOT_STOCK_NO,FORMAT(a.SKS_TRANS_START_DATE,'dd-MM-yyyy') as SKS_TRANS_START_DATE,
                                       FORMAT(a.SKS_TRANS_END_DATE,'dd-MM-yyyy') as SKS_TRANS_END_DATE,a.SKS_TRANS_PRICE,
                                        CASE
                                            WHEN a.SKS_STATUS_INT = 1 THEN 'REQUEST'
                                            WHEN a.SKS_STATUS_INT = 2 THEN 'APPROVE'
                                            WHEN a.SKS_STATUS_INT = 3 THEN 'LOI'
                                            WHEN a.SKS_STATUS_INT = 4 THEN 'PSM'
                                        ELSE 'NONE'
                                        END as SKS_STATUS_INT
                                from SKS_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                INNER JOIN LOT_STOCK as c ON a.LOT_STOCK_ID_INT = c.LOT_STOCK_ID_INT
                                where a.PROJECT_NO_CHAR = '".$project_no."'
                                AND a.SKS_STATUS_INT NOT IN (0)");

        return View::make('page.confirmationletter.listDataConfirmationLetter',
            ['confLetter'=>$confLetter]);

    }

    public function viewAddDataConfLetter(){
        $isLogged = (bool)  Session::get('isLogin');
        //''
        if($isLogged == FALSE){
            //dd($isLogged);
            return redirect('/login');
        }
        $project_no = Session::get('PROJECT_NO_CHAR');

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

        return View::make('page.confirmationletter.addDataConfLetter',
            ['dataLot'=>$dataLot,'dataTenant'=>$dataTenant,'dataSalesType'=>$dataSalesType]);
    }

    public function saveDataConfLetter(Requests\Marketing\AddConfirmationLetterRequest $requestConf){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $inputDataConf = $requestConf->all();
        //dd($inputDataConf);
        $project_no = Session::get('PROJECT_NO_CHAR');
        //$arrayPushVendor = new utilArray();
        $date = Carbon::parse(Carbon::now());
        //$monthYear = $date->year.' '.$date->month;
        $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataCompany = Model\Company::where('ID_COMPANY_INT', '=', $dataProject['ID_COMPANY_INT'])->first();

        $converter = new utilConverter();

        $Counter = str_pad($counter->confletter_count, 5, "0", STR_PAD_LEFT);
        $Year = substr($date->year, 2);
        $Month = $date->month;
        $monthRomawi = $converter->getRomawi($Month);

        Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
            ->update(['confletter_count'=>$counter->confletter_count + 1]);

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$inputDataConf['LOT_STOCK_ID_INT'])
            ->first();

        $bookingDate = date_create($inputDataConf['SKS_TRANS_BOOKING_DATE']);
        $startDate = date_create($inputDataConf['SKS_TRANS_START_DATE']);
        $endDate = date_create($inputDataConf['SKS_TRANS_END_DATE']);
        //$freq_num = $endDate->diff($startDate);
        $freq_num = date_diff($startDate,$endDate);

        $difMonth = (int)($freq_num->days / 30);
        $freq_day_num = $difMonth * 30;
        $difDays = (int)($freq_num->days) - (int)($freq_day_num);
        //dd($difMonth);

        if ($dataLot->id_unit == 1)
        {
            $netBeforeTax = $inputDataConf['SKS_TRANS_RENT_NUM'];
        }
        else
        {
            $netBeforeTax = ($inputDataConf['SKS_TRANS_RENT_NUM'] * $dataLot->LOT_STOCK_SQM) * $difMonth;
        }

        //dd($netBeforeTax);

        $ppn = $netBeforeTax * 0.11;
        $total = $netBeforeTax + $ppn;

        $downPayment = ($inputDataConf['SKS_TRANS_DP_PERSEN']/100) * $netBeforeTax;

//        if ($inputDataConf['SKS_DEPOSIT_TYPE'] == 'SC')
//        {
//            $depositNum = ($inputDataConf['SKS_TRANS_SC_NUM'] * $dataLot->LOT_STOCK_SQM) * $inputDataConf['SKS_DEPOSIT_MONTH'];
//        }
//        elseif ($inputDataConf['SKS_DEPOSIT_TYPE'] == 'SW')
//        {
//            $depositNum = ($inputDataConf['SKS_TRANS_RENT_NUM'] * $dataLot->LOT_STOCK_SQM) * $inputDataConf['SKS_DEPOSIT_MONTH'];
//        }
//        else
//        {
//            $depositNum = 0;
//        }

        DB::table('SKS_TRANS')
            ->insert([
                'SKS_TRANS_NOCHAR'=>$Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'.LS/'.$monthRomawi.'/'.$Year,
                'MD_TENANT_ID_INT'=>$inputDataConf['MD_TENANT_ID_INT'],
                //'SKS_TRANS_TYPE'=>$inputDataConf['SKS_TRANS_TYPE'],
                'MD_SALES_TYPE_ID_INT'=>$inputDataConf['MD_SALES_TYPE_ID_INT'],
                'LOT_STOCK_ID_INT'=>$inputDataConf['LOT_STOCK_ID_INT'],
                'LOT_STOCK_NO'=>$inputDataConf['LOT_STOCK_NO'],
                'SHOP_NAME_CHAR'=>$inputDataConf['SHOP_NAME_CHAR'],
                'SKS_TRANS_BOOKING_DATE'=>$bookingDate,
                'SKS_TRANS_START_DATE'=>$startDate,
                'SKS_TRANS_END_DATE'=>$endDate,
                'SKS_TRANS_FREQ_NUM'=>$difMonth,
                'SKS_TRANS_FREQ_DAY_NUM'=>$difDays,
                'SKS_TRANS_TIME_PERIOD_SCHED'=>$inputDataConf['SKS_TRANS_TIME_PERIOD_SCHED'],
                'SKS_TRANS_RENT_NUM'=>$inputDataConf['SKS_TRANS_RENT_NUM'],
                'SKS_TRANS_SC_NUM'=>$inputDataConf['SKS_TRANS_SC_NUM'],
                'SKS_TRANS_DESCRIPTION'=>$inputDataConf['SKS_TRANS_DESCRIPTION'],
                'SKS_TRANS_NET_BEFORE_TAX'=>$netBeforeTax,
                'SKS_TRANS_PPN'=>$ppn,
                'SKS_TRANS_PRICE'=>$total,
                'SKS_TRANS_DP_PERSEN'=>$inputDataConf['SKS_TRANS_DP_PERSEN'],
                'SKS_TRANS_DP_NUM'=>$downPayment,
                'SKS_TRANS_DP_PERIOD'=>$inputDataConf['SKS_TRANS_DP_PERIOD'],
                'SKS_DEPOSIT_MONTH'=>0,
                'SKS_DEPOSIT_TYPE'=>$inputDataConf['SKS_DEPOSIT_TYPE'],
                'SKS_DEPOSIT_NUM'=>$inputDataConf['SKS_DEPOSIT_NUM'],
                'SKS_DEPOSIT_DATE'=>$inputDataConf['SKS_DEPOSIT_DATE'],
                'SKS_INVEST_NUM'=>$inputDataConf['SKS_INVEST_NUM'],
                'SKS_INVEST_RATE'=>$inputDataConf['SKS_INVEST_RATE'],
                'SKS_REVENUE_LOW_NUM'=>$inputDataConf['SKS_REVENUE_LOW_NUM'],
                'SKS_REVENUE_LOW_RATE'=>$inputDataConf['SKS_REVENUE_LOW_RATE'],
                'SKS_REVENUE_HIGH_NUM'=>$inputDataConf['SKS_REVENUE_HIGH_NUM'],
                'SKS_REVENUE_HIGH_RATE'=>$inputDataConf['SKS_REVENUE_HIGH_RATE'],
                'SKS_TRANS_GRASS_TYPE'=>$inputDataConf['SKS_TRANS_GRASS_TYPE'],
                'SKS_TRANS_GRASS_PERIOD'=>$inputDataConf['SKS_TRANS_GRASS_PERIOD'],
                //'SKS_TRANS_GRASS_DATE'=>$inputDataConf['SKS_TRANS_GRASS_DATE'],
                'SKS_STATUS_INT'=>2,
                'SKS_REQUEST_CHAR'=>Session::get('name'),
                'SKS_REQUEST_DATE'=>$date,
                'PROJECT_NO_CHAR'=>$project_no,
                'created_at'=>$date,
                'updated_at'=>$date
            ]);
        // dd($dataVendor);

        DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$inputDataConf['LOT_STOCK_ID_INT'])
            ->update([
                'ON_RENT_STAT_INT'=>1,
                'LOT_UPDATE_BY'=>Session::get('name'),
                'LOT_UPDATE_DATE'=>$date,
                'updated_at'=>$date
            ]);

        \Session::flash('message', 'Saving Data Confirmation Letter '.$Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'.LS/'.$monthRomawi.'/'.$Year);
        $action = "ADD DATA";
        $description = 'Saving Data Confirmation Letter '.$Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'.LS/'.$monthRomawi.'/'.$Year;
        $this->saveToLog($action, $description);

        return redirect()->route('marketing.confirmationletter.viewlistdata')
            ->with('success','Saving Data Confirmation Letter '.$Counter.'/'.$dataCompany['COMPANY_CODE'].'/'.$dataProject['PROJECT_CODE'].'.LS/'.$monthRomawi.'/'.$Year.' Successfully');
    }

    public function saveToLog($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Confirmation Letter';
        $module = 'Marketing';
        $by = $userName;
        $table = 'SKS_TRANS';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function cancelDataConfLetter($SKS_TRANS_ID_INT){
//        $isLogged = (bool) Session::get('dataSession.isLogin');
//        //''
//        if ($isLogged == FALSE) {
//            //dd($isLogged);
//            return redirect('/login');
//        }
        $date = Carbon::parse(Carbon::now());

        $dataConfLetter = DB::table('SKS_TRANS')
            ->where('SKS_TRANS_ID_INT','=',$SKS_TRANS_ID_INT)
            ->first();

        DB::table('SKS_TRANS')
            ->where('SKS_TRANS_ID_INT','=',$SKS_TRANS_ID_INT)
            ->update([
                'SKS_STATUS_INT'=>0,
                'SKS_CANCEL_DATE'=>$date,
                'SKS_CANCEL_CHAR'=>Session::get('name'),
                'updated_at'=>$date
            ]);

        DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$dataConfLetter['LOT_STOCK_ID_INT'])
            ->update([
                'ON_RENT_STAT_INT'=>0,
                'LOT_UPDATE_BY'=>Session::get('name'),
                'LOT_UPDATE_DATE'=>$date,
                'updated_at'=>$date
            ]);

        $action = "CANCEL DATA";
        $description = 'Cancel Confirmation Letter : '.$dataConfLetter->SKS_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        //return View::make('accounting.addDataGlTrans', ['dataJournal' => $dataJournal], ['dataCoa' => $datacoa]);
        return redirect()->route('marketing.confirmationletter.viewlistdata')
            ->with('success','Cancel Confirmation Letter '.$dataConfLetter->SKS_TRANS_NOCHAR.' Succesfully');
    }

    public function viewFormEditDataConfLetter($SKS_TRANS_ID_INT){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $project_no = Session::get('PROJECT_NO_CHAR');

        $dataConfLetter = DB::table('SKS_TRANS')
            ->where('SKS_TRANS_ID_INT','=',$SKS_TRANS_ID_INT)
            ->first();

        $tenantData = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataConfLetter->MD_TENANT_ID_INT)
            ->first();

        $lotData = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$dataConfLetter->LOT_STOCK_ID_INT)
            ->first();

        $salesTypedata = DB::table("MD_SALES_TYPE")
            ->where('MD_SALES_TYPE_ID_INT','=',$dataConfLetter->MD_SALES_TYPE_ID_INT)
            ->first();

//        $dataLot = DB::table('LOT_STOCK')->where('ON_RELEASE_STAT_INT','=',1)
//            ->where('ON_RENT_STAT_INT','=',0)
//            ->where('IS_DELETE','=',0)
//            ->where('PROJECT_NO_CHAR','=',$project_no)
//            ->get();

        $dataSalesType = DB::table("MD_SALES_TYPE")
            ->where('IS_ACTIVE','=',1)
            ->get();

        $dataTenant = DB::table('MD_TENANT')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->get();

        return View::make('page.confirmationletter.editDataConfLetter',
            ['dataConfLetter'=>$dataConfLetter,'tenantData'=>$tenantData,
             'lotData'=>$lotData,'dataTenant'=>$dataTenant,'salesTypedata'=>$salesTypedata,
             'dataSalesType'=>$dataSalesType]);
    }

    public function saveEditDataConfLetter(Requests\Marketing\AddConfirmationLetterRequest $requestConf){
        $isLogged = (bool) Session::get('isLogin');
        //''
        if ($isLogged == FALSE) {
            //dd($isLogged);
            return redirect('/login');
        }

        $inputDataConf = $requestConf->all();
        //dd($inputDataConf);
        $project_no = Session::get('PROJECT_NO_CHAR');
        //$arrayPushVendor = new utilArray();
        $date = Carbon::parse(Carbon::now());

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$inputDataConf['LOT_STOCK_ID_INT'])
            ->first();
        //dd($dataLot);

        $bookingDate = date_create($inputDataConf['SKS_TRANS_BOOKING_DATE']);
        $startDate = date_create($inputDataConf['SKS_TRANS_START_DATE']);
        $endDate = date_create($inputDataConf['SKS_TRANS_END_DATE']);
        $freq_num = $endDate->diff($startDate);
        //$freq_num = date_diff($startDate,$endDate);
        //dd($freq_num);
        $difMonth = (int)($freq_num->days / 30);
        $freq_day_num = $difMonth * 30;
        $difDays = (int)($freq_num->days) - (int)($freq_day_num);

        //dd($difMonth.'-'.$difDays);

        $netBeforeTax = ($inputDataConf['SKS_TRANS_RENT_NUM'] * $dataLot->LOT_STOCK_SQM) * $difMonth;
        //dd($difMonth);

        $ppn = $netBeforeTax * 0.11;
        $total = $netBeforeTax + $ppn;

        $downPayment = ($inputDataConf['SKS_TRANS_DP_PERSEN']/100) * $netBeforeTax;

//        if ($inputDataConf['SKS_DEPOSIT_TYPE'] == 'SC')
//        {
//            $depositNum = ($inputDataConf['SKS_TRANS_SC_NUM'] * $dataLot->LOT_STOCK_SQM) * $inputDataConf['SKS_DEPOSIT_MONTH'];
//        }
//        elseif ($inputDataConf['SKS_DEPOSIT_TYPE'] == 'SW')
//        {
//            $depositNum = ($inputDataConf['SKS_TRANS_RENT_NUM'] * $dataLot->LOT_STOCK_SQM) * $inputDataConf['SKS_DEPOSIT_MONTH'];
//        }
//        else
//        {
//            $depositNum = 0;
//        }

        DB::table('SKS_TRANS')
            ->where('SKS_TRANS_NOCHAR','=',$inputDataConf['SKS_TRANS_NOCHAR'])
            ->update([
                'MD_TENANT_ID_INT'=>$inputDataConf['MD_TENANT_ID_INT'],
                //'SKS_TRANS_TYPE'=>$inputDataConf['SKS_TRANS_TYPE'],
                'MD_SALES_TYPE_ID_INT'=>$inputDataConf['MD_SALES_TYPE_ID_INT'],
                'SHOP_NAME_CHAR'=>$inputDataConf['SHOP_NAME_CHAR'],
                'SKS_TRANS_BOOKING_DATE'=>$bookingDate,
                'SKS_TRANS_START_DATE'=>$startDate,
                'SKS_TRANS_END_DATE'=>$endDate,
                'SKS_TRANS_FREQ_NUM'=>$difMonth,
                'SKS_TRANS_FREQ_DAY_NUM'=>$difDays,
                'SKS_TRANS_TIME_PERIOD_SCHED'=>$inputDataConf['SKS_TRANS_TIME_PERIOD_SCHED'],
                'SKS_TRANS_RENT_NUM'=>$inputDataConf['SKS_TRANS_RENT_NUM'],
                'SKS_TRANS_SC_NUM'=>$inputDataConf['SKS_TRANS_SC_NUM'],
                'SKS_TRANS_DESCRIPTION'=>$inputDataConf['SKS_TRANS_DESCRIPTION'],
                'SKS_TRANS_NET_BEFORE_TAX'=>$netBeforeTax,
                'SKS_TRANS_PPN'=>$ppn,
                'SKS_TRANS_PRICE'=>$total,
                'SKS_TRANS_DP_PERSEN'=>$inputDataConf['SKS_TRANS_DP_PERSEN'],
                'SKS_TRANS_DP_NUM'=>$downPayment,
                'SKS_DEPOSIT_MONTH'=>0,
                'SKS_DEPOSIT_TYPE'=>$inputDataConf['SKS_DEPOSIT_TYPE'],
                'SKS_DEPOSIT_NUM'=>$inputDataConf['SKS_DEPOSIT_NUM'],
                'SKS_DEPOSIT_DATE'=>$inputDataConf['SKS_DEPOSIT_DATE'],
                'SKS_INVEST_NUM'=>$inputDataConf['SKS_INVEST_NUM'],
                'SKS_INVEST_RATE'=>$inputDataConf['SKS_INVEST_RATE'],
                'SKS_REVENUE_LOW_NUM'=>$inputDataConf['SKS_REVENUE_LOW_NUM'],
                'SKS_REVENUE_LOW_RATE'=>$inputDataConf['SKS_REVENUE_LOW_RATE'],
                'SKS_REVENUE_HIGH_NUM'=>$inputDataConf['SKS_REVENUE_HIGH_NUM'],
                'SKS_REVENUE_HIGH_RATE'=>$inputDataConf['SKS_REVENUE_HIGH_RATE'],
                'SKS_TRANS_GRASS_TYPE'=>$inputDataConf['SKS_TRANS_GRASS_TYPE'],
                'SKS_TRANS_GRASS_PERIOD'=>$inputDataConf['SKS_TRANS_GRASS_PERIOD'],
                //'SKS_TRANS_GRASS_DATE'=>$inputDataConf['SKS_TRANS_GRASS_DATE'],
                'SKS_REQUEST_CHAR'=>Session::get('name'),
                'SKS_REQUEST_DATE'=>$date,
                'PROJECT_NO_CHAR'=>$project_no,
                'updated_at'=>$date
            ]);
        // dd($dataVendor);

        \Session::flash('message', 'Saving Edit Data Confirmation Letter '.$inputDataConf['SKS_TRANS_NOCHAR']);
        $action = "EDIT DATA";
        $description = 'Saving Edit Data Confirmation Letter '.$inputDataConf['SKS_TRANS_NOCHAR'];
        $this->saveToLog($action, $description);

        return redirect()->route('marketing.confirmationletter.viewlistdata')
            ->with('success','Saving Edit Data Confirmation Letter '.$inputDataConf['SKS_TRANS_NOCHAR'].' Successfully');
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

        $confLetter = DB::select("select a.SKS_TRANS_ID_INT,a.SKS_TRANS_NOCHAR,b.MD_TENANT_NAME_CHAR,c.LOT_STOCK_NO,FORMAT(a.SKS_TRANS_START_DATE,'dd-MM-yyyy') as SKS_TRANS_START_DATE,
                                       FORMAT(a.SKS_TRANS_END_DATE,'dd-MM-yyyy') as SKS_TRANS_END_DATE,a.SKS_TRANS_PRICE
                                from SKS_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                                INNER JOIN LOT_STOCK as c ON a.LOT_STOCK_ID_INT = c.LOT_STOCK_ID_INT
                                where a.PROJECT_NO_CHAR = '".$project_no."'
                                AND a.SKS_STATUS_INT IN (1)");

        return View::make('page.confirmationletter.listDataConfirmationLetterAppr',
            ['confLetter'=>$confLetter]);
    }

    public function approveDataConfLetter($SKS_TRANS_ID_INT){
//        $isLogged = (bool) Session::get('dataSession.isLogin');
//        //''
//        if ($isLogged == FALSE) {
//            //dd($isLogged);
//            return redirect('/login');
//        }
        $date = Carbon::parse(Carbon::now());

        $dataConfLetter = DB::table('SKS_TRANS')
            ->where('SKS_TRANS_ID_INT','=',$SKS_TRANS_ID_INT)
            ->first();

        DB::table('SKS_TRANS')
            ->where('SKS_TRANS_ID_INT','=',$SKS_TRANS_ID_INT)
            ->update([
                'SKS_STATUS_INT'=>2,
                'SKS_APPR_DATE'=>$date,
                'SKS_APPR_CHAR'=>Session::get('name'),
                'updated_at'=>$date
            ]);

        $action = "APPROVE DATA";
        $description = 'Approve Confirmation Letter : '.$dataConfLetter->SKS_TRANS_NOCHAR.' succesfully';
        $this->saveToLog($action, $description);
        return redirect()->route('marketing.confirmationletter.viewlistdataappr')
            ->with('success','Approve Confirmation Letter '.$dataConfLetter->SKS_TRANS_NOCHAR.' Succesfully');
    }

    public function PrintSKS($SKS_TRANS_ID_INT){
        $project_no = session('current_project');
        $converter = new utilConverter();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $UserNameSales = trim(session('first_name') . ' ' . session('last_name'));

        $dataSKS = DB::table('SKS_TRANS')
            ->where('SKS_TRANS_ID_INT','=',$SKS_TRANS_ID_INT)
            ->first();

        $tahun = (int)$dataSKS->SKS_TRANS_FREQ_NUM/12;

        $bulan = $dataSKS->SKS_TRANS_FREQ_NUM - ($tahun * 12);

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

        if ($dataSKS->SKS_TRANS_FREQ_DAY_NUM > 0){
            $sewaHari = number_format($dataSKS->SKS_TRANS_FREQ_DAY_NUM,0,'','').' Hari';
        }else{
            $sewaHari = '';
        }

        $masaSewa = $sewaTahun.' '.$sewaBulan.' '.$sewaHari;

        if($dataSKS->SKS_DEPOSIT_TYPE == 'SC')
        {
            $deposit = $dataSKS->SKS_DEPOSIT_MONTH.' Bulan Service Charge';
        }
        else
        {
            $deposit = '';
        }

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataSKS->MD_TENANT_ID_INT)
            ->first();

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$dataSKS->LOT_STOCK_ID_INT)
            ->first();

        $datePrint = $converter->indonesian_date($dateNow, 'd F Y');
        $dateDocument = $converter->indonesian_date($dataSKS->SKS_TRANS_BOOKING_DATE, 'd F Y');

        $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

        return View::make('page.confirmationletter.pdfCetakConfirmationLetter',
            ['dataSKS'=>$dataSKS,'datePrint'=>$datePrint,'project_no'=>$project_no,
                'dataProject'=>$dataProject,'UserNameSales'=>$UserNameSales,'dataLot'=>$dataLot,
                'dateDocument'=>$dateDocument,'dataTenant'=>$dataTenant,'masaSewa'=>$masaSewa,
                'deposit'=>$deposit
            ]);
    }
}
