<?php

namespace App\Http\Controllers\MasterData\TicketPrice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class TicketPriceController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.TicketPrice.ticketPrice')
            ->with('project_no', $project_no);
    }

    public function listTblTicketPrice(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'MD_PRICE_TICKET_DESC',
            1 =>'MD_PRICE_FNB_NUM',
            2 =>'MD_PRICE_OPR_NUM',
            3 =>'MD_PRICE_TICKET_NUM',
            4 =>'MD_PRICE_TICKET_PB1_PERCENT_INT',
            5 =>'MD_PRICE_TICKET_PPH_PERCENT_INT',
            5 =>'DESC_CHAR'
        );

        $totalData = \DB::table('MD_PRICE_TICKET')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('MD_PRICE_TICKET')
                ->selectRaw('MD_PRICE_TICKET.*, MD_GROUP_TICKET.MD_GROUP_TICKET_DESC, MD_PRICE_TICKET_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                ->join('MD_GROUP_TICKET', 'MD_GROUP_TICKET.MD_GROUP_TICKET_ID_INT', '=', 'MD_PRICE_TICKET.MD_GROUP_TICKET_ID_INT')
                ->join('MD_PRICE_TICKET_STATUS', 'MD_PRICE_TICKET_STATUS.ID_STATUS', '=', 'MD_PRICE_TICKET.STATUS')
                ->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                ->where('MD_PRICE_TICKET.STATUS', "1")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('MD_PRICE_TICKET')
                            ->selectRaw('MD_PRICE_TICKET.*, MD_GROUP_TICKET.MD_GROUP_TICKET_DESC, MD_PRICE_TICKET_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_GROUP_TICKET', 'MD_GROUP_TICKET.MD_GROUP_TICKET_ID_INT', '=', 'MD_PRICE_TICKET.MD_GROUP_TICKET_ID_INT')
                            ->join('MD_PRICE_TICKET_STATUS', 'MD_PRICE_TICKET_STATUS.ID_STATUS', '=', 'MD_PRICE_TICKET.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_GROUP_TICKET_DESC', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_DESC', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_FNB_NUM', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_OPR_NUM', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_NUM', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_PB1_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_PPH_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('MD_PRICE_TICKET')
                            ->selectRaw('MD_PRICE_TICKET.*, MD_GROUP_TICKET.MD_GROUP_TICKET_DESC, MD_PRICE_TICKET_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_GROUP_TICKET', 'MD_GROUP_TICKET.MD_GROUP_TICKET_ID_INT', '=', 'MD_PRICE_TICKET.MD_GROUP_TICKET_ID_INT')
                            ->join('MD_PRICE_TICKET_STATUS', 'MD_PRICE_TICKET_STATUS.ID_STATUS', '=', 'MD_PRICE_TICKET.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_GROUP_TICKET_DESC', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_DESC', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_FNB_NUM', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_OPR_NUM', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_NUM', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_PB1_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_PRICE_TICKET_PPH_PERCENT_INT', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PRICE_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_PRICE_TICKET.STATUS', "1");
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $edit =  route('edit_view_ticket_price', base64_encode($post->MD_PRICE_TICKET_ID_INT));

                $nestedData['MD_GROUP_TICKET_DESC'] = $post->MD_GROUP_TICKET_DESC;
                $nestedData['MD_PRICE_TICKET_DESC'] = $post->MD_PRICE_TICKET_DESC;
                $nestedData['MD_PRICE_FNB_NUM'] = number_format($post->MD_PRICE_FNB_NUM, 0, ',', '.');
                $nestedData['MD_PRICE_OPR_NUM'] = number_format($post->MD_PRICE_OPR_NUM, 0, ',', '.');
                $nestedData['MD_PRICE_TICKET_NUM'] = number_format($post->MD_PRICE_TICKET_NUM, 0, ',', '.');
                $nestedData['MD_PRICE_TICKET_PB1_PERCENT_INT'] = (float) $post->MD_PRICE_TICKET_PB1_PERCENT_INT . "%";
                $nestedData['MD_PRICE_TICKET_PPH_PERCENT_INT'] = (float) $post->MD_PRICE_TICKET_PPH_PERCENT_INT . "%";
                $nestedData['STATUS_DESC_CHAR'] = $post->DESC_CHAR_STATUS;

                $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->MD_PRICE_TICKET_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewTicketPrice() {
        $project_no = session('current_project');
        $ddlTicketGroup = DB::table('MD_GROUP_TICKET')->where('STATUS', 1)->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('MasterData.TicketPrice.add_new_ticket_price')
            ->with('project_no', $project_no)
            ->with('ddlTicketGroup', $ddlTicketGroup);
    }

    public function saveTicketPrice(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRICE_TICKET')->insert([
                'MD_GROUP_TICKET_ID_INT' => $request->DDL_GROUP,
                'MD_PRICE_TICKET_DESC' => $request->TXT_DESC,
                'MD_PRICE_TICKET_NUM' => $request->TXT_TICKET,
                'MD_PRICE_TICKET_PB1_PERCENT_INT' => $request->TXT_PB1,
                'MD_PRICE_TICKET_PPH_PERCENT_INT' => $request->TXT_PPH,
                'PROJECT_NO_CHAR' => $project_no,
                'STATUS' => 1,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('ticketPrice');
        }

        session()->flash('success', "Save Ticket Price Successfully!");
        return redirect()->route('ticketPrice');
    }

    public function editViewTicketPrice($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlTicketGroup = DB::table('MD_GROUP_TICKET')->where('STATUS', 1)->where('PROJECT_NO_CHAR', $project_no)->get();
        $dataTicketPrice = DB::table('MD_PRICE_TICKET')->where('MD_PRICE_TICKET_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.TicketPrice.edit_view_ticket_price')
            ->with('project_no', $project_no)
            ->with('ddlTicketGroup', $ddlTicketGroup)
            ->with('dataTicketPrice', $dataTicketPrice);
    }

    public function editTicketPrice(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRICE_TICKET')->where('MD_PRICE_TICKET_ID_INT', $request->TXT_ID)->update([
                'MD_GROUP_TICKET_ID_INT' => $request->DDL_GROUP,
                'MD_PRICE_TICKET_DESC' => $request->TXT_DESC,
                'MD_PRICE_TICKET_NUM' => $request->TXT_TICKET,
                'MD_PRICE_TICKET_PB1_PERCENT_INT' => $request->TXT_PB1,
                'MD_PRICE_TICKET_PPH_PERCENT_INT' => $request->TXT_PPH,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('ticketPrice');
        }

        session()->flash('success', "Edit Ticket Price Successfully!");
        return redirect()->route('ticketPrice');
    }

    public function deleteTicketPrice($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRICE_TICKET')->where('MD_PRICE_TICKET_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('ticketPrice');
        }

        session()->flash('success', "Delete Ticket Price Successfully!");
        return redirect()->route('ticketPrice');
    }
}
