<?php namespace App\Http\Controllers\Rental\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Rental\Report\LockerReport\LockerReport;

class ReportLockerController extends Controller
{
    
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Rental.Report.locker.index',
        [
            'START_DATE_PARAM' => NULL,
            'END_DATE_PARAM' => NULL,
            'IS_POST' => $IS_POST
        ]);
    }

    public function viewReportLocker($start_date, $end_date) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new LockerReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        return View::make('Rental.Report.locker.index',
        [
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'IS_POST' => $IS_POST,
            'report' => $report
        ]);
    }

    public function viewReportLockerPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new LockerReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataLocker = $report->dataStore("locker_report_table1")->data();

        return View::make("Rental.Report.locker.pdfReportLocker",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataLocker' => $dataLocker
        ]);
    }

    public function viewReportLockerDetailsPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new LockerReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataLockerDetails = $report->dataStore("locker_details_report_table1")->data();

        return View::make("Rental.Report.locker.pdfReportLockerDetails",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataLockerDetails' => $dataLockerDetails
        ]);
    }

    public function viewReportLockerExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new LockerReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("locker_report_excel")->toBrowser("Locker.xlsx");
    }

    public function viewReportLockerDetailsExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new LockerReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("locker_details_report_excel")->toBrowser("Locker Details.xlsx");
    }
}
