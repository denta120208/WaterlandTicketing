<?php namespace App\Http\Controllers\Rental\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Rental\Report\EquipmentReport\EquipmentReport;

class ReportEquipmentController extends Controller
{
    
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Rental.Report.equipment.index',
        [
            'START_DATE_PARAM' => NULL,
            'END_DATE_PARAM' => NULL,
            'IS_POST' => $IS_POST
        ]);
    }

    public function viewReportEquipment($start_date, $end_date) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new EquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        return View::make('Rental.Report.equipment.index',
        [
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'IS_POST' => $IS_POST,
            'report' => $report
        ]);
    }

    public function viewReportEquipmentPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new EquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataEquipment = $report->dataStore("equipment_report_table1")->data();

        return View::make("Rental.Report.equipment.pdfReportEquipment",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataEquipment' => $dataEquipment
        ]);
    }

    public function viewReportEquipmentDetailsPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new EquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataEquipmentDetails = $report->dataStore("equipment_details_report_table1")->data();

        return View::make("Rental.Report.equipment.pdfReportEquipmentDetails",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataEquipmentDetails' => $dataEquipmentDetails
        ]);
    }

    public function viewReportEquipmentExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new EquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("equipment_report_excel")->toBrowser("Equipment.xlsx");
    }

    public function viewReportEquipmentDetailsExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new EquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("equipment_details_report_excel")->toBrowser("Equipment Details.xlsx");
    }
}
