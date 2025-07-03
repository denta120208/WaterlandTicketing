<?php

namespace App\Http\Controllers\Rental\Locker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class LockerController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        $dataTransLockerRent = DB::table("TRANS_RENTAL_LOCKER")
        ->where('PROJECT_NO_CHAR', $project_no)
        ->where('STATUS', "1")->get();

        $dataTransLockerRentDetails = DB::select("SELECT b.TRANS_RENTAL_LOCKER_DETAIL_ID_INT, b.TRANS_LOCKER_NO_CHAR,
            b.DESC_CHAR, b.HARGA_FLOAT, b.created_at
            FROM TRANS_RENTAL_LOCKER AS a
            INNER JOIN TRANS_RENTAL_LOCKER_DETAILS AS b ON b.TRANS_LOCKER_NO_CHAR = a.TRANS_LOCKER_NO_CHAR
            INNER JOIN MD_LOCKER AS c ON c.MD_LOCKER_ID_INT = b.MD_LOCKER_ID_INT
            WHERE a.[STATUS] = 1 AND b.PROJECT_NO_CHAR = '".$project_no."'");

        $dataTransLockerRetur = DB::select("SELECT * FROM TRANS_RENTAL_LOCKER AS a
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 2 AND CAST(a.RETUR_AT AS DATE) = '".date('Y-m-d')."'");

        $dataTransLockerReturDetails = DB::select("SELECT b.TRANS_RENTAL_LOCKER_DETAIL_ID_INT, b.TRANS_LOCKER_NO_CHAR,
            b.DESC_CHAR, b.HARGA_FLOAT, b.created_at
            FROM TRANS_RENTAL_LOCKER AS a
            INNER JOIN TRANS_RENTAL_LOCKER_DETAILS AS b ON b.TRANS_LOCKER_NO_CHAR = a.TRANS_LOCKER_NO_CHAR
            INNER JOIN MD_LOCKER AS c ON c.MD_LOCKER_ID_INT = b.MD_LOCKER_ID_INT
            WHERE a.[STATUS] = 2 AND b.PROJECT_NO_CHAR = '".$project_no."' AND CAST(a.RETUR_AT AS DATE) = '".date('Y-m-d')."'");

        return view('Rental.Locker.locker')
            ->with('dataTransLockerRent', $dataTransLockerRent)
            ->with('dataTransLockerRentDetails', $dataTransLockerRentDetails)
            ->with('dataTransLockerRetur', $dataTransLockerRetur)
            ->with('dataTransLockerReturDetails', $dataTransLockerReturDetails);
    }

    public function rentalLocker() {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));

        $ddlDataLocker = DB::table('MD_LOCKER')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->orderBy('DESC_CHAR', 'ASC')->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('Rental.Locker.rental_locker')
            ->with('ddlDataLocker', $ddlDataLocker)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('cashierName', $cashierName);
    }

    public function getLockerPriceById($id) {
        $project_no = session('current_project');
        $id = explode(",", $id);

        $dataLockerPrice = DB::table('MD_LOCKER')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('MD_LOCKER_ID_INT', $id)
            ->sum('HARGA_SATUAN_FLOAT');
        
        return response()->json([
            'dataLockerPrice' => $dataLockerPrice
        ]);
    }

    public function saveLocker(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $counterTransLocker = DB::table('counter_table')
                ->where('PROJECT_NO_CHAR', $project_no)->first();

            $TRANS_LOCKER_NO_CHAR = "RENTLOC/" . $dataProject->PROJECT_CODE . "/" . sprintf("%02d", date('m')) . "/" . $dateNow->format('y') . "/" . sprintf("%04d", $counterTransLocker->trans_rental_locker_no_char);

            // UPDATE TABLE COUNTER
            DB::table('counter_table')->where('PROJECT_NO_CHAR', $project_no)->update([
                'trans_rental_locker_no_char' => ($counterTransLocker->trans_rental_locker_no_char + 1)
            ]);

            // AMBIL DATA TOTAL QTY DAN TOTAL BILL
            $totalQty = 0;
            $totalHargaTagihan = 0;
            foreach($request->DDL_LOCKER as $data) {
                $dataLocker = DB::table('MD_LOCKER')->where('MD_LOCKER_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->first();
                $totalQty += 1;
                $totalHargaTagihan += $dataLocker->HARGA_SATUAN_FLOAT;
            }

            // INSERT TABLE TRANS
            $paymentAmount1 = $request->TXT_PAYMENT_AMOUNT1 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT1;
            $paymentAmount2 = $request->TXT_PAYMENT_AMOUNT2 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT2;
            $totalChange = ($paymentAmount1 + $paymentAmount2) - $totalHargaTagihan;
            DB::table('TRANS_RENTAL_LOCKER')->insert([
                'TRANS_LOCKER_NO_CHAR' => $TRANS_LOCKER_NO_CHAR,
                'CUSTOMER_NAME_CHAR' => $request->TXT_CUSTOMER_NAME,
                'NO_TELP_CHAR' => $request->TXT_NO_TELP,
                'QTY_INT' => $totalQty,
                'TOTAL_HARGA_FLOAT' => $totalHargaTagihan,
                'PAYMENT_METHOD_ID_INT_1' => $request->DDL_PAYMENT_METHOD1,
                'PAYMENT_METHOD_NUMBER_1' => $request->TXT_NUMBER1,
                'PAYMENT_AMOUNT_1' => $request->TXT_PAYMENT_AMOUNT1,
                'PAYMENT_METHOD_ID_INT_2' => $request->DDL_PAYMENT_METHOD2,
                'PAYMENT_METHOD_NUMBER_2' => $request->TXT_NUMBER2,
                'PAYMENT_AMOUNT_2' => $request->TXT_PAYMENT_AMOUNT2,
                'TOTAL_PAID_FLOAT' => ($paymentAmount1 + $paymentAmount2),
                'TOTAL_CHANGE_FLOAT' => $totalChange,
                'CASHIER_NAME_CHAR' => $request->TXT_CASHIER_NAME,
                'STATUS' => "1",
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $request->TXT_CASHIER_NAME,
                'created_at' => $dateNow
            ]);

            // INSERT TABLE TRANS DETAILS
            foreach($request->DDL_LOCKER as $data) {
                $dataLocker = DB::table('MD_LOCKER')->where('MD_LOCKER_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->first();
                DB::table('TRANS_RENTAL_LOCKER_DETAILS')->insert([
                    'TRANS_LOCKER_NO_CHAR' => $TRANS_LOCKER_NO_CHAR,
                    'MD_LOCKER_ID_INT' => $data,
                    'DESC_CHAR' => $dataLocker->DESC_CHAR,
                    'HARGA_FLOAT' => $dataLocker->HARGA_SATUAN_FLOAT,
                    'PROJECT_NO_CHAR' => $project_no,
                    'created_by' => $request->TXT_CASHIER_NAME,
                    'created_at' => $dateNow
                ]);

                DB::table('MD_LOCKER')->where('MD_LOCKER_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'IS_RENT' => "1"
                ]);
            }

            $dataTransLocker = DB::table('TRANS_RENTAL_LOCKER')
                ->where('TRANS_LOCKER_NO_CHAR', $TRANS_LOCKER_NO_CHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('STATUS', "1")
                ->first();
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('locker');
        }

        $urlRedirect = URL("/print_rental_locker/" . $dataTransLocker->TRANS_RENTAL_LOCKER_ID_INT);
        session()->flash('urlRedirect', $urlRedirect);
        session()->flash('success', "Transaction Successfully!");
        session()->flash('change', number_format($totalChange, 0, ",", "."));

        return redirect()->route('locker');
    }

    public function printLocker($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTransLocker = DB::table('TRANS_RENTAL_LOCKER')
            ->where('STATUS', "1")
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('TRANS_RENTAL_LOCKER_ID_INT', $id)->first();

        $dataTransLockerDetails = DB::table('TRANS_RENTAL_LOCKER_DETAILS')
            ->where('TRANS_LOCKER_NO_CHAR', $dataTransLocker->TRANS_LOCKER_NO_CHAR)
            ->where('PROJECT_NO_CHAR', $project_no)->get();
        
        return view('Rental.Locker.print_locker')
            ->with('dataTransLockerDetails', $dataTransLockerDetails)
            ->with('dataTransLocker', $dataTransLocker)
            ->with('dataProject', $dataProject);
    }

    public function returLocker($id) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $dataTransLocker = DB::table('TRANS_RENTAL_LOCKER')->where('TRANS_RENTAL_LOCKER_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dataTransLockerDetails = DB::table('TRANS_RENTAL_LOCKER_DETAILS')->where('TRANS_LOCKER_NO_CHAR', $dataTransLocker->TRANS_LOCKER_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

            DB::table('TRANS_RENTAL_LOCKER')->where('TRANS_RENTAL_LOCKER_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'RETUR_BY' => $cashierName,
                'RETUR_AT' => $dateNow,
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            foreach($dataTransLockerDetails as $data) {
                DB::table('MD_LOCKER')->where('MD_LOCKER_ID_INT', $data->MD_LOCKER_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'IS_RENT' => "0"
                ]);
            }
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed retur data, errmsg : ' . $ex);
            return redirect()->route('locker');
        }

        session()->flash('success', "Retur Transaction Successfully!");

        return redirect()->route('locker');
    }

    public function cancelLocker($id) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $dataTransLocker = DB::table('TRANS_RENTAL_LOCKER')->where('TRANS_RENTAL_LOCKER_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dataTransLockerDetails = DB::table('TRANS_RENTAL_LOCKER_DETAILS')->where('TRANS_LOCKER_NO_CHAR', $dataTransLocker->TRANS_LOCKER_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

            DB::table('TRANS_RENTAL_LOCKER')->where('TRANS_RENTAL_LOCKER_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            foreach($dataTransLockerDetails as $data) {
                DB::table('MD_LOCKER')->where('MD_LOCKER_ID_INT', $data->MD_LOCKER_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'IS_RENT' => "0"
                ]);
            }
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex);
            return redirect()->route('locker');
        }

        session()->flash('success', "Cancel Transaction Successfully!");

        return redirect()->route('locker');
    }
}
