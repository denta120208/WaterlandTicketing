<?php

namespace App\Http\Controllers\MasterData\TicketGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class TicketGroupController extends Controller
{
    
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.TicketGroup.ticketGroup')
            ->with('project_no', $project_no);
    }

    public function listTblTicketGroup(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'MD_GROUP_TICKET_DESC',
            1 =>'MD_GROUP_TICKET_PERSON', 
            2 =>'DESC_CHAR'
        );

        $totalData = \DB::table('MD_GROUP_TICKET')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('MD_GROUP_TICKET')
                ->selectRaw('MD_GROUP_TICKET.*, MD_GROUP_TICKET_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                ->join('MD_GROUP_TICKET_STATUS', 'MD_GROUP_TICKET_STATUS.ID_STATUS', '=', 'MD_GROUP_TICKET.STATUS')
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('MD_GROUP_TICKET.STATUS', "1")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('MD_GROUP_TICKET')
                            ->selectRaw('MD_GROUP_TICKET.*, MD_GROUP_TICKET_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_GROUP_TICKET_STATUS', 'MD_GROUP_TICKET_STATUS.ID_STATUS', '=', 'MD_GROUP_TICKET.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_GROUP_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_GROUP_TICKET_DESC', 'LIKE', "%{$search}%")
                                ->where('MD_GROUP_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_GROUP_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_GROUP_TICKET_PERSON', 'LIKE', "%{$search}%")
                                ->where('MD_GROUP_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_GROUP_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_GROUP_TICKET.STATUS', "1");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('MD_GROUP_TICKET')
                            ->selectRaw('MD_GROUP_TICKET.*, MD_GROUP_TICKET_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_GROUP_TICKET_STATUS', 'MD_GROUP_TICKET_STATUS.ID_STATUS', '=', 'MD_GROUP_TICKET.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_GROUP_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_GROUP_TICKET_DESC', 'LIKE', "%{$search}%")
                                ->where('MD_GROUP_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_GROUP_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('MD_GROUP_TICKET_PERSON', 'LIKE', "%{$search}%")
                                ->where('MD_GROUP_TICKET.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_GROUP_TICKET.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_GROUP_TICKET.STATUS', "1");
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $edit =  route('edit_view_ticket_group', base64_encode($post->MD_GROUP_TICKET_ID_INT));

                $nestedData['MD_GROUP_TICKET_DESC'] = $post->MD_GROUP_TICKET_DESC;
                $nestedData['MD_GROUP_TICKET_PERSON'] = $post->MD_GROUP_TICKET_PERSON;
                $nestedData['STATUS_DESC_CHAR'] = $post->DESC_CHAR_STATUS;

                $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->MD_GROUP_TICKET_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewTicketGroup() {
        $project_no = session('current_project');

        return view('MasterData.TicketGroup.add_new_ticket_group')
            ->with('project_no', $project_no);
    }

    public function saveTicketGroup(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_GROUP_TICKET')->insert([
                'MD_GROUP_TICKET_DESC' => $request->TXT_DESC,
                'MD_GROUP_TICKET_PERSON' => $request->TXT_QTY,
                'STATUS' => 1,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('ticketGroup');
        }

        session()->flash('success', "Save Ticket Group Successfully!");
        return redirect()->route('ticketGroup');
    }

    public function editViewTicketGroup($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $dataTicketGroup = DB::table('MD_GROUP_TICKET')->where('MD_GROUP_TICKET_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.TicketGroup.edit_view_ticket_group')
            ->with('project_no', $project_no)
            ->with('dataTicketGroup', $dataTicketGroup);
    }

    public function editTicketGroup(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_GROUP_TICKET')->where('MD_GROUP_TICKET_ID_INT', $request->TXT_ID)->update([
                'MD_GROUP_TICKET_DESC' => $request->TXT_DESC,
                'MD_GROUP_TICKET_PERSON' => $request->TXT_QTY,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('ticketGroup');
        }

        session()->flash('success', "Edit Ticket Group Successfully!");
        return redirect()->route('ticketGroup');
    }

    public function deleteTicketGroup($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_GROUP_TICKET')->where('MD_GROUP_TICKET_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('ticketGroup');
        }

        session()->flash('success', "Delete Ticket Group Successfully!");
        return redirect()->route('ticketGroup');
    }
}
