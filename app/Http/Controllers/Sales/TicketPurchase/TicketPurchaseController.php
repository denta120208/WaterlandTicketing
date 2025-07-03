<?php

namespace App\Http\Controllers\Sales\TicketPurchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class TicketPurchaseController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        $dataTicket = DB::select("SELECT a.TRANS_TICKET_ID_INT, a.TRANS_TICKET_NOCHAR, a.TRANS_TICKET_COUNT_INT,
            a.QTY_TICKET_INT, a.TOTAL_PRICE_NUM, a.TOTAL_PAID_NUM, a.TOTAL_CHANGE_NUM, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT
                END
            AS TICKET_FREE_INT,
            ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT,
            c.DESC_CHAR AS PROMO_DESC_CHAR
            FROM TRANS_TICKET_PURCHASE AS a
            LEFT JOIN TRANS_PROMO_TICKET_PURCHASE AS b ON b.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            LEFT JOIN MD_PROMO_TICKET_PURCHASE AS c ON c.PROMO_TICKET_PURCHASE_ID_INT = b.PROMO_TICKET_PURCHASE_ID_INT
            LEFT JOIN TRANS_TICKET_PURCHASE_DETAILS AS d ON d.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = '1' AND CAST(a.created_at AS DATE) = '".date('Y-m-d')."'
            GROUP BY a.TRANS_TICKET_ID_INT, a.TRANS_TICKET_NOCHAR, a.TRANS_TICKET_COUNT_INT,
            a.QTY_TICKET_INT, a.TOTAL_PRICE_NUM, a.TOTAL_PAID_NUM, a.TOTAL_CHANGE_NUM, a.CASHIER_NAME_CHAR, a.created_at,
            c.DESC_CHAR
            ORDER BY a.TRANS_TICKET_ID_INT DESC");

        $dataTicketDetails = DB::select("SELECT b.TRANS_TICKET_DETAIL_ID_INT, b.TRANS_TICKET_NOCHAR, b.TRANS_TICKET_DETAIL_COUNT_INT,
            b.NUMBER_TICKET, a.CASHIER_NAME_CHAR, b.IS_SCAN, b.SCAN_BY, b.SCAN_AT, b.created_at
            FROM TRANS_TICKET_PURCHASE AS a
            INNER JOIN TRANS_TICKET_PURCHASE_DETAILS AS b ON b.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = '1' AND CAST(a.created_at AS DATE) = '".date('Y-m-d')."'
            ORDER BY a.TRANS_TICKET_ID_INT DESC");

        return view('Sales.TicketPurchase.ticket_purchase')
            ->with('dataTicket', $dataTicket)
            ->with('dataTicketDetails', $dataTicketDetails);
    }

    public function buyTicketPurchase() {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $ddlDataTicketGroup = DB::table('MD_GROUP_TICKET')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $ddlDataPaymentMethod = DB::table('MD_PAYMENT_METHOD')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $dataHoliday = DB::table('MD_HOLIDAY')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('HOLIDAY_DATE', '=', $dateNow->format('Y-m-d'))
            ->count();

        if($dataHoliday > 0) {
            $IS_HOLIDAY = 1;
            $dataHoliday = DB::table('MD_HOLIDAY')->where('STATUS', '1')->where('PROJECT_NO_CHAR', $project_no)->where('HOLIDAY_DATE', '=', $dateNow->format('Y-m-d'))->first();
            $HOLIDAY_ID_INT = $dataHoliday->HOLIDAY_ID_INT;
        }
        else {
            $IS_HOLIDAY = 0;
            $HOLIDAY_ID_INT = "";
        }

        return view('Sales.TicketPurchase.buy_ticket_purchase')
            ->with('ddlDataTicketGroup', $ddlDataTicketGroup)
            ->with('ddlDataPaymentMethod', $ddlDataPaymentMethod)
            ->with('cashierName', $cashierName)
            ->with('IS_HOLIDAY', $IS_HOLIDAY)
            ->with('HOLIDAY_ID_INT', $HOLIDAY_ID_INT);
    }

    public function getTicketPriceByIdGroup($id) {
        $project_no = session('current_project');

        $dataTicketGroup = DB::table('MD_GROUP_TICKET')
            ->where('MD_GROUP_TICKET_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)->first();

        $ddlDataTicketPrice = DB::table('MD_PRICE_TICKET')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('MD_GROUP_TICKET_ID_INT', $id)->get();
        
        return response()->json([
            'dataTicketGroup' => $dataTicketGroup,
            'ddlDataTicketPrice' => $ddlDataTicketPrice
        ]);
    }

    public function getTicketPriceByIdPrice($id) {
        $project_no = session('current_project');

        $dataTicketPrice = DB::table('MD_PRICE_TICKET')
            ->where('MD_PRICE_TICKET_ID_INT', $id)
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)->first();
        
        return response()->json([
            'dataTicketPrice' => $dataTicketPrice
        ]);
    }

    public function getPromoTicketPurchase($paymentMethod1, $paymentMethod2, $ticketType, $ticketPrice, $qty, $totalPrice) {
        $project_no = session('current_project');

        $dataPromo = DB::select("SELECT *
            FROM MD_PROMO_TICKET_PURCHASE AS a
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
            AND (a.MD_GROUP_TICKET_CHAR = '".$ticketType."' OR a.MD_GROUP_TICKET_CHAR = 'ALL')
            AND (a.MD_PRICE_TICKET_CHAR = '".$ticketPrice."' OR a.MD_PRICE_TICKET_CHAR = 'ALL')
            AND (a.PAYMENT_METHOD_CHAR = '".$paymentMethod1."' OR a.PAYMENT_METHOD_CHAR = '".$paymentMethod2."' OR a.PAYMENT_METHOD_CHAR = 'ALL')
            AND a.MIN_TICKET_QTY <= '".$qty."'
            AND a.MIN_TICKET_PAYMENT <= '".$totalPrice."'
            AND a.[STATUS] = '3'
            AND (a.MAX_TRX_NUMBER = 0 OR a.MAX_TRX_NUMBER > (SELECT COUNT(*) FROM TRANS_TICKET_PURCHASE WHERE PROJECT_NO_CHAR = '".$project_no."' AND CAST(created_at AS DATE) = '".date('Y-m-d')."' AND [STATUS] <> 0))
            AND a.START_PROMO_DATE <= '".date('Y-m-d H:i')."' AND a.END_PROMO_DATE >= '".date('Y-m-d H:i')."'");

        return response()->json([
            'dataPromo' => $dataPromo
        ]);
    }

    public function getPromoByIdTicketPurchase($id) {
        $project_no = session('current_project');
        $id = explode(",", $id);

        $dataPromoTicketFree = DB::table('MD_PROMO_TICKET_PURCHASE')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_TICKET_PURCHASE_ID_INT', $id)
            ->sum('TICKET_FREE_INT');

        $dataPromoDiscountPercent = DB::table('MD_PROMO_TICKET_PURCHASE')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_TICKET_PURCHASE_ID_INT', $id)
            ->sum('DISCOUNT_PERCENT_FLOAT');

        $dataPromoDiscountNominal = DB::table('MD_PROMO_TICKET_PURCHASE')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_TICKET_PURCHASE_ID_INT', $id)
            ->sum('DISCOUNT_NOMINAL_FLOAT');

        $dataPromoMinQty = DB::table('MD_PROMO_TICKET_PURCHASE')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->whereIn('PROMO_TICKET_PURCHASE_ID_INT', $id)
            ->sum('MIN_TICKET_QTY');

        return response()->json([
            'dataPromoTicketFree' => $dataPromoTicketFree,
            'dataPromoDiscountPercent' => $dataPromoDiscountPercent,
            'dataPromoDiscountNominal' => $dataPromoDiscountNominal,
            'dataPromoMinQty' => $dataPromoMinQty
        ]);
    }

    public function saveTicketPurchase(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            \DB::beginTransaction();

            $dataTicketPrice = DB::table('MD_PRICE_TICKET')
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('STATUS', '1')
                ->where('MD_PRICE_TICKET_ID_INT', $request->DDL_TICKET_PRICE)->first();

            $counterTransTicket = DB::table('counter_table')
                ->where('PROJECT_NO_CHAR', $project_no)->first();

            $TRANS_TICKET_NOCHAR = "TCK/" . $dataProject->PROJECT_CODE . "/" . sprintf("%02d", date('m')) . "/" . $dateNow->format('y') . "/" . sprintf("%04d", $counterTransTicket->trans_ticket_no_char);

            // UPDATE TABLE COUNTER
            DB::table('counter_table')->where('PROJECT_NO_CHAR', $project_no)->update([
                'trans_ticket_no_char' => ($counterTransTicket->trans_ticket_no_char + 1)
            ]);

            // INSERT TABLE TRANS
            $countDataTicketPurchase = DB::table('TRANS_TICKET_PURCHASE')->where('PROJECT_NO_CHAR', $project_no)->count();
            $qty = $request->TXT_QTY_TICKET;
            $priceTicket = (float) str_replace('.', '', $request->TXT_PRICE_TICKET);
            $totalPriceAfterDiscount = (float) str_replace('.', '', $request->TXT_TOTAL_PRICE_AFTER_DISCOUNT);
            $paymentAmount1 = $request->TXT_PAYMENT_AMOUNT1 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT1;
            $paymentAmount2 = $request->TXT_PAYMENT_AMOUNT2 == NULL ? 0 : $request->TXT_PAYMENT_AMOUNT2;
            $totalPaid = $paymentAmount1 + $paymentAmount2;

            $freeTicket = $request->TXT_FREE_TICKET;
            // $totalPriceRealAfterFreeTicket = ($qty + $freeTicket) * $priceTicket;
            // $totalPriceRealBeforeFreeTicket = $qty * $priceTicket;

            // $totalPB1 = ($totalPriceRealBeforeFreeTicket / (($dataTicketPrice->MD_PRICE_TICKET_PB1_PERCENT_INT / 100) + 1)) * ($dataTicketPrice->MD_PRICE_TICKET_PB1_PERCENT_INT / 100);
            // $totalPPH = ($totalPriceRealBeforeFreeTicket - $totalPB1) * ($dataTicketPrice->MD_PRICE_TICKET_PPH_PERCENT_INT / 100);
            $totalPB1 = ($totalPriceAfterDiscount / (($dataTicketPrice->MD_PRICE_TICKET_PB1_PERCENT_INT / 100) + 1)) * ($dataTicketPrice->MD_PRICE_TICKET_PB1_PERCENT_INT / 100);
            $totalPPH = ($totalPriceAfterDiscount - $totalPB1) * ($dataTicketPrice->MD_PRICE_TICKET_PPH_PERCENT_INT / 100);
            $totalChange = $totalPaid - $totalPriceAfterDiscount;

            // Jika Bukan Holiday Maka ID Holiday Set NULL
            if($request->IS_HOLIDAY == 0) {
                $HOLIDAY_ID_INT = NULL;
            }
            else { // Jika Holiday Maka ID Holiday Di Set
                $HOLIDAY_ID_INT = $request->HOLIDAY_ID_INT;
            }

            DB::table('TRANS_TICKET_PURCHASE')->insert([
                'TRANS_TICKET_NOCHAR' => $TRANS_TICKET_NOCHAR,
                'TRANS_TICKET_COUNT_INT' => ($countDataTicketPurchase + 1),
                'MD_GROUP_TICKET_ID_INT' => $request->DDL_TICKET_TYPE,
                'MD_PRICE_TICKET_ID_INT' => $request->DDL_TICKET_PRICE,
                'PRICE_AMOUNT_TICKET_NUM' => $priceTicket,
                'PRICE_TICKET_PB1_PERCENT' => $dataTicketPrice->MD_PRICE_TICKET_PB1_PERCENT_INT,
                'PRICE_TICKET_PPH_PERCENT' => $dataTicketPrice->MD_PRICE_TICKET_PPH_PERCENT_INT,
                'QTY_TICKET_INT' => $qty,
                'TOTAL_PRICE_NUM' => $totalPriceAfterDiscount,
                'PAYMENT_METHOD_ID_INT_1' => $request->DDL_PAYMENT_METHOD1,
                'PAYMENT_METHOD_NUMBER_1' => $request->TXT_NUMBER1,
                'PAYMENT_AMOUNT_1' => $request->TXT_PAYMENT_AMOUNT1,
                'PAYMENT_METHOD_ID_INT_2' => $request->DDL_PAYMENT_METHOD2,
                'PAYMENT_METHOD_NUMBER_2' => $request->TXT_NUMBER2,
                'PAYMENT_AMOUNT_2' => $request->TXT_PAYMENT_AMOUNT2,
                'TOTAL_PAID_NUM' => $totalPaid,
                'TOTAL_CHANGE_NUM' => $totalChange,
                'TOTAL_PB1_NUM' => $totalPB1,
                'TOTAL_PPH_NUM' => $totalPPH,
                'IS_HOLIDAY' => $request->IS_HOLIDAY,
                'HOLIDAY_ID_INT' => $HOLIDAY_ID_INT,
                'CASHIER_NAME_CHAR' => $request->TXT_CASHIER_NAME,
                'STATUS' => "1",
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $request->TXT_CASHIER_NAME,
                'created_at' => $dateNow
            ]);

            // INSERT TABLE TRANS DETAILS
            for($i = 0; $i < ($qty + $freeTicket); $i++) {
                $NUMBER_TICKET = rand(1000000000, 9999999999);
                $isNumberExist = DB::table('TRANS_TICKET_PURCHASE_DETAILS')->where('NUMBER_TICKET', $NUMBER_TICKET)->count();
                $countDataTicketPurchaseDetails = DB::table('TRANS_TICKET_PURCHASE_DETAILS')->where('PROJECT_NO_CHAR', $project_no)->count();
                if($isNumberExist == 0) {
                    DB::table('TRANS_TICKET_PURCHASE_DETAILS')->insert([
                        'TRANS_TICKET_NOCHAR' => $TRANS_TICKET_NOCHAR,
                        'TRANS_TICKET_DETAIL_COUNT_INT' => ($countDataTicketPurchaseDetails + 1),
                        'NUMBER_TICKET' => $NUMBER_TICKET,
                        'PROJECT_NO_CHAR' => $project_no,
                        'created_by' => $request->TXT_CASHIER_NAME,
                        'created_at' => $dateNow
                    ]);
                }
                else {
                    $i--;
                }
            }

            // INSERT TABLE TRANS PROMO
            if($request->DDL_PROMO != null) {
                DB::table('TRANS_PROMO_TICKET_PURCHASE')->insert([
                    'TRANS_TICKET_NOCHAR' => $TRANS_TICKET_NOCHAR,
                    'PROMO_TICKET_PURCHASE_ID_INT' => $request->DDL_PROMO,
                    'CASHIER_NAME_CHAR' => $request->TXT_CASHIER_NAME,
                    'PROJECT_NO_CHAR' => $project_no,
                    'STATUS' => "1",
                    'created_by' => $request->TXT_CASHIER_NAME,
                    'created_at' => $dateNow
                ]);
            }

            $dataTransTicketCurrent = DB::table('TRANS_TICKET_PURCHASE')
                ->where('TRANS_TICKET_NOCHAR', $TRANS_TICKET_NOCHAR)
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('STATUS', "1")
                ->first();
        
            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('ticket_purchase');
        }

        $urlRedirect = URL("/print_ticket_purchase/" . $dataTransTicketCurrent->TRANS_TICKET_ID_INT);
        $urlRedirectReceipt = URL("/print_receipt_ticket_purchase/" . $dataTransTicketCurrent->TRANS_TICKET_ID_INT);
        session()->flash('urlRedirect', $urlRedirect);
        session()->flash('urlRedirectReceipt', $urlRedirectReceipt);
        session()->flash('change', number_format($totalChange, 0, ",", "."));

        return redirect()->route('buy_ticket_purchase');
    }

    public function printTicketPurchase($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTicketTrans = DB::table('TRANS_TICKET_PURCHASE')
            ->where('STATUS', "1")
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('TRANS_TICKET_ID_INT', $id)->first();

        $dataPriceTicket = DB::table("MD_PRICE_TICKET")
            ->where("MD_PRICE_TICKET_ID_INT", $dataTicketTrans->MD_PRICE_TICKET_ID_INT)
            ->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTicketDetails = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('TRANS_TICKET_NOCHAR', $dataTicketTrans->TRANS_TICKET_NOCHAR)
            ->where('PROJECT_NO_CHAR', $project_no)->get();
        
        return view('Sales.TicketPurchase.print_ticket_purchase')
            ->with('dataTicketDetails', $dataTicketDetails)
            ->with('dataTicketTrans', $dataTicketTrans)
            ->with('dataProject', $dataProject)
            ->with('dataPriceTicket', $dataPriceTicket);
    }

    public function printReceiptTicketPurchase($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTicketTrans = DB::select("SELECT a.TRANS_TICKET_ID_INT, a.TRANS_TICKET_NOCHAR, a.TRANS_TICKET_COUNT_INT,
            a.QTY_TICKET_INT, (a.QTY_TICKET_INT * a.PRICE_AMOUNT_TICKET_NUM) AS PRICE_REAL_NUM,
            a.TOTAL_PRICE_NUM, a.TOTAL_PAID_NUM, a.TOTAL_CHANGE_NUM, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT
                END
            AS TICKET_FREE_INT,
            ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT,
            e.MD_PRICE_TICKET_DESC
            FROM TRANS_TICKET_PURCHASE AS a
            LEFT JOIN TRANS_PROMO_TICKET_PURCHASE AS b ON b.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            LEFT JOIN MD_PROMO_TICKET_PURCHASE AS c ON c.PROMO_TICKET_PURCHASE_ID_INT = b.PROMO_TICKET_PURCHASE_ID_INT
            LEFT JOIN TRANS_TICKET_PURCHASE_DETAILS AS d ON d.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            LEFT JOIN MD_PRICE_TICKET AS e ON e.MD_PRICE_TICKET_ID_INT = a.MD_PRICE_TICKET_ID_INT
            WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.TRANS_TICKET_ID_INT = '".$id."'
            GROUP BY a.TRANS_TICKET_ID_INT, a.TRANS_TICKET_NOCHAR, a.TRANS_TICKET_COUNT_INT,
            a.QTY_TICKET_INT, a.TOTAL_PRICE_NUM, a.TOTAL_PAID_NUM, a.TOTAL_CHANGE_NUM, a.CASHIER_NAME_CHAR, a.created_at,
            a.PRICE_AMOUNT_TICKET_NUM, e.MD_PRICE_TICKET_DESC
            ORDER BY a.TRANS_TICKET_ID_INT DESC");
        
        return view('Sales.TicketPurchase.print_receipt_ticket_purchase')
            ->with('dataTicketTrans', $dataTicketTrans)
            ->with('dataProject', $dataProject);
    }

    public function printTicketPurchaseOne($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();

        $dataTicketDetails = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('TRANS_TICKET_DETAIL_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)->get();

        $dataTicketTrans = DB::table('TRANS_TICKET_PURCHASE')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('TRANS_TICKET_NOCHAR', $dataTicketDetails[0]->TRANS_TICKET_NOCHAR)->first();

        $dataPriceTicket = DB::table("MD_PRICE_TICKET")
            ->where("MD_PRICE_TICKET_ID_INT", $dataTicketTrans->MD_PRICE_TICKET_ID_INT)
            ->where('PROJECT_NO_CHAR', $project_no)->first();
        
        return view('Sales.TicketPurchase.print_ticket_purchase')
            ->with('dataTicketDetails', $dataTicketDetails)
            ->with('dataTicketTrans', $dataTicketTrans)
            ->with('dataProject', $dataProject)
            ->with('dataPriceTicket', $dataPriceTicket);
    }

    public function cancelTicketPurchase($id) {
        $project_no = session('current_project');
        $cashierName = trim(session('first_name') . ' ' . session('last_name'));
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        try {
            DB::beginTransaction();

            $dataTransTicket = DB::table('TRANS_TICKET_PURCHASE')->where('TRANS_TICKET_ID_INT', $id)->first();

            DB::table('TRANS_TICKET_PURCHASE')->where('TRANS_TICKET_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->update([
                'STATUS' => "0",
                'updated_by' => $cashierName,
                'updated_at' => $dateNow
            ]);

            if($dataTransTicket != null) {
                DB::table('TRANS_PROMO_TICKET_PURCHASE')->where('TRANS_TICKET_NOCHAR', $dataTransTicket->TRANS_TICKET_NOCHAR)->where('PROJECT_NO_CHAR', $project_no)->update([
                    'STATUS' => "0",
                    'updated_by' => $cashierName,
                    'updated_at' => $dateNow
                ]);
            }
        
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed cancel data, errmsg : ' . $ex);
            return redirect()->route('ticket_purchase');
        }

        session()->flash('success', "Cancel Ticket Purchase Successfully!");

        return redirect()->route('ticket_purchase');
    }
}
