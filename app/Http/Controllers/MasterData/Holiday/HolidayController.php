<?php

namespace App\Http\Controllers\MasterData\Holiday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class HolidayController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.Holiday.holiday')
            ->with('project_no', $project_no);
    }

    public function listTblHoliday(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'HOLIDAY_NAME',
            1 =>'HOLIDAY_DATE',
            2 =>'DESC_CHAR',
            3 =>'created_by',
            4 =>'updated_by'
        );

        $totalData = \DB::table('MD_HOLIDAY')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('MD_HOLIDAY')
                ->selectRaw('MD_HOLIDAY.*, MD_HOLIDAY_STATUS.DESC_CHAR AS STATUS_CHAR')
                ->join('MD_HOLIDAY_STATUS', 'MD_HOLIDAY_STATUS.ID_STATUS', '=', 'MD_HOLIDAY.STATUS')
                ->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                ->where('MD_HOLIDAY.STATUS', 1)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('MD_HOLIDAY')
                            ->selectRaw('MD_HOLIDAY.*, MD_HOLIDAY_STATUS.DESC_CHAR AS STATUS_CHAR')
                            ->join('MD_HOLIDAY_STATUS', 'MD_HOLIDAY_STATUS.ID_STATUS', '=', 'MD_HOLIDAY.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.HOLIDAY_NAME', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.HOLIDAY_DATE', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY_STATUS.DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.created_by', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.updated_by', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('MD_HOLIDAY')
                            ->selectRaw('MD_HOLIDAY.*, MD_HOLIDAY_STATUS.DESC_CHAR AS STATUS_CHAR')
                            ->join('MD_HOLIDAY_STATUS', 'MD_HOLIDAY_STATUS.ID_STATUS', '=', 'MD_HOLIDAY.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.HOLIDAY_NAME', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.HOLIDAY_DATE', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY_STATUS.DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.created_by', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_HOLIDAY.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_HOLIDAY.updated_by', 'LIKE', "%{$search}%")
                                ->where('MD_HOLIDAY.STATUS', 1);
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $edit =  route('edit_view_holiday', base64_encode($post->HOLIDAY_ID_INT));

                $nestedData['HOLIDAY_NAME'] = $post->HOLIDAY_NAME;
                $nestedData['HOLIDAY_DATE'] = $post->HOLIDAY_DATE;
                $nestedData['STATUS_CHAR'] = $post->STATUS_CHAR;
                $nestedData['created_by'] = $post->created_by;
                $nestedData['updated_by'] = $post->updated_by == NULL ? "-" : $post->updated_by;

                // Jika Holiday Sudah Digunakan Maka Tidak Bisa Diedit Maupun Di Hapus
                $dataHolidayUsed = \DB::table('TRANS_TICKET_PURCHASE')->where('HOLIDAY_ID_INT', $post->HOLIDAY_ID_INT)->count();
                if($dataHolidayUsed > 0) {
                    $nestedData['EDIT'] = "<a href='javascript:void(0)' title='Edit' class='btn-default btn-sm'>Edit</a>";
                    $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Non Active' class='btn-default btn-sm'>Non Active</a>";
                }
                else {
                    $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                    $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Non Active' onclick='swalDeleteData(".$post->HOLIDAY_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Non Active</a>";
                }

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

    public function addNewHoliday() {
        $project_no = session('current_project');

        return view('MasterData.Holiday.add_new_holiday')
            ->with('project_no', $project_no);
    }

    public function saveHoliday(Request $request) {
        $project_no = session('current_project');
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        // Ambil Data Holiday Dengan Tanggal Yang Sama
        $dataHoliday = DB::table('MD_HOLIDAY')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('HOLIDAY_DATE', '=', $request->HOLIDAY_DATE)
            ->count();

        // Jika Holiday Dengan Tanggal Yang Sama Sudah Ada Maka Tidak Boleh Input Lagi
        if($dataHoliday > 0) {
            session()->flash('error', 'Holiday ' . date('d/m/Y', strtotime($request->HOLIDAY_DATE)) . ' Already Exists!');
            return redirect()->route('holiday');
        }

        try {
            \DB::beginTransaction();

            \DB::table('MD_HOLIDAY')->insert([
                'HOLIDAY_NAME' => $request->HOLIDAY_NAME,
                'HOLIDAY_DATE' => $request->HOLIDAY_DATE,
                'STATUS' => 1,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('holiday');
        }

        session()->flash('success', "Save Holiday Successfully!");
        return redirect()->route('holiday');
    }

    public function editViewHoliday($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $dataHoliday = DB::table('MD_HOLIDAY')->where('HOLIDAY_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.Holiday.edit_view_holiday')
            ->with('project_no', $project_no)
            ->with('dataHoliday', $dataHoliday);
    }

    public function editHoliday(Request $request) {
        $project_no = session('current_project');
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        // Ambil Data Holiday Dengan Tanggal Yang Sama
        $dataHoliday = DB::table('MD_HOLIDAY')
            ->where('STATUS', '1')
            ->where('PROJECT_NO_CHAR', $project_no)
            ->where('HOLIDAY_DATE', '=', $request->HOLIDAY_DATE)
            ->where('HOLIDAY_ID_INT', '<>', $request->HOLIDAY_ID_INT)
            ->count();

        // Jika Holiday Dengan Tanggal Yang Sama Sudah Ada Maka Tidak Boleh Input Lagi
        if($dataHoliday > 0) {
            session()->flash('error', 'Holiday ' . date('d/m/Y', strtotime($request->HOLIDAY_DATE)) . ' Already Exists!');
            return redirect()->route('holiday');
        }

        try {
            \DB::beginTransaction();

            \DB::table('MD_HOLIDAY')->where('HOLIDAY_ID_INT', $request->HOLIDAY_ID_INT)->update([
                'HOLIDAY_NAME' => $request->HOLIDAY_NAME,
                'HOLIDAY_DATE' => $request->HOLIDAY_DATE,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('holiday');
        }

        session()->flash('success', "Edit Holiday Successfully!");
        return redirect()->route('holiday');
    }

    public function deleteHoliday($id) {
        $project_no = session('current_project');
        $dataProject = \DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();

            \DB::table('MD_HOLIDAY')->where('HOLIDAY_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('holiday');
        }

        session()->flash('success', "Non Active Holiday Successfully!");
        return redirect()->route('holiday');
    }
}
