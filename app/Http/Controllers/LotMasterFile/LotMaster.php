<?php

namespace App\Http\Controllers\LotMasterFile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogActivity\LogActivityController;
use App\Http\Requests\LotMaster\addDataLotMaster;
use App\Model\BTCategoryModel;
use App\Model\Divisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use View;
use Session;
use DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel;

class LotMaster extends Controller
{
    public function listDataLot(){
        $project_no = session('current_project');

        $listDataLot = DB::select("SELECT a.LOT_STOCK_ID_INT,a.LOT_STOCK_NO,b.LOT_TYPE_DESC,c.LOT_LEVEL_DESC,d.LOT_ZONE_DESC,a.LOT_STOCK_SQM,a.IS_DELETE,
                                          CASE WHEN a.ON_RELEASE_STAT_INT = 1 THEN 'RELEASE' ELSE 'UNRELEASE' END as ON_RELEASE_STAT_INT,
                                          CASE WHEN a.ON_RENT_STAT_INT = 1 THEN 'RENT' ELSE 'AVAILABLE' END as ON_RENT_STAT_INT
                        FROM LOT_STOCK as a INNER JOIN LOT_TYPE as b ON a.LOT_TYPE_ID_INT = b.LOT_TYPE_ID_INT
                        INNER JOIN LOT_LEVEL as c ON a.LOT_LEVEL_ID_INT = c.LOT_LEVEL_ID_INT
                        INNER JOIN LOT_ZONE as d ON a.LOT_ZONE_ID_INT = d.LOT_ZONE_ID_INT
                        WHERE a.PROJECT_NO_CHAR = '".$project_no."'
                        AND a.IS_DELETE = 0");

        return View::make('page.lotmaster.list',
            ['listDataLot'=>$listDataLot]);
    }

    public function viewAddDataLot(){
        $project_no = session('current_project');

        $listLotType = DB::table('LOT_TYPE')
            ->where('IS_DELETE','=',0)
            ->get();

        $listLotLevel = DB::table('LOT_LEVEL')
            ->where('IS_DELETE','=',0)
            ->get();

        $listLotZone = DB::table('LOT_ZONE')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->where('IS_DELETE','=',0)
            ->get();

        $listUom = DB::table('PL_UOM')
            ->whereIN('id_unit',[1,2])
            ->get();

        $dataCategory = DB::table('PSM_CATEGORY')
            ->where('IS_DELETE','=',0)
            ->get();

        return View::make('page.lotmaster.create',
            ['listLotType' => $listLotType,'listLotLevel'=>$listLotLevel,
             'listLotZone'=>$listLotZone,'listUom'=>$listUom,'dataCategory'=>$dataCategory]);
    }

    public function saveDataLot(addDataLotMaster $requestLot){
        $inputDataLot = $requestLot->all();
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $cekDataLotNo = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_NO','=',$inputDataLot['LOT_STOCK_NO'])
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->where('IS_DELETE','=',0)
            ->count();

        if ($cekDataLotNo > 0)
        {
            return redirect()->route('lot.lotmaster.listdatalot')->with('errorFailed','Unit Already Registered');
        }
        else
        {
            $lot_master = DB::table('LOT_STOCK')
                ->insert([
                    'LOT_STOCK_NO'=>$inputDataLot['LOT_STOCK_NO'],
                    'LOT_LEVEL_ID_INT'=>$inputDataLot['LOT_LEVEL_ID_INT'],
                    'LOT_TYPE_ID_INT'=>$inputDataLot['LOT_TYPE_ID_INT'],
                    'LOT_ZONE_ID_INT'=>$inputDataLot['LOT_ZONE_ID_INT'],
                    'PSM_CATEGORY_ID_INT'=>$inputDataLot['PSM_CATEGORY_ID_INT'],
                    'id_unit'=>$inputDataLot['id_unit'],
                    'LOT_STOCK_SQMR'=>$inputDataLot['LOT_STOCK_SQMR'],
                    'LOT_STOCK_SQM'=>$inputDataLot['LOT_STOCK_SQM'],
                    'LOT_STOCK_SQM_SC'=>$inputDataLot['LOT_STOCK_SQM_SC'],
                    'ON_RELEASE_STAT_INT'=>$inputDataLot['ON_RELEASE_STAT_INT'],
                    'PROJECT_NO_CHAR'=>$project_no,
                    'LOT_CREATED_BY'=>$userName,
                    'LOT_CREATED_DATE'=>$dateNow,
                    'created_at'=>$dateNow,
                    'updated_at'=>$dateNow
                ]);

            $dataLot = DB::table('LOT_STOCK')
                ->where('LOT_STOCK_NO','=',$inputDataLot['LOT_STOCK_NO'])
                ->first();
        }

        if($lot_master)
        {
            $action = "INSERT DATA LOT MASTER";
            $description = 'Success Add Data Lot Master : '.$inputDataLot['LOT_STOCK_NO'];
            $this->saveToLog($action, $description);
            return redirect()->route('lot.lotmaster.vieweditdatalot',[$dataLot->LOT_STOCK_ID_INT])->with('message',$description);
        }
        else
        {
            return redirect()->route('lot.lotmaster.listdatalot')->with('errorFailed','Cannot Insert Lot Master');
        }
    }

    public function saveToLog($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'LOT';
        $module = 'Marketing';
        $by = $userName;
        $table = 'LOT_STOCK';

        $logActivity = new LogActivityController();
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function saveToLogPrice($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'LOT';
        $module = 'Marketing';
        $by = $userName;
        $table = 'LOT_STOCK_DETAIL';

        $logActivity = new LogActivityController();
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }

    public function viewEditDataLot($LOT_STOCK_ID_INT){
        $project_no = session('current_project');

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$LOT_STOCK_ID_INT)
            ->first();

        $dataLotDetail = DB::select("Select a.LOT_STOCK_DTL_ID_INT,b.LOT_STOCK_NO,a.LOT_STOCK_TYPE,a.LOT_STOCK_PRICE_NUM
                                    from LOT_STOCK_DETAIL as a INNER JOIN LOT_STOCK as b ON a.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
                                    where a.LOT_STOCK_ID_INT = ".$LOT_STOCK_ID_INT);

        $dataLotZone = DB::table('LOT_ZONE')
            ->where('LOT_ZONE_ID_INT','=',$dataLot->LOT_ZONE_ID_INT)
            ->first();

        $dataLotType = DB::table('LOT_TYPE')
            ->where('LOT_TYPE_ID_INT','=',$dataLot->LOT_TYPE_ID_INT)
            ->first();

        $dataLotLevel = DB::table('LOT_LEVEL')
            ->where('LOT_LEVEL_ID_INT','=',$dataLot->LOT_LEVEL_ID_INT)
            ->first();

        $dataUom = DB::table('PL_UOM')
            ->where('id_unit','=',$dataLot->id_unit)
            ->first();

        $dataCategory = DB::table('PSM_CATEGORY')
            ->where('IS_DELETE','=',0)
            ->where('PSM_CATEGORY_ID_INT','=',$dataLot->PSM_CATEGORY_ID_INT)
            ->first();

        $listLotType = DB::table('LOT_TYPE')
            ->where('IS_DELETE','=',0)
            ->get();

        $listLotLevel = DB::table('LOT_LEVEL')
            ->where('IS_DELETE','=',0)
            ->get();

        $listLotZone = DB::table('LOT_ZONE')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->where('IS_DELETE','=',0)
            ->get();

        $listUom = DB::table('PL_UOM')
            ->whereIN('id_unit',[1,2])
            ->get();

        $listCategory = DB::table('PSM_CATEGORY')
            ->where('IS_DELETE','=',0)
            ->get();

        return View::make('page.lotmaster.edit',
            ['listLotType' => $listLotType,'listLotLevel'=>$listLotLevel,
             'listLotZone'=>$listLotZone,'listUom'=>$listUom,'listCategory'=>$listCategory,
             'dataLot'=>$dataLot,'dataLotZone'=>$dataLotZone,'dataLotType'=>$dataLotType,
             'dataLotLevel'=>$dataLotLevel,'dataUom'=>$dataUom,'dataCategory'=>$dataCategory,
             'dataLotDetail'=>$dataLotDetail]);
    }

    public function saveEditDataLot(addDataLotMaster $requestLot){
        $inputDataLot = $requestLot->all();

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $cekDataLotNo = DB::table('LOT_STOCK')
            ->whereNotIn('LOT_STOCK_ID_INT',[$inputDataLot['LOT_STOCK_ID_INT']])
            ->where('LOT_STOCK_NO','=',$inputDataLot['LOT_STOCK_NO'])
            ->where('IS_DELETE','=',0)
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->count();

        if ($cekDataLotNo > 0)
        {
            return redirect()->route('lot.lotmaster.vieweditdatalot',['id'=>$inputDataLot['LOT_STOCK_ID_INT']])
                ->with('errorFailed','Unit Already Registered');
        }
        else
        {
            DB::table('LOT_STOCK')
                ->where('LOT_STOCK_ID_INT','=',$inputDataLot['LOT_STOCK_ID_INT'])
                ->update([
                    'LOT_STOCK_NO'=>$inputDataLot['LOT_STOCK_NO'],
                    'LOT_LEVEL_ID_INT'=>$inputDataLot['LOT_LEVEL_ID_INT'],
                    'LOT_TYPE_ID_INT'=>$inputDataLot['LOT_TYPE_ID_INT'],
                    'LOT_ZONE_ID_INT'=>$inputDataLot['LOT_ZONE_ID_INT'],
                    'PSM_CATEGORY_ID_INT'=>$inputDataLot['PSM_CATEGORY_ID_INT'],
                    'id_unit'=>$inputDataLot['id_unit'],
                    'LOT_STOCK_SQMR'=>$inputDataLot['LOT_STOCK_SQMR'],
                    'LOT_STOCK_SQM'=>$inputDataLot['LOT_STOCK_SQM'],
                    'LOT_STOCK_SQM_SC'=>$inputDataLot['LOT_STOCK_SQM_SC'],
                    'ON_RELEASE_STAT_INT'=>$inputDataLot['ON_RELEASE_STAT_INT'],
                    'LOT_UPDATE_BY'=>$userName,
                    'LOT_UPDATE_DATE'=>$dateNow,
                    'updated_at'=>$dateNow
                ]);
        }

        $action = "UPDATE DATA LOT MASTER";
        $description = 'Success Update Data Lot Master : '.$inputDataLot['LOT_STOCK_NO'];
        $this->saveToLog($action, $description);
        
        return redirect()->route('lot.lotmaster.vieweditdatalot',[$inputDataLot['LOT_STOCK_ID_INT']])->with('message',$description);
    }

    public function deleteDataLot($LOT_STOCK_ID_INT){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$LOT_STOCK_ID_INT)
            ->first();

        DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$LOT_STOCK_ID_INT)
            ->update([
                'IS_DELETE'=>1,
                'LOT_DELETE_BY'=>$userName,
                'LOT_DELETE_DATE'=>$dateNow,
                'updated_at'=>$dateNow
            ]);

        $action = "DELETE DATA LOT MASTER";
        $description = 'Success Delete Data Lot Master : '.$dataLot->LOT_STOCK_NO;
        $this->saveToLog($action, $description);
        return redirect()->route('lot.lotmaster.listdatalot')->with('message',$description);
    }

