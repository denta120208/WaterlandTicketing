<?php

namespace App\Http\Controllers\MasterData\ProductPOSCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

require_once dirname(__FILE__)."/../../../../../vendor/koolreport/core/autoload.php";

class ProductPOSCategoryController extends Controller
{
    public function index() {
        $project_no = session('current_project');

        $dataProductPOSCategorys = \DB::table("MD_PRODUCT_POS_CATEGORY AS a")
            ->select("a.*", "b.DESC_CHAR AS STATUS_DESC_CHAR")
            ->leftJoin("MD_PRODUCT_POS_CATEGORY_STATUS AS b", "b.ID_STATUS", "a.STATUS")
            ->where("a.PROJECT_NO_CHAR", $project_no)
            ->where("a.STATUS", 1)
            ->orderBy("a.created_at", "DESC")
            ->get();

        return view('MasterData.ProductPOSCategory.productposcategory')
            ->with('project_no', $project_no)
            ->with('dataProductPOSCategorys', $dataProductPOSCategorys);
    }

    public function viewAddProductPOSCategory() {
        $project_no = session('current_project');

        return view('MasterData.ProductPOSCategory.add_new_product_pos_category')
            ->with('project_no', $project_no);
    }

    public function addProductPOSCategory(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRODUCT_POS_CATEGORY')->insert([
                'DESC_CHAR' => $request->TXT_DESC,
                'STATUS' => 1,
                'PROJECT_NO_CHAR' => $project_no,
                'created_by' => $userName,
                'created_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed save data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Save Product POS Category Successfully!");
        return redirect()->route('product_pos_category');
    }

    public function viewEditProductPOSCategory($id) {
        $project_no = session('current_project');
        $id = base64_decode($id, TRUE);

        $dataProductPOSCategory = DB::table('MD_PRODUCT_POS_CATEGORY')
            ->where('MD_PRODUCT_POS_CATEGORY_ID_INT', $id)
            ->where('PROJECT_NO_CHAR', $project_no)
            ->first();

        return view('MasterData.ProductPOSCategory.edit_view_product_pos_category')
            ->with('project_no', $project_no)
            ->with('dataProductPOSCategory', $dataProductPOSCategory);
    }

    public function editProductPOSCategory(Request $request) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRODUCT_POS_CATEGORY')->where('MD_PRODUCT_POS_CATEGORY_ID_INT', $request->TXT_ID)->update([
                'DESC_CHAR' => $request->TXT_DESC,
                'updated_by' => $userName,
                'updated_at' => $dateNow
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('error', 'Failed edit data, errmsg : ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('success', "Edit Product POS Category Successfully!");
        return redirect()->route('product_pos_category');
    }

    public function deleteProductPOSCategory($id) {
        $project_no = session('current_project');
        $dataProject = DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR', $project_no)->first();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            DB::beginTransaction();

            DB::table('MD_PRODUCT_POS_CATEGORY')->where('MD_PRODUCT_POS_CATEGORY_ID_INT', $id)->update([
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

        session()->flash('success', "Delete Product POS Category Successfully!");
        return redirect()->route('product_pos_category');
    }
}