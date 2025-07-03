<?php


namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogActivity\LogActivityController;
use App\Model\Engineerings\UtilsBilling;
use App\Model\Engineerings\UtilsType;
use App\Model\Master\Tenant;
use App\Model\ProjectModel;
use App\Model\Vendor;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use DB, View;
use App\Model\Engineerings\UtilsFormula, App\Model\Engineerings\UtilsMeter, App\Model\Engineerings\UtilsTenant;


class MasterEngineeringController extends Controller
{
    public function __construct() {
    }

    public function index()
    {
        if (Session::get('isLogin') == false) {
            return redirect('/login');
        }
        $project_no = Session::get('PROJECT_NO_CHAR');
        $utils = UtilsFormula::where('UTILS_STATUS', '1')->where('PROJECT_NO_CHAR', $project_no)->get();
        return View::make('page.engineering.list', compact('utils'));
    }

    public function util_formula()
    {
        $project_no = session('current_project');
        $utils = UtilsFormula::where('UTILS_STATUS', '1')->where('PROJECT_NO_CHAR', $project_no)->get();
        return View::make('page.engineering.list_formula', compact('utils'));
    }

    public function set_formula(Request $request) {
        $now = date('Y-m-d H:i:s');
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        UtilsFormula::create([
            'NAME_U_FORMULA' => $request->NAME_U_FORMULA,
            'UTILS_TYPE' => $request->UTILS_TYPE,
            'UTILS_CATEGORY_ID_INT' => $request->UTILS_CATEGORY_ID_INT,
            'UTILS_LOW_RATE' => $request->UTILS_LOW_RATE,
            'UTILS_HIGH_RATE' => $request->UTILS_HIGH_RATE,
            'UTILS_BILLBOARD_RATE' => $request->UTILS_BILLBOARD_RATE,
            'UTILS_RELIABILITY_RATE' => $request->UTILS_RELIABILITY_RATE,
            'UTILS_HANDLING_FEE_RATE' => $request->UTILS_HANDLING_FEE_RATE,
            'UTILS_HANDLING_FEE_FIXAMT' => $request->UTILS_HANDLING_FEE_FIXAMT,
            'UTILS_BPJU_RATE' => $request->UTILS_BPJU_RATE,
            'UTILS_LOST_FACTOR_RATE' => $request->UTILS_LOST_FACTOR_RATE,
            'UTILS_LOST_FACTOR_FIXAMT' => $request->UTILS_LOST_FACTOR_FIXAMT,
            'UTILS_KVA_RATE' => $request->UTILS_KVA_RATE,
            'UTILS_ADMIN_RATE' => $request->UTILS_ADMIN_RATE,
            'UTILS_PPJU_RATE' => $request->UTILS_PPJU_RATE,
            'UTILS_STATUS' => '1',
            'PROJECT_NO_CHAR' => $project_no,
            'created_at' => $now,
            'created_by' => $userName
        ]);

        $action = "SAVE DATA FORMULA";
        $description = 'Save Data Formula ' . $request->NAME_U_FORMULA . ' H: ' . $request->UTILS_HIGH_RATE . ' L: ' . $request->UTILS_LOW_RATE;
        $this->saveToLogFormula($action, $description);

        return redirect()->route('engineering.util_formula')->with('success', $description);
    }

    public function edit_formula(Request $request) {
        $now = date('Y-m-d H:i:s');
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        UtilsFormula::where('ID_U_FORMULA', $request->ID_U_FORMULA)
            ->where('PROJECT_NO_CHAR', $project_no)->update([
                'NAME_U_FORMULA' => $request->NAME_U_FORMULA,
                'UTILS_TYPE' => $request->UTILS_TYPE,
                'UTILS_CATEGORY_ID_INT' => $request->UTILS_CATEGORY_ID_INT,
                'UTILS_LOW_RATE' => $request->UTILS_LOW_RATE,
                'UTILS_HIGH_RATE' => $request->UTILS_HIGH_RATE,
                'UTILS_BILLBOARD_RATE' => $request->UTILS_BILLBOARD_RATE,
                'UTILS_RELIABILITY_RATE' => $request->UTILS_RELIABILITY_RATE,
                'UTILS_HANDLING_FEE_RATE' => $request->UTILS_HANDLING_FEE_RATE,
                'UTILS_HANDLING_FEE_FIXAMT' => $request->UTILS_HANDLING_FEE_FIXAMT,
                'UTILS_BPJU_RATE' => $request->UTILS_BPJU_RATE,
                'UTILS_LOST_FACTOR_RATE' => $request->UTILS_LOST_FACTOR_RATE,
                'UTILS_LOST_FACTOR_FIXAMT' => $request->UTILS_LOST_FACTOR_FIXAMT,
                'UTILS_KVA_RATE' => $request->UTILS_KVA_RATE,
                'UTILS_ADMIN_RATE' => $request->UTILS_ADMIN_RATE,
                'UTILS_PPJU_RATE' => $request->UTILS_PPJU_RATE,
                'UTILS_STATUS' => '1',
                'PROJECT_NO_CHAR' => $project_no,
                'updated_at' => $now,
                'updated_by' => $userName
            ]);

        $action = "UPDATE DATA FORMULA";
        $description = 'Update Data Formula ' . $request->NAME_U_FORMULA . ' H: ' . $request->UTILS_HIGH_RATE . ' L: ' . $request->UTILS_LOW_RATE;
        $this->saveToLogFormula($action, $description);

        return redirect()->route('engineering.util_formula')->with('success', $description);
    }

