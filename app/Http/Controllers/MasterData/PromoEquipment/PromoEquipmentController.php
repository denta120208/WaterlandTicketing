<?php

namespace App\Http\Controllers\MasterData\PromoEquipment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class PromoEquipmentController extends Controller
{   
    public function index() {
        $project_no = session('current_project');

        $dataPromoEquipment = DB::select("SELECT a.PROMO_EQUIPMENT_ID_INT, a.DESC_CHAR, a.QTY_FREE_INT, a.DISCOUNT_PERCENT_FLOAT, a.DISCOUNT_NOMINAL_FLOAT,
            a.START_PROMO_DATE, a.END_PROMO_DATE,
            CASE WHEN a.MD_EQUIPMENT_CATEGORY_CHAR = 'ALL' THEN 'ALL' ELSE b.MD_EQUIPMENT_CATEGORY_DESC_CHAR END AS MD_EQUIPMENT_CATEGORY_DESC_CHAR,
            CASE WHEN a.PAYMENT_METHOD_CHAR = 'ALL' THEN 'ALL' ELSE c.PAYMENT_METHOD_DESC_CHAR END AS PAYMENT_METHOD_DESC_CHAR,
            a.MIN_QTY, a.MIN_PAYMENT, a.created_by, a.created_at, a.updated_by, a.updated_at,
            (SELECT COUNT(*) FROM TRANS_PROMO_EQUIPMENT WHERE PROMO_EQUIPMENT_ID_INT = a.PROMO_EQUIPMENT_ID_INT) AS COUNT_TRANS_PROMO,
            d.DESC_CHAR AS DESC_CHAR_STATUS, a.[STATUS], a.MAX_TRX_NUMBER
            FROM MD_PROMO_EQUIPMENT AS a
            LEFT JOIN MD_EQUIPMENT_CATEGORY AS b ON CAST(b.MD_EQUIPMENT_CATEGORY_ID_INT AS VARCHAR) = a.MD_EQUIPMENT_CATEGORY_CHAR
            LEFT JOIN MD_PAYMENT_METHOD AS c ON CAST(c.PAYMENT_METHOD_ID_INT AS VARCHAR) = a.PAYMENT_METHOD_CHAR
            LEFT JOIN MD_PROMO_EQUIPMENT_STATUS AS d ON d.ID_STATUS = a.[STATUS]
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'");

        return view('MasterData.PromoEquipment.promoEquipment')
            ->with('dataPromoEquipment', $dataPromoEquipment);
    }

    public function addNewPromoEquipment() {
        $project_no = session('current_project');

        $ddlDataCategory = DB::table('MD_EQUIPMENT_CATEGORY')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('MasterData.PromoEquipment.add_new_promo_equipment')
            ->with('ddlDataCategory', $ddlDataCategory)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod);
    }

    public function editViewPromoEquipment($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlDataCategory = DB::table('MD_EQUIPMENT_CATEGORY')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $dataPromo = DB::table('MD_PROMO_EQUIPMENT')
            ->where('PROMO_EQUIPMENT_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.PromoEquipment.edit_view_promo_equipment')
            ->with('ddlDataCategory', $ddlDataCategory)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('dataPromo', $dataPromo);
    }

    public function savePromoEquipment(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        try {
            DB::beginTransaction();

            $startDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_START_PROMO));
            $endDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_END_PROMO));

            DB::table('MD_PROMO_EQUIPMENT')->insert([
                'MD_EQUIPMENT_CATEGORY_CHAR' => $request->DDL_CATEGORY,
                'DESC_CHAR' => $request->TXT_DESC,
                'QTY_FREE_INT' => $request->TXT_EQUIPMENT_FREE,
                'DISCOUNT_PERCENT_FLOAT' => $request->TXT_DISCOUNT_PERCENT,
                'DISCOUNT_NOMINAL_FLOAT' => $request->TXT_DISCOUNT_NOMINAL,
                'START_PROMO_DATE' => $startDatetimePromo,
                'END_PROMO_DATE' => $endDatetimePromo,
                'PAYMENT_METHOD_CHAR' => $request->DDL_PAYMENT_METHOD,
                'MIN_QTY' => $request->TXT_MIN_QTY,
                'MIN_PAYMENT' => $request->TXT_MIN_PAYMENT,
                'MAX_TRX_NUMBER' => $request->TXT_MAX_TRX_NUM,
                'PROJECT_NO_CHAR' => $project_no,
                'STATUS' => "1",
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();            
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Save Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }

    public function editPromoEquipment(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        try {
            DB::beginTransaction();

            $startDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_START_PROMO));
            $endDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_END_PROMO));

            DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $request->TXT_PROMO_ID)->update([
                'MD_EQUIPMENT_CATEGORY_CHAR' => $request->DDL_CATEGORY,
                'DESC_CHAR' => $request->TXT_DESC,
                'QTY_FREE_INT' => $request->TXT_EQUIPMENT_FREE,
                'DISCOUNT_PERCENT_FLOAT' => $request->TXT_DISCOUNT_PERCENT,
                'DISCOUNT_NOMINAL_FLOAT' => $request->TXT_DISCOUNT_NOMINAL,
                'START_PROMO_DATE' => $startDatetimePromo,
                'END_PROMO_DATE' => $endDatetimePromo,
                'PAYMENT_METHOD_CHAR' => $request->DDL_PAYMENT_METHOD,
                'MIN_QTY' => $request->TXT_MIN_QTY,
                'MIN_PAYMENT' => $request->TXT_MIN_PAYMENT,
                'MAX_TRX_NUMBER' => $request->TXT_MAX_TRX_NUM,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Edit Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }

    public function deletePromoEquipment($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Cancel Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }

    public function terminatePromoEquipment($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            \DB::beginTransaction();

            DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "4",
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);
        
            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed terminate data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Terminate Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }

    public function apprSMMPromoEquipment($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'APPR_SMM_BY' => $userName,
                'APPR_SMM_AT' => $dateNow
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed approve SMM data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Approve SMM Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }

    public function apprGMPromoEquipment($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "3",
                'APPR_GM_BY' => $userName,
                'APPR_GM_AT' => $dateNow
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed approve GM data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Approve GM Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }

    public function unapprSMMPromoEquipment($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "1",
                'APPR_SMM_BY' => NULL,
                'APPR_SMM_AT' => NULL
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed unapprove SMM data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Unapprove SMM Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }

    public function unapprGMPromoEquipment($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'APPR_GM_BY' => NULL,
                'APPR_GM_AT' => NULL
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed unapprove GM data, errmsg : ' . $ex);
            return redirect()->route('promoEquipment');
        }

        session()->flash('success', "Unapprove GM Promo Equipment Successfully!");
        return redirect()->route('promoEquipment');
    }
}
