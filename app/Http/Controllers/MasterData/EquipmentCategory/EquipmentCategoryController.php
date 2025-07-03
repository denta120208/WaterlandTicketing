<?php

namespace App\Http\Controllers\MasterData\EquipmentCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class EquipmentCategoryController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.EquipmentCategory.equipmentCategory')
            ->with('project_no', $project_no);
    }

    public function listTblEquipmentCategory(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'MD_EQUIPMENT_CATEGORY_DESC_CHAR',
            1 =>'DESC_CHAR'
        );

        $totalData = \DB::table('MD_EQUIPMENT_CATEGORY')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('MD_EQUIPMENT_CATEGORY')
                ->selectRaw('MD_EQUIPMENT_CATEGORY.*, MD_EQUIPMENT_CATEGORY_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                ->join('MD_EQUIPMENT_CATEGORY_STATUS', 'MD_EQUIPMENT_CATEGORY_STATUS.ID_STATUS', '=', 'MD_EQUIPMENT_CATEGORY.STATUS')
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('MD_EQUIPMENT_CATEGORY.STATUS', "1")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('MD_EQUIPMENT_CATEGORY')
                            ->selectRaw('MD_EQUIPMENT_CATEGORY.*, MD_EQUIPMENT_CATEGORY_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_EQUIPMENT_CATEGORY_STATUS', 'MD_EQUIPMENT_CATEGORY_STATUS.ID_STATUS', '=', 'MD_EQUIPMENT_CATEGORY.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT_CATEGORY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_EQUIPMENT_CATEGORY_DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT_CATEGORY.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT_CATEGORY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_EQUIPMENT_CATEGORY_STATUS.DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT_CATEGORY.STATUS', "1");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('MD_EQUIPMENT_CATEGORY')
                            ->selectRaw('MD_EQUIPMENT_CATEGORY.*, MD_EQUIPMENT_CATEGORY_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_EQUIPMENT_CATEGORY_STATUS', 'MD_EQUIPMENT_CATEGORY_STATUS.ID_STATUS', '=', 'MD_EQUIPMENT_CATEGORY.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT_CATEGORY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_EQUIPMENT_CATEGORY_DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT_CATEGORY.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT_CATEGORY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_EQUIPMENT_CATEGORY_STATUS.DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT_CATEGORY.STATUS', "1");
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $edit =  route('edit_view_equipment_category', base64_encode($post->MD_EQUIPMENT_CATEGORY_ID_INT));

                $nestedData['MD_EQUIPMENT_CATEGORY_DESC_CHAR'] = $post->MD_EQUIPMENT_CATEGORY_DESC_CHAR;
                $nestedData['STATUS_DESC_CHAR'] = $post->DESC_CHAR_STATUS;

                $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->MD_EQUIPMENT_CATEGORY_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewEquipmentCategory() {
        $project_no = session('current_project');

        return view('MasterData.EquipmentCategory.add_new_equipment_category')
            ->with('project_no', $project_no);
    }

    public function saveEquipmentCategory(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_EQUIPMENT_CATEGORY')->insert([
                'MD_EQUIPMENT_CATEGORY_DESC_CHAR' => $request->TXT_DESC,
                'STATUS' => 1,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('equipmentCategory');
        }

        session()->flash('success', "Save Equipment Category Successfully!");
        return redirect()->route('equipmentCategory');
    }

    public function editViewEquipmentCategory($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $dataEquipmentCategory = DB::table('MD_EQUIPMENT_CATEGORY')->where('MD_EQUIPMENT_CATEGORY_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.EquipmentCategory.edit_view_equipment_category')
            ->with('project_no', $project_no)
            ->with('dataEquipmentCategory', $dataEquipmentCategory);
    }

    public function editEquipmentCategory(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_EQUIPMENT_CATEGORY')->where('MD_EQUIPMENT_CATEGORY_ID_INT', $request->TXT_ID)->update([
                'MD_EQUIPMENT_CATEGORY_DESC_CHAR' => $request->TXT_DESC,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('equipmentCategory');
        }

        session()->flash('success', "Edit Equipment Category Successfully!");
        return redirect()->route('equipmentCategory');
    }

    public function deleteEquipmentCategory($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_EQUIPMENT_CATEGORY')->where('MD_EQUIPMENT_CATEGORY_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('equipmentCategory');
        }

        session()->flash('success', "Delete Equipment Category Successfully!");
        return redirect()->route('equipmentCategory');
    }
}
