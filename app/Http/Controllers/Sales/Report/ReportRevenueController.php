<?php namespace App\Http\Controllers\Sales\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Sales\Report\RevenueReport\RevenueReport;

class ReportRevenueController extends Controller
{
    
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Sales.Report.revenue.index',
            [
                'START_DATE_PARAM' => NULL,
                'END_DATE_PARAM' => NULL,
                'IS_POST' => $IS_POST
            ]);
    }

    public function viewReportRevenue($start_date, $end_date) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new RevenueReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        return View::make('Sales.Report.revenue.index',
            [
                'START_DATE_PARAM' => $START_DATE_PARAM,
                'END_DATE_PARAM' => $END_DATE_PARAM,
                'IS_POST' => $IS_POST,
                'report' => $report
            ]);
    }

    public function viewReportRevenuePrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new RevenueReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataRevenue = $report->dataStore("revenue_table1")->data();

        return View::make("Sales.Report.revenue.pdfReportRevenue",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataRevenue' => $dataRevenue
        ]);
    }

    public function viewReportRevenueExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new RevenueReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("revenue_report_excel")->toBrowser("Revenue.xlsx");
    }
}
