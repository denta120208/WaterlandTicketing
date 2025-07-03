<?php

namespace App\Http\Controllers\Sales\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class POSController extends Controller {

    public function index() {
        $project_no = session('current_project');

        $dataTransPOS = DB::select("SELECT a.TRANS_POS_ID_INT,
            a.TRANS_POS_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.QTY_INT, a.TOTAL_HARGA_FLOAT,
            a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_POS_DETAIL_ID_INT), 0) - a.QTY_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_POS_DETAIL_ID_INT), 0) - a.QTY_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_POS_DETAIL_ID_INT), 0) - a.QTY_INT
                END
            AS QTY_FREE_INT,
            ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT,
            c.DESC_CHAR AS PROMO_DESC_CHAR
            FROM TRANS_POS AS a
            LEFT JOIN TRANS_PROMO_POS AS b ON b.TRANS_POS_NOCHAR = a.TRANS_POS_NO_CHAR
            LEFT JOIN MD_PROMO_POS AS c ON c.PROMO_POS_ID_INT = b.PROMO_POS_ID_INT
            LEFT JOIN TRANS_POS_DETAILS AS d ON d.TRANS_POS_NO_CHAR = a.TRANS_POS_NO_CHAR
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = '1'
            GROUP BY a.TRANS_POS_ID_INT, a.TRANS_POS_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.QTY_INT,
            a.TOTAL_HARGA_FLOAT, a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            c.DESC_CHAR
            ORDER BY a.TRANS_POS_ID_INT DESC");

        $dataTransPOSDetails = DB::select("SELECT b.TRANS_POS_DETAIL_ID_INT, b.TRANS_POS_NO_CHAR,
            b.DESC_CHAR, d.DESC_CHAR AS MD_PRODUCT_POS_CATEGORY_DESC_CHAR, b.QTY_INT, b.HARGA_SATUAN_FLOAT, b.TOTAL_HARGA_FLOAT, b.created_at
            FROM TRANS_POS AS a
            INNER JOIN TRANS_POS_DETAILS AS b ON b.TRANS_POS_NO_CHAR = a.TRANS_POS_NO_CHAR
            INNER JOIN MD_PRODUCT_POS AS c ON c.MD_PRODUCT_POS_ID_INT = b.MD_PRODUCT_POS_ID_INT
            INNER JOIN MD_PRODUCT_POS_CATEGORY AS d ON d.MD_PRODUCT_POS_CATEGORY_ID_INT = c.MD_PRODUCT_POS_CATEGORY_ID_INT
            WHERE a.[STATUS] = 1 AND b.PROJECT_NO_CHAR = '".$project_no."'");

        return view('Sales.POS.pos')
            ->with('dataTransPOS', $dataTransPOS)
            ->with('dataTransPOSDetails', $dataTransPOSDetails);
    }

    public function viewPos() {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));

        $ddlDataProductPOS = DB::table('MD_PRODUCT_POS')
            ->selectRaw('MD_PRODUCT_POS.*, MD_PRODUCT_POS_CATEGORY.DESC_CHAR AS MD_PRODUCT_POS_CATEGORY_DESC_CHAR')
            ->join('MD_PRODUCT_POS_CATEGORY', 'MD_PRODUCT_POS_CATEGORY.MD_PRODUCT_POS_CATEGORY_ID_INT', '=', 'MD_PRODUCT_POS.MD_PRODUCT_POS_CATEGORY_ID_INT')
            ->where('MD_PRODUCT_POS.STATUS', '1')
            ->where('MD_PRODUCT_POS.PROJECT_NO_CHAR', $project_no)
            ->orderBy('MD_PRODUCT_POS_CATEGORY.DESC_CHAR', 'ASC')->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('Sales.POS.view_pos')
            ->with('ddlDataProductPOS', $ddlDataProductPOS)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('cashierName', $cashierName);
    }

    public function getProductPOSPriceById($id) {
        $project_no = session('current_project');
        $id = explode(",", $id);

        $dataProductPOSPrice = DB::table('MD_PRODUCT_POS')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('MD_PRODUCT_POS_ID_INT', $id)
            ->sum('HARGA_SATUAN_FLOAT');
        
        return response()->json([
            'dataProductPOSPrice' => $dataProductPOSPrice
        ]);
    }

    public function savePOS(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            $cart = json_decode($request->TXT_DATATABLE);

            $counterTransPOS = DB::table('counter_table')
                ->where('PROJECT_NO_CHAR', $project_no)->first();

            $TRANS_POS_NO_CHAR = "POS/" . $dataProject->PROJECT_CODE . "/" . sprintf("%02d", date('m')) . "/" . $dateNow->format('y') . "/" . sprintf("%04d", $counterTransPOS->trans_pos_no_char);

            // UPDATE TABLE COUNTER
            DB::table('counter_table')->where('PROJECT_NO_CHAR', $project_no)->update([
                'trans_pos_no_char' => ($counterTransPOS->trans_pos_no_char + 1)
            ]);

            $totalQty = $request->TXT_TOTAL_QTY;
            $totalHargaTagihan = (float) str_replace('.', '', $request->TXT_TOTAL_PRICE_AFTER_DISCOUNT);

            // INSERT TABLE TRANS
            $paymentAmount1 = $request->TXT_PAYMENT_AMOUNT1 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT1;
            $paymentAmount2 = $request->TXT_PAYMENT_AMOUNT2 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT2;
            $totalChange = ($paymentAmount1 + $paymentAmount2) - $totalHargaTagihan;
            DB::table('TRANS_POS')->insert([
                'TRANS_POS_NO_CHAR' => $TRANS_POS_NO_CHAR,
                'CUSTOMER_NAME_CHAR' => $request->TXT_CUSTOMER_NAME,
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
            $totalPB1 = 0;
            $totalPPH = 0;
            for($i = 0; $i < count($cart); $i++) {
                $dataProductPOS = DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $cart[$i][0])->where('PROJECT_NO_CHAR', $project_no)->first();
                $totalPB1Satuan = ($dataProductPOS->HARGA_SATUAN_FLOAT / (($dataProductPOS->PB1_PERCENT_INT / 100) + 1)) * ($dataProductPOS->PB1_PERCENT_INT / 100);
                $totalPPHSatuan = ($dataProductPOS->HARGA_SATUAN_FLOAT - $totalPB1Satuan) * ($dataProductPOS->PPH_PERCENT_INT / 100);

                DB::table('TRANS_POS_DETAILS')->insert([
                    'TRANS_POS_NO_CHAR' => $TRANS_POS_NO_CHAR,
                    'MD_PRODUCT_POS_ID_INT' => $cart[$i][0],
                    'DESC_CHAR' => $dataProductPOS->NAMA_PRODUCT,
                    'QTY_INT' => $cart[$i][3],
                    'HARGA_SATUAN_FLOAT' => $dataProductPOS->HARGA_SATUAN_FLOAT,
                    'TOTAL_HARGA_FLOAT' => $cart[$i][3] * $dataProductPOS->HARGA_SATUAN_FLOAT,
                    'PB1_PERCENT' => $dataProductPOS->PB1_PERCENT_INT,
                    'PPH_PERCENT' => $dataProductPOS->PPH_PERCENT_INT,
                    'TOTAL_PB1_NUM' => $totalPB1Satuan,
                    'TOTAL_PPH_NUM' => $totalPPHSatuan,
                    'PROJECT_NO_CHAR' => $project_no,
                    'created_by' => $request->TXT_CASHIER_NAME,
                    'created_at' => $dateNow
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
            DB::table('TRANS_POS')->where("TRANS_POS_NO_CHAR", $TRANS_POS_NO_CHAR)->update([
                "TOTAL_PB1_NUM" => $totalPB1,
                "TOTAL_PPH_NUM" => $totalPPH
            ]);

            // INSERT TABLE TRANS PROMO
            if($request->DDL_PROMO != null) {
                DB::table('TRANS_PROMO_POS')->insert([
                    'TRANS_POS_NOCHAR' => $TRANS_POS_NO_CHAR,
                    'PROMO_POS_ID_INT' => $request->DDL_PROMO,
                    'CASHIER_NAME_CHAR' => $request->TXT_CASHIER_NAME,
                    'PROJECT_NO_CHAR' => $project_no,
                    'STATUS' => "1",
                    'created_by' => $request->TXT_CASHIER_NAME,
                    'created_at' => $dateNow
                ]);

                // INSERT TABLE TRANS DETAILS (FREE QTY)
                $dataPromoPOS = DB::table('MD_PROMO_POS')->where('PROMO_POS_ID_INT', $request->DDL_PROMO)->where('PROJECT_NO_CHAR', $project_no)->first();
                $dataPromoMinQty = DB::table('MD_PROMO_POS')->where('PROJECT_NO_CHAR', $project_no)->where('PROMO_POS_ID_INT', $request->DDL_PROMO)->sum('MIN_QTY');
                $dataPromoPOSFree = DB::table('MD_PROMO_POS')->where('PROJECT_NO_CHAR', $project_no)->where('PROMO_POS_ID_INT', $request->DDL_PROMO)->sum('QTY_FREE_INT');

                $arrProductPOSQtyCurr = [];
                foreach($cart as $key => $data) {
                    $dataProductPOS = DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $data[0])->where('PROJECT_NO_CHAR', $project_no)->first();
                    array_push($arrProductPOSQtyCurr, ["MD_PRODUCT_POS_CATEGORY_ID_INT" => $dataProductPOS->MD_PRODUCT_POS_CATEGORY_ID_INT, "MD_PRODUCT_POS_ID_INT" => $data[0], "QTY" => $data[3]]);
                }

                if($dataPromoPOS->MD_PRODUCT_POS_CATEGORY_CHAR == "ALL") {
                    foreach($arrProductPOSQtyCurr as $key => $data) {
                        $minQty = (float) $dataPromoMinQty;
                        if($minQty > 0) {
                            $QtyCurr = (int) $data["QTY"];
                            $perkalianKelipatan = (int) ($QtyCurr / $minQty);
                            $dataFree = (float) ($dataPromoPOSFree);
                            $posFree = $dataFree * $perkalianKelipatan;
                            $arrProductPOSQtyCurr[$key]["QTY_FREE"] = $posFree;
                        }
                        else {
                            $arrProductPOSQtyCurr[$key]["QTY_FREE"] = $dataPromoPOSFree;
                        }
                    }
                }
                else {
                    if($dataPromoPOS->MD_PRODUCT_POS_CHAR == "ALL") {
                        foreach($arrProductPOSQtyCurr as $key => $data) {
                            if($dataPromoPOS->MD_PRODUCT_POS_CATEGORY_CHAR == $data["MD_PRODUCT_POS_CATEGORY_ID_INT"]) {
                                $minQty = (float) $dataPromoMinQty;
                                if($minQty > 0) {
                                    $QtyCurr = (int) $data["QTY"];
                                    $perkalianKelipatan = (int) ($QtyCurr / $minQty);
                                    $dataFree = (float) ($dataPromoPOSFree);
                                    $posFree = $dataFree * $perkalianKelipatan;
                                    $arrProductPOSQtyCurr[$key]["QTY_FREE"] = $posFree;
                                }
                                else {
                                    $arrProductPOSQtyCurr[$key]["QTY_FREE"] = $dataPromoPOSFree;
                                }
                            }
                            else {
                                $arrProductPOSQtyCurr[$key]["QTY_FREE"] = 0;
                            }
                        }
                    }
                    else {
                        foreach($arrProductPOSQtyCurr as $key => $data) {
                            if($dataPromoPOS->MD_PRODUCT_POS_CHAR == $data["MD_PRODUCT_POS_ID_INT"]) {
                                $minQty = (float) $dataPromoMinQty;
                                if($minQty > 0) {
                                    $QtyCurr = (int) $data["QTY"];
                                    $perkalianKelipatan = (int) ($QtyCurr / $minQty);
                                    $dataFree = (float) ($dataPromoPOSFree);
                                    $posFree = $dataFree * $perkalianKelipatan;
                                    $arrProductPOSQtyCurr[$key]["QTY_FREE"] = $posFree;
                                }
                                else {
                                    $arrProductPOSQtyCurr[$key]["QTY_FREE"] = 0;
                                }
                            }
                            else {
                                $arrProductPOSQtyCurr[$key]["QTY_FREE"] = 0;
                            }
                        }
                    }
                }

                foreach($arrProductPOSQtyCurr as $data) {
                    if($data["QTY_FREE"] > 0) {
                        $dataProductPOS = DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $data["MD_PRODUCT_POS_ID_INT"])->where('PROJECT_NO_CHAR', $project_no)->first();
                        DB::table('TRANS_POS_DETAILS')->insert([
                            'TRANS_POS_NO_CHAR' => $TRANS_POS_NO_CHAR,
                            'MD_PRODUCT_POS_ID_INT' => $data["MD_PRODUCT_POS_ID_INT"],
                            'DESC_CHAR' => $dataProductPOS->NAMA_PRODUCT,
                            'QTY_INT' => $data["QTY_FREE"],
                            'HARGA_SATUAN_FLOAT' => 0,
                            'TOTAL_HARGA_FLOAT' => 0,
                            'PROJECT_NO_CHAR' => $project_no,
                            'created_by' => $request->TXT_CASHIER_NAME,
                            'created_at' => $dateNow
                        ]);
                    }
                }
            }

            $dataTransPOS = DB::table('TRANS_POS')
                ->where('TRANS_POS_NO_CHAR', $TRANS_POS_NO_CHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('STATUS', "1")
                ->first();

            \DB::commit();
        }
        catch (\Exception $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        $urlRedirect = URL("/print_pos/" . $dataTransPOS->TRANS_POS_ID_INT);
        session()->flash('urlRedirect', $urlRedirect);
        session()->flash('change', number_format($totalChange, 0, ",", "."));

        return redirect()->route('pos');
    }

    public function printPOS($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTransPOS = DB::table('TRANS_POS')
            ->where('STATUS', "1")
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('TRANS_POS_ID_INT', $id)->first();

        $dataTransPOSDetails = DB::table('TRANS_POS_DETAILS')
            ->select('TRANS_POS_DETAILS.*', 'MD_PRODUCT_POS.NAMA_PRODUCT AS MD_PRODUCT_POS_DESC_CHAR', 'MD_PRODUCT_POS_CATEGORY.DESC_CHAR AS MD_PRODUCT_POS_CATEGORY_DESC_CHAR')
            ->join('MD_PRODUCT_POS', 'TRANS_POS_DETAILS.MD_PRODUCT_POS_ID_INT', '=', 'MD_PRODUCT_POS.MD_PRODUCT_POS_ID_INT')
            ->join('MD_PRODUCT_POS_CATEGORY', 'MD_PRODUCT_POS_CATEGORY.MD_PRODUCT_POS_CATEGORY_ID_INT', '=', 'MD_PRODUCT_POS.MD_PRODUCT_POS_CATEGORY_ID_INT')
            ->where('TRANS_POS_DETAILS.TRANS_POS_NO_CHAR', $dataTransPOS->TRANS_POS_NO_CHAR)
            ->where('TRANS_POS_DETAILS.PROJECT_NO_CHAR', $project_no)->get();
        
        return view('Sales.POS.print_pos')
            ->with('dataTransPOSDetails', $dataTransPOSDetails)
            ->with('dataTransPOS', $dataTransPOS)
            ->with('dataProject', $dataProject);
    }

    public function cancelPOS($id) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $dataTransPOS = DB::table('TRANS_POS')->where('TRANS_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();
            $dataTransPOSDetails = DB::table('TRANS_POS_DETAILS')->where('TRANS_POS_NO_CHAR', $dataTransPOS->TRANS_POS_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->get();

            DB::table('TRANS_POS')->where('TRANS_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            if($dataTransPOS != null) {
                DB::table('TRANS_PROMO_POS')->where('TRANS_POS_NOCHAR', $dataTransPOS->TRANS_POS_NO_CHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'STATUS' => "0",
                    'updated_by' => $cashierName,
                    'updated_at' => $dateNow
                ]);
            }
        
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Cancel Transaction Successfully!");

        return redirect()->route('listPOS');
    }

    public function getPromoPOS($paymentMethod1, $paymentMethod2, $dataTableProductPOS, $qty, $totalPrice) {
        $project_no = session('current_project');

        if($dataTableProductPOS == "null") {
            $dataPromoArr = array();
        }
        else {
            $arrDataTableProductPOS = json_decode($dataTableProductPOS);

            $dataPromoArr = array();
            foreach($arrDataTableProductPOS as $key => $data) {
                $dataProductPOS = DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $data[0])->where('PROJECT_NO_CHAR', $project_no)->first();

                $dataPromo = DB::select("SELECT a.*,
                    CASE WHEN a.MD_PRODUCT_POS_CATEGORY_CHAR = 'ALL' THEN a.MD_PRODUCT_POS_CATEGORY_CHAR ELSE b.DESC_CHAR END AS MD_PRODUCT_POS_CATEGORY_DESC_CHAR,
                    CASE WHEN a.MD_PRODUCT_POS_CHAR = 'ALL' THEN a.MD_PRODUCT_POS_CHAR ELSE c.NAMA_PRODUCT END AS MD_PRODUCT_POS_DESC_CHAR
                    FROM MD_PROMO_POS AS a
                    LEFT JOIN MD_PRODUCT_POS_CATEGORY AS b ON CAST(b.MD_PRODUCT_POS_CATEGORY_ID_INT AS VARCHAR) = a.MD_PRODUCT_POS_CATEGORY_CHAR
                    LEFT JOIN MD_PRODUCT_POS AS c ON CAST(c.MD_PRODUCT_POS_ID_INT AS VARCHAR) = a.MD_PRODUCT_POS_CHAR
                    WHERE a.PROJECT_NO_CHAR = '".$project_no."'
                    AND (a.MD_PRODUCT_POS_CATEGORY_CHAR = '".$dataProductPOS->MD_PRODUCT_POS_CATEGORY_ID_INT."' OR a.MD_PRODUCT_POS_CATEGORY_CHAR = 'ALL')
                    AND (a.MD_PRODUCT_POS_CHAR = '".$data[0]."' OR a.MD_PRODUCT_POS_CHAR = 'ALL')
                    AND (a.PAYMENT_METHOD_CHAR = '".$paymentMethod1."' OR a.PAYMENT_METHOD_CHAR = '".$paymentMethod2."' OR a.PAYMENT_METHOD_CHAR = 'ALL')
                    AND a.MIN_QTY <= '".$data[3]."'
                    AND a.MIN_PAYMENT <= '".$data[4]."'
                    AND a.[STATUS] = '3'
                    AND (a.MAX_TRX_NUMBER = 0 OR a.MAX_TRX_NUMBER > (SELECT COUNT(*) FROM TRANS_POS WHERE PROJECT_NO_CHAR = '".$project_no."' AND CAST(created_at AS DATE) = '".date('Y-m-d')."' AND [STATUS] <> 0))
                    AND a.START_PROMO_DATE <= '".date('Y-m-d H:i')."' AND a.END_PROMO_DATE >= '".date('Y-m-d H:i')."'");

                foreach($dataPromo as $item) {
                    array_push($dataPromoArr, $item);
                }
            }

            if(count($dataPromoArr) > 0) {
                $dataPromoArr = collect($dataPromoArr);
                $dataPromoArr = $dataPromoArr->unique('PROMO_POS_ID_INT')->values()->all();
            }
        }

        return response()->json([
            'dataPromo' => $dataPromoArr
        ]);
    }

    public function getPromoByIdProductPOS($id, $dataTableProductPOS) {
        $project_no = session('current_project');
        $id = explode(",", $id);

        $arrDataTableProductPOS = json_decode($dataTableProductPOS);
        
        $arrProductPOSQtyCurr = [];
        $totalAllProductQty = 0;
        foreach($arrDataTableProductPOS as $key => $data) {
            $dataProductPOS = DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $data[0])->where('PROJECT_NO_CHAR', $project_no)->first();
            array_push($arrProductPOSQtyCurr, ["MD_PRODUCT_POS_CATEGORY_ID_INT" => $dataProductPOS->MD_PRODUCT_POS_CATEGORY_ID_INT, "MD_PRODUCT_POS_ID_INT" => $data[0], "QTY" => $data[3]]);
            $totalAllProductQty += $data[3];
        }

        $dataPromoPOS = DB::table('MD_PROMO_POS')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_POS_ID_INT', $id)
            ->first();

        $dataPromoPOSFree = DB::table('MD_PROMO_POS')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_POS_ID_INT', $id)
            ->sum('QTY_FREE_INT');

        $dataPromoDiscountPercent = DB::table('MD_PROMO_POS')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_POS_ID_INT', $id)
            ->sum('DISCOUNT_PERCENT_FLOAT');

        $dataPromoDiscountNominal = DB::table('MD_PROMO_POS')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_POS_ID_INT', $id)
            ->sum('DISCOUNT_NOMINAL_FLOAT');

        $dataPromoMinQty = DB::table('MD_PROMO_POS')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_POS_ID_INT', $id)
            ->sum('MIN_QTY');

        $qtyCurr = 0;
        if($dataPromoPOS->MD_PRODUCT_POS_CATEGORY_CHAR == "ALL") {
            $qtyCurr = $totalAllProductQty;
        }
        else {
            if($dataPromoPOS->MD_PRODUCT_POS_CHAR == "ALL") {
                foreach($arrProductPOSQtyCurr as $data) {
                    if($dataPromoPOS->MD_PRODUCT_POS_CATEGORY_CHAR == $data["MD_PRODUCT_POS_CATEGORY_ID_INT"]) {
                        $qtyCurr += $data["QTY"];
                    }
                }
            }
            else {
                foreach($arrProductPOSQtyCurr as $data) {
                    if($dataPromoPOS->MD_PRODUCT_POS_CHAR == $data["MD_PRODUCT_POS_ID_INT"]) {
                        $qtyCurr += $data["QTY"];
                    }
                }
            }
        }

        return response()->json([
            'dataPromoPOSFree' => $dataPromoPOSFree,
            'dataPromoDiscountPercent' => $dataPromoDiscountPercent,
            'dataPromoDiscountNominal' => $dataPromoDiscountNominal,
            'dataPromoMinQty' => $dataPromoMinQty,
            'qtyCurr' => $qtyCurr
        ]);
    }
}
