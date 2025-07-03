<?php namespace App\Http\Controllers\Scanning\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Scanning\Report\VisitorsLoginReport\VisitorsLoginReport;

class ReportVisitorsLoginController extends Controller
{
    
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Scanning.Report.visitorsLogin.index',
            [
                'START_DATE_PARAM' => NULL,
                'END_DATE_PARAM' => NULL,
                'IS_POST' => $IS_POST
            ]);
    }

    public function viewReportVisitorsLogin($start_date, $end_date) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new VisitorsLoginReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        return View::make('Scanning.Report.visitorsLogin.index',
            [
                'START_DATE_PARAM' => $START_DATE_PARAM,
                'END_DATE_PARAM' => $END_DATE_PARAM,
                'IS_POST' => $IS_POST,
                'report' => $report
            ]);
    }

    public function viewReportVisitorsLoginPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new VisitorsLoginReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataVisitorsLogin = $report->dataStore("visitors_login_table1")->data();

        return View::make("Scanning.Report.visitorsLogin.pdfReportVisitorsLogin",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataVisitorsLogin' => $dataVisitorsLogin
        ]);
    }

    public function viewReportVisitorsLoginExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new VisitorsLoginReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("visitors_login_report_excel")->toBrowser("Visitors Login.xlsx");
    }
}
