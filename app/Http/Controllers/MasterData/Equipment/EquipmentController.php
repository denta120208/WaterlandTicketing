<?php

namespace App\Http\Controllers\MasterData\Equipment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class EquipmentController extends Controller
{

    public function index() {
        $project_no = session('current_project');

        return view('MasterData.Equipment.equipment')
            ->with('project_no', $project_no);
    }

    public function listTblEquipment(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'MD_EQUIPMENT_CATEGORY_DESC_CHAR',
            1 =>'EQUIPMENT_ASSET_NUMBER',
            2 =>'HARGA_SATUAN_FLOAT',
            3 =>'PB1_PERCENT_INT',
            4 =>'PPH_PERCENT_INT',
            5 =>'DESC_CHAR'
        );

        $totalData = \DB::table('MD_EQUIPMENT')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('MD_EQUIPMENT')
                ->selectRaw('MD_EQUIPMENT.*, MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_DESC_CHAR, MD_EQUIPMENT_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                ->join('MD_EQUIPMENT_CATEGORY', 'MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_ID_INT', '=', 'MD_EQUIPMENT.MD_EQUIPMENT_CATEGORY_ID_INT')
                ->join('MD_EQUIPMENT_STATUS', 'MD_EQUIPMENT_STATUS.ID_STATUS', '=', 'MD_EQUIPMENT.STATUS')
                ->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                ->where('MD_EQUIPMENT.STATUS', "1")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('MD_EQUIPMENT')
                            ->selectRaw('MD_EQUIPMENT.*, MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_DESC_CHAR, MD_EQUIPMENT_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_EQUIPMENT_CATEGORY', 'MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_ID_INT', '=', 'MD_EQUIPMENT.MD_EQUIPMENT_CATEGORY_ID_INT')
                            ->join('MD_EQUIPMENT_STATUS', 'MD_EQUIPMENT_STATUS.ID_STATUS', '=', 'MD_EQUIPMENT.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_EQUIPMENT_CATEGORY_DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('EQUIPMENT_ASSET_NUMBER', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('HARGA_SATUAN_FLOAT', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('PB1_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('PPH_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('MD_EQUIPMENT')
                            ->selectRaw('MD_EQUIPMENT.*, MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_DESC_CHAR, MD_EQUIPMENT_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_EQUIPMENT_CATEGORY', 'MD_EQUIPMENT_CATEGORY.MD_EQUIPMENT_CATEGORY_ID_INT', '=', 'MD_EQUIPMENT.MD_EQUIPMENT_CATEGORY_ID_INT')
                            ->join('MD_EQUIPMENT_STATUS', 'MD_EQUIPMENT_STATUS.ID_STATUS', '=', 'MD_EQUIPMENT.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_EQUIPMENT_CATEGORY_DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('EQUIPMENT_ASSET_NUMBER', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('HARGA_SATUAN_FLOAT', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('PB1_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('PPH_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_EQUIPMENT.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_EQUIPMENT.STATUS', "1");
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $edit =  route('edit_view_equipment', base64_encode($post->MD_EQUIPMENT_ID_INT));

                $nestedData['MD_EQUIPMENT_CATEGORY_DESC_CHAR'] = $post->MD_EQUIPMENT_CATEGORY_DESC_CHAR;
                $nestedData['EQUIPMENT_ASSET_NUMBER'] = $post->EQUIPMENT_ASSET_NUMBER;
                $nestedData['HARGA_SATUAN_FLOAT'] = number_format($post->HARGA_SATUAN_FLOAT, 0, ',', '.');
                $nestedData['PB1_PERCENT_INT'] = (float) $post->PB1_PERCENT_INT . "%";
                $nestedData['PPH_PERCENT_INT'] = (float) $post->PPH_PERCENT_INT . "%";
                $nestedData['STATUS_DESC_CHAR'] = $post->DESC_CHAR_STATUS;

                $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->MD_EQUIPMENT_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewEquipment() {
        $project_no = session('current_project');
        $ddlEquipment = DB::table('MD_EQUIPMENT_CATEGORY')->where('STATUS', 1)->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('MasterData.Equipment.add_new_equipment')
            ->with('project_no', $project_no)
            ->with('ddlEquipment', $ddlEquipment);
    }

    public function saveEquipment(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_EQUIPMENT')->insert([
                'MD_EQUIPMENT_CATEGORY_ID_INT' => $request->DDL_GROUP,
                'EQUIPMENT_ASSET_NUMBER' => $request->TXT_ASSET_NUMBER,
                'HARGA_SATUAN_FLOAT' => $request->TXT_PRICE,
                'PB1_PERCENT_INT' => $request->TXT_PB1,
                'PPH_PERCENT_INT' => $request->TXT_PPH,
                'PROJECT_NO_CHAR' => $project_no,
                'STATUS' => 1,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('equipment');
        }

        session()->flash('success', "Save Equipment Successfully!");
        return redirect()->route('equipment');
    }

    public function editViewEquipment($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlEquipment = DB::table('MD_EQUIPMENT_CATEGORY')->where('STATUS', 1)->where('PROJECT_NO_CHAR', $project_no)->get();
        $dataEquipment = DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.Equipment.edit_view_equipment')
            ->with('project_no', $project_no)
            ->with('ddlEquipment', $ddlEquipment)
            ->with('dataEquipment', $dataEquipment);
    }

    public function editEquipment(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $request->TXT_ID)->update([
                'MD_EQUIPMENT_CATEGORY_ID_INT' => $request->DDL_GROUP,
                'EQUIPMENT_ASSET_NUMBER' => $request->TXT_ASSET_NUMBER,
                'HARGA_SATUAN_FLOAT' => $request->TXT_PRICE,
                'PB1_PERCENT_INT' => $request->TXT_PB1,
                'PPH_PERCENT_INT' => $request->TXT_PPH,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('equipment');
        }

        session()->flash('success', "Edit Equipment Successfully!");
        return redirect()->route('equipment');
    }

    public function deleteEquipment($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_EQUIPMENT')->where('MD_EQUIPMENT_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('equipment');
        }

        session()->flash('success', "Delete Equipment Successfully!");
        return redirect()->route('equipment');
    }
}
