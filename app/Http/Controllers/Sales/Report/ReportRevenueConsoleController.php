<?php

namespace App\Http\Controllers\Sales\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Sales\Report\RevenueConsoleReport\RevenueConsoleReport;

class ReportRevenueConsoleController extends Controller
{
    public function index() {
        $IS_POST = FALSE;

        return View::make('Sales.Report.revenue_console.index',
        [
            'CUT_OFF_PARAM' => NULL,
            'IS_POST' => $IS_POST
        ]);
    }

    public function viewReportRevenueConsole($cut_off) {
        $IS_POST = TRUE;

        $CUT_OFF_PARAM = base64_decode($cut_off, TRUE);

        $report = new RevenueConsoleReport(array(
            "cut_off_param" => $CUT_OFF_PARAM
        ));

        $report->run();

        return View::make('Sales.Report.revenue_console.index',
        [
            'CUT_OFF_PARAM' => $CUT_OFF_PARAM,
            'IS_POST' => $IS_POST,
            'report' => $report
        ]);
    }

    public function viewReportRevenueConsolePrint($cut_off) {
        $CUT_OFF_PARAM = base64_decode($cut_off, TRUE);

        $report = new RevenueConsoleReport(array(
            "cut_off_param" => $CUT_OFF_PARAM
        ));

        $report->run();

        $dataRevenueConsole = $report->dataStore("revenue_console_table1")->data();

        return View::make("Sales.Report.revenue_console.pdfReportRevenueConsole",
        [
            'report' => $report,
            'CUT_OFF_PARAM' => $CUT_OFF_PARAM,
            'dataRevenueConsole' => $dataRevenueConsole
        ]);
    }

    public function viewReportRevenueConsoleExcel($cut_off) {
        $CUT_OFF_PARAM = base64_decode($cut_off, TRUE);

        $report = new RevenueConsoleReport(array(
            "cut_off_param" => $CUT_OFF_PARAM
        ));

        $report->run();

        $report->exportToExcel("revenue_console_report_excel")->toBrowser("Revenue Console.xlsx");
    }
}
