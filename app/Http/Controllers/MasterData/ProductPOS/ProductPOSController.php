<?php

namespace App\Http\Controllers\MasterData\ProductPOS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class ProductPOSController extends Controller
{
    public function index() {
        $project_no = session('current_project');

        $dataProductPOSs = \DB::table("MD_PRODUCT_POS AS a")
            ->select("a.*", "c.DESC_CHAR AS CATEGORY_DESC_CHAR", "b.DESC_CHAR AS STATUS_DESC_CHAR")
            ->leftJoin("MD_PRODUCT_POS_STATUS AS b", "b.ID_STATUS", "a.STATUS")
            ->leftJoin("MD_PRODUCT_POS_CATEGORY AS c", "c.MD_PRODUCT_POS_CATEGORY_ID_INT", "a.MD_PRODUCT_POS_CATEGORY_ID_INT")
            ->where("a.PROJECT_NO_CHAR", $project_no)
            ->where("a.STATUS", 1)
            ->orderBy("a.created_at", "DESC")
            ->get();

        return view('MasterData.ProductPOS.productpos')
            ->with('project_no', $project_no)
            ->with('dataProductPOSs', $dataProductPOSs);
    }

    public function viewAddProductPOS() {
        $project_no = session('current_project');

        $ddlProductPOSCategory = DB::table('MD_PRODUCT_POS_CATEGORY')->where('STATUS', 1)->where('PROJECT_NO_CHAR', $project_no)->get();

        return view('MasterData.ProductPOS.add_new_product_pos')
            ->with('project_no', $project_no)
            ->with('ddlProductPOSCategory', $ddlProductPOSCategory);
    }

    public function addProductPOS(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRODUCT_POS')->insert([
                'MD_PRODUCT_POS_CATEGORY_ID_INT' => $request->DDL_CATEGORY,
                'NAMA_PRODUCT' => $request->TXT_NAME,
                'HARGA_SATUAN_FLOAT' => $request->TXT_PRICE,
                'PB1_PERCENT_INT' => $request->TXT_PB1,
                'PPH_PERCENT_INT' => $request->TXT_PPH,
                'PROJECT_NO_CHAR' => $project_no,
                'STATUS' => 1,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Save Product POS Successfully!");
        return redirect()->route('product_pos');
    }

    public function viewEditProductPOS($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $ddlProductPOSCategory = DB::table('MD_PRODUCT_POS_CATEGORY')->where('STATUS', 1)->where('PROJECT_NO_CHAR', $project_no)->get();
        $dataProductPOS = DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $id)->where('PROJECT_NO_CHAR', $project_no)->first();

        return view('MasterData.ProductPOS.edit_view_product_pos')
            ->with('project_no', $project_no)
            ->with('ddlProductPOSCategory', $ddlProductPOSCategory)
            ->with('dataProductPOS', $dataProductPOS);
    }

    public function editProductPOS(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $request->TXT_ID)->update([
                'MD_PRODUCT_POS_CATEGORY_ID_INT' => $request->DDL_CATEGORY,
                'NAMA_PRODUCT' => $request->TXT_NAME,
                'HARGA_SATUAN_FLOAT' => $request->TXT_PRICE,
                'PB1_PERCENT_INT' => $request->TXT_PB1,
                'PPH_PERCENT_INT' => $request->TXT_PPH,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Edit Product POS Successfully!");
        return redirect()->route('product_pos');
    }

    public function deleteProductPOS($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRODUCT_POS')->where('MD_PRODUCT_POS_ID_INT', $id)->update([
                'STATUS' => 0,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed delete data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Delete Product POS Successfully!");
        return redirect()->route('product_pos');
    }
}