    public function insertUpdateLotPrice(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$request->LOT_STOCK_ID_INT)
            ->first();

        if ($request->insert_id == 1) {
            DB::table('LOT_STOCK_DETAIL')
                ->insert([
                    'LOT_STOCK_ID_INT'=>$request->LOT_STOCK_ID_INT,
                    'LOT_STOCK_TYPE'=>$request->LOT_STOCK_TYPE,
                    'LOT_STOCK_PRICE_NUM'=>$request->LOT_STOCK_PRICE_NUM,
                    'PROJECT_NO_CHAR'=>$project_no,
                    'created_at'=>$dateNow,
                    'updated_at'=>$dateNow
                ]);

            $action = "INSERT DATA LOT PRICE";
            $description = 'Insert Data Lot Price 0-' . $dataLot->LOT_STOCK_NO . ' Type : ' . $request->LOT_STOCK_TYPE.', Price : '.$request->LOT_STOCK_PRICE_NUM;
            $this->saveToLogPrice($action, $description);
            return response()->json(['Success' => 'Berhasil Insert Item']);
        } else {
            $dataLotDetail = DB::table('LOT_STOCK_DETAIL')
                ->where('LOT_STOCK_DTL_ID_INT','=',$request->LOT_STOCK_DTL_ID_INT)
                ->first();

            DB::table('LOT_STOCK_DETAIL')
                ->where('LOT_STOCK_DTL_ID_INT','=',$request->LOT_STOCK_DTL_ID_INT)
                ->update([
                    'LOT_STOCK_ID_INT'=>$request->LOT_STOCK_ID_INT,
                    'LOT_STOCK_TYPE'=>$request->LOT_STOCK_TYPE,
                    'LOT_STOCK_PRICE_NUM'=>$request->LOT_STOCK_PRICE_NUM,
                    'updated_at'=>$dateNow
                ]);

            $action = "UPDATE DATA LOT PRICE";
            $description = 'Update Data Lot Price ' . $dataLotDetail->LOT_STOCK_DTL_ID_INT.'-'.$dataLot->LOT_STOCK_NO . ' Type : ' . $request->LOT_STOCK_TYPE.', Price : '.$request->LOT_STOCK_PRICE_NUM;
            $this->saveToLogPrice($action, $description);
            return response()->json(['Success' => 'Berhasil Update Item']);
        }
    }

    public function getitemLotPrice(Request $request){
        $itemLotPrice = DB::table('LOT_STOCK_DETAIL')
            ->where('LOT_STOCK_DTL_ID_INT', '=', $request->LOT_STOCK_DTL_ID_INT)
            ->first();

        if ($itemLotPrice) {
            return response()->json([
                'status' => 'success',
                'LOT_STOCK_TYPE'=>$itemLotPrice->LOT_STOCK_TYPE,
                'LOT_STOCK_DTL_ID_INT'=>$itemLotPrice->LOT_STOCK_DTL_ID_INT,
                'LOT_STOCK_PRICE_NUM'=>$itemLotPrice->LOT_STOCK_PRICE_NUM
            ]);
        } else {
            return response()->json(['status' => 'error', 'msg' => 'Data Not Found']);
        }
    }

    public function deleteItemLotPrice(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');

        $itemLotPrice = DB::table('LOT_STOCK_DETAIL')
            ->where('LOT_STOCK_DTL_ID_INT', '=', $request->LOT_STOCK_DTL_ID_INT)
            ->first();

        $dataLot = DB::table('LOT_STOCK')
            ->where('LOT_STOCK_ID_INT','=',$itemLotPrice->LOT_STOCK_ID_INT)
            ->first();

        DB::table('LOT_STOCK_DETAIL')
            ->where('LOT_STOCK_DTL_ID_INT', '=', $request->LOT_STOCK_DTL_ID_INT)
            ->delete();

        $action = "DELETE DATA LOT PRICE";
        $description = 'Delete Data Lot Price ' . $request->LOT_STOCK_DTL_ID_INT.'-'.$dataLot->LOT_STOCK_NO . ' Type : ' . $itemLotPrice->LOT_STOCK_TYPE.', Price : '.$itemLotPrice->LOT_STOCK_PRICE_NUM;
        $this->saveToLogPrice($action, $description);
        return response()->json(['Success' => 'Berhasil Delete Item']);
    }
}
