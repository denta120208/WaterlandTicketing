<?php

namespace App\Http\Controllers\Scanning\VisitorsCounter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class VisitorsCounterController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        return view('Scanning.VisitorsCounter.visitors_counter');
    }

    public function getVisitorsCounter() {
        $project_no = session('current_project');

        $dataVisitorsCounterIn = DB::select("SELECT ISNULL(SUM(a.AMOUNT_INT), 0) AS AMOUNT_INT
            FROM LOG_COUNTING_VISITORS AS a
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
            AND a.LOG_COUNTING_VISITORS_TYPE_ID_INT = '1'
            AND CAST(a.TIME_DTTIME AS DATE) = '".date('Y-m-d')."'");

        $dataVisitorsCounterOut = DB::select("SELECT ISNULL(SUM(a.AMOUNT_INT), 0) AS AMOUNT_INT
            FROM LOG_COUNTING_VISITORS AS a
            WHERE a.PROJECT_NO_CHAR = '".$project_no."'
            AND a.LOG_COUNTING_VISITORS_TYPE_ID_INT = '2'
            AND CAST(a.TIME_DTTIME AS DATE) = '".date('Y-m-d')."'");
        
        return response()->json([
            'dataVisitorsCounterIn' => $dataVisitorsCounterIn,
            'dataVisitorsCounterOut' => $dataVisitorsCounterOut
        ]);
    }

    public function sendVisitorsCounterPlus($param) {
        $param = base64_decode($param, TRUE);

        $project_no = session('current_project');
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('LOG_COUNTING_VISITORS')->insert([
                'LOG_COUNTING_VISITORS_TYPE_ID_INT' => "1",
                'AMOUNT_INT' => $param,
                'TIME_DTTIME' => $dateNow,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
        }
        
        return $this->getVisitorsCounter();
    }

    public function sendVisitorsCounterMinus($param) {
        $param = base64_decode($param, TRUE);

        $project_no = session('current_project');
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('LOG_COUNTING_VISITORS')->insert([
                'LOG_COUNTING_VISITORS_TYPE_ID_INT' => "2",
                'AMOUNT_INT' => $param,
                'TIME_DTTIME' => $dateNow,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
        }
        
        return $this->getVisitorsCounter();
    }
}
