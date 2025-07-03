<?php

namespace App\Http\Controllers\MasterData\PeriodeMembership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class PeriodeMembershipController extends Controller
{   
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.PeriodeMembership.periodeMembership')
            ->with('project_no', $project_no);
    }

    public function listTblPeriodeMembership(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'a.DESC_CHAR',
            1 =>'PERIODE_IN_MONTH_INT',
            2 =>'b.DESC_CHAR'
        );

        $totalData = \DB::table('MD_PERIODE_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_STATUS FROM MD_PERIODE_MEMBERSHIP AS a
                LEFT JOIN MD_PERIODE_MEMBERSHIP_STATUS AS b ON b.ID_STATUS = a.[STATUS]
                WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 1
                ORDER BY ".$order." ".$dir."
                OFFSET ".$start." ROWS
                FETCH NEXT ".$limit." ROWS ONLY");
        }
        else {
            $search = $request->input('search.value');

            $posts = DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_STATUS
                FROM MD_PERIODE_MEMBERSHIP AS a
                LEFT JOIN MD_PERIODE_MEMBERSHIP_STATUS AS b ON b.ID_STATUS = a.[STATUS]
                WHERE
                (
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.PERIODE_IN_MONTH_INT LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND b.DESC_CHAR LIKE '%".$search."%')
                ) AND
                a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 1
                ORDER BY ".$order." ".$dir."
                OFFSET ".$start." ROWS
                FETCH NEXT ".$limit." ROWS ONLY");

            $totalFiltered = count(DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_STATUS
                FROM MD_PERIODE_MEMBERSHIP AS a
                LEFT JOIN MD_PERIODE_MEMBERSHIP_STATUS AS b ON b.ID_STATUS = a.[STATUS]
                WHERE
                (
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.PERIODE_IN_MONTH_INT LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND b.DESC_CHAR LIKE '%".$search."%')
                ) AND
                a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 1
                ORDER BY ".$order." ".$dir."
                OFFSET ".$start." ROWS
                FETCH NEXT ".$limit." ROWS ONLY"));
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                // $dataPriceMembershipCurrent = DB::table('MD_PRICE_MEMBERSHIP')
                //     ->where('PROJECT_NO_CHAR', $project_no)
                //     ->where('MD_PERIODE_MEMBERSHIP_ID_INT', $post->MD_PERIODE_MEMBERSHIP_ID_INT)
                //     ->where('STATUS', 1)
                //     ->count();

                $nestedData['DESC_CHAR'] = $post->DESC_CHAR;
                $nestedData['PERIODE_IN_MONTH_INT'] = $post->PERIODE_IN_MONTH_INT;
                $nestedData['DESC_CHAR_STATUS'] = $post->DESC_CHAR_STATUS;

                // if($dataPriceMembershipCurrent > 0) {
                //     $nestedData['EDIT'] = "<a href='javascript:void(0)' title='Edit' class='btn bg-gradient-default btn-sm'>Edit</a>";
                //     $nestedData['HAPUS'] = "<a href='javascript:void(0)' title='Delete' class='btn bg-gradient-default btn-sm'>Delete</a>";
                // }
                // else {
                    $nestedData['EDIT'] = "<a href='javascript:void(0)' title='Edit' onclick='showModalEdit($post->MD_PERIODE_MEMBERSHIP_ID_INT)' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                    $nestedData['HAPUS'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData($post->MD_PERIODE_MEMBERSHIP_ID_INT)' class='btn bg-gradient-danger btn-sm'>Delete</a>";
                // }

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return json_encode($json_data);
    }

    public function getPeriodeMembership($id) {
        $project_no = session('current_project');

        $dataPeriodeMembership = DB::table('MD_PERIODE_MEMBERSHIP')
            ->where('MD_PERIODE_MEMBERSHIP_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)->first();
        
        return response()->json([
            'dataPeriodeMembership' => $dataPeriodeMembership
        ]);
    }

    public function savePeriodeMembership(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PERIODE_MEMBERSHIP')->insert([
                'DESC_CHAR' => $request->TXT_DESC,
                'PERIODE_IN_MONTH_INT' => $request->TXT_PERIODE_MONTH,
                'STATUS' => 1,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('periodeMembership');
        }

        session()->flash('success', "Save Periode Membership Successfully!");
        return redirect()->route('periodeMembership');
    }

    public function editPeriodeMembership(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PERIODE_MEMBERSHIP')->where('MD_PERIODE_MEMBERSHIP_ID_INT', $request->TXT_ID_EDIT)->update([
                'DESC_CHAR' => $request->TXT_DESC_EDIT,
                'PERIODE_IN_MONTH_INT' => $request->TXT_PERIODE_MONTH_EDIT,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('periodeMembership');
        }

        session()->flash('success', "Edit Periode Membership Successfully!");
        return redirect()->route('periodeMembership');
    }

    public function deletePeriodeMembership($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PERIODE_MEMBERSHIP')->where('MD_PERIODE_MEMBERSHIP_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('periodeMembership');
        }

        session()->flash('success', "Delete Periode Membership Successfully!");
        return redirect()->route('periodeMembership');
    }
}
