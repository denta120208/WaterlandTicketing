<?php namespace App\Http\Controllers\MasterData;

use App\Http\Requests;
use App\Model\Engineerings\UtilsMeter;
use App\Model\Engineerings\UtilsTenant;
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

define("CustProjID","CUST_PROJ_ID_CHAR");
define("Name","NAME_CHAR");
define("Pekerjaan","PEKERJAAN_CHAR");
define("Address","ADDRESS1_CHAR");
define("MailAddress","MAIL_ADDRESS_CHAR");
define("Email","EMAIL_ADDRESS_CHAR");
define("Telephone","TELEPHONE_NO_CHAR");
define("Handphone","HANDPHONE_CHAR");
define("Handphone2","HANDPHONE2_CHAR");
define("RtRw","RTRW_CHAR");
define("Kota","KOTA_CHAR");
define("CorrAddress","CORR_ADDRESS");
define("KotaKodepos","KOTAKODEPOS_CHAR");
define("KelKec","KELKEC_CHAR");
define("Fax","FAX_NO_CHAR");
define("NoKTP","NO_KTP_CHAR");
define("NPWP","NPWP_CHAR");
define("Nationality","NATIONALITY_CHAR");
define("Religion","RELIGION_CHAR");
define("BirthPlace","BIRTHPLACE_CHAR");
define("BirthDate","BIRTHDATE_DTTIME");
define("Sex","SEX_NUM");
define("MaritalStatus","MARITAL_STATUS");
define("BankAcc","BANK_ACCOUNT_CHAR");
define("BankAccName","BANK_ACCOUNT_NAME_CHAR");
define("BankAccOwner","BANK_ACCOUNT_OWNER_CHAR");
define("masterdata","MasterData");
define("ACTION_SAVE","SAVE DATA");
define("ACTION_UPDATE","UPDATE DATA");
define("ACTION_DELETE","DELETE DATA");
define("DESC_SAVE","Saving data into customer table");
define("DESC_UPDATE","Editing data in Customer table");
define("DESC_DELETE","Deleting data in Customer table");

define("TYPE_INDV","INDV");
define("TYPE_CORP","CORP");

class Tenant extends Controller {

