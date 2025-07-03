<?php

namespace App\Http\Controllers\AccountReceivable;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Response;
use Session;
use View;

define("ACTION_UPDATE_DEBTOR_VA","UPDATE DATA DEBTOR VA");
define("ACTION_CANCEL_DEBTOR_VA","CANCEL DATA DEBTOR VA");
define("ACTION_DELETE_VA_NUMBER","DELETE VA NUMBER");

class VirtualAccount extends Controller {

    public function uploadDownloadVA(){
        $project_no = session('current_project');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $dateFormat = date('Y-m-d', strtotime($dateNow));
        
        return View::make('page.accountreceivable.virtualaccount.uploadDownloadVA',
            ['project_no'=>$project_no,'dateFormat'=>$dateFormat]);
    }

    public function prosesUploadDownloadVA(Requests\AccountReceivable\prosesUploadDownloadVARequest $requestUD){
        $project_no = session('current_project');

        try {
            \DB::beginTransaction();

            $dataProject = Model\ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
            $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
            $dateFormat = date('Ymd', strtotime($dateNow));
            $dateFormatTime = date('Ymd-His', strtotime($dateNow));
            
            $inputUD = $requestUD->all();
            
            if ($inputUD['transaction'] == 1){
                
                Model\VAExim::where('VA_EXIM_TRX_DATE','=',$inputUD['transaction_date'])
                    ->where('PROJECT_NO_CHAR','=',$project_no)->delete();

                DB::statement("exec sp_va_exim '". DB::raw($inputUD['transaction_date'])."',".DB::raw($project_no));

                $dataVAExim = Model\VAExim::where('VA_EXIM_TRX_DATE','=',$inputUD['transaction_date'])
                    ->where('PROJECT_NO_CHAR','=',$project_no)->get();
                
                $headers = array(
                    "Content-type" => "text/csv",
                    "Content-Disposition" => "attachment; filename=".$dataProject['VA_MANDIRI']."".$dataProject['PROJECT_CODE'].".csv",
                    "Pragma" => "no-cache",
                    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                    "Expires" => "0"
                );

                try {
                    $callback = function() use ($dataVAExim){
                        $FH = fopen('php://output', 'w');

                        foreach ($dataVAExim as $row) {
                            $field = array(trim($row->VA_NUMBER).'|||IDR|'.$row->PROJECT_DESC.'|'.rtrim($row->CUSTOMER_NAME_CHAR," ").
                                '|'.trim($row->KODE_STOK_UNIQUE_ID_CHAR).'|Total-'.$row->VA_EXIM_TOTAL_AMT_NUM.
                                '||||||||||||||||||||||'.$row->VA_EXIM_OPEN_PERIOD.'|'.$row->VA_EXIM_CLOSE_PERIOD.
                                '|'.$row->VA_EXIM_SUBBILL.'|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|\\\\\|~');
                            fwrite($FH,implode('"', $field)."\r\n");
                        }

                        fclose($FH);
                    };

                    return Response::stream($callback, 200, $headers);
                } catch(Exception $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }
            elseif ($inputUD['transaction'] == 2){
                $file = $requestUD->file('sheet');
                $ext = $file->getClientOriginalExtension();
                $newName = "MPS-".$dataProject['PROJECT_CODE']."-".$dateFormatTime.".".$ext;
                $file->move('uploads/ImportFile',$newName);

                $file_name = File::get('uploads/ImportFile/'.$newName);
                foreach (explode(":61", $file_name) as $key=>$line){
                    if($key >= 1){
                        $header[$key] = explode(':', $line);
                        $dateVA = substr($header[$key][1], 0, 6);
                        $dt= date_parse_from_format("y-m-d", $dateVA);
                        $m = $dt['year'].'-'.substr($dateVA,2,2).'-'.$dt['day'];
                        $trx_type = substr($header[$key][1], 6,1);
                        $amt = intval(substr($header[$key][1], 7));
                        $va = substr(trim(preg_replace('/\s+/', ' ', $header[$key][3])), 20);
                        $this->saveImport($va,$m,$amt,$trx_type);
                    }
                }
            }

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
			return redirect()->route('virtualaccount.uploaddownloadva')->with('error', 'Failed save data, errmsg : ' . $ex);
        }
        
        return redirect()->route('virtualaccount.uploaddownloadva');
    }
}
