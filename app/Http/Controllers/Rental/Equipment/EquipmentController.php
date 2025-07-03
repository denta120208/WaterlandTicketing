<?php

namespace App\Http\Controllers\Rental\Equipment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class EquipmentController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        // $dataTransEquipmentRent = DB::table("TRANS_RENTAL_EQUIPMENT")
        // ->where('PROJECT_NO_CHAR', $project_no)
        // ->where('STATUS', "1")->get();

        $dataTransEquipmentRent = DB::select("SELECT a.TRANS_RENTAL_EQUIPMENT_ID_INT,
            a.TRANS_EQUIPMENT_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.NO_TELP_CHAR, a.QTY_INT, a.TOTAL_HARGA_FLOAT, a.DEPOSIT_FLOAT,
            a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT
                END
            AS QTY_FREE_INT,
            ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT,
            c.DESC_CHAR AS PROMO_DESC_CHAR
            FROM TRANS_RENTAL_EQUIPMENT AS a
            LEFT JOIN TRANS_PROMO_EQUIPMENT AS b ON b.TRANS_EQUIPMENT_NOCHAR = a.TRANS_EQUIPMENT_NO_CHAR
            LEFT JOIN MD_PROMO_EQUIPMENT AS c ON c.PROMO_EQUIPMENT_ID_INT = b.PROMO_EQUIPMENT_ID_INT
            LEFT JOIN TRANS_RENTAL_EQUIPMENT_DETAILS AS d ON d.TRANS_EQUIPMENT_NO_CHAR = a.TRANS_EQUIPMENT_NO_CHAR
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = '1'
            --AND CAST(a.created_at AS DATE) = '".date('Y-m-d')."'
            GROUP BY a.TRANS_RENTAL_EQUIPMENT_ID_INT, a.TRANS_EQUIPMENT_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.NO_TELP_CHAR, a.QTY_INT,
            a.TOTAL_HARGA_FLOAT, a.DEPOSIT_FLOAT, a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            c.DESC_CHAR
            ORDER BY a.TRANS_RENTAL_EQUIPMENT_ID_INT DESC");

        $dataTransEquipmentRentDetails = DB::select("SELECT b.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT, b.TRANS_EQUIPMENT_NO_CHAR,
            b.DESC_CHAR, d.MD_EQUIPMENT_CATEGORY_DESC_CHAR, b.HARGA_FLOAT, b.created_at
            FROM TRANS_RENTAL_EQUIPMENT AS a
            INNER JOIN TRANS_RENTAL_EQUIPMENT_DETAILS AS b ON b.TRANS_EQUIPMENT_NO_CHAR = a.TRANS_EQUIPMENT_NO_CHAR
            INNER JOIN MD_EQUIPMENT AS c ON c.MD_EQUIPMENT_ID_INT = b.MD_EQUIPMENT_ID_INT
            INNER JOIN MD_EQUIPMENT_CATEGORY AS d ON d.MD_EQUIPMENT_CATEGORY_ID_INT = c.MD_EQUIPMENT_CATEGORY_ID_INT
            WHERE a.[STATUS] = 1 AND b.PROJECT_NO_CHAR = '".$project_no."'");

        // $dataTransEquipmentRetur = DB::select("SELECT * FROM TRANS_RENTAL_EQUIPMENT AS a
        //     WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 2 AND CAST(a.RETUR_AT AS DATE) = '".date('Y-m-d')."'");

        $dataTransEquipmentRetur = DB::select("SELECT a.TRANS_RENTAL_EQUIPMENT_ID_INT,
            a.TRANS_EQUIPMENT_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.NO_TELP_CHAR, a.QTY_INT, a.TOTAL_HARGA_FLOAT, a.DEPOSIT_FLOAT,
            a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT
                END
            AS QTY_FREE_INT,
            ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT, a.REFUND_FLOAT, a.REFUND_DESC_CHAR, a.REFUND_DATE,
            a.RETUR_BY, a.RETUR_AT,
            c.DESC_CHAR AS PROMO_DESC_CHAR
            FROM TRANS_RENTAL_EQUIPMENT AS a
            LEFT JOIN TRANS_PROMO_EQUIPMENT AS b ON b.TRANS_EQUIPMENT_NOCHAR = a.TRANS_EQUIPMENT_NO_CHAR
            LEFT JOIN MD_PROMO_EQUIPMENT AS c ON c.PROMO_EQUIPMENT_ID_INT = b.PROMO_EQUIPMENT_ID_INT
            LEFT JOIN TRANS_RENTAL_EQUIPMENT_DETAILS AS d ON d.TRANS_EQUIPMENT_NO_CHAR = a.TRANS_EQUIPMENT_NO_CHAR
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = '2' AND CAST(a.RETUR_AT AS DATE) = '".date('Y-m-d')."'
            GROUP BY a.TRANS_RENTAL_EQUIPMENT_ID_INT, a.TRANS_EQUIPMENT_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.NO_TELP_CHAR, a.QTY_INT,
            a.TOTAL_HARGA_FLOAT, a.DEPOSIT_FLOAT, a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            a.REFUND_FLOAT, a.REFUND_DESC_CHAR, a.REFUND_DATE, a.RETUR_BY, a.RETUR_AT,
            c.DESC_CHAR
            ORDER BY a.TRANS_RENTAL_EQUIPMENT_ID_INT DESC");

        $dataTransEquipmentReturDetails = DB::select("SELECT b.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT, b.TRANS_EQUIPMENT_NO_CHAR,
            b.DESC_CHAR, d.MD_EQUIPMENT_CATEGORY_DESC_CHAR, b.HARGA_FLOAT, b.created_at
            FROM TRANS_RENTAL_EQUIPMENT AS a
            INNER JOIN TRANS_RENTAL_EQUIPMENT_DETAILS AS b ON b.TRANS_EQUIPMENT_NO_CHAR = a.TRANS_EQUIPMENT_NO_CHAR
            INNER JOIN MD_EQUIPMENT AS c ON c.MD_EQUIPMENT_ID_INT = b.MD_EQUIPMENT_ID_INT
            INNER JOIN MD_EQUIPMENT_CATEGORY AS d ON d.MD_EQUIPMENT_CATEGORY_ID_INT = c.MD_EQUIPMENT_CATEGORY_ID_INT
            WHERE a.[STATUS] = 2 AND b.PROJECT_NO_CHAR = '".$project_no."' AND CAST(a.RETUR_AT AS DATE) = '".date('Y-m-d')."'");

        return view('Rental.Equipment.equipment')
            ->with('dataTransEquipmentRent', $dataTransEquipmentRent)
            ->with('dataTransEquipmentRentDetails', $dataTransEquipmentRentDetails)
            ->with('dataTransEquipmentRetur', $dataTransEquipmentRetur)
            ->with('dataTransEquipmentReturDetails', $dataTransEquipmentReturDetails);
    }

    public function rentalEquipment() {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));

        $ddlDataEquipment = DB::table('MD_EQUIPMENT')
            ->selectRaw('MD_EQUIPMENT.*, MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_DESC_CHAR')
            ->join('MD_EQUIPMENT_CATEGORY', 'MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_ID_INT', '=', 'MD_EQUIPMENT.MD_EQUIPMENT_CATEGORY_ID_INT')
            ->where('MD_EQUIPMENT.STATUS', '1')
            ->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
            ->orderBy('MD_EQUIPMENT_CATEGORY_DESC_CHAR', 'ASC')->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('Rental.Equipment.rental_equipment')
            ->with('ddlDataEquipment', $ddlDataEquipment)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('cashierName', $cashierName);
    }

    public function getEquipmentPriceById($id) {
        $project_no = session('current_project');
        $id = explode(",", $id);

        $dataEquipmentPrice = DB::table('MD_EQUIPMENT')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('MD_EQUIPMENT_ID_INT', $id)
            ->sum('HARGA_SATUAN_FLOAT');

        $dataEquipmentCount = DB::table('MD_EQUIPMENT')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('MD_EQUIPMENT_ID_INT', $id)
            ->count();
        
        return response()->json([
            'dataEquipmentPrice' => $dataEquipmentPrice,
            'dataEquipmentCount' => $dataEquipmentCount
        ]);
    }

    public function saveEquipment(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            $counterTransEquipment = DB::table('counter_table')
                ->where('PROJECT_NO_CHAR', $project_no)->first();

            $TRANS_EQUIPMENT_NO_CHAR = "RENTEQ/" . $dataProject->PROJECT_CODE . "/" . sprintf("%02d", date('m')) . "/" . $dateNow->format('y') . "/" . sprintf("%04d", $counterTransEquipment->trans_rental_equipment_no_char);

            // UPDATE TABLE COUNTER
            DB::table('counter_table')->where('PROJECT_NO_CHAR', $project_no)->update([
                'trans_rental_equipment_no_char' => ($counterTransEquipment->trans_rental_equipment_no_char + 1)
            ]);

            // AMBIL DATA TOTAL QTY DAN TOTAL BILL
            // $totalQty = 0;
            // $totalHargaTagihan = 0;
            // foreach($request->DDL_EQUIPMENT as $data) {
            //     $dataEquipment = DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->first();
            //     $totalQty += 1;
            //     $totalHargaTagihan += $dataEquipment->HARGA_SATUAN_FLOAT;
            // }

            $totalQty = $request->TXT_QTY;
            $totalHargaTagihan = ((float) str_replace('.', '', $request->TXT_TOTAL_PRICE_AFTER_DISCOUNT)) - $request->TXT_DEPOSIT;

            // INSERT TABLE TRANS
            $paymentAmount1 = $request->TXT_PAYMENT_AMOUNT1 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT1;
            $paymentAmount2 = $request->TXT_PAYMENT_AMOUNT2 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT2;
            $totalChange = (($paymentAmount1 + $paymentAmount2) - $request->TXT_DEPOSIT) - $totalHargaTagihan;
            DB::table('TRANS_RENTAL_EQUIPMENT')->insert([
                'TRANS_EQUIPMENT_NO_CHAR' => $TRANS_EQUIPMENT_NO_CHAR,
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
                'TOTAL_PAID_FLOAT' => (($paymentAmount1 + $paymentAmount2) - $request->TXT_DEPOSIT),
                'TOTAL_CHANGE_FLOAT' => $totalChange,
                'DEPOSIT_FLOAT' => $request->TXT_DEPOSIT,
                'DEPOSIT_DATE' => $dateNow,
                'CASHIER_NAME_CHAR' => $request->TXT_CASHIER_NAME,
                'STATUS' => "1",
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $request->TXT_CASHIER_NAME,
                'created_at' => $dateNow
            ]);

            // INSERT TABLE TRANS DETAILS
            $totalPB1 = 0;
            $totalPPH = 0;
            foreach($request->DDL_EQUIPMENT as $data) {
                $dataEquipment = DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->first();
                $totalPB1Satuan = ($dataEquipment->HARGA_SATUAN_FLOAT / (($dataEquipment->PB1_PERCENT_INT / 100) + 1)) * ($dataEquipment->PB1_PERCENT_INT / 100);
                $totalPPHSatuan = ($dataEquipment->HARGA_SATUAN_FLOAT - $totalPB1Satuan) * ($dataEquipment->PPH_PERCENT_INT / 100);

                DB::table('TRANS_RENTAL_EQUIPMENT_DETAILS')->insert([
                    'TRANS_EQUIPMENT_NO_CHAR' => $TRANS_EQUIPMENT_NO_CHAR,
                    'MD_EQUIPMENT_ID_INT' => $data,
                    'DESC_CHAR' => $dataEquipment->EQUIPMENT_ASSET_NUMBER,
                    'HARGA_FLOAT' => $dataEquipment->HARGA_SATUAN_FLOAT,
                    'PB1_PERCENT' => $dataEquipment->PB1_PERCENT_INT,
                    'PPH_PERCENT' => $dataEquipment->PPH_PERCENT_INT,
                    'TOTAL_PB1_NUM' => $totalPB1Satuan,
                    'TOTAL_PPH_NUM' => $totalPPHSatuan,
                    'PROJECT_NO_CHAR' => $project_no,
                    'created_by' => $request->TXT_CASHIER_NAME,
                    'created_at' => $dateNow
                ]);

                DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'IS_RENT' => "1"
                ]);

                $totalPB1 += $totalPB1Satuan;
                $totalPPH += $totalPPHSatuan;
            }

            // UPDATE KE TABLE TRANS TERKAIT NOMINAL PB1 DAN PPH
            $discPersen = $request->TXT_DISCOUNT_PERCENT;
            $totalPB1 = $totalPB1 > 0 ? ($totalPB1 - ($totalPB1 * ($discPersen / 100))) : 0;
            $totalPPH = $totalPPH > 0 ? ($totalPPH - ($totalPPH * ($discPersen / 100))) : 0;
            $discNominal = $request->TXT_DISCOUNT;
            $totalPB1 = $totalPB1 > 0 ? ($totalPB1 - $discNominal) : 0;
            $totalPPH = $totalPPH > 0 ? ($totalPPH - $discNominal) : 0;
            DB::table('TRANS_RENTAL_EQUIPMENT')->where("TRANS_EQUIPMENT_NO_CHAR", $TRANS_EQUIPMENT_NO_CHAR)->update([
                "TOTAL_PB1_NUM" => $totalPB1,
                "TOTAL_PPH_NUM" => $totalPPH
            ]);

            // INSERT TABLE TRANS PROMO
            if($request->DDL_PROMO != null) {
                DB::table('TRANS_PROMO_EQUIPMENT')->insert([
                    'TRANS_EQUIPMENT_NOCHAR' => $TRANS_EQUIPMENT_NO_CHAR,
                    'PROMO_EQUIPMENT_ID_INT' => $request->DDL_PROMO,
                    'CASHIER_NAME_CHAR' => $request->TXT_CASHIER_NAME,
                    'PROJECT_NO_CHAR' => $project_no,
                    'STATUS' => "1",
                    'created_by' => $request->TXT_CASHIER_NAME,
                    'created_at' => $dateNow
                ]);

                // INSERT TABLE TRANS DETAILS (FREE QTY)
                $dataPromoEquipment = DB::table('MD_PROMO_EQUIPMENT')->where('PROMO_EQUIPMENT_ID_INT', $request->DDL_PROMO)->where('PROJECT_NO_CHAR', $project_no)->first();
                $dataEquipmentAvailable = DB::table('MD_EQUIPMENT')
                    ->where('MD_EQUIPMENT_CATEGORY_ID_INT', $dataPromoEquipment->MD_EQUIPMENT_CATEGORY_CHAR)
                    ->where('PROJECT_NO_CHAR', $project_no)
                    ->where('IS_RENT', 0)->get();

                for($i = 0; $i < $request->TXT_FREE_QTY; $i++) {
                    $dataEquipment = DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $dataEquipmentAvailable[$i]->MD_EQUIPMENT_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->first();
                    DB::table('TRANS_RENTAL_EQUIPMENT_DETAILS')->insert([
                        'TRANS_EQUIPMENT_NO_CHAR' => $TRANS_EQUIPMENT_NO_CHAR,
                        'MD_EQUIPMENT_ID_INT' => $dataEquipmentAvailable[$i]->MD_EQUIPMENT_ID_INT,
                        'DESC_CHAR' => $dataEquipment->EQUIPMENT_ASSET_NUMBER,
                        'HARGA_FLOAT' => 0,
                        'PROJECT_NO_CHAR' => $project_no,
                        'created_by' => $request->TXT_CASHIER_NAME,
                        'created_at' => $dateNow
                    ]);

                    DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $dataEquipmentAvailable[$i]->MD_EQUIPMENT_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->update([
                        'IS_RENT' => "1"
                    ]);
                }
            }

            $dataTransEquipment = DB::table('TRANS_RENTAL_EQUIPMENT')
                ->where('TRANS_EQUIPMENT_NO_CHAR', $TRANS_EQUIPMENT_NO_CHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('STATUS', "1")
                ->first();
        
            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('rentEquipment');
        }

        $urlRedirect = URL("/print_rental_equipment/" . $dataTransEquipment->TRANS_RENTAL_EQUIPMENT_ID_INT);
        session()->flash('urlRedirect', $urlRedirect);
        session()->flash('change', number_format($totalChange, 0, ",", "."));

        return redirect()->route('rental_equipment');
    }

    public function printEquipment($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTransEquipment = DB::table('TRANS_RENTAL_EQUIPMENT')
            ->where('STATUS', "1")
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('TRANS_RENTAL_EQUIPMENT_ID_INT', $id)->first();

        $dataTransEquipmentDetails = DB::table('TRANS_RENTAL_EQUIPMENT_DETAILS')
            ->selectRaw('TRANS_RENTAL_EQUIPMENT_DETAILS.*, MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_DESC_CHAR')
            ->join('MD_EQUIPMENT', 'TRANS_RENTAL_EQUIPMENT_DETAILS.MD_EQUIPMENT_ID_INT', '=', 'MD_EQUIPMENT.MD_EQUIPMENT_ID_INT')
            ->join('MD_EQUIPMENT_CATEGORY', 'MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_ID_INT', '=', 'MD_EQUIPMENT.MD_EQUIPMENT_CATEGORY_ID_INT')
            ->where('TRANS_RENTAL_EQUIPMENT_DETAILS.TRANS_EQUIPMENT_NO_CHAR', $dataTransEquipment->TRANS_EQUIPMENT_NO_CHAR)
            ->where('TRANS_RENTAL_EQUIPMENT_DETAILS.PROJECT_NO_CHAR', $project_no)->get();
        
        return view('Rental.Equipment.print_equipment')
            ->with('dataTransEquipmentDetails', $dataTransEquipmentDetails)
            ->with('dataTransEquipment', $dataTransEquipment)
            ->with('dataProject', $dataProject);
    }

    public function returEquipment(Request $request) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $TXT_DEPOSIT = str_replace('.', '', $request->TXT_DEPOSIT);

        if($request->TXT_REFUND > $TXT_DEPOSIT) {
            session()->flash('error', 'Total Refund Cannot Be Over Than Total Deposit!');
            return redirect()->route('rentEquipment');
        }

        try {
            \DB::beginTransaction();

            $dataTransEquipment = DB::table('TRANS_RENTAL_EQUIPMENT')->where('TRANS_RENTAL_EQUIPMENT_ID_INT', $request->TXT_ID)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dataTransEquipmentDetails = DB::table('TRANS_RENTAL_EQUIPMENT_DETAILS')->where('TRANS_EQUIPMENT_NO_CHAR', $dataTransEquipment->TRANS_EQUIPMENT_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

            DB::table('TRANS_RENTAL_EQUIPMENT')->where('TRANS_RENTAL_EQUIPMENT_ID_INT', $request->TXT_ID)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "2",
                'REFUND_FLOAT' => $request->TXT_REFUND,
                'REFUND_DESC_CHAR' => $request->TXT_REFUND_DESC,
                'REFUND_DATE' => $dateNow,
                'RETUR_BY' => $cashierName,
                'RETUR_AT' => $dateNow,
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            foreach($dataTransEquipmentDetails as $data) {
                DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $data->MD_EQUIPMENT_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'IS_RENT' => "0"
                ]);
            }
        
            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed retur data, errmsg : ' . $ex);
            return redirect()->route('rentEquipment');
        }

        session()->flash('success', "Retur Transaction Successfully!");

        return redirect()->route('rentEquipment');
    }

    public function cancelEquipment($id) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $dataTransEquipment = DB::table('TRANS_RENTAL_EQUIPMENT')->where('TRANS_RENTAL_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dataTransEquipmentDetails = DB::table('TRANS_RENTAL_EQUIPMENT_DETAILS')->where('TRANS_EQUIPMENT_NO_CHAR', $dataTransEquipment->TRANS_EQUIPMENT_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

            DB::table('TRANS_RENTAL_EQUIPMENT')->where('TRANS_RENTAL_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            if($dataTransEquipment != null) {
                DB::table('TRANS_PROMO_EQUIPMENT')->where('TRANS_EQUIPMENT_NOCHAR', $dataTransEquipment->TRANS_EQUIPMENT_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'STATUS' => "0",
                    'updated_by' => $cashierName,
                    'updated_at' => $dateNow
                ]);
            }

            foreach($dataTransEquipmentDetails as $data) {
                DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $data->MD_EQUIPMENT_ID_INT)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'IS_RENT' => "0"
                ]);
            }
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex);
            return redirect()->route('rentEquipment');
        }

        session()->flash('success', "Cancel Transaction Successfully!");

        return redirect()->route('rentEquipment');
    }

    public function getPromoEquipment($paymentMethod1, $paymentMethod2, $category, $qty, $totalPrice) {
        $project_no = session('current_project');
        if($category == "null") {
            $dataPromoArr = array();
        }
        else {
            $category = explode(",", $category);
            $categoryId = array();

            foreach($category as $data) {
                $dataEquipment = DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->first();
                array_push($categoryId, $dataEquipment->MD_EQUIPMENT_CATEGORY_ID_INT);
            }

            $category = array_count_values($categoryId);
            $dataPromoArr = array();
            foreach ($category as $key => $value) {
                $dataPromo = DB::select("SELECT a.*, CASE WHEN a.MD_EQUIPMENT_CATEGORY_CHAR = 'ALL' THEN a.MD_EQUIPMENT_CATEGORY_CHAR ELSE b.MD_EQUIPMENT_CATEGORY_DESC_CHAR END AS MD_EQUIPMENT_CATEGORY_DESC_CHAR
                    FROM MD_PROMO_EQUIPMENT AS a
                    LEFT JOIN MD_EQUIPMENT_CATEGORY AS b ON b.MD_EQUIPMENT_CATEGORY_ID_INT = a.MD_EQUIPMENT_CATEGORY_CHAR
                    WHERE a.PROJECT_NO_CHAR = '".$project_no."'
                    AND (a.MD_EQUIPMENT_CATEGORY_CHAR = '".$key."' OR a.MD_EQUIPMENT_CATEGORY_CHAR = 'ALL')
                    AND (a.PAYMENT_METHOD_CHAR = '".$paymentMethod1."' OR a.PAYMENT_METHOD_CHAR = '".$paymentMethod2."' OR a.PAYMENT_METHOD_CHAR = 'ALL')
                    AND a.MIN_QTY <= '".$value."'
                    AND a.MIN_PAYMENT <= '".$totalPrice."'
                    AND a.[STATUS] = '3'
                    AND (a.MAX_TRX_NUMBER = 0 OR a.MAX_TRX_NUMBER > (SELECT COUNT(*) FROM TRANS_RENTAL_EQUIPMENT WHERE PROJECT_NO_CHAR = '".$project_no."' AND CAST(created_at AS DATE) = '".date('Y-m-d')."' AND [STATUS] <> 0))
                    AND a.START_PROMO_DATE <= '".date('Y-m-d H:i')."' AND a.END_PROMO_DATE >= '".date('Y-m-d H:i')."'");

                if(count($dataPromo) > 0) {
                    array_push($dataPromoArr, $dataPromo);
                }
            }
        }

        return response()->json([
            'dataPromo' => $dataPromoArr
        ]);
    }

    public function getPromoByIdEquipment($id, $id2) {
        $project_no = session('current_project');
        $id = explode(",", $id);

        // Mengambil Data Category Dari Equipment Yang Sudah Dipilih
        $category = explode(",", $id2);
        $categoryId = array();
        foreach($category as $data) {
            $dataEquipment = DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $data)->where('PROJECT_NO_CHAR', $project_no)->first();
            array_push($categoryId, $dataEquipment->MD_EQUIPMENT_CATEGORY_ID_INT);
        }

        // Menghitung Value Dari Category Yang Sudah Dipilih Dan Di Grouping Berdasarkan Category-nya
        $category = array_count_values($categoryId);

        $dataPromoEquipment = DB::table('MD_PROMO_EQUIPMENT')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_EQUIPMENT_ID_INT', $id)
            ->first();

        $dataPromoEquipmentFree = DB::table('MD_PROMO_EQUIPMENT')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_EQUIPMENT_ID_INT', $id)
            ->sum('QTY_FREE_INT');

        $dataPromoDiscountPercent = DB::table('MD_PROMO_EQUIPMENT')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_EQUIPMENT_ID_INT', $id)
            ->sum('DISCOUNT_PERCENT_FLOAT');

        $dataPromoDiscountNominal = DB::table('MD_PROMO_EQUIPMENT')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_EQUIPMENT_ID_INT', $id)
            ->sum('DISCOUNT_NOMINAL_FLOAT');

        $dataPromoMinQty = DB::table('MD_PROMO_EQUIPMENT')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_EQUIPMENT_ID_INT', $id)
            ->sum('MIN_QTY');

        // Mengambil Value Dari Data Category Yang Sudah Di Grouping Sebelumnya
        $valueCategoryFree = $category[(string) $dataPromoEquipment->MD_EQUIPMENT_CATEGORY_CHAR];

        return response()->json([
            'dataPromoEquipmentFree' => $dataPromoEquipmentFree,
            'dataPromoDiscountPercent' => $dataPromoDiscountPercent,
            'dataPromoDiscountNominal' => $dataPromoDiscountNominal,
            'dataPromoMinQty' => $dataPromoMinQty,
            'valueCategoryFree' => $valueCategoryFree
        ]);
    }
}