    public function find_formula($id) {
        $now = date('Y-m-d H:i:s');
        $project_no = session('current_project');

        $util = UtilsFormula::where('ID_U_FORMULA', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return response()->json($util);
    }

    public function util_meter() {
        $project_no = session('current_project');
        $utils = UtilsMeter::where('UTILS_METER_STATUS', '>=', '1')->where('PROJECT_NO_CHAR', $project_no)->get();
        $ddlMeterType = DB::table('UTILS_TYPE')->where('UTILS_TYPE_STATUS', 1)->get();
        return View::make('page.engineering.list_meter', compact('utils', 'ddlMeterType'));
    }

    public function util_billing() {
        $project_no = session('current_project');

        $utilsBilling = DB::Select("
            Select a.ID_BILLING,b.MD_TENANT_NAME_CHAR,c.NAME_U_FORMULA,d.UTILS_METER_CHAR,e.UTILS_CATEGORY_NAME,
                    f.UTILS_TYPE_NAME,a.BILLING_DATE,BILLING_METER_START_LWBP,a.BILLING_METER_END_LWBP,
                    a.BILLING_METER_START_WBP,a.BILLING_METER_END_WBP,a.BILLING_METER_BILLBOARD_HOUR,
                    a.BILLING_METER_BILLBOARD_DAY,
                    a.BILLING_METER_LWBP_DIFF, a.BILLING_METER_WBP_DIFF,
                    CASE
                        WHEN a.BILLING_STATUS = 1 THEN 'REQUEST'
                        WHEN a.BILLING_STATUS = 2 THEN 'APPROVE'
                        WHEN a.BILLING_STATUS = 3 THEN 'INVOICE'
                        WHEN a.BILLING_STATUS = 4 THEN 'PAID'
                    ELSE 'NONE' END  AS BILLING_STATUS,
                    CASE WHEN a.IS_HANDLING = 1 THEN 'YES' ELSE 'NO' END as IS_HANDLING,
                    CASE WHEN a.IS_BPJU = 1 THEN 'YES' ELSE 'NO' END as IS_BPJU,
                    CASE WHEN a.IS_LOST_FACTOR = 1 THEN 'YES' ELSE 'NO' END as IS_LOST_FACTOR,
                   ((((a.BILLING_AMOUNT_LWBP + a.BILLING_AMOUNT_WBP) + a.BILLING_HANDLING_FEE_NUM) + a.BILLING_LOST_FACTOR_NUM) + a.BILLING_BPJU_NUM) as UTIL_AMOUNT
            from UTILS_BILLING as a INNER JOIN MD_TENANT as b ON a.ID_TENANT = b.MD_TENANT_ID_INT
            INNER JOIN UTILS_FORMULA as c ON a.ID_FORMULA = c.ID_U_FORMULA
            INNER JOIN UTILS_METER as d ON a.ID_METER = d.ID_METER
            INNER JOIN UTILS_CATEGORY as e ON a.ID_CATEGORY = e.UTILS_CATEGORY_ID_INT
            INNER JOIN UTILS_TYPE as f ON a.BILLING_TYPE = f.id
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
            AND a.BILLING_STATUS NOT IN (0)
        ");

        return View::make('page.engineering.list_billing', compact('utilsBilling'));
    }

    public function util_billing_appr() {
        $project_no = session('current_project');

        $utilsBilling = DB::Select("
            Select a.ID_BILLING,b.MD_TENANT_NAME_CHAR,c.NAME_U_FORMULA,d.UTILS_METER_CHAR,e.UTILS_CATEGORY_NAME,
                    f.UTILS_TYPE_NAME,a.BILLING_DATE,BILLING_METER_START_LWBP,a.BILLING_METER_END_LWBP,
                    a.BILLING_METER_START_WBP,a.BILLING_METER_END_WBP,a.BILLING_METER_BILLBOARD_HOUR,
                    a.BILLING_METER_BILLBOARD_DAY,
                    CASE
                        WHEN a.BILLING_STATUS = 1 THEN 'REQUEST'
                        WHEN a.BILLING_STATUS = 2 THEN 'APPROVE'
                        WHEN a.BILLING_STATUS = 3 THEN 'INVOICE'
                        WHEN a.BILLING_STATUS = 4 THEN 'PAID'
                    ELSE 'NONE' END  AS BILLING_STATUS,
                    CASE WHEN a.IS_HANDLING = 1 THEN 'YES' ELSE 'NO' END as IS_HANDLING,
                    CASE WHEN a.IS_BPJU = 1 THEN 'YES' ELSE 'NO' END as IS_BPJU,
                    CASE WHEN a.IS_LOST_FACTOR = 1 THEN 'YES' ELSE 'NO' END as IS_LOST_FACTOR,
                   (((((a.BILLING_AMOUNT_LWBP + a.BILLING_AMOUNT_WBP) + a.BILLING_HANDLING_FEE_NUM) + a.BILLING_LOST_FACTOR_NUM) + a.BILLING_BPJU_NUM) + a.BILLING_BILLBOARD_NUM + a.BILLING_AMOUNT_RELIABILITY + a.BILLING_PPJU_NUM + a.BILLING_ADMIN_NUM) as UTIL_AMOUNT
            from UTILS_BILLING as a INNER JOIN MD_TENANT as b ON a.ID_TENANT = b.MD_TENANT_ID_INT
            INNER JOIN UTILS_FORMULA as c ON a.ID_FORMULA = c.ID_U_FORMULA
            INNER JOIN UTILS_METER as d ON a.ID_METER = d.ID_METER
            INNER JOIN UTILS_CATEGORY as e ON a.ID_CATEGORY = e.UTILS_CATEGORY_ID_INT
            INNER JOIN UTILS_TYPE as f ON a.BILLING_TYPE = f.id
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
            AND a.BILLING_STATUS IN (1)
        ");

        return View::make('page.engineering.list_billing_appr', compact('utilsBilling'));
    }

    public function util_billing_unappr() {
        $project_no = session('current_project');

        $utilsBillingApprove = DB::Select("exec sp_list_util_billing_approve '".$project_no."'");

        return View::make('page.engineering.list_billing_unappr', compact('utilsBillingApprove'));
    }

    public function set_meter(Request $request) {
        try {
            \DB::beginTransaction();

            $now = date('Y-m-d H:i:s');
            $project_no = session('current_project');
            $userName = trim(session('first_name') . ' ' . session('last_name'));

            $cekUtilMeter = UtilsMeter::where('UTILS_METER_CHAR', '=', $request->UTILS_METER_CHAR)
                ->where('PROJECT_NO_CHAR', '=', $project_no)
                ->where('UTILS_METER_TYPE','=',$request->UTILS_METER_TYPE)
                ->count();

            if($cekUtilMeter > 0) {
                DB::rollback();
                return redirect()->route('engineering.util_meter')
                    ->with('error', 'Meter Name Already Exist. Save Data Fail...');
            }

            UtilsMeter::create([
                'UTILS_METER_CHAR' => $request->UTILS_METER_CHAR,
                'UTILS_METER_DT_TYPE' => 0,
                'UTILS_METER_TYPE' => $request->UTILS_METER_TYPE,
                'PROJECT_NO_CHAR' => $project_no,
                // 'UTILS_LOW_RATE' => $request->UTILS_LOW_RATE,
                'UTILS_METER_STATUS' => '1',
                'METER_STAND_START_LWBP' => $request->METER_STAND_START_LWBP,
                'METER_STAND_START_WBP' => $request->METER_STAND_START_WBP,
                'UTILS_METER_MULTIPLIER' => $request->METER_MULTIPLIER,
                'created_at' => $now,
                'created_by' => $userName
            ]);

            $action = "SAVE DATA METERS";
            $description = 'Save Data Meters ' . $request->UTILS_METER_CHAR;
            $this->saveToLogMeter($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('engineering.util_meter')->with('error', 'Failed save data, errmsg : ' . $ex);
        }

        return redirect()->route('engineering.util_meter')->with('success', $description);
    }

    public function edit_meter2(Request $request) {
        try {
            \DB::beginTransaction();

            $now = date('Y-m-d H:i:s');
            $project_no = session('current_project');
            $userName = trim(session('first_name') . ' ' . session('last_name'));

            $cekUtilMeter = UtilsMeter::where('UTILS_METER_CHAR', '=', $request->UTILS_METER_CHAR)
                ->where('PROJECT_NO_CHAR', '=', $project_no)
                ->where('UTILS_METER_TYPE','=',$request->UTILS_METER_TYPE)
                ->count();

            if($cekUtilMeter > 0) {
                DB::rollback();
                return redirect()->route('engineering.util_meter')->with('error', 'Meter Name Already Exist. Save Data Fail...');
            }

            DB::table('UTILS_METER')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('ID_METER', $request->ID_METER_EDIT)
            ->update([
                'UTILS_METER_CHAR' => $request->UTILS_METER_CHAR_EDIT,
                'UTILS_METER_TYPE' => $request->UTILS_METER_TYPE_EDIT,
                'METER_STAND_START_LWBP' => $request->METER_STAND_START_LWBP_EDIT,
                'METER_STAND_START_WBP' => $request->METER_STAND_START_WBP_EDIT,
                'UTILS_METER_MULTIPLIER' => $request->METER_MULTIPLIER_EDIT,
                'updated_at' => $now,
                'updated_by' => $userName
            ]);

            $action = "EDIT DATA METERS";
            $description = 'Edit Data Meters ' . $request->UTILS_METER_CHAR_EDIT;
            $this->saveToLogMeter($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('engineering.util_meter')->with('error', 'Failed edit data, errmsg : ' . $ex);
        }

        return redirect()->route('engineering.util_meter')->with('success', $description);
    }

    public function edit_meter(Request $request) {
        $now = date('Y-m-d H:i:s');
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        UtilsMeter::create([
            'UTILS_METER_CHAR' => $request->UTILS_METER_CHAR,
            'UTILS_METER_DT_TYPE' => $request->UTILS_METER_DT_TYPE,
            'UTILS_METER_TYPE' => $request->UTILS_METER_TYPE,
            'PROJECT_NO_CHAR' => $project_no,
            'UTILS_LOW_RATE' => $request->UTILS_LOW_RATE,
            'UTILS_METER_STATUS' => '1',
            'updated_at' => $now,
            'updated_by' => $userName
        ]);

        $action = "SAVE DATA METERS";
        $description = 'Sales Data Meters ' . $request->UTILS_METER_CHAR;
        $this->saveToLog($action, $description);

        return redirect()->route('engineering.util_tenant')->with('success', $description);
    }

    public function find_meter($id) {
        $project_no = session('current_project');
        $now = date('Y-m-d H:i:s');
        $util = UtilsMeter::where('ID_METER', $id)->where('PROJECT_NO_CHAR', $project_no)->first();
        return response()->json($util);
    }

    public function util_tenant() {
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $utils = DB::select("Select a.ID_UTILS_TENANT,c.MD_TENANT_NAME_CHAR,b.SHOP_NAME_CHAR,d.NAME_U_FORMULA,e.UTILS_METER_CHAR,f.UTILS_TYPE_NAME
                            from UTILS_TENANTS as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                            INNER JOIN MD_TENANT as c ON a.ID_TENANT = c.MD_TENANT_ID_INT
                            INNER JOIN UTILS_FORMULA as d ON a.ID_FORMULA = d.ID_U_FORMULA
                            INNER JOIN UTILS_METER as e ON a.ID_METER = e.ID_METER
                            INNER JOIN UTILS_TYPE as f ON e.UTILS_METER_TYPE = f.id
                            WHERE a.PROJECT_NO_CHAR = '" . $project_no . "'
                            AND a.TENANT_STATUS = 1
                            ");

        $meter = DB::select("Select a.ID_METER,a.UTILS_METER_CHAR,b.UTILS_TYPE_NAME
                            from UTILS_METER as a INNER JOIN UTILS_TYPE as b ON a.UTILS_METER_TYPE = b.id
                            WHere a.PROJECT_NO_CHAR = '" . $project_no . "'
                            and a.UTILS_METER_STATUS = 1");

        $util_types = DB::Select("Select a.PSM_TRANS_NOCHAR,a.MD_TENANT_ID_INT,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.SHOP_NAME_CHAR
                            from PSM_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                            where a.PROJECT_NO_CHAR = '" . $project_no . "'
                            and a.PSM_TRANS_STATUS_INT = 2");

        $uForm = DB::select("Select a.ID_U_FORMULA,a.NAME_U_FORMULA,b.UTILS_TYPE_NAME,c.UTILS_CATEGORY_NAME
                            from UTILS_FORMULA as a INNER JOIN UTILS_TYPE as b ON a.UTILS_TYPE = b.id
                            INNER JOIN UTILS_CATEGORY as c ON a.UTILS_CATEGORY_ID_INT = c.UTILS_CATEGORY_ID_INT
                            where a.PROJECT_NO_CHAR = '" . $project_no . "'
                            and a.UTILS_STATUS = 1");

        return View::make('page.engineering.list_tenant',
            compact('utils', 'meter', 'util_types', 'uForm','dataProject'));
    }

    public function add_tenant_meter()
    {
        if (Session::get('isLogin') == false) {
            return redirect('/login');
        }
        $project_no = Session::get('PROJECT_NO_CHAR');


        $meter = DB::select("Select a.ID_METER,a.UTILS_METER_CHAR,b.UTILS_TYPE_NAME
                            from UTILS_METER as a INNER JOIN UTILS_TYPE as b ON a.UTILS_METER_TYPE = b.id
                            WHere a.PROJECT_NO_CHAR = '" . $project_no . "'
                            and a.UTILS_METER_STATUS = 1");

        $util_types = DB::Select("Select a.PSM_TRANS_NOCHAR,a.MD_TENANT_ID_INT,a.LOT_STOCK_NO,b.MD_TENANT_NAME_CHAR,a.SHOP_NAME_CHAR
                            from PSM_TRANS as a INNER JOIN MD_TENANT as b ON a.MD_TENANT_ID_INT = b.MD_TENANT_ID_INT
                            where a.PROJECT_NO_CHAR = '" . $project_no . "'
                            and a.PSM_TRANS_STATUS_INT = 2");

        $uForm = DB::select("Select a.ID_U_FORMULA,a.NAME_U_FORMULA,b.UTILS_TYPE_NAME,c.UTILS_CATEGORY_NAME
                            from UTILS_FORMULA as a INNER JOIN UTILS_TYPE as b ON a.UTILS_TYPE = b.id
                            INNER JOIN UTILS_CATEGORY as c ON a.UTILS_CATEGORY_ID_INT = c.UTILS_CATEGORY_ID_INT
                            where a.PROJECT_NO_CHAR = '" . $project_no . "'
                            and a.UTILS_STATUS = 1");

        return View::make('page.engineering.form',
            compact('meter', 'util_types', 'uForm'));
    }

    public function save_tenant_meter(Request $request) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $meter = UtilsMeter::where('PROJECT_NO_CHAR', $project_no)
            ->where('ID_METER', $request->ID_METER)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR', '=', $request->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT', '=', $request->ID_TENANT)
            ->first();

        if ($request->insert_id == 1) {
            UtilsTenant::create([
                'PSM_TRANS_NOCHAR' => $dataPSM->PSM_TRANS_NOCHAR,
                'ID_TENANT' => $request->ID_TENANT,
                'ID_FORMULA' => $request->ID_U_FORMULA,
                'ID_METER' => $request->ID_METER,
                'PROJECT_NO_CHAR' => $project_no,
                'METER_TYPE' => $meter->UTILS_METER_TYPE,
                'METER_STAND_START_LWBP' => 0,
                'METER_STAND_END_LWBP' => $meter->METER_STAND_START_LWBP,
                'METER_STAND_START_WBP' => 0,
                'METER_STAND_END_WBP' => $meter->METER_STAND_START_WBP,
                'TENANT_STATUS' => '1',
                'created_at' => $dateNow,
                'created_by' => $userName
            ]);

            UtilsMeter::where('PROJECT_NO_CHAR', $project_no)
                ->where('ID_METER', $request->ID_METER)
                ->update(['UTILS_METER_STATUS' => 2]);

            $action = "INSERT DATA TENANT METER";
            $description = 'Insert Data Tenant Meter ' . $dataTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR;
            $this->saveToLogTenant($action, $description);

            return response()->json(['Success' => 'Berhasil Insert Item']);

        } else {
            UtilsTenant::where('ID_UTILS_TENANT','=',$request->ID_UTILS_TENANT)
            ->update([
                'PSM_TRANS_NOCHAR' => $dataPSM->PSM_TRANS_NOCHAR,
                'ID_TENANT' => $request->ID_TENANT,
                'ID_FORMULA' => $request->ID_U_FORMULA,
                'ID_METER' => $request->ID_METER,
                'PROJECT_NO_CHAR' => $project_no,
                'METER_TYPE' => $meter->UTILS_METER_TYPE,
                'METER_STAND_START_LWBP' => 0,
                'METER_STAND_END_LWBP' => $meter->METER_STAND_START_LWBP,
                'METER_STAND_START_WBP' => 0,
                'METER_STAND_END_WBP' => $meter->METER_STAND_START_WBP,
                'TENANT_STATUS' => '1',
                'updated_at' => $dateNow,
                'updated_by' => $userName
            ]);

            if ($request->ID_METER <> $request->ID_METER_OLD)
            {
                UtilsMeter::where('PROJECT_NO_CHAR', $project_no)
                    ->where('ID_METER', $request->ID_METER_OLD)
                    ->update(['UTILS_METER_STATUS' => 1]);

                UtilsMeter::where('PROJECT_NO_CHAR', $project_no)
                    ->where('ID_METER', $request->ID_METER)
                    ->update(['UTILS_METER_STATUS' => 2]);
            }

            $action = "UPDATE DATA TENANT METER";
            $description = 'Update Data Tenant Meter ' . $dataTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR;
            $this->saveToLogTenant($action, $description);

            return response()->json(['Success' => 'Berhasil Update Item']);
        }
    }

    public function set_tenant(Request $request)
    {
        if (Session::get('isLogin') == false) {
            return redirect('/login');
        }
        $project_no = Session::get('PROJECT_NO_CHAR');
        $utils = UtilsTenant::where('UTILS_STATUS', '1')->where('PROJECT_NO_CHAR', $project_no)->get();
        return View::make('page.engineering.list', compact('utils'));
    }

    public function edit_tenant_meter($id)
    {
        if (Session::get('isLogin') == false) {
            return redirect('/login');
        }
        $project_no = Session::get('PROJECT_NO_CHAR');
        $utils = UtilsTenant::where('TENANT_STATUS', '1')->where('PROJECT_NO_CHAR', $project_no)->where('ID_UTILS_TENANT', $id)->first();
        $meter = UtilsMeter::where('PROJECT_NO_CHAR', $project_no)->where('UTILS_METER_STATUS', '1')->get();
        return View::make('page.engineering.form_editTenant', compact('utils', 'meter'));
    }

    public function edit_tenant(Request $request)
    {
        if (Session::get('isLogin') == false) {
            return redirect('/login');
        }
        $now = date('Y-m-d H:i:s');
        UtilsTenant::where('ID_U_FORMULA', $request->ID_U_FORMULA)
            ->where('PROJECT_NO_CHAR', Session::get('PROJECT_NO_CHAR'))->update([
                'UTILS_TYPE' => $request->UTILS_TYPE,
                'NAME_U_FORMULA' => $request->NAME_U_FORMULA,
                'UTILS_STATUS' => '1',
                'PROJECT_NO_CHAR' => Session::get('PROJECT_NO_CHAR'),
                'UTILS_HIGH_RATE' => $request->UTILS_HIGH_RATE,
                'UTILS_LOW_RATE' => $request->UTILS_LOW_RATE,
                'updated_at' => $now,
                'updated_by' => Session::get('id')
            ]);
        return redirect()->route('engineering.util_formula');
    }

    public function find_tenant($id)
    {
        if (Session::get('isLogin') == false) {
            return redirect('/login');
        }
        $util = Tenant::where('MD_TENANT_ID_INT', $id)->where('PROJECT_NO_CHAR', Session::get('PROJECT_NO_CHAR'))->first();
        return response()->json($util);
    }

    public function meterIn()
    {
        $project_no = session('current_project');
        $utils = UtilsTenant::select('ID_TENANT', DB::raw('count(*) as total'))->where('TENANT_STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->groupBy('ID_TENANT')->pluck('ID_TENANT')->toArray();
        $tenants = Tenant::where('PROJECT_NO_CHAR', $project_no)->whereIn('MD_TENANT_ID_INT', $utils)->get();
        return View::make('page.engineering.list_tenantIn', compact('utils', 'tenants'));
    }

    public function meter_input($id) {
        $project_no = session('current_project');

        $utils = DB::select("Select a.ID_UTILS_TENANT,a.PSM_TRANS_NOCHAR,c.MD_TENANT_NAME_CHAR,b.SHOP_NAME_CHAR,
                                   d.NAME_U_FORMULA,g.UTILS_CATEGORY_NAME,e.UTILS_METER_CHAR,f.UTILS_TYPE_NAME,
                                   a.METER_STAND_START_LWBP,a.METER_STAND_END_LWBP,
                                   a.METER_STAND_START_WBP,a.METER_STAND_END_WBP,a.TENANT_STATUS
                            from UTILS_TENANTS as a INNER JOIN PSM_TRANS as b ON a.PSM_TRANS_NOCHAR = b.PSM_TRANS_NOCHAR
                            INNER JOIN MD_TENANT as c ON a.ID_TENANT = c.MD_TENANT_ID_INT
                            INNER JOIN UTILS_FORMULA as d ON a.ID_FORMULA = d.ID_U_FORMULA
                            INNER JOIN UTILS_METER as e ON a.ID_METER = e.ID_METER
                            INNER JOIN UTILS_TYPE as f ON a.METER_TYPE = f.id
                            INNER JOIN UTILS_CATEGORY as g ON d.UTILS_CATEGORY_ID_INT = g.UTILS_CATEGORY_ID_INT
                            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
                            --and a.TENANT_STATUS = 1
                            and a.ID_TENANT = ".$id);

        $tenant = Tenant::where('PROJECT_NO_CHAR', $project_no)->where('MD_TENANT_ID_INT', $id)->first();

        return View::make('page.engineering.form_meter', compact('utils', 'tenant'));
    }

    public function meterInput(Request $request) {
        $now = date('Y-m-d H:i:s');
        $meter_stand = 0;
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        // Validasi apabila ada input meter pada bulan yang sama (START)
        $dataMonthUtilBillReq = date('m', strtotime($request->BILLING_DATE));
        $dataYearUtilBillReq = date('Y', strtotime($request->BILLING_DATE));
        $dataUtilBillCount = \DB::select("SELECT COUNT(*) AS COUNT_UTILS_BILLING FROM UTILS_BILLING WHERE PROJECT_NO_CHAR = '".$project_no."' AND ID_UTILS_TENANT = '".$request->ID_UTILS_TENANT."' AND MONTH(BILLING_DATE) = '".$dataMonthUtilBillReq."' AND YEAR(BILLING_DATE) = '".$dataYearUtilBillReq."' AND BILLING_STATUS NOT IN (0)")[0];
        if($dataUtilBillCount->COUNT_UTILS_BILLING > 0) {
            return response()->json(['Error' => 'Input Meter Already Exists!']);
        }
        // Validasi apabila ada input meter pada bulan yang sama (END)

        // Validasi apabila ada input meter yang statusnya masih request (START)
        $dataUtilBillCount = \DB::table('UTILS_BILLING')->where('PROJECT_NO_CHAR', $project_no)->where('ID_UTILS_TENANT', $request->ID_UTILS_TENANT)->where('BILLING_STATUS', 1)->count();
        if($dataUtilBillCount > 0) {
            return response()->json(['Error' => 'The Input Meter Still Has A Status Request!']);
        }
        // Validasi apabila ada input meter yang statusnya masih request (END)

        $meter = UtilsMeter::where('ID_METER', $request->ID_METER)->where('PROJECT_NO_CHAR', $project_no)->first();

        $formula = UtilsFormula::where('ID_U_FORMULA', $request->ID_U_FORMULA)->where('PROJECT_NO_CHAR', $project_no)->first();

        if($request->METER_TYPE == 3 && $project_no == 4) // Water
        {
            // LWBP
            $nilaiLDiffLWBP = $request->BILLING_METER_END_LWBP  - $request->BILLING_METER_START_LWBP;

            if ($nilaiLDiffLWBP > 100)
            {
                $nilaiWater1 = 100;
                $nilaiWater2 = $nilaiLDiffLWBP - $nilaiWater1;

                $nilaiLWBPWater1 = round(($nilaiWater1 * $meter['UTILS_METER_MULTIPLIER']) * $formula['UTILS_LOW_RATE']);
                $nilaiLWBPWater2 = round(($nilaiWater2 * $meter['UTILS_METER_MULTIPLIER']) * $formula['UTILS_HIGH_RATE']);
                $nilaiLWBP = $nilaiLWBPWater1 + $nilaiLWBPWater2;
            }
            else
            {
                // LWBP
                $nilaiLDiffLWBP = $request->BILLING_METER_END_LWBP  - $request->BILLING_METER_START_LWBP;
                $nilaiLWBP = round(($nilaiLDiffLWBP * $meter['UTILS_METER_MULTIPLIER']) * $formula['UTILS_LOW_RATE']);
            }

            $nilaiReliability = 0;
        }
        else
        {
            $nilaiLDiffLWBP = $request->BILLING_METER_END_LWBP  - $request->BILLING_METER_START_LWBP;
            //update Aditya Sugiharto
            //20 September 2022
            if($formula['UTILS_CATEGORY_ID_INT'] == 1 ) //single tarif
            {
                $nilaiReliability = round(($nilaiLDiffLWBP * $meter['UTILS_METER_MULTIPLIER']) * $formula['UTILS_RELIABILITY_RATE']);
            }
            else
            {
                $nilaiReliability = 0;
            }

            //LWBP
            $nilaiLWBP = round(($nilaiLDiffLWBP * $meter['UTILS_METER_MULTIPLIER']) * $formula['UTILS_LOW_RATE']);
        }

        // WBP
        if($request->UTILS_CATEGORY_ID_INT == 2) // Double Tarif
        {
            $nilaiLDiffWBP = $request->BILLING_METER_END_WBP  - $request->BILLING_METER_START_WBP;
            $nilaiWBP = ($nilaiLDiffWBP * $meter['UTILS_METER_MULTIPLIER']) * $formula['UTILS_HIGH_RATE'];
        }
        else
        {
            $nilaiLDiffWBP = 0;
            $nilaiWBP = 0;
        }

        //Other Payment
        if($request->METER_TYPE == 2)  //ELECTRICITY
        {
            //Handling Fee
            if($request->IS_HANDLING == 1)
            {
                if ($formula['UTILS_HANDLING_FEE_RATE'] > 0 && $formula['UTILS_HANDLING_FEE_FIXAMT'] == 0)
                {
                    $nilaiHandlingfee = ($formula['UTILS_HANDLING_FEE_RATE']) * $formula['UTILS_KVA_RATE'];
                }
                elseif ($formula['UTILS_HANDLING_FEE_RATE'] == 0 && $formula['UTILS_HANDLING_FEE_FIXAMT'] > 0)
                {
                    $nilaiHandlingfee = $formula['UTILS_HANDLING_FEE_FIXAMT'];
                }
                else
                {
                    $nilaiHandlingfee = 0;
                }
            }
            else
            {
                $nilaiHandlingfee = 0;
            }

            //BPJU
            if($request->IS_BPJU == 1)
            {
                $nilaiBPJU = ($formula['UTILS_BPJU_RATE']/100) * ($nilaiLWBP + $nilaiWBP);
            }
            else
            {
                $nilaiBPJU = 0;
            }

            //PPJU
            if($request->IS_PPJU == 1)
            {
                $nilaiPPJU = ($formula['UTILS_PPJU_RATE']/100) * ($nilaiLWBP + $nilaiWBP);
            }
            else
            {
                $nilaiPPJU = 0;
            }

            //Lost Factor
            if ($request->IS_LOST_FACTOR == 1)
            {
                if ($formula['UTILS_LOST_FACTOR_RATE'] > 0 && $formula['UTILS_LOST_FACTOR_FIXAMT'] == 0)
                {
                    $nilaiLostFactor = ($formula['UTILS_LOST_FACTOR_RATE']/100) * ($nilaiBPJU + $nilaiLWBP + $nilaiWBP);
                }
                elseif ($formula['UTILS_LOST_FACTOR_RATE'] == 0 && $formula['UTILS_LOST_FACTOR_FIXAMT'] > 0)
                {
                    $nilaiLostFactor = $formula['UTILS_LOST_FACTOR_FIXAMT'];
                }
                else
                {
                    $nilaiLostFactor = 0;
                }
            }
            else
            {
                $nilaiLostFactor = 0;
            }
        }
        else
        {
            $nilaiHandlingfee = 0;
            $nilaiBPJU = 0;
            $nilaiLostFactor = 0;
            $nilaiPPJU = 0;
            //$nilaiAdmin = 0;
        }

        //Billboard
        $nilaiBillboard = $formula['UTILS_LOW_RATE'] * (($request->BILLING_METER_BILLBOARD_DAY * $request->BILLING_METER_BILLBOARD_HOUR * $formula['UTILS_BILLBOARD_RATE'])/1000);

        //Administration
        if($request->IS_ADMIN == 1)
        {
            $nilaiAdmin = ($formula['UTILS_ADMIN_RATE']/100) * ($nilaiLWBP + $nilaiWBP + $nilaiHandlingfee + $nilaiBPJU + $nilaiPPJU + $nilaiLostFactor + $nilaiBillboard);
        }
        else
        {
            $nilaiAdmin = 0;
        }

        try {
            UtilsBilling::create([
                'ID_TENANT'=>$request->MD_TENANT_ID_INT,
                'ID_UTILS_TENANT'=>$request->ID_UTILS_TENANT,
                'ID_FORMULA'=>$request->ID_U_FORMULA,
                'ID_METER'=>$request->ID_METER,
                'UTILS_METER_MULTIPLIER'=>$meter['UTILS_METER_MULTIPLIER'],
                'ID_CATEGORY'=>$request->UTILS_CATEGORY_ID_INT,
                'BILLING_TYPE'=>$request->METER_TYPE,
                'BILLING_DATE'=>$request->BILLING_DATE,
                'BILLING_METER_START_LWBP'=>$request->BILLING_METER_START_LWBP,
                'BILLING_METER_END_LWBP'=>$request->BILLING_METER_END_LWBP,
                'BILLING_METER_LWBP_DIFF'=>$nilaiLDiffLWBP,
                'BILLING_AMOUNT_LWBP'=>$nilaiLWBP,
                'BILLING_AMOUNT_RELIABILITY'=>$nilaiReliability,
                'BILLING_METER_START_WBP'=>$request->BILLING_METER_START_WBP,
                'BILLING_METER_END_WBP'=>$request->BILLING_METER_END_WBP,
                'BILLING_METER_WBP_DIFF'=>$nilaiLDiffWBP,
                'BILLING_AMOUNT_WBP'=>$nilaiWBP,
                'BILLING_METER_BILLBOARD_DAY'=>$request->BILLING_METER_BILLBOARD_DAY,
                'BILLING_METER_BILLBOARD_HOUR'=>$request->BILLING_METER_BILLBOARD_HOUR,
                'BILLING_BILLBOARD_NUM'=>$nilaiBillboard,
                'IS_HANDLING'=>$request->IS_HANDLING,
                'BILLING_HANDLING_FEE_NUM'=>$nilaiHandlingfee,
                'IS_BPJU'=>$request->IS_BPJU,
                'BILLING_BPJU_NUM'=>$nilaiBPJU,
                'IS_LOST_FACTOR'=>$request->IS_LOST_FACTOR,
                'BILLING_LOST_FACTOR_NUM'=>$nilaiLostFactor,
                'IS_ADMIN'=>$request->IS_ADMIN,
                'BILLING_ADMIN_NUM'=>$nilaiAdmin,
                'IS_PPJU'=>$request->IS_PPJU,
                'BILLING_PPJU_NUM'=>$nilaiPPJU,
                'BILLING_STATUS'=>1,
                'PROJECT_NO_CHAR'=>$project_no,
                'created_at'=>$now,
                'created_by'=>$userName
            ]);

            // UtilsMeter::where('ID_METER', $request->ID_METER)->where('PROJECT_NO_CHAR', $project_no)->update([
            //     'METER_STAND_START_LWBP' => $request->BILLING_METER_START_LWBP,
            //     'METER_STAND_END_LWBP' => $request->BILLING_METER_END_LWBP,
            //     'METER_STAND_START_WBP' => $request->BILLING_METER_START_WBP,
            //     'METER_STAND_END_WBP' => $request->BILLING_METER_END_WBP
            // ]);

            UtilsTenant::where('ID_UTILS_TENANT', $request->ID_UTILS_TENANT)->where('PROJECT_NO_CHAR', $project_no)->update([
                // 'METER_STAND_START_LWBP' => $request->BILLING_METER_START_LWBP,
                // 'METER_STAND_END_LWBP' => $request->BILLING_METER_END_LWBP,
                // 'METER_STAND_START_WBP' => $request->BILLING_METER_START_WBP,
                // 'METER_STAND_END_WBP' => $request->BILLING_METER_END_WBP,
                'TENANT_STATUS'=>2 //request input meter
            ]);

            $action = "SET REQ METER BILLING";
            $description = 'Set Request Meter Billing ' . $request->MD_TENANT_NAME_CHAR . ' Shop : ' . $request->SHOP_NAME_CHAR.' Type: '.$request->UTILS_TYPE_NAME.'Billing Date :'.$request->BILLING_DATE;
            $this->saveToLogBilling($action, $description);

            return response()->json(['Success' => 'Process Set Input Meter']);
        }catch (\Exception $e) {
            return response()->json(['Error' => 'Process Fail, Contact Your Administrator '.$e]);
        }
    }

    public function saveToLogFormula($action, $description)
    {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Engineering';
        $module = 'Engineering';
        $by = $userName;
        $table = 'UTILS_FORMULA';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action, $module, $submodule, $by, $table, $description));
    }

    public function saveToLogMeter($action, $description)
    {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Engineering';
        $module = 'Engineering';
        $by = $userName;
        $table = 'UTILS_METER';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action, $module, $submodule, $by, $table, $description));
    }

    public function saveToLogTenant($action, $description)
    {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Engineering';
        $module = 'Engineering';
        $by = $userName;
        $table = 'UTILS_TENANT';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action, $module, $submodule, $by, $table, $description));
    }

    public function saveToLogBilling($action, $description)
    {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Engineering';
        $module = 'Engineering';
        $by = $userName;
        $table = 'UTILS_BILLING';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action, $module, $submodule, $by, $table, $description));
    }

    public function getItemUtilsTenant(Request $request){
        $itemUtilsTenant = DB::table('UTILS_TENANTS')
            ->where('ID_UTILS_TENANT', '=', $request->ID_UTILS_TENANT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR', '=', $itemUtilsTenant->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT', '=', $dataPSM->MD_TENANT_ID_INT)
            ->first();

        $dataFormula = DB::table('UTILS_FORMULA')
            ->where('ID_U_FORMULA', '=', $itemUtilsTenant->ID_FORMULA)
            ->first();

        $dataMeter = DB::table('UTILS_METER')
            ->where('ID_METER', '=', $itemUtilsTenant->ID_METER)
            ->first();

        if ($itemUtilsTenant) {
            return response()->json([
                'status' => 'success',
                'MD_TENANT_NAME_CHAR' => $dataTenant->MD_TENANT_NAME_CHAR,
                'PSM_TRANS_NOCHAR' => $dataPSM->PSM_TRANS_NOCHAR,
                'ID_TENANT' => $dataTenant->MD_TENANT_ID_INT,
                'SHOP_NAME_CHAR' => $dataPSM->SHOP_NAME_CHAR,
                'ID_UTILS_TENANT' => $itemUtilsTenant->ID_UTILS_TENANT,
                'NAME_U_FORMULA' => $dataFormula->NAME_U_FORMULA,
                'ID_U_FORMULA' => $dataFormula->ID_U_FORMULA,
                'UTILS_METER_CHAR' => $dataMeter->UTILS_METER_CHAR,
                'ID_METER' => $dataMeter->ID_METER,
            ]);
        } else {
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function deleteItemUtilsTenant(Request $request){
        $project_no = session('current_project');

        $itemUtilsTenant = DB::table('UTILS_TENANTS')
            ->where('ID_UTILS_TENANT', '=', $request->ID_UTILS_TENANT)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR', '=', $itemUtilsTenant->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT', '=', $dataPSM->MD_TENANT_ID_INT)
            ->first();

        DB::table('UTILS_TENANTS')
            ->where('ID_UTILS_TENANT', '=', $request->ID_UTILS_TENANT)
            ->delete();

        UtilsMeter::where('PROJECT_NO_CHAR', $project_no)
            ->where('ID_METER','=', $itemUtilsTenant->ID_METER)
            ->update(['UTILS_METER_STATUS' => 1]);

        $action = "DELETE DATA TENANT METER";
        $description = 'Delete Data Tenant Meter ' . $dataTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR;
        $this->saveToLogTenant($action, $description);

        return response()->json(['Success' => 'Berhasil Delete Item']);
    }

    public function getItemUtilsTenantMeter(Request $request){
        $itemUtilsTenant = DB::table('UTILS_TENANTS')
            ->where('ID_UTILS_TENANT', '=', $request->ID_UTILS_TENANT)
            ->first();

        $dataBillType = DB::table('UTILS_TYPE')
            ->where('id','=',$itemUtilsTenant->METER_TYPE)
            ->first();

        $dataPSM = DB::table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR', '=', $itemUtilsTenant->PSM_TRANS_NOCHAR)
            ->first();

        $dataTenant = DB::table('MD_TENANT')
            ->where('MD_TENANT_ID_INT', '=', $dataPSM->MD_TENANT_ID_INT)
            ->first();

        $dataFormula = DB::table('UTILS_FORMULA')
            ->where('ID_U_FORMULA', '=', $itemUtilsTenant->ID_FORMULA)
            ->first();

        $dataCategory = DB::table('UTILS_CATEGORY')
            ->where('UTILS_CATEGORY_ID_INT','=',$dataFormula->UTILS_CATEGORY_ID_INT)
            ->first();

        $dataMeter = DB::table('UTILS_METER')
            ->where('ID_METER', '=', $itemUtilsTenant->ID_METER)
            ->first();

        if ($itemUtilsTenant) {
            return response()->json([
                'status' => 'success',
                'MD_TENANT_NAME_CHAR' => $dataTenant->MD_TENANT_NAME_CHAR,
                'MD_TENANT_ID_INT' => $dataTenant->MD_TENANT_ID_INT,
                'PSM_TRANS_NOCHAR' => $dataPSM->PSM_TRANS_NOCHAR,
                'ID_UTILS_TENANT' => $itemUtilsTenant->ID_UTILS_TENANT,
                'SHOP_NAME_CHAR' => $dataPSM->SHOP_NAME_CHAR,
                'UTILS_TYPE_NAME' => $dataBillType->UTILS_TYPE_NAME,
                'METER_TYPE' => $dataBillType->id,
                'NAME_U_FORMULA' => $dataFormula->NAME_U_FORMULA,
                'ID_U_FORMULA' => $dataFormula->ID_U_FORMULA,
                'UTILS_CATEGORY_NAME' => $dataCategory->UTILS_CATEGORY_NAME,
                'UTILS_CATEGORY_ID_INT' => $dataCategory->UTILS_CATEGORY_ID_INT,
                'UTILS_METER_CHAR' => $dataMeter->UTILS_METER_CHAR,
                'ID_METER' => $dataMeter->ID_METER,
                'BILLING_METER_START_LWBP' => number_format($itemUtilsTenant->METER_STAND_END_LWBP,2,'.',''),
                'BILLING_METER_START_WBP' => number_format($itemUtilsTenant->METER_STAND_END_WBP,2,'.',''),
            ]);
        } else {
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function deleteUtilBilling(Request $request) {
        $project_no = session('current_project');

        $dataUtilBilling = UtilsBilling::where('ID_BILLING','=',$request->ID_BILLING)
            ->first();

        $dataUtilType = UtilsType::where('id','=',$dataUtilBilling['BILLING_TYPE'])->first();

        $dataUtilTenant = UtilsTenant::where('ID_UTILS_TENANT','=',$dataUtilBilling['ID_UTILS_TENANT'])->first();

        $dataPSM = DB::Table('PSM_TRANS')
            ->where('PSM_TRANS_NOCHAR','=',$dataUtilTenant['PSM_TRANS_NOCHAR'])
            ->first();

        $datatTenant = DB::Table('MD_TENANT')
            ->where('MD_TENANT_ID_INT','=',$dataUtilBilling['ID_TENANT'])
            ->first();

        try {
            UtilsBilling::where('ID_BILLING','=',$request->ID_BILLING)
                ->update([
                    'BILLING_STATUS'=>0
                ]);

            UtilsTenant::where('ID_UTILS_TENANT', $dataUtilBilling->ID_UTILS_TENANT)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->update([
                    'TENANT_STATUS'=>1 // request input meter
                ]);

            $action = "DELETE REQ METER BILLING";
            $description = 'Delete Request Meter Billing ' . $datatTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR.' Type: '.$dataUtilType['UTILS_TYPE_NAME'].' Billing Date :'.$dataUtilBilling->BILLING_DATE;
            $this->saveToLogBilling($action, $description);

            return response()->json(['Success' => 'Process Set Input Meter']);
        } catch (\Exception $e) {
            return response()->json(['Error' => 'Process Fail, Contact Your Administrator '.$e]);
        }
    }

    public function approveUtilBilling(Request $requestUtilBilling){
        $inputDataUtilBilling = $requestUtilBilling->all();
        $project_no = session('current_project');
        $date = Carbon::parse(Carbon::now());

        $idBilling = '';
        if ($inputDataUtilBilling['countSelect'] > 0 )
        {
            for($i=0;$i<count($inputDataUtilBilling['billing']);$i++)
            {
                $dataBilling = UtilsBilling::where('ID_BILLING','=',$inputDataUtilBilling['billing'][$i])
                    ->first();

                UtilsMeter::where('ID_METER', $dataBilling['ID_METER'])
                    ->where('PROJECT_NO_CHAR', $project_no)
                    ->update([
                        'METER_STAND_START_LWBP' => $dataBilling['BILLING_METER_START_LWBP'],
                        'METER_STAND_END_LWBP' => $dataBilling['BILLING_METER_END_LWBP'],
                        'METER_STAND_START_WBP' => $dataBilling['BILLING_METER_START_WBP'],
                        'METER_STAND_END_WBP' => $dataBilling['BILLING_METER_END_WBP']
                    ]);

                UtilsTenant::where('ID_UTILS_TENANT', $dataBilling['ID_UTILS_TENANT'])
                    ->where('PROJECT_NO_CHAR', $project_no)
                    ->update([
                        'METER_STAND_START_LWBP' => $dataBilling['BILLING_METER_START_LWBP'],
                        'METER_STAND_END_LWBP' => $dataBilling['BILLING_METER_END_LWBP'],
                        'METER_STAND_START_WBP' => $dataBilling['BILLING_METER_START_WBP'],
                        'METER_STAND_END_WBP' => $dataBilling['BILLING_METER_END_WBP'],
                        'TENANT_STATUS'=>1 // active input meter
                    ]);

                UtilsBilling::where('ID_BILLING','=',$inputDataUtilBilling['billing'][$i])
                    ->update([
                        'BILLING_STATUS'=>2 // Approve Billing
                    ]);

                $idBilling .= $inputDataUtilBilling['billing'][$i].',';
            }
        }
        else
        {
            return redirect()->route('engineering.util_billing_appr')
                ->with('error','You Dont Mark Any Document');
        }

        $action = "APPROVE REQ METER BILLING";
        $description = 'Approve Req Meter Billing ID '.$idBilling;
        $this->saveToLogBilling($action, $description);

        return redirect()->route('engineering.util_billing_appr')
            ->with('success',$description);
    }

    public function unapproveUtilBilling(Request $request){
        $project_no = session('current_project');
        $dataCurrUtilBilling = UtilsBilling::where('ID_BILLING','=',$request->ID_BILLING)->first();

        // Validasi apabila ada input meter yang statusnya masih request (START)
        $dataUtilBillCount = \DB::table('UTILS_BILLING')->where('PROJECT_NO_CHAR', $project_no)->where('ID_UTILS_TENANT', $dataCurrUtilBilling['ID_UTILS_TENANT'])->where('BILLING_STATUS', 1)->count();
        if($dataUtilBillCount > 0) {
            return response()->json(['Error' => 'The Input Meter Still Has A Status Request!']);
        }
        // Validasi apabila ada input meter yang statusnya masih request (END)
        
        try {
            \DB::beginTransaction();

            $dataBackUtilBilling = \DB::table('UTILS_BILLING')->where('PROJECT_NO_CHAR', $project_no)
                ->where('ID_UTILS_TENANT', $dataCurrUtilBilling['ID_UTILS_TENANT'])
                ->whereNotIn('BILLING_STATUS', [0])
                ->whereNotIn('ID_BILLING', [$request->ID_BILLING])
                ->orderBy('BILLING_DATE', 'DESC')
                ->first();

            if(empty($dataBackUtilBilling)) {
                UtilsMeter::where('ID_METER', $dataCurrUtilBilling->ID_METER)
                    ->where('PROJECT_NO_CHAR', $project_no)
                    ->update([
                        'METER_STAND_END_LWBP' => 0,
                        'METER_STAND_END_WBP' => 0
                    ]);

                UtilsTenant::where('ID_UTILS_TENANT', $dataCurrUtilBilling->ID_UTILS_TENANT)
                    ->where('PROJECT_NO_CHAR', $project_no)
                    ->update([
                        'METER_STAND_END_LWBP' => 0,
                        'METER_STAND_END_WBP' => 0,
                        'TENANT_STATUS'=>1 // active input meter
                    ]);
            }
            else {
                UtilsMeter::where('ID_METER', $dataBackUtilBilling->ID_METER)
                    ->where('PROJECT_NO_CHAR', $project_no)
                    ->update([
                        'METER_STAND_START_LWBP' => $dataBackUtilBilling->BILLING_METER_START_LWBP,
                        'METER_STAND_END_LWBP' => $dataBackUtilBilling->BILLING_METER_END_LWBP,
                        'METER_STAND_START_WBP' => $dataBackUtilBilling->BILLING_METER_START_WBP,
                        'METER_STAND_END_WBP' => $dataBackUtilBilling->BILLING_METER_END_WBP
                    ]);

                UtilsTenant::where('ID_UTILS_TENANT', $dataBackUtilBilling->ID_UTILS_TENANT)
                    ->where('PROJECT_NO_CHAR', $project_no)
                    ->update([
                        'METER_STAND_START_LWBP' => $dataBackUtilBilling->BILLING_METER_START_LWBP,
                        'METER_STAND_END_LWBP' => $dataBackUtilBilling->BILLING_METER_END_LWBP,
                        'METER_STAND_START_WBP' => $dataBackUtilBilling->BILLING_METER_START_WBP,
                        'METER_STAND_END_WBP' => $dataBackUtilBilling->BILLING_METER_END_WBP,
                        'TENANT_STATUS'=>1 // active input meter
                    ]);
            }

            UtilsBilling::where('ID_BILLING','=',$request->ID_BILLING)
                ->update([
                    'BILLING_STATUS'=>1 // Request Status
                ]);

            $dataUtilType = UtilsType::where('id','=',$dataCurrUtilBilling['BILLING_TYPE'])->first();
            $dataUtilTenant = UtilsTenant::where('ID_UTILS_TENANT','=',$dataCurrUtilBilling['ID_UTILS_TENANT'])->first();
            $dataPSM = DB::Table('PSM_TRANS')->where('PSM_TRANS_NOCHAR','=',$dataUtilTenant['PSM_TRANS_NOCHAR'])->first();
            $datatTenant = DB::Table('MD_TENANT')->where('MD_TENANT_ID_INT','=',$dataCurrUtilBilling['ID_TENANT'])->first();

            $action = "UNAPPROVE METER BILLING";
            $description = 'Unapprove Meter Billing ' . $datatTenant->MD_TENANT_NAME_CHAR . ' Shop : ' . $dataPSM->SHOP_NAME_CHAR.' Type: '.$dataUtilType['UTILS_TYPE_NAME'].' Billing Date :'.$dataCurrUtilBilling['BILLING_DATE'];
            $this->saveToLogBilling($action, $description);

            \DB::commit();
            return response()->json(['Success' => 'Process Unapprove Meter Billing']);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['Error' => 'Process Fail, Contact Your Administrator '.$e]);
        }
    }
}
