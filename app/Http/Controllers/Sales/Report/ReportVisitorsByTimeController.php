<?php namespace App\Http\Controllers\Sales\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Sales\Report\VisitorsByTimeReport\VisitorsByTimeReport;

class ReportVisitorsByTimeController extends Controller
{
    
    public function index(){
        $project_no = session('current_project');
        $IS_POST = FALSE;

        return View::make('Sales.Report.visitorsByTime.index',
            [
                'START_DATE_PARAM' => NULL,
                'END_DATE_PARAM' => NULL,
                'KATEGORI_PARAM' => NULL,
                'IS_POST' => $IS_POST
            ]);
    }

    public function viewReportVisitorsByTime($start_date, $end_date, $kategori) {
        $IS_POST = TRUE;
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);
        $KATEGORI_PARAM = base64_decode($kategori, TRUE);

        $report = new VisitorsByTimeReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM,
            "kategori_param"=>$KATEGORI_PARAM
        ));

        $report->run();

        return View::make('Sales.Report.visitorsByTime.index',
            [
                'START_DATE_PARAM' => $START_DATE_PARAM,
                'END_DATE_PARAM' => $END_DATE_PARAM,
                'KATEGORI_PARAM' => $KATEGORI_PARAM,
                'IS_POST' => $IS_POST,
                'report' => $report
            ]);
    }

    public function viewReportVisitorsByTimePrint($start_date, $end_date, $kategori) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);
        $KATEGORI_PARAM = base64_decode($kategori, TRUE);

        $report = new VisitorsByTimeReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM,
            "kategori_param"=>$KATEGORI_PARAM
        ));

        $report->run();

        $dataVisitorsByTime = $report->dataStore("visitors_by_time_table1")->data();

        $viewPath = "Sales.Report.visitorsByTime.pdfReportVisitorsByTime" . $KATEGORI_PARAM;

        return View::make($viewPath,
        [
            'project_no' => $project_no,
            'report' => $report,
            'START_DATE_PARAM' => $START_DATE_PARAM,
            'END_DATE_PARAM' => $END_DATE_PARAM,
            'KATEGORI_PARAM' => $KATEGORI_PARAM,
            'dataVisitorsByTime' => $dataVisitorsByTime
        ]);
    }

    public function viewReportVisitorsByTimeExcel($start_date, $end_date, $kategori) {
        $project_no = session('current_project');

        $START_DATE_PARAM = base64_decode($start_date, TRUE);
        $END_DATE_PARAM = base64_decode($end_date, TRUE);
        $KATEGORI_PARAM = base64_decode($kategori, TRUE);

        $report = new VisitorsByTimeReport(array(
            "project"=>$project_no,
            "start_date_param"=>$START_DATE_PARAM,
            "end_date_param"=>$END_DATE_PARAM,
            "kategori_param"=>$KATEGORI_PARAM
        ));

        $report->run();

        if($KATEGORI_PARAM == "Perhari") {
            $report->exportToExcel("visitors_by_time_perhari_report_excel")->toBrowser("Visitors By Time.xlsx");
        }
        else if($KATEGORI_PARAM == "Perminggu") {
            $report->exportToExcel("visitors_by_time_perminggu_report_excel")->toBrowser("Visitors By Time.xlsx");
        }
        else if($KATEGORI_PARAM == "Perbulan") {
            $report->exportToExcel("visitors_by_time_perbulan_report_excel")->toBrowser("Visitors By Time.xlsx");
        }
    }
}
