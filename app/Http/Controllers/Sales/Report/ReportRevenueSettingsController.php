<?php namespace App\Http\Controllers\Sales\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, Validation, DB, View, Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Sales\Report\RevenueSettingsReport\RevenueSettingsReport;

class ReportRevenueSettingsController extends Controller
{
    
    public function index(){
        $project_no = session('current_project');
        $dataProject = DB::table("MD_PROJECT")->where("PROJECT_NO_CHAR", $project_no)->first();

        $IS_POST = FALSE;

        if($dataProject->PROJECT_CODE == "HO") {
            $dataAllProjects = DB::table("MD_PROJECT")->where("PROJECT_ACTIVE_CHAR", 1)->whereNotIn("PROJECT_CODE", ["HO", "WTRIAL"])->orderBy("PROJECT_NAME", "ASC")->get();
        }
        else {
            $dataAllProjects = DB::table("MD_PROJECT")->where("PROJECT_NO_CHAR", $project_no)->get();
        }

        return View::make('Sales.Report.revenue_settings.index',
        [
            'dataAllProjects' => $dataAllProjects,
            'PROJECT_PARAM' => NULL,
            'CUT_OFF_PARAM' => NULL,
            'IS_POST' => $IS_POST
        ]);
    }

    public function viewReportRevenueSettings($project, $cut_off) {
        $project_no = session('current_project');
        $dataProject = DB::table("MD_PROJECT")->where("PROJECT_NO_CHAR", $project_no)->first();

        $IS_POST = TRUE;

        $PROJECT_PARAM = base64_decode($project, TRUE);
        $CUT_OFF_PARAM = base64_decode($cut_off, TRUE);

        if($dataProject->PROJECT_CODE == "HO") {
            $dataAllProjects = DB::table("MD_PROJECT")->where("PROJECT_ACTIVE_CHAR", 1)->whereNotIn("PROJECT_CODE", ["HO", "WTRIAL"])->orderBy("PROJECT_NAME", "ASC")->get();
        }
        else {
            $dataAllProjects = DB::table("MD_PROJECT")->where("PROJECT_NO_CHAR", $project_no)->get();
        }

        $report = new RevenueSettingsReport(array(
            "project"=>$PROJECT_PARAM,
            "cut_off_param"=>$CUT_OFF_PARAM
        ));

        $report->run();

        return View::make('Sales.Report.revenue_settings.index',
        [
            'dataAllProjects' => $dataAllProjects,
            'PROJECT_PARAM' => $PROJECT_PARAM,
            'CUT_OFF_PARAM' => $CUT_OFF_PARAM,
            'IS_POST' => $IS_POST,
            'report' => $report
        ]);
    }

    public function viewReportRevenueSettingsPrint($project, $cut_off) {
        $PROJECT_PARAM = base64_decode($project, TRUE);
        $CUT_OFF_PARAM = base64_decode($cut_off, TRUE);

        $report = new RevenueSettingsReport(array(
            "project"=>$PROJECT_PARAM,
            "cut_off_param"=>$CUT_OFF_PARAM
        ));

        $report->run();

        $dataRevenueSettings = $report->dataStore("revenue_settings_table1")->data();

        return View::make("Sales.Report.revenue_settings.pdfReportRevenueSettings",
        [
            'project_no' => $PROJECT_PARAM,
            'report' => $report,
            'CUT_OFF_PARAM' => $CUT_OFF_PARAM,
            'dataRevenueSettings' => $dataRevenueSettings
        ]);
    }

    public function viewReportRevenueSettingsExcel($project, $cut_off) {
        $PROJECT_PARAM = base64_decode($project, TRUE);
        $CUT_OFF_PARAM = base64_decode($cut_off, TRUE);

        $report = new RevenueSettingsReport(array(
            "project"=>$PROJECT_PARAM,
            "cut_off_param"=>$CUT_OFF_PARAM
        ));

        $report->run();

        $report->exportToExcel("revenue_settings_report_excel")->toBrowser("Revenue Project.xlsx");
    }
}
