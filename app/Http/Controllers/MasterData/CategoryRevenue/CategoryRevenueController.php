<?php

namespace App\Http\Controllers\MasterData\CategoryRevenue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class CategoryRevenueController extends Controller
{

    public function index() {
        $project_no = session('current_project');

        return view('MasterData.CategoryRevenue.category_revenue')
            ->with('project_no', $project_no);
    }

    public function listTblCategoryRevenue(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'MD_CATEGORY_REVENUE_NAME',
            1 =>'MD_CATEGORY_REVENUE_NAME',
            2 =>'SOURCE_NAME'
        );

        $totalData = \DB::table('MD_CATEGORY_REVENUE')->where('PROJECT_NO_CHAR', $project_no)->where("IS_DELETE", 0)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('MD_CATEGORY_REVENUE')
                ->selectRaw('MD_CATEGORY_REVENUE.*, MD_CATEGORY_REVENUE_SOURCE.SOURCE_NAME')
                ->join('MD_CATEGORY_REVENUE_SOURCE', 'MD_CATEGORY_REVENUE_SOURCE.MD_CATEGORY_REVENUE_SOURCE_ID_INT', '=', 'MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_SOURCE_ID_INT')
                ->where('MD_CATEGORY_REVENUE.PROJECT_NO_CHAR', $project_no)
                ->where('MD_CATEGORY_REVENUE.IS_DELETE', "0")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('MD_CATEGORY_REVENUE')
                            ->selectRaw('MD_CATEGORY_REVENUE.*, MD_CATEGORY_REVENUE_SOURCE.SOURCE_NAME')
                            ->join('MD_CATEGORY_REVENUE_SOURCE', 'MD_CATEGORY_REVENUE_SOURCE.MD_CATEGORY_REVENUE_SOURCE_ID_INT', '=', 'MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_SOURCE_ID_INT')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_CATEGORY_REVENUE.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_NAME', 'LIKE', "%{$search}%")
                                ->where('MD_CATEGORY_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_CATEGORY_REVENUE.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_CATEGORY_REVENUE_SOURCE.SOURCE_NAME', 'LIKE', "%{$search}%")
                                ->where('MD_CATEGORY_REVENUE.IS_DELETE', "0");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('MD_CATEGORY_REVENUE')
                            ->selectRaw('MD_CATEGORY_REVENUE.*, MD_CATEGORY_REVENUE_SOURCE.SOURCE_NAME')
                            ->join('MD_CATEGORY_REVENUE_SOURCE', 'MD_CATEGORY_REVENUE_SOURCE.MD_CATEGORY_REVENUE_SOURCE_ID_INT', '=', 'MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_SOURCE_ID_INT')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_CATEGORY_REVENUE.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_NAME', 'LIKE', "%{$search}%")
                                ->where('MD_CATEGORY_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_CATEGORY_REVENUE.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_CATEGORY_REVENUE_SOURCE.SOURCE_NAME', 'LIKE', "%{$search}%")
                                ->where('MD_CATEGORY_REVENUE.IS_DELETE', "0");
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $edit =  route('edit_view_category_revenue', base64_encode($post->MD_CATEGORY_REVENUE_ID_INT));

                $nestedData['MD_CATEGORY_REVENUE_NAME'] = $post->MD_CATEGORY_REVENUE_NAME;
                $nestedData['SOURCE_NAME'] = $post->SOURCE_NAME;

                $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->MD_CATEGORY_REVENUE_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewCategoryRevenue() {
        $project_no = session('current_project');
        $ddlCategoryRevenueSource = DB::table('MD_CATEGORY_REVENUE_SOURCE')->where('IS_DELETE', 0)->orderBy("MD_CATEGORY_REVENUE_SOURCE_ID_INT", "ASC")->get();

        return view('MasterData.CategoryRevenue.add_new_category_revenue')
            ->with('project_no', $project_no)
            ->with('ddlCategoryRevenueSource', $ddlCategoryRevenueSource);
    }

    public function saveCategoryRevenue(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_CATEGORY_REVENUE')->insert([
                'MD_CATEGORY_REVENUE_NAME' => $request->MD_CATEGORY_REVENUE_NAME,
                'MD_CATEGORY_REVENUE_SOURCE_ID_INT' => $request->MD_CATEGORY_REVENUE_SOURCE_ID_INT,
                'IS_DELETE' => 0,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('category_revenue');
        }

        session()->flash('success', "Save Category Revenue Successfully!");
        return redirect()->route('category_revenue');
    }

    public function editViewCategoryRevenue($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlCategoryRevenueSource = DB::table('MD_CATEGORY_REVENUE_SOURCE')->where('IS_DELETE', 0)->orderBy("MD_CATEGORY_REVENUE_SOURCE_ID_INT", "ASC")->get();
        $dataCategoryRevenue = DB::table('MD_CATEGORY_REVENUE')->where('MD_CATEGORY_REVENUE_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.CategoryRevenue.edit_view_category_revenue')
            ->with('project_no', $project_no)
            ->with('ddlCategoryRevenueSource', $ddlCategoryRevenueSource)
            ->with('dataCategoryRevenue', $dataCategoryRevenue);
    }

    public function editCategoryRevenue(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_CATEGORY_REVENUE')->where('MD_CATEGORY_REVENUE_ID_INT', $request->MD_CATEGORY_REVENUE_ID_INT)->update([
                'MD_CATEGORY_REVENUE_NAME' => $request->MD_CATEGORY_REVENUE_NAME,
                'MD_CATEGORY_REVENUE_SOURCE_ID_INT' => $request->MD_CATEGORY_REVENUE_SOURCE_ID_INT,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('category_revenue');
        }

        session()->flash('success', "Edit Category Revenue Successfully!");
        return redirect()->route('category_revenue');
    }

    public function deleteCategoryRevenue($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_CATEGORY_REVENUE')->where('MD_CATEGORY_REVENUE_ID_INT', $id)->update([
                'IS_DELETE' => 1,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('category_revenue');
        }

        session()->flash('success', "Delete Category Revenue Successfully!");
        return redirect()->route('category_revenue');
    }
}
