<?php namespace App\Http\Controllers\Sales\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Sales\Report\POSReport\POSReport;

class ReportPOSController extends Controller {
    
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Sales.Report.pos.index',
        [
            'START_DATE_PARAM' => NULL,
            'END_DATE_PARAM' => NULL,
            'IS_POST' => $IS_POST
        ]);
    }

    public function viewReportPOS($start_date, $end_date) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new POSReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        return View::make('Sales.Report.pos.index',
        [
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'IS_POST' => $IS_POST,
            'report' => $report
        ]);
    }

    public function viewReportPOSPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new POSReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataPOS = $report->dataStore("pos_report_table1")->data();

        return View::make("Sales.Report.pos.pdfReportPOS",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataPOS' => $dataPOS
        ]);
    }

    public function viewReportPOSDetailsPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new POSReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataPOSDetails = $report->dataStore("pos_details_report_table1")->data();

        return View::make("Sales.Report.pos.pdfReportPOSDetails",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataPOSDetails' => $dataPOSDetails
        ]);
    }

    public function viewReportPOSExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new POSReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("pos_report_excel")->toBrowser("POS.xlsx");
    }

    public function viewReportPOSDetailsExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new POSReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("pos_details_report_excel")->toBrowser("POS Details.xlsx");
    }
}
