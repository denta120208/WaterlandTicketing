<?php

namespace App\Http\Controllers\MasterData\PaymentMethod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class PaymentMethodController extends Controller
{   
    public function index() {
        $project_no = session('current_project');

        return view('MasterData.PaymentMethod.paymentMethod')
            ->with('project_no', $project_no);
    }

    public function listTblPaymentMethod(Request $request) {
        $project_no = session('current_project');

        $columns = array(
            0 =>'CATEGORY_NAME_CHAR',
            1 =>'PAYMENT_METHOD_DESC_CHAR', 
            2 =>'DESC_CHAR'
        );

        $totalData = \DB::table('MD_PAYMENT_METHOD')->where('PROJECT_NO_CHAR', $project_no)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = \DB::table('MD_PAYMENT_METHOD')
                ->selectRaw('MD_PAYMENT_METHOD.*, MD_PAYMENT_METHOD_CATEGORY.CATEGORY_NAME_CHAR AS CATEGORY_NAME_CHAR, MD_PAYMENT_METHOD_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                ->join('MD_PAYMENT_METHOD_CATEGORY', 'MD_PAYMENT_METHOD_CATEGORY.PAYMENT_METHOD_CATEGORY_ID_INT', '=', 'MD_PAYMENT_METHOD.PAYMENT_METHOD_CATEGORY_ID_INT')
                ->join('MD_PAYMENT_METHOD_STATUS', 'MD_PAYMENT_METHOD_STATUS.ID_STATUS', '=', 'MD_PAYMENT_METHOD.STATUS')
                ->where('PROJECT_NO_CHAR', $project_no)
                ->where('MD_PAYMENT_METHOD.STATUS', "1")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts =  \DB::table('MD_PAYMENT_METHOD')
                            ->selectRaw('MD_PAYMENT_METHOD.*, MD_PAYMENT_METHOD_CATEGORY.CATEGORY_NAME_CHAR AS CATEGORY_NAME_CHAR, MD_PAYMENT_METHOD_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_PAYMENT_METHOD_CATEGORY', 'MD_PAYMENT_METHOD_CATEGORY.PAYMENT_METHOD_CATEGORY_ID_INT', '=', 'MD_PAYMENT_METHOD.PAYMENT_METHOD_CATEGORY_ID_INT')
                            ->join('MD_PAYMENT_METHOD_STATUS', 'MD_PAYMENT_METHOD_STATUS.ID_STATUS', '=', 'MD_PAYMENT_METHOD.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_PAYMENT_METHOD.PROJECT_NO_CHAR', $project_no)
                                ->where('CATEGORY_NAME_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_PAYMENT_METHOD.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PAYMENT_METHOD.PROJECT_NO_CHAR', $project_no)
                                ->where('PAYMENT_METHOD_DESC_CHAR', 'LIKE',"%{$search}%")
                                ->where('MD_PAYMENT_METHOD.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PAYMENT_METHOD.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE',"%{$search}%")
                                ->where('MD_PAYMENT_METHOD.STATUS', "1");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = \DB::table('MD_PAYMENT_METHOD')
                            ->selectRaw('MD_PAYMENT_METHOD.*, MD_PAYMENT_METHOD_CATEGORY.CATEGORY_NAME_CHAR AS CATEGORY_NAME_CHAR, MD_PAYMENT_METHOD_STATUS.DESC_CHAR AS DESC_CHAR_STATUS')
                            ->join('MD_PAYMENT_METHOD_CATEGORY', 'MD_PAYMENT_METHOD_CATEGORY.PAYMENT_METHOD_CATEGORY_ID_INT', '=', 'MD_PAYMENT_METHOD.PAYMENT_METHOD_CATEGORY_ID_INT')
                            ->join('MD_PAYMENT_METHOD_STATUS', 'MD_PAYMENT_METHOD_STATUS.ID_STATUS', '=', 'MD_PAYMENT_METHOD.STATUS')
                            ->where(function ($query) use ($project_no, $search) {
                                $query->where('MD_PAYMENT_METHOD.PROJECT_NO_CHAR', $project_no)
                                ->where('CATEGORY_NAME_CHAR', 'LIKE', "%{$search}%")
                                ->where('MD_PAYMENT_METHOD.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PAYMENT_METHOD.PROJECT_NO_CHAR', $project_no)
                                ->where('PAYMENT_METHOD_DESC_CHAR', 'LIKE',"%{$search}%")
                                ->where('MD_PAYMENT_METHOD.STATUS', "1");
                            })
                            ->orWhere(function ($query) use ($project_no, $search) {
                                $query->where('MD_PAYMENT_METHOD.PROJECT_NO_CHAR', $project_no)
                                ->where('DESC_CHAR', 'LIKE',"%{$search}%")
                                ->where('MD_PAYMENT_METHOD.STATUS', "1");
                            })
                            ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $edit =  route('edit_view_payment_method', base64_encode($post->PAYMENT_METHOD_ID_INT));

                $nestedData['CATEGORY_NAME_CHAR'] = $post->CATEGORY_NAME_CHAR;
                $nestedData['PAYMENT_METHOD_DESC_CHAR'] = $post->PAYMENT_METHOD_DESC_CHAR;
                $nestedData['STATUS_DESC_CHAR'] = $post->DESC_CHAR_STATUS;

                $nestedData['EDIT'] = "<a href='{$edit}' title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a>";
                $nestedData['CANCEL'] = "<a href='javascript:void(0)' title='Delete' onclick='swalDeleteData(".$post->PAYMENT_METHOD_ID_INT.")' class='btn bg-gradient-danger btn-sm'>Delete</a>";

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

    public function addNewPaymentMethod() {
        $project_no = session('current_project');

        $ddlPaymentMethodCategory = DB::table('MD_PAYMENT_METHOD_CATEGORY')->get();

        return view('MasterData.PaymentMethod.add_new_payment_method')
            ->with('project_no', $project_no)
            ->with('ddlPaymentMethodCategory', $ddlPaymentMethodCategory);
    }

    public function savePaymentMethod(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PAYMENT_METHOD')->insert([
                'PAYMENT_METHOD_CATEGORY_ID_INT' => $request->DDL_CATEGORY,
                'PAYMENT_METHOD_DESC_CHAR' => $request->TXT_DESC,
                'STATUS' => 1,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex);
            return redirect()->route('paymentMethod');
        }

        session()->flash('success', "Save Payment Method Successfully!");
        return redirect()->route('paymentMethod');
    }

