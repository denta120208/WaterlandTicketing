<?php

namespace App\Http\Controllers\Sales\RevenueSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class RevenueSettingsController extends Controller
{

    public function index() {
        $project_no = session('current_project');

        return view('Sales.RevenueSettings.revenue_settings')
            ->with('project_no', $project_no);
    }

    public function listTblRevenueSettings(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table("MD_PROJECT")->where("PROJECT_NO_CHAR", $project_no)->first();

        $arrAllProject = [];
        if($dataProject->PROJECT_CODE == "HO") {
            $dataAllProjects = DB::table("MD_PROJECT")->where("PROJECT_ACTIVE_CHAR", 1)->whereNotIn("PROJECT_CODE", ["HO", "WTRIAL"])->orderBy("PROJECT_NAME", "ASC")->get();
            foreach($dataAllProjects as $dataAllProject) {
                array_push($arrAllProject, $dataAllProject->PROJECT_NO_CHAR);
            }
        }
        else {
            array_push($arrAllProject, $project_no);
        }

        $columns = array(
            0 =>'MD_CATEGORY_REVENUE_NAME',
            1 =>'MD_CATEGORY_REVENUE_NAME',
            2 =>'TRANS_REVENUE_DATE',
            3 =>'ACTUAL_AMT',
            4 =>'BUDGET_AMT',
            5 =>'PROJECT_NAME'
        );

        $totalData = \DB::table('TRANS_REVENUE')->whereIn('PROJECT_NO_CHAR', $arrAllProject)->where("IS_DELETE", 0)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('TRANS_REVENUE')
                ->selectRaw('TRANS_REVENUE.*, MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_NAME, MD_PROJECT.PROJECT_NAME')
                ->join('MD_CATEGORY_REVENUE', 'MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_ID_INT', '=', 'TRANS_REVENUE.MD_CATEGORY_REVENUE_ID_INT')
                ->join('MD_PROJECT', 'MD_PROJECT.PROJECT_NO_CHAR', '=', 'TRANS_REVENUE.PROJECT_NO_CHAR')
                ->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                ->where('TRANS_REVENUE.IS_DELETE', "0")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('TRANS_REVENUE')
                            ->selectRaw('TRANS_REVENUE.*, MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_NAME, MD_PROJECT.PROJECT_NAME')
                            ->join('MD_CATEGORY_REVENUE', 'MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_ID_INT', '=', 'TRANS_REVENUE.MD_CATEGORY_REVENUE_ID_INT')
                            ->join('MD_PROJECT', 'MD_PROJECT.PROJECT_NO_CHAR', '=', 'TRANS_REVENUE.PROJECT_NO_CHAR')
                            ->where(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_NAME', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('TRANS_REVENUE.TRANS_REVENUE_DATE', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('TRANS_REVENUE.ACTUAL_AMT', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('TRANS_REVENUE.BUDGET_AMT', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('MD_PROJECT.PROJECT_NAME', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('TRANS_REVENUE')
                            ->selectRaw('TRANS_REVENUE.*, MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_NAME, MD_PROJECT.PROJECT_NAME')
                            ->join('MD_CATEGORY_REVENUE', 'MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_ID_INT', '=', 'TRANS_REVENUE.MD_CATEGORY_REVENUE_ID_INT')
                            ->join('MD_PROJECT', 'MD_PROJECT.PROJECT_NO_CHAR', '=', 'TRANS_REVENUE.PROJECT_NO_CHAR')
                            ->where(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('MD_CATEGORY_REVENUE.MD_CATEGORY_REVENUE_NAME', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('TRANS_REVENUE.TRANS_REVENUE_DATE', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('TRANS_REVENUE.ACTUAL_AMT', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('TRANS_REVENUE.BUDGET_AMT', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->orWhere(function ($query) use ($arrAllProject, $search) {
                                $query->whereIn('TRANS_REVENUE.PROJECT_NO_CHAR', $arrAllProject)
                                ->where('MD_PROJECT.PROJECT_NAME', 'LIKE', "%{$search}%")
                                ->where('TRANS_REVENUE.IS_DELETE', "0");
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $nestedData['MD_CATEGORY_REVENUE_NAME'] = $post->MD_CATEGORY_REVENUE_NAME;
                $nestedData['TRANS_REVENUE_DATE'] = date("d-m-Y", strtotime($post->TRANS_REVENUE_DATE));
                $nestedData['ACTUAL_AMT'] = number_format($post->ACTUAL_AMT, 0, ',', '.');
                $nestedData['BUDGET_AMT'] = number_format($post->BUDGET_AMT, 0, ',', '.');
                $nestedData['PROJECT_NAME'] = $post->PROJECT_NAME;

                $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->TRANS_REVENUE_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewRevenueSettings() {
        $project_no = session('current_project');
        $dataProject = DB::table("MD_PROJECT")->where("PROJECT_NO_CHAR", $project_no)->first();

        $yesterdayDate = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $yesterdayDate = $yesterdayDate->subDay();
        $yesterdayDate = $yesterdayDate->toDateString();

        if($dataProject->PROJECT_CODE == "HO") {
            $ddlProject = DB::table("MD_PROJECT")->where("PROJECT_ACTIVE_CHAR", 1)->whereNotIn("PROJECT_CODE", ["HO", "WTRIAL"])->orderBy("PROJECT_NAME", "ASC")->get();
        }
        else {
            $ddlProject = DB::table("MD_PROJECT")->where("PROJECT_NO_CHAR", $project_no)->get();
        }

        return view('Sales.RevenueSettings.add_new_revenue_settings')
            ->with('project_no', $project_no)
            ->with('dataProject', $dataProject)
            ->with('yesterdayDate', $yesterdayDate)
            ->with('ddlProject', $ddlProject);
    }

    public function saveRevenueSettings(Request $request) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            $countTransRevenue = DB::table('TRANS_REVENUE')
                ->where("MD_CATEGORY_REVENUE_ID_INT", $request->MD_CATEGORY_REVENUE_ID_INT)
                ->where("TRANS_REVENUE_DATE", $request->TRANS_REVENUE_DATE)
                ->where("PROJECT_NO_CHAR", $request->PROJECT_NO_CHAR)
                ->where("IS_DELETE", 0)
                ->count();

            if($countTransRevenue > 0) {
                session()->flash('error', 'Revenue Settings Data Already Exists!');
                return redirect()->route('revenue_settings');
            }

            $TRANS_REVENUE_ID_INT = DB::table('TRANS_REVENUE')->insertGetId([
                'MD_CATEGORY_REVENUE_ID_INT' => $request->MD_CATEGORY_REVENUE_ID_INT,
                'TRANS_REVENUE_DATE' => $request->TRANS_REVENUE_DATE,
                'ACTUAL_AMT' => $request->ACTUAL_AMT,
                'BUDGET_AMT' => $request->BUDGET_AMT,
                'PROJECT_NO_CHAR' => $request->PROJECT_NO_CHAR,
                'IS_DELETE' => 0,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            $dataCategoryRevenue = \DB::table("MD_CATEGORY_REVENUE")->where("MD_CATEGORY_REVENUE_ID_INT", $request->MD_CATEGORY_REVENUE_ID_INT)->first();

            if($dataCategoryRevenue->MD_CATEGORY_REVENUE_SOURCE_ID_INT == "1") {
                $dataRevenues = \DB::select("exec sp_get_rev_settings 'DETAILS','".$dataCategoryRevenue->MD_CATEGORY_REVENUE_NAME."','".$request->PROJECT_NO_CHAR."','".$request->TRANS_REVENUE_DATE."'");
            }
            else {
                $dataRevenues = [];
            }

            foreach($dataRevenues as $dataRevenue) {
                DB::table('TRANS_REVENUE_DETAILS')->insert([
                    'TRANS_REVENUE_ID_INT' => $TRANS_REVENUE_ID_INT,
                    'REV_DESC' => $dataRevenue->REV_DESC,
                    'QTY_REV' => $dataRevenue->QTY_REV,
                    'REV_BEFORE_PB1' => $dataRevenue->REV_BEFORE_PB1,
                    'REV_PB1' => $dataRevenue->REV_PB1,
                    'REV' => $dataRevenue->REV,
                    'PROJECT_NO_CHAR' => $request->PROJECT_NO_CHAR,
                    'created_by' => $userName,
                    'created_at' => $dateNow
                ]);
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('revenue_settings');
        }

        session()->flash('success', "Save Revenue Settings Successfully!");
        return redirect()->route('revenue_settings');
    }

    public function deleteRevenueSettings($id) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('TRANS_REVENUE')->where('TRANS_REVENUE_ID_INT', $id)->update([
                'IS_DELETE' => 1,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('revenue_settings');
        }

        session()->flash('success', "Delete Revenue Settings Successfully!");
        return redirect()->route('revenue_settings');
    }

    public function getCategoryRevenueByProject($id) {
        $project_no = $id;

        $dataCategoryRevenue = DB::table('MD_CATEGORY_REVENUE')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where("IS_DELETE", 0)
            ->get();
        
        return response()->json([
            'dataCategoryRevenue' => $dataCategoryRevenue
        ]);
    }

    public function getCategoryRevenueSource($id, $revDate) {
        $dataCategoryRevenue = DB::table('MD_CATEGORY_REVENUE')
            ->where('MD_CATEGORY_REVENUE_ID_INT', $id)
            ->first();

        $FIELD_ACTUAL = "ENABLED";
        $FIELD_ACTUAL_VALUE = 0;

        if($dataCategoryRevenue->MD_CATEGORY_REVENUE_SOURCE_ID_INT == "1") {
            $dataRevenue = \DB::select("exec sp_get_rev_settings 'SUMMARY','".$dataCategoryRevenue->MD_CATEGORY_REVENUE_NAME."','".$dataCategoryRevenue->PROJECT_NO_CHAR."','".$revDate."'");
            // $REV = $dataRevenue[0]->REV ?? 0;
            $REV = $dataRevenue[0]->REV_BEFORE_PB1 ?? 0;
            $REV = (float) $REV;
            // $REV = round($REV, 0);

            $FIELD_ACTUAL = "DISABLED";
            $FIELD_ACTUAL_VALUE = $REV;
        }
        
        return response()->json([
            'FIELD_ACTUAL' => $FIELD_ACTUAL,
            'FIELD_ACTUAL_VALUE' => $FIELD_ACTUAL_VALUE
        ]);
    }
}
