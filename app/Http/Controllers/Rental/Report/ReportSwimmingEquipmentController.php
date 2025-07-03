<?php namespace App\Http\Controllers\Rental\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Rental\Report\SwimmingEquipmentReport\SwimmingEquipmentReport;

class ReportSwimmingEquipmentController extends Controller
{
    
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Rental.Report.swimmingEquipment.index',
        [
            'START_DATE_PARAM' => NULL,
            'END_DATE_PARAM' => NULL,
            'IS_POST' => $IS_POST
        ]);
    }

    public function viewReportSwimmingEquipment($start_date, $end_date) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new SwimmingEquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        return View::make('Rental.Report.swimmingEquipment.index',
        [
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'IS_POST' => $IS_POST,
            'report' => $report
        ]);
    }

    public function viewReportSwimmingEquipmentPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new SwimmingEquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataSwimmingEquipment = $report->dataStore("swimming_equipment_report_table1")->data();

        return View::make("Rental.Report.swimmingEquipment.pdfReportSwimmingEquipment",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataSwimmingEquipment' => $dataSwimmingEquipment
        ]);
    }

    public function viewReportSwimmingEquipmentDetailsPrint($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new SwimmingEquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $dataSwimmingEquipmentDetails = $report->dataStore("swimming_equipment_details_report_table1")->data();

        return View::make("Rental.Report.swimmingEquipment.pdfReportSwimmingEquipmentDetails",
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'dataSwimmingEquipmentDetails' => $dataSwimmingEquipmentDetails
        ]);
    }

    public function viewReportSwimmingEquipmentExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new SwimmingEquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("swimming_equipment_report_excel")->toBrowser("Swimming Equipment.xlsx");
    }

    public function viewReportSwimmingEquipmentDetailsExcel($start_date, $end_date) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);

        $report = new SwimmingEquipmentReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM
        ));

        $report->run();

        $report->exportToExcel("swimming_equipment_details_report_excel")->toBrowser("Swimming Equipment Details.xlsx");
    }
}
