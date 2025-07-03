<?php

namespace App\Http\Controllers\MasterData\PromoPOS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class PromoPOSController extends Controller
{   
    public function index() {
        $project_no = session('current_project');

        $dataPromoPOS = DB::select("SELECT a.PROMO_POS_ID_INT, a.DESC_CHAR, a.QTY_FREE_INT, a.DISCOUNT_PERCENT_FLOAT, a.DISCOUNT_NOMINAL_FLOAT,
            a.START_PROMO_DATE, a.END_PROMO_DATE,
            CASE WHEN a.MD_PRODUCT_POS_CATEGORY_CHAR = 'ALL' THEN 'ALL' ELSE b.DESC_CHAR END AS MD_PRODUCT_POS_CATEGORY_DESC_CHAR,
            CASE WHEN a.MD_PRODUCT_POS_CHAR = 'ALL' THEN 'ALL' ELSE e.NAMA_PRODUCT END AS MD_PRODUCT_POS_DESC_CHAR,
            CASE WHEN a.PAYMENT_METHOD_CHAR = 'ALL' THEN 'ALL' ELSE c.PAYMENT_METHOD_DESC_CHAR END AS PAYMENT_METHOD_DESC_CHAR,
            a.MIN_QTY, a.MIN_PAYMENT, a.created_by, a.created_at, a.updated_by, a.updated_at,
            (SELECT COUNT(*) FROM TRANS_PROMO_POS WHERE PROMO_POS_ID_INT = a.PROMO_POS_ID_INT) AS COUNT_TRANS_PROMO,
            d.DESC_CHAR AS DESC_CHAR_STATUS, a.[STATUS], a.MAX_TRX_NUMBER
            FROM MD_PROMO_POS AS a
            LEFT JOIN MD_PRODUCT_POS_CATEGORY AS b ON CAST(b.MD_PRODUCT_POS_CATEGORY_ID_INT AS VARCHAR) = a.MD_PRODUCT_POS_CATEGORY_CHAR
            LEFT JOIN MD_PAYMENT_METHOD AS c ON CAST(c.PAYMENT_METHOD_ID_INT AS VARCHAR) = a.PAYMENT_METHOD_CHAR
            LEFT JOIN MD_PROMO_POS_STATUS AS d ON d.ID_STATUS = a.[STATUS]
            LEFT JOIN MD_PRODUCT_POS AS e ON CAST(e.MD_PRODUCT_POS_ID_INT AS VARCHAR) = a.MD_PRODUCT_POS_CHAR
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
            ORDER BY a.created_at DESC");

        return view('MasterData.PromoPOS.promoPOS')
            ->with('dataPromoPOS', $dataPromoPOS);
    }

    public function addNewPromoPOS() {
        $project_no = session('current_project');

        $ddlDataCategory = DB::table('MD_PRODUCT_POS_CATEGORY')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('MasterData.PromoPOS.add_new_promo_pos')
            ->with('ddlDataCategory', $ddlDataCategory)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod);
    }

    public function editViewPromoPOS($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlDataCategory = DB::table('MD_PRODUCT_POS_CATEGORY')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $dataPromo = DB::table('MD_PROMO_POS')
            ->where('PROMO_POS_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.PromoPOS.edit_view_promo_pos')
            ->with('ddlDataCategory', $ddlDataCategory)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('dataPromo', $dataPromo);
    }

    public function savePromoPOS(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        try {
            DB::beginTransaction();

            $startDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_START_PROMO));
            $endDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_END_PROMO));

            DB::table('MD_PROMO_POS')->insert([
                'MD_PRODUCT_POS_CATEGORY_CHAR' => $request->DDL_CATEGORY,
                'MD_PRODUCT_POS_CHAR' => $request->DDL_PRODUCT,
                'DESC_CHAR' => $request->TXT_DESC,
                'QTY_FREE_INT' => $request->TXT_PRODUCT_POS_FREE,
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
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex->getMessage());
            session()->flash('error', 'Failed save data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Save Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function editPromoPOS(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        try {
            DB::beginTransaction();

            $startDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_START_PROMO));
            $endDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_END_PROMO));

            DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $request->TXT_PROMO_ID)->update([
                'MD_PRODUCT_POS_CATEGORY_CHAR' => $request->DDL_CATEGORY,
                'MD_PRODUCT_POS_CHAR' => $request->DDL_PRODUCT,
                'DESC_CHAR' => $request->TXT_DESC,
                'QTY_FREE_INT' => $request->TXT_PRODUCT_POS_FREE,
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
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Edit Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function deletePromoPOS($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);
        
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Cancel Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function terminatePromoPOS($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            \DB::beginTransaction();

            DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "4",
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);
        
            \DB::commit();
        } catch (\Exception $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed terminate data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Terminate Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function apprSMMPromoPOS($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'APPR_SMM_BY' => $userName,
                'APPR_SMM_AT' => $dateNow
            ]);
        
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed approve SMM data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Approve SMM Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function apprGMPromoPOS($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "3",
                'APPR_GM_BY' => $userName,
                'APPR_GM_AT' => $dateNow
            ]);
        
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed approve GM data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Approve GM Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function unapprSMMPromoPOS($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "1",
                'APPR_SMM_BY' => NULL,
                'APPR_SMM_AT' => NULL
            ]);
        
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed unapprove SMM data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Unapprove SMM Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function unapprGMPromoPOS($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'APPR_GM_BY' => NULL,
                'APPR_GM_AT' => NULL
            ]);
        
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed unapprove GM data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Unapprove GM Promo POS Successfully!");
        return redirect()->route('promoPOS');
    }

    public function getProduct($id) {
        $project_no = session('current_project');

        $ddlDataProductPOS = DB::table('MD_PRODUCT_POS')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('MD_PRODUCT_POS_CATEGORY_ID_INT', $id)->get();
        
        return response()->json([
            'ddlDataProductPOS' => $ddlDataProductPOS
        ]);
    }
}
