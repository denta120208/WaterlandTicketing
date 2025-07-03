<?php namespace App\Http\Controllers\LogActivity;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;

use Illuminate\Http\Request;

class LogActivityController extends Controller {

	//
    public function createLog($arrayLog){

        list($action,$module,$submodule,$by,$table,$description)= $arrayLog;
        $inputLog = new \App\Model\LogActivity;
        $inputLog->action = $action;
        $inputLog->module = $module;
        $inputLog->sub_module = $submodule;
        $inputLog->modified_by = $by;
        $inputLog->table_activity = $table;
        $inputLog->description = $description;
        try{
             $inputLog->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::warning('Failed saved log to database error : '. $ex);
        }

        \Log::info('Saving log activity into database');
    }

    public function createLogLA($arrayLog){

        list($action,$module,$submodule,$by,$doc,$description)= $arrayLog;
        $inputLog = new \App\Model\Log\LogActivityLA;
        $inputLog->LA_DOC = $doc;
        $inputLog->LA_LOG_ACTION = $action;
        $inputLog->LA_LOG_MODIFY_ON = $by;
        $inputLog->LA_LOG_DESC = $description;
        $inputLog->LA_LOG_MODULE = $module;
        $inputLog->LA_LOG_SUB = $submodule;

        try{
             $inputLog->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::warning('Failed saved log to database error : '. $ex);
        }

        \Log::info('Saving log activity into database');
    }

    public function createLogSU($arrayLog){
        $project_no = \Session::get('dataSession.project_no');
        list($action,$module,$submodule,$by,$idUnit,$progress,$description)= $arrayLog;
        $inputLog = new \App\Model\Log\StockUnitLog();
        $inputLog->ID_UNIT_APARTMENT_INT = $idUnit;
        $inputLog->PROGRESS_TECH_INT = $progress;
        $inputLog->MD_STOCK_LOG_ACTION = $action;
        $inputLog->MD_STOCK_LOG_DESC = $description;
        $inputLog->MD_STOCK_LOG_BY = $by;
        $inputLog->MD_STOCK_MODULE = $module;
        $inputLog->MD_STOCK_LOG_SUBMODULE = $submodule;
        $inputLog->PROJECT_NO_CHAR = $project_no;

        try{
             $inputLog->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::warning('Failed saved log to database error : '. $ex);
        }

        \Log::info('Saving log activity into database');
    }

    public function createLogPrice($arrayLog){
        $project_no = \Session::get('dataSession.project_no');
        list($submodule,$module,$action,$by,$description,$idunit,$RELEASE_OLD,$RELEASE_NEW,$RELEASE_DATE_OLD,$RELEASE_DATE_NEW,
                              $LAND_OLD,$LAND_NEW,$BUILDING_OLD,$BUILDING_NEW,$BF_OLD,$BF_NEW,$HC_OLD,$HC_NEW,$KPA_OLD,$KPA_NEW,$INSTL_OLD,$INSTL_NEW)= $arrayLog;

        $inputLog = new \App\Model\Log\PriceUnitLog();
        $inputLog->MD_PRICING_LOG_MODULE = $module;
        $inputLog->MD_PRICING_LOG_SUBMODULE = $submodule;
        $inputLog->NOUNIT_CHAR = $idunit;
        $inputLog->RELEASE_NUM_OLD = $RELEASE_OLD;
        $inputLog->RELEASE_NUM_NEW = $RELEASE_NEW;
        $inputLog->RELEASE_NUM_DATE_OLD = $RELEASE_DATE_OLD;
        $inputLog->RELEASE_NUM_DATE_NEW = $RELEASE_DATE_NEW;
        $inputLog->LAND_NUM_OLD = $LAND_OLD;
        $inputLog->LAND_NUM_NEW = $LAND_NEW;
        $inputLog->BUILDING_NUM_OLD = $BUILDING_OLD;
        $inputLog->BUILDING_NUM_NEW = $BUILDING_NEW;
        $inputLog->BF_OLD = $BF_OLD;
        $inputLog->BF_NEW = $BF_NEW;
        $inputLog->HC_OLD = $HC_OLD;
        $inputLog->HC_NEW = $HC_NEW;
        $inputLog->KPR_OLD = $KPA_OLD;
        $inputLog->KPR_NEW = $KPA_NEW;
        $inputLog->INSTL_OLD = $INSTL_OLD;
        $inputLog->INSTL_NEW = $INSTL_NEW;
        $inputLog->MD_PRICING_LOG_ACTION = $action;
        $inputLog->MD_PRICING_LOG_DESC = $description;
        $inputLog->MD_PRICING_LOG_BY = $by;
        $inputLog->PROJECT_NO_CHAR = $project_no;
        //dd($inputLog);
        try{
             $inputLog->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            Log::error('================UPDATE RECORD===================');
            Log::error('Error :'.$ex);
            Log::error('================================================');
            return response()->json(['Error' => ' ']);
        }

        \Log::info('Saving log activity into database');
    }

    public function createLogVA($arrayLog){

        list($action,$module,$submodule,$by,$bank,$description,$project_no)= $arrayLog;
        $inputLog = new \App\Model\Log\LogActivityVA();
        $inputLog->MD_VA_BANK_LOG_MODULE = $module;
        $inputLog->MD_VA_BANK_LOG_SUBMODULE = $submodule;
        $inputLog->MD_VA_BANK_CODE_CHAR = $bank;
        $inputLog->MD_VA_BANK_LOG_ACTION = $action;
        $inputLog->MD_VA_BANK_LOG_DESC = $description;
        $inputLog->MD_VA_BANK_LOG_BY = $by;
        $inputLog->PROJECT_NO_CHAR = $project_no;

        try{
             $inputLog->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::warning('Failed saved log to database error : '. $ex);
        }

        \Log::info('Saving log activity into database');
    }

    public function createLogDebtVA($arrayLog){

        list($action,$module,$submodule,$by,$doc,$description,$project_no)= $arrayLog;
        $inputLog = new \App\Model\Log\LogActivityDebtVA();
        $inputLog->VA_DEBTOR_LOG_MODULE = $module;
        $inputLog->VA_DEBTOR_LOG_SUBMODULE = $submodule;
        $inputLog->KODE_STOK_UNIQUE_ID_CHAR = $doc;
        $inputLog->PROJECT_NO_CHAR = $project_no;
        $inputLog->MD_VA_BANK_LOG_ACTION = $action;
        $inputLog->MD_VA_BANK_LOG_DESC = $description;
        $inputLog->MD_VA_BANK_LOG_BY = $by;

        try{
             $inputLog->save();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::warning('Failed saved log to database error : '. $ex);
        }

        \Log::info('Saving log activity into database');
    }

}
