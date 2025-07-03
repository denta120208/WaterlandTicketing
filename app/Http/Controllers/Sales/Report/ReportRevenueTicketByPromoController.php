<?php

namespace App\Http\Controllers\Sales\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Sales\Report\RevenueTicketByPromoReport\RevenueTicketByPromoReport;

class ReportRevenueTicketByPromoController extends Controller
{
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Sales.Report.revenueTicketByPromo.index',
            [
                'START_DATE_PARAM' => NULL,
                'END_DATE_PARAM' => NULL,
                'KATEGORI_PARAM' => NULL,
                'IS_POST' => $IS_POST
            ]);
    }

    public function viewReportRevenueTicketByPromo($start_date, $end_date, $kategori) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);
        $KATEGORI_PARAM = base64_decode($kategori, TRUE);

        $report = new RevenueTicketByPromoReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM,
            "kategori_param"=>$KATEGORI_PARAM
        ));

        $report->run();

        return View::make('Sales.Report.revenueTicketByPromo.index',
            [
                'START_DATE_PARAM' => $START_DATE_PARAM,
                'END_DATE_PARAM' => $END_DATE_PARAM,
                'KATEGORI_PARAM' => $KATEGORI_PARAM,
                'IS_POST' => $IS_POST,
                'report' => $report
            ]);
    }

    public function viewReportRevenueTicketByPromoPrint($start_date, $end_date, $kategori) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);
        $KATEGORI_PARAM = base64_decode($kategori, TRUE);

        $report = new RevenueTicketByPromoReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM,
            "kategori_param"=>$KATEGORI_PARAM
        ));

        $report->run();

        $dataRevenueTicketByPromo = $report->dataStore("revenue_ticket_by_promo_table1")->data();

        return View::make("Sales.Report.revenueTicketByPromo.pdfReportRevenueTicketByPromo",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'KATEGORI_PARAM' => $KATEGORI_PARAM,
            'dataRevenueTicketByPromo' => $dataRevenueTicketByPromo
        ]);
    }

    public function viewReportRevenueTicketByPromoExcel($start_date, $end_date, $kategori) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);
        $KATEGORI_PARAM = base64_decode($kategori, TRUE);

        $report = new RevenueTicketByPromoReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM,
            "kategori_param"=>$KATEGORI_PARAM
        ));

        $report->run();

        $report->exportToExcel("revenue_ticket_by_promo_report_excel")->toBrowser("Revenue Ticket By Promo.xlsx");
    }
}