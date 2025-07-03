<?php

namespace App\Http\Controllers\MasterData\GroupMembership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class GroupMembershipController extends Controller
{   
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.GroupMembership.groupMembership')
            ->with('project_no', $project_no);
    }

    public function listTblGroupMembership(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'a.DESC_CHAR',
            1 =>'QTY_INT', 
            2 =>'b.DESC_CHAR'
        );

        $totalData = \DB::table('MD_GROUP_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_STATUS FROM MD_GROUP_MEMBERSHIP AS a
                LEFT JOIN MD_GROUP_MEMBERSHIP_STATUS AS b ON b.ID_STATUS = a.[STATUS]
                WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 1
                ORDER BY ".$order." ".$dir."
                OFFSET ".$start." ROWS
                FETCH NEXT ".$limit." ROWS ONLY");
        }
        else {
            $search = $request->input('search.value');

            $posts = DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_STATUS
                FROM MD_GROUP_MEMBERSHIP AS a
                LEFT JOIN MD_GROUP_MEMBERSHIP_STATUS AS b ON b.ID_STATUS = a.[STATUS]
                WHERE
                (
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.QTY_INT LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND b.DESC_CHAR LIKE '%".$search."%')
                ) AND
                a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 1
                ORDER BY ".$order." ".$dir."
                OFFSET ".$start." ROWS
                FETCH NEXT ".$limit." ROWS ONLY");

            $totalFiltered = count(DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_STATUS
                FROM MD_GROUP_MEMBERSHIP AS a
                LEFT JOIN MD_GROUP_MEMBERSHIP_STATUS AS b ON b.ID_STATUS = a.[STATUS]
                WHERE
                (
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.QTY_INT LIKE '%".$search."%') OR
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
                //     ->where('MD_GROUP_MEMBERSHIP_ID_INT', $post->MD_GROUP_MEMBERSHIP_ID_INT)
                //     ->where('STATUS', 1)
                //     ->count();
                
                $nestedData['DESC_CHAR'] = $post->DESC_CHAR;
                $nestedData['QTY_INT'] = $post->QTY_INT;
                $nestedData['DESC_CHAR_STATUS'] = $post->DESC_CHAR_STATUS;

                // if($dataPriceMembershipCurrent > 0) {
                //     $nestedData['EDIT'] = "<a href='javascript:void(0)' title='Edit' class='btn bg-gradient-default btn-sm'>Edit</a>";
                //     $nestedData['HAPUS'] = "<a href='javascript:void(0)' title='Delete' class='btn bg-gradient-default btn-sm'>Delete</a>";
                // }
                // else {
                    $nestedData['EDIT'] = "<a href='javascript:void(0)' title='Edit' onclick='showModalEdit($post->MD_GROUP_MEMBERSHIP_ID_INT)' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                    $nestedData['HAPUS'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData($post->MD_GROUP_MEMBERSHIP_ID_INT)' class='btn bg-gradient-danger btn-sm'>Delete</a>";
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

    public function getGroupMembership($id) {
        $project_no = session('current_project');

        $dataGroupMembership = DB::table('MD_GROUP_MEMBERSHIP')
            ->where('MD_GROUP_MEMBERSHIP_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)->first();
        
        return response()->json([
            'dataGroupMembership' => $dataGroupMembership
        ]);
    }

    public function saveGroupMembership(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_GROUP_MEMBERSHIP')->insert([
                'DESC_CHAR' => $request->TXT_DESC,
                'QTY_INT' => $request->TXT_QTY,
                'STATUS' => 1,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('groupMembership');
        }

        session()->flash('success', "Save Group Membership Successfully!");
        return redirect()->route('groupMembership');
    }

    public function editGroupMembership(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_GROUP_MEMBERSHIP')->where('MD_GROUP_MEMBERSHIP_ID_INT', $request->TXT_ID_EDIT)->update([
                'DESC_CHAR' => $request->TXT_DESC_EDIT,
                'QTY_INT' => $request->TXT_QTY_EDIT,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('groupMembership');
        }

        session()->flash('success', "Edit Group Membership Successfully!");
        return redirect()->route('groupMembership');
    }

    public function deleteGroupMembership($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_GROUP_MEMBERSHIP')->where('MD_GROUP_MEMBERSHIP_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('groupMembership');
        }

        session()->flash('success', "Delete Group Membership Successfully!");
        return redirect()->route('groupMembership');
    }
}
