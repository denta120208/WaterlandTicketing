<?php

namespace App\Http\Controllers\MasterData\Promo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class PromoController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        $dataPromo = DB::select("SELECT a.PROMO_TICKET_PURCHASE_ID_INT, a.DESC_CHAR, a.TICKET_FREE_INT, a.DISCOUNT_PERCENT_FLOAT, a.DISCOUNT_NOMINAL_FLOAT,
            a.START_PROMO_DATE, a.END_PROMO_DATE,
            CASE WHEN a.MD_GROUP_TICKET_CHAR = 'ALL' THEN 'ALL' ELSE b.MD_GROUP_TICKET_DESC END AS MD_GROUP_TICKET_DESC,
            CASE WHEN a.MD_PRICE_TICKET_CHAR = 'ALL' THEN 'ALL' ELSE c.MD_PRICE_TICKET_DESC END AS MD_PRICE_TICKET_DESC,
            CASE WHEN a.PAYMENT_METHOD_CHAR = 'ALL' THEN 'ALL' ELSE d.PAYMENT_METHOD_DESC_CHAR END AS PAYMENT_METHOD_DESC_CHAR,
            a.MIN_TICKET_QTY, a.MIN_TICKET_PAYMENT, a.created_by, a.created_at, a.updated_by, a.updated_at,
            (SELECT COUNT(*) FROM TRANS_PROMO_TICKET_PURCHASE WHERE PROMO_TICKET_PURCHASE_ID_INT = a.PROMO_TICKET_PURCHASE_ID_INT) AS COUNT_TRANS_PROMO,
            e.DESC_CHAR AS DESC_CHAR_STATUS, a.[STATUS], a.MAX_TRX_NUMBER
            FROM MD_PROMO_TICKET_PURCHASE AS a
            LEFT JOIN MD_GROUP_TICKET AS b ON CAST(b.MD_GROUP_TICKET_ID_INT AS VARCHAR) = a.MD_GROUP_TICKET_CHAR
            LEFT JOIN MD_PRICE_TICKET AS c ON CAST(c.MD_PRICE_TICKET_ID_INT AS VARCHAR) = a.MD_PRICE_TICKET_CHAR
            LEFT JOIN MD_PAYMENT_METHOD AS d ON CAST(d.PAYMENT_METHOD_ID_INT AS VARCHAR) = a.PAYMENT_METHOD_CHAR
            LEFT JOIN MD_PROMO_TICKET_PURCHASE_STATUS AS e ON e.ID_STATUS = a.[STATUS]
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'");

        return view('MasterData.Promo.promo')
            ->with('dataPromo', $dataPromo);
    }

    public function addNewPromo() {
        $project_no = session('current_project');

        $ddlDataTicketGroup = DB::table('MD_GROUP_TICKET')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('MasterData.Promo.add_new_promo')
            ->with('ddlDataTicketGroup', $ddlDataTicketGroup)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod);
    }

    public function editViewPromo($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlDataTicketGroup = DB::table('MD_GROUP_TICKET')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $dataPromo = DB::table('MD_PROMO_TICKET_PURCHASE')
            ->where('PROMO_TICKET_PURCHASE_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.Promo.edit_view_promo')
            ->with('ddlDataTicketGroup', $ddlDataTicketGroup)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('dataPromo', $dataPromo);
    }

    public function getTicketPrice($id) {
        $project_no = session('current_project');

        $ddlDataTicketPrice = DB::table('MD_PRICE_TICKET')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('MD_GROUP_TICKET_ID_INT', $id)->get();
        
        return response()->json([
            'ddlDataTicketPrice' => $ddlDataTicketPrice
        ]);
    }

    public function savePromo(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        try {
            DB::beginTransaction();

            $startDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_START_PROMO));
            $endDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_END_PROMO));

            DB::table('MD_PROMO_TICKET_PURCHASE')->insert([
                'DESC_CHAR' => $request->TXT_DESC,
                'TICKET_FREE_INT' => $request->TXT_TICKET_FREE,
                'DISCOUNT_PERCENT_FLOAT' => $request->TXT_DISCOUNT_PERCENT,
                'DISCOUNT_NOMINAL_FLOAT' => $request->TXT_DISCOUNT_NOMINAL,
                'START_PROMO_DATE' => $startDatetimePromo,
                'END_PROMO_DATE' => $endDatetimePromo,
                'MD_GROUP_TICKET_CHAR' => $request->DDL_TICKET_GROUP,
                'MD_PRICE_TICKET_CHAR' => $request->DDL_TICKET_PRICE,
                'PAYMENT_METHOD_CHAR' => $request->DDL_PAYMENT_METHOD,
                'MIN_TICKET_QTY' => $request->TXT_MIN_TICKET_QTY,
                'MIN_TICKET_PAYMENT' => $request->TXT_MIN_TICKET_PAYMENT,
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
            return redirect()->route('promo');
        }

        session()->flash('success', "Save Promo Successfully!");
        return redirect()->route('promo');
    }

    public function editPromo(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));
        
        try {
            DB::beginTransaction();

            $startDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_START_PROMO));
            $endDatetimePromo = strftime('%Y-%m-%d %H:%M:%S', strtotime($request->TXT_END_PROMO));

            DB::table('MD_PROMO_TICKET_PURCHASE')->where('PROMO_TICKET_PURCHASE_ID_INT', $request->TXT_PROMO_ID)->update([
                'DESC_CHAR' => $request->TXT_DESC,
                'TICKET_FREE_INT' => $request->TXT_TICKET_FREE,
                'DISCOUNT_PERCENT_FLOAT' => $request->TXT_DISCOUNT_PERCENT,
                'DISCOUNT_NOMINAL_FLOAT' => $request->TXT_DISCOUNT_NOMINAL,
                'START_PROMO_DATE' => $startDatetimePromo,
                'END_PROMO_DATE' => $endDatetimePromo,
                'MD_GROUP_TICKET_CHAR' => $request->DDL_TICKET_GROUP,
                'MD_PRICE_TICKET_CHAR' => $request->DDL_TICKET_PRICE,
                'PAYMENT_METHOD_CHAR' => $request->DDL_PAYMENT_METHOD,
                'MIN_TICKET_QTY' => $request->TXT_MIN_TICKET_QTY,
                'MIN_TICKET_PAYMENT' => $request->TXT_MIN_TICKET_PAYMENT,
                'MAX_TRX_NUMBER' => $request->TXT_MAX_TRX_NUM,
                'PROJECT_NO_CHAR' => $project_no,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('promo');
        }

        session()->flash('success', "Edit Promo Successfully!");
        return redirect()->route('promo');
    }

    public function deletePromo($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_TICKET_PURCHASE')->where('PROMO_TICKET_PURCHASE_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex);
            return redirect()->route('promo');
        }

        session()->flash('success', "Cancel Promo Successfully!");
        return redirect()->route('promo');
    }

    public function terminatePromo($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            \DB::beginTransaction();

            DB::table('MD_PROMO_TICKET_PURCHASE')->where('PROMO_TICKET_PURCHASE_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "4",
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);
        
            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed terminate data, errmsg : ' . $ex);
            return redirect()->route('promo');
        }

        session()->flash('success', "Terminate Promo Successfully!");
        return redirect()->route('promo');
    }

    public function apprSMMPromo($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_TICKET_PURCHASE')->where('PROMO_TICKET_PURCHASE_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'APPR_SMM_BY' => $userName,
                'APPR_SMM_AT' => $dateNow
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed approve SMM data, errmsg : ' . $ex);
            return redirect()->route('promo');
        }

        session()->flash('success', "Approve SMM Promo Successfully!");
        return redirect()->route('promo');
    }

    public function apprGMPromo($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_TICKET_PURCHASE')->where('PROMO_TICKET_PURCHASE_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "3",
                'APPR_GM_BY' => $userName,
                'APPR_GM_AT' => $dateNow
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed approve GM data, errmsg : ' . $ex);
            return redirect()->route('promo');
        }

        session()->flash('success', "Approve GM Promo Successfully!");
        return redirect()->route('promo');
    }

    public function unapprSMMPromo($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_TICKET_PURCHASE')->where('PROMO_TICKET_PURCHASE_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "1",
                'APPR_SMM_BY' => NULL,
                'APPR_SMM_AT' => NULL
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed unapprove SMM data, errmsg : ' . $ex);
            return redirect()->route('promo');
        }

        session()->flash('success', "Unapprove SMM Promo Successfully!");
        return redirect()->route('promo');
    }

    public function unapprGMPromo($id) {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        try {
            DB::beginTransaction();

            DB::table('MD_PROMO_TICKET_PURCHASE')->where('PROMO_TICKET_PURCHASE_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'APPR_GM_BY' => NULL,
                'APPR_GM_AT' => NULL
            ]);
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed unapprove GM data, errmsg : ' . $ex);
            return redirect()->route('promo');
        }

        session()->flash('success', "Unapprove GM Promo Successfully!");
        return redirect()->route('promo');
    }
}
