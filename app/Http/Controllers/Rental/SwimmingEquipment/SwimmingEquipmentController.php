<?php

namespace App\Http\Controllers\Rental\SwimmingEquipment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class SwimmingEquipmentController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        $dataTransPerlengkapanRenangRent = DB::table("TRANS_RENTAL_PERLENGKAPAN_RENANG")
        ->where('PROJECT_NO_CHAR', $project_no)
        ->where('STATUS', "1")->get();

        $dataTransPerlengkapanRenangRentDetails = DB::select("SELECT b.TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAIL_ID_INT, b.TRANS_PERLENGKAPAN_RENANG_NO_CHAR,
            b.DESC_CHAR, b.QTY_INT, b.HARGA_SATUAN_FLOAT, b.TOTAL_HARGA_FLOAT, b.created_at
            FROM TRANS_RENTAL_PERLENGKAPAN_RENANG AS a
            INNER JOIN TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAILS AS b ON b.TRANS_PERLENGKAPAN_RENANG_NO_CHAR = a.TRANS_PERLENGKAPAN_RENANG_NO_CHAR
            INNER JOIN MD_PERLENGKAPAN_RENANG AS c ON c.MD_PERLENGKAPAN_RENANG_ID_INT = b.MD_PERLENGKAPAN_RENANG_ID_INT
            WHERE a.[STATUS] = 1 AND b.PROJECT_NO_CHAR = '".$project_no."'");

        $dataTransPerlengkapanRenangRetur = DB::select("SELECT * FROM TRANS_RENTAL_PERLENGKAPAN_RENANG AS a
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 2 AND CAST(a.RETUR_AT AS DATE) = '".date('Y-m-d')."'");

        $dataTransPerlengkapanRenangReturDetails = DB::select("SELECT b.TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAIL_ID_INT, b.TRANS_PERLENGKAPAN_RENANG_NO_CHAR,
            b.DESC_CHAR, b.QTY_INT, b.HARGA_SATUAN_FLOAT, b.TOTAL_HARGA_FLOAT, b.created_at
            FROM TRANS_RENTAL_PERLENGKAPAN_RENANG AS a
            INNER JOIN TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAILS AS b ON b.TRANS_PERLENGKAPAN_RENANG_NO_CHAR = a.TRANS_PERLENGKAPAN_RENANG_NO_CHAR
            INNER JOIN MD_PERLENGKAPAN_RENANG AS c ON c.MD_PERLENGKAPAN_RENANG_ID_INT = b.MD_PERLENGKAPAN_RENANG_ID_INT
            WHERE a.[STATUS] = 2 AND b.PROJECT_NO_CHAR = '".$project_no."' AND CAST(a.RETUR_AT AS DATE) = '".date('Y-m-d')."'");

        return view('Rental.SwimmingEquipment.swimming_equipment')
            ->with('dataTransPerlengkapanRenangRent', $dataTransPerlengkapanRenangRent)
            ->with('dataTransPerlengkapanRenangRentDetails', $dataTransPerlengkapanRenangRentDetails)
            ->with('dataTransPerlengkapanRenangRetur', $dataTransPerlengkapanRenangRetur)
            ->with('dataTransPerlengkapanRenangReturDetails', $dataTransPerlengkapanRenangReturDetails);
    }

    public function rentalSwimmingEquipment() {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));

        $ddlDataPerlengkapanRenang = DB::table('MD_PERLENGKAPAN_RENANG')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('Rental.SwimmingEquipment.rental_swimming_equipment')
            ->with('ddlDataPerlengkapanRenang', $ddlDataPerlengkapanRenang)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('cashierName', $cashierName);
    }

    public function getPerlengkapanRenangPriceById($id) {
        $project_no = session('current_project');
        $id = explode(",", $id);

        $dataPerlengkapanRentalPrice = DB::table('MD_PERLENGKAPAN_RENANG')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('MD_PERLENGKAPAN_RENANG_ID_INT', $id)
            ->sum('HARGA_SATUAN_FLOAT');
        
        return response()->json([
            'dataPerlengkapanRentalPrice' => $dataPerlengkapanRentalPrice
        ]);
    }

    public function saveSwimmingEquipment(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $cart = json_decode($request->TXT_DATATABLE);

        try {
            DB::beginTransaction();

            $counterTransPerlengkapanRenang = DB::table('counter_table')
                ->where('PROJECT_NO_CHAR', $project_no)->first();

            $TRANS_PERLENGKAPAN_RENANG_NO_CHAR = "RENTEQ/" . $dataProject->PROJECT_CODE . "/" . sprintf("%02d", date('m')) . "/" . $dateNow->format('y') . "/" . sprintf("%04d", $counterTransPerlengkapanRenang->trans_rental_perlengkapan_renang_char);

            // UPDATE TABLE COUNTER
            DB::table('counter_table')->where('PROJECT_NO_CHAR', $project_no)->update([
                'trans_rental_perlengkapan_renang_char' => ($counterTransPerlengkapanRenang->trans_rental_perlengkapan_renang_char + 1)
            ]);

            // AMBIL DATA TOTAL QTY DAN TOTAL BILL
            $totalQty = 0;
            $totalHargaTagihan = 0;
            for($i = 0; $i < count($cart); $i++) {
                $totalQty += (int) $cart[$i][3];
                $totalHargaTagihan += (int) str_replace('.', '', $cart[$i][4]);
            }

            // INSERT TABLE TRANS
            $paymentAmount1 = $request->TXT_PAYMENT_AMOUNT1 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT1;
            $paymentAmount2 = $request->TXT_PAYMENT_AMOUNT2 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT2;
            $totalChange = ($paymentAmount1 + $paymentAmount2) - $totalHargaTagihan;
            DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG')->insert([
                'TRANS_PERLENGKAPAN_RENANG_NO_CHAR' => $TRANS_PERLENGKAPAN_RENANG_NO_CHAR,
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
            for($i = 0; $i < count($cart); $i++) {
                DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAILS')->insert([
                    'TRANS_PERLENGKAPAN_RENANG_NO_CHAR' => $TRANS_PERLENGKAPAN_RENANG_NO_CHAR,
                    'MD_PERLENGKAPAN_RENANG_ID_INT' => $cart[$i][0],
                    'DESC_CHAR' => $cart[$i][1],
                    'QTY_INT' => $cart[$i][3],
                    'HARGA_SATUAN_FLOAT' => str_replace('.', '', $cart[$i][2]),
                    'TOTAL_HARGA_FLOAT' => str_replace('.', '', $cart[$i][4]),
                    'PROJECT_NO_CHAR' => $project_no,
                    'created_by' => $request->TXT_CASHIER_NAME,
                    'created_at' => $dateNow
                ]);

                $dataPerlengkapanRenang = DB::table('MD_PERLENGKAPAN_RENANG')->where('MD_PERLENGKAPAN_RENANG_ID_INT', $cart[$i][0])->where('PROJECT_NO_CHAR', $project_no)->first();

                DB::table('MD_PERLENGKAPAN_RENANG')->where('MD_PERLENGKAPAN_RENANG_ID_INT', $cart[$i][0])->where('PROJECT_NO_CHAR', $project_no)->update([
                    'CURRENT_RENT_INT' => $dataPerlengkapanRenang->CURRENT_RENT_INT + $cart[$i][3]
                ]);
            }

            $dataTransSwimmingEquipment = DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG')
                ->where('TRANS_PERLENGKAPAN_RENANG_NO_CHAR', $TRANS_PERLENGKAPAN_RENANG_NO_CHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('STATUS', "1")
                ->first();
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('swimming_equipment');            
        }

        $urlRedirect = URL("/print_rental_swimming_equipment/" . $dataTransSwimmingEquipment->TRANS_RENTAL_PERLENGKAPAN_RENANG_ID_INT);
        session()->flash('urlRedirect', $urlRedirect);
        session()->flash('success', "Transaction Successfully!");
        session()->flash('change', number_format($totalChange, 0, ",", "."));

        return redirect()->route('swimming_equipment');
    }

    public function printSwimmingEquipment($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTransSwimmingEquipment = DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG')
            ->where('STATUS', "1")
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('TRANS_RENTAL_PERLENGKAPAN_RENANG_ID_INT', $id)->first();

        $dataTransSwimmingEquipmentDetails = DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAILS')
            ->where('TRANS_PERLENGKAPAN_RENANG_NO_CHAR', $dataTransSwimmingEquipment->TRANS_PERLENGKAPAN_RENANG_NO_CHAR)
            ->where('PROJECT_NO_CHAR', $project_no)->get();
        
        return view('Rental.SwimmingEquipment.print_swimming_equipment')
            ->with('dataTransSwimmingEquipmentDetails', $dataTransSwimmingEquipmentDetails)
            ->with('dataTransSwimmingEquipment', $dataTransSwimmingEquipment)
            ->with('dataProject', $dataProject);
    }

    public function returSwimmingEquipment($id) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $dataTransPerlengkapanRenang = DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG')->where('TRANS_RENTAL_PERLENGKAPAN_RENANG_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dataTransPerlengkapanRenangDetails = DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAILS')->where('TRANS_PERLENGKAPAN_RENANG_NO_CHAR', $dataTransPerlengkapanRenang->TRANS_PERLENGKAPAN_RENANG_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

            DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG')->where('TRANS_RENTAL_PERLENGKAPAN_RENANG_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'RETUR_BY' => $cashierName,
                'RETUR_AT' => $dateNow,
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            foreach($dataTransPerlengkapanRenangDetails as $data) {
                $dataPerlengkapanRenang = DB::table('MD_PERLENGKAPAN_RENANG')->where('MD_PERLENGKAPAN_RENANG_ID_INT', $data->MD_PERLENGKAPAN_RENANG_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->first();
                DB::table('MD_PERLENGKAPAN_RENANG')->where('MD_PERLENGKAPAN_RENANG_ID_INT', $data->MD_PERLENGKAPAN_RENANG_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'CURRENT_RENT_INT' => $dataPerlengkapanRenang->CURRENT_RENT_INT - $data->QTY_INT
                ]);
            }
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed retur data, errmsg : ' . $ex);
            return redirect()->route('swimming_equipment');
        }

        session()->flash('success', "Retur Transaction Successfully!");

        return redirect()->route('swimming_equipment');
    }

    public function cancelSwimmingEquipment($id) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $dataTransPerlengkapanRenang = DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG')->where('TRANS_RENTAL_PERLENGKAPAN_RENANG_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dataTransPerlengkapanRenangDetails = DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG_DETAILS')->where('TRANS_PERLENGKAPAN_RENANG_NO_CHAR', $dataTransPerlengkapanRenang->TRANS_PERLENGKAPAN_RENANG_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

            DB::table('TRANS_RENTAL_PERLENGKAPAN_RENANG')->where('TRANS_RENTAL_PERLENGKAPAN_RENANG_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            foreach($dataTransPerlengkapanRenangDetails as $data) {
                $dataPerlengkapanRenang = DB::table('MD_PERLENGKAPAN_RENANG')->where('MD_PERLENGKAPAN_RENANG_ID_INT', $data->MD_PERLENGKAPAN_RENANG_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->first();
                DB::table('MD_PERLENGKAPAN_RENANG')->where('MD_PERLENGKAPAN_RENANG_ID_INT', $data->MD_PERLENGKAPAN_RENANG_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'CURRENT_RENT_INT' => $dataPerlengkapanRenang->CURRENT_RENT_INT - $data->QTY_INT
                ]);
            }
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex);
            return redirect()->route('swimming_equipment');
        }

        session()->flash('success', "Cancel Transaction Successfully!");

        return redirect()->route('swimming_equipment');
    }
}
