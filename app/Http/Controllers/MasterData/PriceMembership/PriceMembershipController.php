<?php

namespace App\Http\Controllers\MasterData\PriceMembership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class PriceMembershipController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.PriceMembership.priceMembership')
            ->with('project_no', $project_no);
    }

    public function listTblPriceMembership(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'b.DESC_CHAR',
            1 =>'c.DESC_CHAR', 
            2 =>'d.DESC_CHAR',
            3 =>'a.HARGA_FLOAT',
            4 =>'e.DESC_CHAR'
        );

        $totalData = \DB::table('MD_PRICE_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_MEMBERSHIP, c.DESC_CHAR AS DESC_CHAR_TYPE_MEMBERSHIP,
                d.DESC_CHAR AS DESC_CHAR_PERIODE_MEMBERSHIP, e.DESC_CHAR AS DESC_CHAR_STATUS
                FROM MD_PRICE_MEMBERSHIP AS a
                LEFT JOIN MD_GROUP_MEMBERSHIP AS b ON b.MD_GROUP_MEMBERSHIP_ID_INT = a.MD_GROUP_MEMBERSHIP_ID_INT
                LEFT JOIN MD_GROUP_TYPE_MEMBERSHIP AS c ON c.MD_GROUP_TYPE_MEMBERSHIP_ID_INT = a.MD_GROUP_TYPE_MEMBERSHIP_ID_INT
                LEFT JOIN MD_PERIODE_MEMBERSHIP AS d ON d.MD_PERIODE_MEMBERSHIP_ID_INT = a.MD_PERIODE_MEMBERSHIP_ID_INT
                LEFT JOIN MD_PERIODE_MEMBERSHIP_STATUS AS e ON e.ID_STATUS = a.[STATUS]
                WHERE a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 1
                ORDER BY ".$order." ".$dir."
                OFFSET ".$start." ROWS
                FETCH NEXT ".$limit." ROWS ONLY");
        }
        else {
            $search = $request->input('search.value');

            $posts = DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_MEMBERSHIP, c.DESC_CHAR AS DESC_CHAR_TYPE_MEMBERSHIP,
                d.DESC_CHAR AS DESC_CHAR_PERIODE_MEMBERSHIP, e.DESC_CHAR AS DESC_CHAR_STATUS
                FROM MD_PRICE_MEMBERSHIP AS a
                LEFT JOIN MD_GROUP_MEMBERSHIP AS b ON b.MD_GROUP_MEMBERSHIP_ID_INT = a.MD_GROUP_MEMBERSHIP_ID_INT
                LEFT JOIN MD_GROUP_TYPE_MEMBERSHIP AS c ON c.MD_GROUP_TYPE_MEMBERSHIP_ID_INT = a.MD_GROUP_TYPE_MEMBERSHIP_ID_INT
                LEFT JOIN MD_PERIODE_MEMBERSHIP AS d ON d.MD_PERIODE_MEMBERSHIP_ID_INT = a.MD_PERIODE_MEMBERSHIP_ID_INT
                LEFT JOIN MD_PERIODE_MEMBERSHIP_STATUS AS e ON e.ID_STATUS = a.[STATUS]
                WHERE
                (
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND b.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND c.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND d.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND e.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.HARGA_FLOAT LIKE '%".$search."%')
                ) AND
                a.PROJECT_NO_CHAR = '".$project_no."' AND a.[STATUS] = 1
                ORDER BY ".$order." ".$dir."
                OFFSET ".$start." ROWS
                FETCH NEXT ".$limit." ROWS ONLY");

            $totalFiltered = count(DB::select("SELECT a.*, b.DESC_CHAR AS DESC_CHAR_MEMBERSHIP, c.DESC_CHAR AS DESC_CHAR_TYPE_MEMBERSHIP,
                d.DESC_CHAR AS DESC_CHAR_PERIODE_MEMBERSHIP, e.DESC_CHAR AS DESC_CHAR_STATUS
                FROM MD_PRICE_MEMBERSHIP AS a
                LEFT JOIN MD_GROUP_MEMBERSHIP AS b ON b.MD_GROUP_MEMBERSHIP_ID_INT = a.MD_GROUP_MEMBERSHIP_ID_INT
                LEFT JOIN MD_GROUP_TYPE_MEMBERSHIP AS c ON c.MD_GROUP_TYPE_MEMBERSHIP_ID_INT = a.MD_GROUP_TYPE_MEMBERSHIP_ID_INT
                LEFT JOIN MD_PERIODE_MEMBERSHIP AS d ON d.MD_PERIODE_MEMBERSHIP_ID_INT = a.MD_PERIODE_MEMBERSHIP_ID_INT
                LEFT JOIN MD_PERIODE_MEMBERSHIP_STATUS AS e ON e.ID_STATUS = a.[STATUS]
                WHERE
                (
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND b.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND c.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND d.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND e.DESC_CHAR LIKE '%".$search."%') OR
                    (a.PROJECT_NO_CHAR = '".$project_no."' AND a.HARGA_FLOAT LIKE '%".$search."%')
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
                $edit =  route('edit_view_price_membership', base64_encode($post->MD_PRICE_MEMBERSHIP_ID_INT));

                $nestedData['DESC_CHAR_MEMBERSHIP'] = $post->DESC_CHAR_MEMBERSHIP;
                $nestedData['DESC_CHAR_TYPE_MEMBERSHIP'] = $post->DESC_CHAR_TYPE_MEMBERSHIP;
                $nestedData['DESC_CHAR_PERIODE_MEMBERSHIP'] = $post->DESC_CHAR_PERIODE_MEMBERSHIP;
                $nestedData['DESC_CHAR_STATUS'] = $post->DESC_CHAR_STATUS;
                $nestedData['HARGA_FLOAT'] = number_format($post->HARGA_FLOAT, 0, ',', '.');

                $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                $nestedData['HAPUS'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->MD_PRICE_MEMBERSHIP_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewPriceMembership() {
        $project_no = session('current_project');

        $ddlGroupMembership = DB::table('MD_GROUP_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->where('STATUS', 1)->get();
        $ddlGroupTypeMembership = DB::table('MD_GROUP_TYPE_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->where('STATUS', 1)->get();
        $ddlPeriodeMembership = DB::table('MD_PERIODE_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->where('STATUS', 1)->get();

        return view('MasterData.PriceMembership.add_new_price_membership')
            ->with('project_no', $project_no)
            ->with('ddlGroupMembership', $ddlGroupMembership)
            ->with('ddlGroupTypeMembership', $ddlGroupTypeMembership)
            ->with('ddlPeriodeMembership', $ddlPeriodeMembership);
    }

    public function savePriceMembership(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            $dataPriceMembershipCurrent = DB::table('MD_PRICE_MEMBERSHIP')
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('MD_GROUP_MEMBERSHIP_ID_INT', $request->DDL_GROUP)
                ->where('MD_GROUP_TYPE_MEMBERSHIP_ID_INT', $request->DDL_GROUP_TYPE)
                ->where('MD_PERIODE_MEMBERSHIP_ID_INT', $request->DDL_PERIODE)
                ->where('STATUS', 1)
                ->count();

            if($dataPriceMembershipCurrent > 0) {
                DB::rollback();
                session()->flash('error', 'Price Membership Already Exists!');
                return redirect()->route('priceMembership');
            }
            else {
                DB::table('MD_PRICE_MEMBERSHIP')->insert([
                    'MD_GROUP_MEMBERSHIP_ID_INT' => $request->DDL_GROUP,
                    'MD_GROUP_TYPE_MEMBERSHIP_ID_INT' => $request->DDL_GROUP_TYPE,
                    'MD_PERIODE_MEMBERSHIP_ID_INT' => $request->DDL_PERIODE,
                    'HARGA_FLOAT' => $request->TXT_PRICE,
                    'STATUS' => 1,
                    'PROJECT_NO_CHAR' => $project_no,
                    'created_by' => $userName,
                    'created_at' => $dateNow
                ]);
            }            

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('priceMembership');
        }

        session()->flash('success', "Save Price Membership Successfully!");
        return redirect()->route('priceMembership');
    }

    public function editViewPriceMembership($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlGroupMembership = DB::table('MD_GROUP_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->where('STATUS', 1)->get();
        $ddlGroupTypeMembership = DB::table('MD_GROUP_TYPE_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->where('STATUS', 1)->get();
        $ddlPeriodeMembership = DB::table('MD_PERIODE_MEMBERSHIP')->where('PROJECT_NO_CHAR', $project_no)->where('STATUS', 1)->get();

        $dataPriceMembership = DB::table('MD_PRICE_MEMBERSHIP')->where('MD_PRICE_MEMBERSHIP_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.PriceMembership.edit_view_price_membership')
            ->with('project_no', $project_no)
            ->with('ddlGroupMembership', $ddlGroupMembership)
            ->with('ddlGroupTypeMembership', $ddlGroupTypeMembership)
            ->with('ddlPeriodeMembership', $ddlPeriodeMembership)
            ->with('dataPriceMembership', $dataPriceMembership);
    }

    public function editPriceMembership(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            $dataPriceMembershipCurrent = DB::table('MD_PRICE_MEMBERSHIP')
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('MD_GROUP_MEMBERSHIP_ID_INT', $request->DDL_GROUP)
                ->where('MD_GROUP_TYPE_MEMBERSHIP_ID_INT', $request->DDL_GROUP_TYPE)
                ->where('MD_PERIODE_MEMBERSHIP_ID_INT', $request->DDL_PERIODE)
                ->where('STATUS', 1)
                ->count();

            if($dataPriceMembershipCurrent > 0) {
                DB::rollback();
                session()->flash('error', 'Price Membership Already Exists!');
                return redirect()->route('priceMembership');
            }
            else {
                DB::table('MD_PRICE_MEMBERSHIP')->where('MD_PRICE_MEMBERSHIP_ID_INT', $request->TXT_ID)->update([
                    'MD_GROUP_MEMBERSHIP_ID_INT' => $request->DDL_GROUP,
                    'MD_GROUP_TYPE_MEMBERSHIP_ID_INT' => $request->DDL_GROUP_TYPE,
                    'MD_PERIODE_MEMBERSHIP_ID_INT' => $request->DDL_PERIODE,
                    'HARGA_FLOAT' => $request->TXT_PRICE,
                    'updated_by' => $userName,
                    'updated_at' => $dateNow
                ]);
            }

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('priceMembership');
        }

        session()->flash('success', "Edit Price Membership Successfully!");
        return redirect()->route('priceMembership');
    }

    public function deletePriceMembership($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRICE_MEMBERSHIP')->where('MD_PRICE_MEMBERSHIP_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('priceMembership');
        }

        session()->flash('success', "Delete Price Membership Successfully!");
        return redirect()->route('priceMembership');
    }
}