    public function editViewPaymentMethod($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlPaymentMethodCategory = DB::table('MD_PAYMENT_METHOD_CATEGORY')->get();
        $dataPaymentMethod = DB::table('MD_PAYMENT_METHOD')->where('PAYMENT_METHOD_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.PaymentMethod.edit_view_payment_method')
            ->with('project_no', $project_no)
            ->with('ddlPaymentMethodCategory', $ddlPaymentMethodCategory)
            ->with('dataPaymentMethod', $dataPaymentMethod);
    }

    public function editPaymentMethod(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PAYMENT_METHOD')->where('PAYMENT_METHOD_ID_INT', $request->TXT_ID)->update([
                'PAYMENT_METHOD_CATEGORY_ID_INT' => $request->DDL_CATEGORY,
                'PAYMENT_METHOD_DESC_CHAR' => $request->TXT_DESC,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex);
            return redirect()->route('paymentMethod');
        }

        session()->flash('success', "Edit Payment Method Successfully!");
        return redirect()->route('paymentMethod');
    }

    public function deletePaymentMethod($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PAYMENT_METHOD')->where('PAYMENT_METHOD_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex);
            return redirect()->route('paymentMethod');
        }

        session()->flash('success', "Delete Payment Method Successfully!");
        return redirect()->route('paymentMethod');
    }
}