    public function viewListDataTenant(){
        $project_no = session('current_project');

        $tenant = DB::select("SELECT *
                            FROM MD_TENANT
                            WHERE PROJECT_NO_CHAR = '".$project_no."'");

        return View::make('page.masterdata.tenant.listDataTenant',['tenant'=>$tenant]);
    }

    public function viewFormAddDataTenant(){
        return View::make('page.masterdata.tenant.addDataTenant');
    }

    public function saveDataTenant(Requests\MasterData\AddTenantRequest $requestVend) {
        try {
            \DB::beginTransaction();

            $inputDatatenant = $requestVend->all();
            
            $project_no = session('current_project');
            $arrayPushVendor = new utilArray();
            $date = Carbon::parse(Carbon::now());
            $monthYear = $date->year.' '.$date->month;
            $counter = Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)->first();

            $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();

            $Counter = str_pad($counter->tenant_count, 5, "0", STR_PAD_LEFT);
            $Year = substr($date->year, 2);
            $Month = str_pad($date->month, 2, "0", STR_PAD_LEFT);

            $noTenant = 'TNT/'.$dataProject['PROJECT_CODE'].'/'.$Month.'/'.$Year.'/'.$Counter;

            Model\Counter::where('PROJECT_NO_CHAR', '=', $project_no)
                ->update(['tenant_count'=>$counter->tenant_count + 1]);

            DB::table('MD_TENANT')
                ->insert([
                    'MD_TENANT_NOCHAR' => $noTenant,
                    'MD_TENANT_NAME_CHAR' => strtoupper($inputDatatenant['MD_TENANT_NAME_CHAR']),
                    'MD_TENANT_ADDRESS1' => $inputDatatenant['MD_TENANT_ADDRESS1'],
                    //'MD_TENANT_ADDRESS_TAX' => $inputDatatenant['MD_TENANT_ADDRESS_TAX'],
                    'MD_TENANT_CITY_CHAR' => $inputDatatenant['MD_TENANT_CITY_CHAR'],
                    'MD_TENANT_POSCODE' => $inputDatatenant['MD_TENANT_POSCODE'],
                    'MD_TENANT_TELP' => $inputDatatenant['MD_TENANT_TELP'],
                    //'MD_TENANT_FAX' => $inputDatatenant['MD_TENANT_FAX'],
                    'MD_TENANT_EMAIL' => $inputDatatenant['MD_TENANT_EMAIL'],

                    'MD_TENANT_EMAIL_INVOICE1' => $inputDatatenant['MD_TENANT_EMAIL_INVOICE1'],
                    'MD_TENANT_EMAIL_INVOICE2' => $inputDatatenant['MD_TENANT_EMAIL_INVOICE2'],
                    'MD_TENANT_EMAIL_INVOICE3' => $inputDatatenant['MD_TENANT_EMAIL_INVOICE3'],

                    'MD_TENANT_NPWP' => $inputDatatenant['MD_TENANT_NPWP'],
                    'MD_TENANT_NIK' => $inputDatatenant['MD_TENANT_NIK'],
                    //'MD_TENANT_SUJK' => $inputDatatenant['MD_TENANT_SUJK'],
                    'MD_TENANT_BANK_NAME' => $inputDatatenant['MD_TENANT_BANK_NAME'],
                    'MD_TENANT_BANK_LOCATION' => $inputDatatenant['MD_TENANT_BANK_LOCATION'],
                    'MD_TENANT_BANK_ACCOUNT' => $inputDatatenant['MD_TENANT_BANK_ACCOUNT'],
                    'MD_TENANT_BANK_ACCOUNT_NAME' => $inputDatatenant['MD_TENANT_BANK_ACCOUNT_NAME'],
                    'PROJECT_NO_CHAR' => $project_no,
                    'MD_TENANT_DIRECTOR' => ucfirst($inputDatatenant['MD_TENANT_DIRECTOR']),
                    'MD_TENANT_DIRECTOR_JOB_TITLE'=> ucfirst($inputDatatenant['MD_TENANT_DIRECTOR_JOB_TITLE']),
                    'MD_TENANT_CP_NAME'=> ucfirst($inputDatatenant['MD_TENANT_CP_NAME']),
                    'MD_TENANT_CP_NO_TELP'=> $inputDatatenant['MD_TENANT_CP_NO_TELP'],
                    'MD_TENANT_CP_NO_EMAIL'=> $inputDatatenant['MD_TENANT_CP_NO_EMAIL'],
                    'MD_TENANT_CP_NO_HP'=> $inputDatatenant['MD_TENANT_CP_NO_HP'],
                    'MD_TENANT_PPH_INT'=> $inputDatatenant['MD_TENANT_PPH_INT'],
                    'MD_TENANT_BRANDED_INT'=> $inputDatatenant['MD_TENANT_BRANDED_INT'],
                    'created_at'=>$date,
                    'updated_at'=>$date
                ]);

            \Session::flash('message', 'Saving data Tenant '.$inputDatatenant['MD_TENANT_NAME_CHAR']);
            $action = ACTION_SAVE;
            $description = 'Saving data Tenant '.$inputDatatenant['MD_TENANT_NAME_CHAR'];
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('masterdata.tenant.viewlistdatatenant')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('masterdata.tenant.viewlistdatatenant')
            ->with('success','Saving data tenant '.$inputDatatenant['MD_TENANT_NAME_CHAR'].' Succesfully');
    }

    public function saveToLog($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'tenant';
        $module = 'masterdata';
        $by = $userName;
        $table = 'MD_TENANT';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function deleteDataTenant($MD_TENANT_ID_INT) {
        try {
            \DB::beginTransaction();
        
            $dataTenant = DB::table('MD_TENANT')
                ->where('MD_TENANT_ID_INT','=',$MD_TENANT_ID_INT)
                ->first();

            $cekDataPSM = DB::table('PSM_TRANS')
                ->where('MD_TENANT_ID_INT','=',$MD_TENANT_ID_INT)
                ->count();

            $cekDataLOI = DB::table('LOI_TRANS')
                ->where('MD_TENANT_ID_INT','=',$MD_TENANT_ID_INT)
                ->count();

            $cekDataInvoice = DB::table('INVOICE_TRANS')
                ->where('MD_TENANT_ID_INT','=',$MD_TENANT_ID_INT)
                ->count();

            if ($cekDataPSM > 0 || $cekDataLOI > 0 || $cekDataInvoice > 0)
            {
                return redirect()->route('masterdata.tenant.viewlistdatatenant')
                    ->with('error','Cannot delete '.$dataTenant->MD_TENANT_NAME_CHAR);
            }
            else
            {
                DB::statement("DELETE FROM MD_TENANT WHERE MD_TENANT_ID_INT = ".$MD_TENANT_ID_INT);
            }

            $action = ACTION_DELETE;
            $description = 'Delete Tenant Name : '.$dataTenant->MD_TENANT_NAME_CHAR.' succesfully';
            $this->saveToLog($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('masterdata.tenant.viewlistdatatenant')->with('error', 'Failed delete data, errmsg : ' . $ex);
        }

        return redirect()->route('masterdata.tenant.viewlistdatatenant')
            ->with('success','Delete data tenant '.$dataTenant->MD_TENANT_NAME_CHAR.' Succesfully');
    }

    public function viewFormEditDataTenant($MD_TENANT_ID_INT){
        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$MD_TENANT_ID_INT)
            ->first();

        $dataAddressTax = DB::select("Select a.MD_TENANT_TAX_ID_INT,cast(a.MD_TENANT_ADDRESS_TAX as varchar(max)) as MD_TENANT_ADDRESS_TAX,COUNT(b.MD_TENANT_TAX_ID_INT) as JML_PSM
                                    from MD_TENANT_ADDRESS_TAX as a LEFT JOIN PSM_TRANS as b ON a.MD_TENANT_TAX_ID_INT = b.MD_TENANT_TAX_ID_INT
                                    where a.MD_TENANT_NOCHAR = '".$dataTenant->MD_TENANT_NOCHAR."'
                                    GROUP BY a.MD_TENANT_TAX_ID_INT,cast(a.MD_TENANT_ADDRESS_TAX as varchar(max))");

        return View::make('page.masterdata.tenant.editDataTenant',
            ['dataTenant'=>$dataTenant,'dataAddressTax'=>$dataAddressTax]);
    }

    public function saveEditDataTenant(Requests\MasterData\AddTenantRequest $requestEditVend){
        $inputDataTenant = $requestEditVend->all();
        
        $project_no = session('current_project');

        DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$inputDataTenant['MD_TENANT_ID_INT'])
            ->update([
                'MD_TENANT_NAME_CHAR' => $inputDataTenant['MD_TENANT_NAME_CHAR'],
                'MD_TENANT_ADDRESS1' => $inputDataTenant['MD_TENANT_ADDRESS1'],
                'MD_TENANT_ADDRESS_TAX' => $inputDataTenant['MD_TENANT_ADDRESS_TAX'],
                'MD_TENANT_CITY_CHAR' => $inputDataTenant['MD_TENANT_CITY_CHAR'],
                'MD_TENANT_POSCODE' => $inputDataTenant['MD_TENANT_POSCODE'],
                'MD_TENANT_TELP' => $inputDataTenant['MD_TENANT_TELP'],
                //'MD_TENANT_FAX' => $inputDataTenant['MD_TENANT_FAX'],
                'MD_TENANT_EMAIL' => $inputDataTenant['MD_TENANT_EMAIL'],
                'MD_TENANT_NPWP' => $inputDataTenant['MD_TENANT_NPWP'],

                'MD_TENANT_EMAIL_INVOICE1' => $inputDataTenant['MD_TENANT_EMAIL_INVOICE1'],
                'MD_TENANT_EMAIL_INVOICE2' => $inputDataTenant['MD_TENANT_EMAIL_INVOICE2'],
                'MD_TENANT_EMAIL_INVOICE3' => $inputDataTenant['MD_TENANT_EMAIL_INVOICE3'],

                'MD_TENANT_NIK' => $inputDataTenant['MD_TENANT_NIK'],
                //'MD_TENANT_SUJK' => $inputDataTenant['MD_TENANT_SUJK'],
                'MD_TENANT_BANK_NAME' => $inputDataTenant['MD_TENANT_BANK_NAME'],
                'MD_TENANT_BANK_LOCATION' => $inputDataTenant['MD_TENANT_BANK_LOCATION'],
                'MD_TENANT_BANK_ACCOUNT' => $inputDataTenant['MD_TENANT_BANK_ACCOUNT'],
                'MD_TENANT_BANK_ACCOUNT_NAME' => $inputDataTenant['MD_TENANT_BANK_ACCOUNT_NAME'],
                'PROJECT_NO_CHAR' => $project_no,
                'MD_TENANT_DIRECTOR' => ucfirst($inputDataTenant['MD_TENANT_DIRECTOR']),
                'MD_TENANT_DIRECTOR_JOB_TITLE'=> ucfirst($inputDataTenant['MD_TENANT_DIRECTOR_JOB_TITLE']),
                'MD_TENANT_CP_NAME'=> ucfirst($inputDataTenant['MD_TENANT_CP_NAME']),
                'MD_TENANT_CP_NO_TELP'=> $inputDataTenant['MD_TENANT_CP_NO_TELP'],
                'MD_TENANT_CP_NO_EMAIL'=> $inputDataTenant['MD_TENANT_CP_NO_EMAIL'],
                'MD_TENANT_CP_NO_HP'=> $inputDataTenant['MD_TENANT_CP_NO_HP'],
                'MD_TENANT_PPH_INT'=> $inputDataTenant['MD_TENANT_PPH_INT'],
                'MD_TENANT_BRANDED_INT'=> $inputDataTenant['MD_TENANT_BRANDED_INT'],
            ]);

        \Session::flash('message', 'Saving data tenant '.$inputDataTenant['MD_TENANT_NAME_CHAR']);
        $action = ACTION_UPDATE;
        $description = 'Saving data tenant '.$inputDataTenant['MD_TENANT_NAME_CHAR'];
        $this->saveToLog($action, $description);
        
        return redirect()->route('masterdata.tenant.viewlistdatatenant')
            ->with('success','Saving data tenant '.$inputDataTenant['MD_TENANT_NAME_CHAR'].' Succesfully');
    }

    public function saveAddressTenant(\Illuminate\Http\Request $request) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_NOCHAR','=',$request->MD_TENANT_NOCHAR)
            ->first();

        if ($request->insert_id == 1) {
            DB::table('MD_TENANT_ADDRESS_TAX')
                ->insert([
                    'MD_TENANT_NOCHAR'=>$request->MD_TENANT_NOCHAR,
                    'MD_TENANT_ADDRESS_TAX'=>$request->MD_TENANT_ADDRESS_TAX,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'CREATE_BY'=>$userName,
                    'CREATE_DATE'=>$dateNow,
                    'created_at'=>$dateNow,
                    'updated_at'=>$dateNow
                ]);

            $action = "INSERT DATA ADDRESS TAX TENANT ";
            $description = 'Insert Data Address Tax Tenant ' . $dataTenant->MD_TENANT_NAME_CHAR;
            $this->saveToLog($action, $description);
            return response()->json(['Success' => 'Berhasil Insert Item']);
        } else {
            DB::table('MD_TENANT_ADDRESS_TAX')
                ->where('MD_TENANT_TAX_ID_INT','=',$request->MD_TENANT_TAX_ID_INT)
                ->update([
                    'MD_TENANT_ADDRESS_TAX'=>$request->MD_TENANT_ADDRESS_TAX,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'UPDATE_BY'=>$userName,
                    'UPDATE_DATE'=>$dateNow,
                    'updated_at'=>$dateNow
                ]);

            $action = "UPDATE DATA ADDRESS TAX TENANT";
            $description = 'Update Data Address Tax Tenant' . $dataTenant->MD_TENANT_NAME_CHAR;
            $this->saveToLog($action, $description);
            return response()->json(['Success' => 'Berhasil Update Item']);
        }
    }

    public function getItemAddressTenant(\Illuminate\Http\Request $request){
        $itemAddresstax = DB::table('MD_TENANT_ADDRESS_TAX')
            ->where('MD_TENANT_TAX_ID_INT', '=', $request->MD_TENANT_TAX_ID_INT)
            ->first();

        if ($itemAddresstax) {
            return response()->json([
                'status' => 'success',
                'MD_TENANT_ADDRESS_TAX' => $itemAddresstax->MD_TENANT_ADDRESS_TAX,
                'MD_TENANT_TAX_ID_INT' => $itemAddresstax->MD_TENANT_TAX_ID_INT
            ]);
        } else {
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function deleteItemAddressTenant(\Illuminate\Http\Request $request){
        $itemAddresstax = DB::table('MD_TENANT_ADDRESS_TAX')
            ->where('MD_TENANT_TAX_ID_INT', '=', $request->MD_TENANT_TAX_ID_INT)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_NOCHAR', '=', $itemAddresstax->MD_TENANT_NOCHAR)
            ->first();

        DB::table('MD_TENANT_ADDRESS_TAX')
            ->where('MD_TENANT_TAX_ID_INT', '=', $request->MD_TENANT_TAX_ID_INT)
            ->delete();

        $action = "DELETE DATA ADDRESS TAX TENANT";
        $description = 'Delete Data Address Tax Tenant' . $dataTenant->MD_TENANT_NAME_CHAR;
        $this->saveToLog($action, $description);
        return response()->json(['Success' => 'Berhasil Delete Item']);
    }
}
