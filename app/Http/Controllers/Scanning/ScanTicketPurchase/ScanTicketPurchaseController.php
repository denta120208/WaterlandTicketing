<?php

namespace App\Http\Controllers\Scanning\ScanTicketPurchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class ScanTicketPurchaseController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');
        $dataCameraSetting = DB::table('CAMERA_SETTING')->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('Scanning.ScanTicketPurchase.scan_ticket_purchase')
            ->with('dataCameraSetting', $dataCameraSetting);
    }

    public function changeCameraSetting(Request $request) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            if($request->CAM_SETTING == "user") {
                $DESC_CHAR = "Kamera Depan";
            }
            else if($request->CAM_SETTING == "environment") {
                $DESC_CHAR = "Kamera Belakang";
            }
            else {
                $DESC_CHAR = "Camera Disabled";
            }

            DB::table('CAMERA_SETTING')->where('PROJECT_NO_CHAR', $project_no)->update([
                'DESC_CHAR' => $DESC_CHAR,
                'SETTING' => $request->CAM_SETTING,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('scan_ticket_purchase');
        }        

        return redirect()->route('scan_ticket_purchase');
    }

    public function inputQRNumber(Request $request) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));

        $dataTicketDetail = count(DB::select("SELECT * FROM TRANS_TICKET_PURCHASE_DETAILS AS a
            WHERE a.NUMBER_TICKET = '".$request->QR_NUMBER."'
            AND a.IS_SCAN = '0'
            AND a.PROJECT_NO_CHAR = '".$project_no."'
            AND CAST(a.created_at AS DATE) = '".date('Y-m-d')."'"));

        if($dataTicketDetail == 0) {
            session()->flash('error', "QR Number Not Found!");
            return redirect()->route('scan_ticket_purchase');
        }

        try {
            \DB::beginTransaction();

            DB::table('TRANS_TICKET_PURCHASE_DETAILS')->where('NUMBER_TICKET', $request->QR_NUMBER)->update([
                'IS_SCAN' => "1",
                'SCAN_BY' => $cashierName,
                'SCAN_AT' => $dateNow,
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            DB::table('LOG_COUNTING_VISITORS')->insert([
                'LOG_COUNTING_VISITORS_TYPE_ID_INT' => "1",
                'AMOUNT_INT' => "1",
                'TIME_DTTIME' => $dateNow,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $cashierName,
                'created_at' => $dateNow
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('scan_ticket_purchase');
        }

        session()->flash('success', "Scan QRCode Number Successfully!");
        return redirect()->route('scan_ticket_purchase');
    }

    public function scanningTicketPurchase($param1) {
        $project_no = session('current_project');
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));

        $dataTicketDetail = count(DB::select("SELECT * FROM TRANS_TICKET_PURCHASE_DETAILS AS a
            WHERE a.NUMBER_TICKET = '".$param1."'
            AND a.IS_SCAN = '0'
            AND a.PROJECT_NO_CHAR = '".$project_no."'
            AND CAST(a.created_at AS DATE) = '".date('Y-m-d')."'"));

        if($dataTicketDetail == 0) {
            $code = 404;
            $message = "Data Not Found!";
        }
        else {
            try {
                \DB::beginTransaction();
                
                DB::table('TRANS_TICKET_PURCHASE_DETAILS')->where('NUMBER_TICKET', $param1)->update([
                    'IS_SCAN' => "1",
                    'SCAN_BY' => $cashierName,
                    'SCAN_AT' => $dateNow,
                    'updated_by' => $cashierName,
                    'updated_at' => $dateNow
                ]);

                DB::table('LOG_COUNTING_VISITORS')->insert([
                    'LOG_COUNTING_VISITORS_TYPE_ID_INT' => "1",
                    'AMOUNT_INT' => "1",
                    'TIME_DTTIME' => $dateNow,
                    'PROJECT_NO_CHAR' => $project_no,
                    'created_by' => $cashierName,
                    'created_at' => $dateNow
                ]);

                $code = 200;
                $message = "Thank You!";
            
                \DB::commit();
            } catch (QueryException $ex) {
                \DB::rollback();
            }
        }
        
        return response()->json([
            'code' => $code,
            'message' => $message
        ]);
    }
}
