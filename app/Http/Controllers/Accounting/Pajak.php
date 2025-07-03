<?php
namespace App\Http\Controllers\Accounting;

use App\Model\Company;
use App\Model\Counter;
use App\Model\Engineerings\UtilsMeter;
use App\Model\Engineerings\UtilsTenant;
use App\Model\GlTrans;
use App\Model\Journal;
use Maatwebsite\Excel;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Model;
use Carbon\Carbon;
use View;
use Session;
use App\Model\PricingApartmentModel;
use Illuminate\Http\Request;
use App\Model\StockUnitApartmentModel;
use App\Model\SalesManModel;
use App\Model\Customer;
use App\Model\ProjectModel;
use App\Model\BrokerModel;
use App\Model\BillingScheduleModel;
use App\Http\Controllers\LogActivity\LogActivityController;
use App\Http\Requests\Accounting\JournalEntryRequest;
use DB;
use Redirect;
use URL;
use Response;

use App\Http\Controllers\Util\utilArray;
use App\Http\Controllers\Util\utilConverter;
use App\Http\Controllers\Util\utilGenerator;
use App\Http\Controllers\Util\utilSession;

define("Stat_Stock", "1");
define("ACTION_GENERATE_EF","GENERATE DATA EF");
define("ACTION_GENERATE_PPH_FINAL","GENERATE DATA PPH FINAL");

define("KODE_TRANSAKSI_IS_BE", "IS");
define("STATUS_NO", "N");
define("STATUS_YES", "Y");
define("ERROR_ROUTE_BE", "salesadministration.bookingentry.viewdata");
define("ERROR_ROUTE_JE1", "accounting.journal.listjournal");

class Pajak extends Controller {
    public function listDataFakturPajak(){
        $project_no = session('current_project');

        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $tahun = $dataProject['YEAR_PERIOD'];
        $dataFakturPajak = DB::table('TAX_MD_FP')
            ->where('PROJECT_NO_CHAR','=',$project_no)
            ->where('IS_DELETE','=',0)
            ->get();

        return View('page.accounting.tax.listDataFakturPajak',
            compact('dataFakturPajak','tahun','dataProject'));
    }

    public function generateTaxInvoice(Request $request) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $userName = trim(session('first_name') . ' ' . session('last_name'));

        try {
            \DB::beginTransaction();
            
            
            $yearTaxPeriod = substr($request->TAX_MD_FP_YEAR_CHAR,2,4);
            
            $numberFailInput = '';
            
            $diffNomor = $request->FP_END_NUMBER - $request->FP_START_NUMBER;
            
            if ($request->FP_START_NUMBER > $request->FP_END_NUMBER)
            {
                return response()->json(['Error' => 'Your Start Number Bigger Than End Number']);
            }
            
            if ($diffNomor > 3000)
            {
                return response()->json(['Error' => 'Max Generate 3000 Data']);
            }
            
            for($i=$request->FP_START_NUMBER;$i<=$request->FP_END_NUMBER;$i++)
            {
                $cekTaxInvoice = DB::table('TAX_MD_FP')
                    ->where('PROJECT_NO_CHAR','=',$project_no)
                    ->where('IS_DELETE','=',0)
                    ->where('TAX_MD_FP_NOCHAR','=',$i)
                    ->where('TAX_MD_FP_YEAR_CHAR','=',$yearTaxPeriod)
                    ->count();

                if ($cekTaxInvoice <= 0)
                {
                    DB::table('TAX_MD_FP')
                        ->insert([
                            'TAX_MD_FP_YEAR_CHAR'=>$yearTaxPeriod,
                            'TAX_MD_FP_KODE_CHAR'=>$request->TAX_MD_FP_CODE_CHAR,
                            'TAX_MD_FP_NOCHAR'=>$i,
                            'IS_TAKEN'=>0,
                            'IS_DELETE'=>0,
                            'PROJECT_NO_CHAR'=>$project_no,
                            'CREATED_BY'=>$userName,
                            'created_at'=>$dateNow
                        ]);
                }
                else
                {
                    $numberFailInput .= $i.',';
                }
            }

            $action = "GENERATE TAX INVOICE";
            $description = 'Generate Tax Invoice ' . $dataProject['PROJECT_NAME'] .' '. $request->FP_START_NUMBER.' - '.$request->FP_END_NUMBER.' Fail Number: '.$numberFailInput;
            $this->saveToLogTaxInvoice($action, $description);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return response()->json(['Error' => 'Failed generate data, errmsg : ' . $ex]);
        }

        return response()->json(['Success' => 'Berhasil Insert Item']);
    }

    public function deleteTaxInvoice(Request $request) {
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();

        $dataTaxInvoice = DB::table('TAX_MD_FP')
            ->where('TAX_MD_FP_ID_INT','=',$request->TAX_MD_FP_ID_INT)
            ->first();

        $deleteTaxInvoice = DB::table('TAX_MD_FP')
            ->where('TAX_MD_FP_ID_INT','=',$request->TAX_MD_FP_ID_INT)
            ->update([
                'IS_DELETE'=>1,
                'UPDATED_BY'=>Session::get('name'),
                'updated_at'=>$dateNow
            ]);

        if($deleteTaxInvoice)
        {
            $action = "DELETE TAX INVOICE";
            $description = 'Delete Tax Invoice ' . $dataProject['PROJECT_NAME'] .' '. $dataTaxInvoice->TAX_MD_FP_NOCHAR;
            $this->saveToLogTaxInvoice($action, $description);
            return response()->json(['Success' => 'Berhasil Insert Item']);
        }
        else
        {
            return response()->json(['Error' => 'Tidak bisa Delete Data']);
        }
    }

    public function saveToLogTaxInvoice($action, $description)
    {
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'Tax Invoice';
        $module = 'Accounting';
        $by = $userName;
        $table = 'TAX_MD_FP';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action, $module, $submodule, $by, $table, $description));
    }

    public function listTransaksiFaktur(){
        $startDate = '';
        $endDate = '';
        $category = '';

        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataFaktur = 0;
        
        return View('page.accounting.tax.listDataTransaksiFaktur',
            compact('dataProject','dataFaktur','startDate','endDate','category'));
    }

    public function viewListTransaksiFaktur(Request $request){
        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        $startDate = $request->START_DATE;
        $endDate = $request->END_DATE;
        $category = $request->CATEGORY;

        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR','=',$project_no)->first();
        $dataFaktur = 1;

        $dataTransFaktur = DB::select("exec sp_tax_invoice '".$startDate."','".$endDate."','".$project_no."','".$category."'");

        return View('page.accounting.tax.listDataTransaksiFaktur',
            compact('dataProject','dataFaktur','dataTransFaktur','startDate','endDate','category'));
    }

    public function editTrxCodeFaktur(Request $request) {
        $project_no = session('current_project');

        // Nomor Faktur Tanpa Formatting
        // $FPNumber = $request->TRX_CODE_EDIT . '' . $request->INVOICE_NO_FAKTUR_EDIT;

        // Formatting Nomor Faktur
        $arrFPNumber = str_split($request->INVOICE_NO_FAKTUR_EDIT);
        $FPNumberFormatting = $request->TRX_CODE_EDIT . '.';
        for($i = 0; $i < count($arrFPNumber); $i++) {
            if($i == 2) {
                $FPNumberFormatting .= $arrFPNumber[$i] . '-';
            }
            else if($i == 4) {
                $FPNumberFormatting .= $arrFPNumber[$i] . '.';
            }
            else {
                $FPNumberFormatting .= $arrFPNumber[$i];
            }
        }

        try {
            \DB::beginTransaction();

            DB::table('INVOICE_TRANS')->where('INVOICE_TRANS_ID_INT', $request->INVOICE_ID_EDIT)->update([
                'INVOICE_FP_NOCHAR' => $FPNumberFormatting
            ]);

            \DB::commit();
        } catch (QueryException $ex) {
            \DB::rollback();
            return redirect()->route('accounting.tax.listtransaksifaktur')->with('error', 'Failed update data, errmsg : ' . $ex);
        }

        \Session::flash('success', 'Transaction Code has been updated...');
        return redirect()->route('accounting.tax.listtransaksifaktur');
    }

    public function exportDataFaktur(){
        $project_no = session('current_project');
        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $project_no)->first();
        $dataTaxTrans = \Request::all();

        $startDate = date_format(date_create($dataTaxTrans['startDate']),'dmy');
        $endDate = date_format(date_create($dataTaxTrans['endDate']),'dmy');

        $dateNow = Carbon::parse(Carbon::now(new \DateTimeZone('Asia/Jakarta')));

        if($dataTaxTrans['selectall'] == 'all')
        {
            $dataTransFaktur = DB::select("exec sp_tax_invoice '".$dataTaxTrans['startDate']."','".$dataTaxTrans['endDate']."','".$project_no."','".$dataTaxTrans['category']."'");

            $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
                ,   'Content-type'        => 'text/csv'
                ,   'Content-Disposition' => 'attachment; filename='.$dataProject['PROJECT_CODE'].''.$startDate.''.$endDate.'.csv'
                ,   'Expires'             => '0'
                ,   'Pragma'              => 'public'
            ];

            # add headers for each column in the CSV download
            $columns = array('FK', 'KD_JENIS_TRANSAKSI', 'FG_PENGGANTI', 'NOMOR_FAKTUR', 'MASA_PAJAK', 'TAHUN_PAJAK', 'TANGGAL_FAKTUR', 'NPWP', 'NAMA', 'ALAMAT_LENGKAP', 'JUMLAH_DPP', 'JUMLAH_PPN', 'JUMLAH_PPNBM', 'ID_KETERANGAN_TAMBAHAN', 'FG_UANG_MUKA', 'UANG_MUKA_DPP', 'UANG_MUKA_PPN', 'UANG_MUKA_PPNBM', 'REFERENSI','KODE_DOKUMEN_PENDUKUNG');
            $columns1 = array('LT', 'NPWP', 'NAMA', 'JALAN', 'BLOK', 'NOMOR', 'RT', 'RW', 'KECAMATAN', 'KELURAHAN', 'KABUPATEN', 'PROPINSI', 'KODE_POS', 'NOMOR_TELEPON');
            $columns2 = array('OF', 'KODE_OBJEK', 'NAMA', 'HARGA_SATUAN', 'JUMLAH_BARANG', 'HARGA_TOTAL', 'DISKON', 'DPP', 'PPN', 'TARIF_PPNBM', 'PPNBM');

            $callback = function() use ($dataTransFaktur, $columns, $columns1, $columns2)
            {
                $FH = fopen('php://output', 'w');
                fputcsv($FH, $columns);
                fputcsv($FH, $columns1);
                fputcsv($FH, $columns2);

                $jmlData = count($dataTransFaktur);

                $no = 1;

                for($i=0;$i<$jmlData;$i++)
                {
                    DB::table('INVOICE_TRANS')
                        ->where('INVOICE_TRANS_NOCHAR','=',$dataTransFaktur[$i]->INVOICE_TRANS_NOCHAR)
                        ->update([
                            'IS_EXPORT_FAKTUR'=>1
                        ]);

                    if ($i == 0)
                    {
                        if ($dataTransFaktur[$i]->INVOICE_TRANS_TYPE == 'UT')
                        {
                            fputcsv($FH, array('FK',$dataTransFaktur[$i]->KODE_PAJAK,$dataTransFaktur[$i]->PEMBETULAN_PAJAK,$dataTransFaktur[$i]->INVOICE_FP_NOCHAR,$dataTransFaktur[$i]->BULAN_FAKTUR,$dataTransFaktur[$i]->TAHUN_FAKTUR,$dataTransFaktur[$i]->TGL_SCHEDULE_DATE,$dataTransFaktur[$i]->MD_TENANT_NPWP,$dataTransFaktur[$i]->MD_TENANT_NAME_CHAR,$dataTransFaktur[$i]->MD_TENANT_ADDRESS_TAX,$dataTransFaktur[$i]->INVOICE_TRANS_DPP_HD,$dataTransFaktur[$i]->INVOICE_TRANS_PPN_HD,'0','','0','0','0','0','',''));
                            fputcsv($FH, array('OF','1',$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                            ++$no;
                        }
                        else
                        {
                            fputcsv($FH, array('FK',$dataTransFaktur[$i]->KODE_PAJAK,$dataTransFaktur[$i]->PEMBETULAN_PAJAK,$dataTransFaktur[$i]->INVOICE_FP_NOCHAR,$dataTransFaktur[$i]->BULAN_FAKTUR,$dataTransFaktur[$i]->TAHUN_FAKTUR,$dataTransFaktur[$i]->TGL_SCHEDULE_DATE,$dataTransFaktur[$i]->MD_TENANT_NPWP,$dataTransFaktur[$i]->MD_TENANT_NAME_CHAR,$dataTransFaktur[$i]->MD_TENANT_ADDRESS_TAX,$dataTransFaktur[$i]->INVOICE_TRANS_DPP_HD,$dataTransFaktur[$i]->INVOICE_TRANS_PPN_HD,'0','','0','0','0','0','',''));
                            fputcsv($FH, array('OF','1',$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                            $no = 1;
                        }
                    }
                    else
                    {
                        $j = $i-1;
                        if ($dataTransFaktur[$i]->INVOICE_TRANS_TYPE == 'UT' && $dataTransFaktur[$i]->INVOICE_TRANS_NOCHAR == $dataTransFaktur[$j]->INVOICE_TRANS_NOCHAR)
                        {
                            fputcsv($FH, array('OF',$no,$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                            ++$no;
                        }
                        else
                        {
                            fputcsv($FH, array('FK',$dataTransFaktur[$i]->KODE_PAJAK,$dataTransFaktur[$i]->PEMBETULAN_PAJAK,$dataTransFaktur[$i]->INVOICE_FP_NOCHAR,$dataTransFaktur[$i]->BULAN_FAKTUR,$dataTransFaktur[$i]->TAHUN_FAKTUR,$dataTransFaktur[$i]->TGL_SCHEDULE_DATE,$dataTransFaktur[$i]->MD_TENANT_NPWP,$dataTransFaktur[$i]->MD_TENANT_NAME_CHAR,$dataTransFaktur[$i]->MD_TENANT_ADDRESS_TAX,$dataTransFaktur[$i]->INVOICE_TRANS_DPP_HD,$dataTransFaktur[$i]->INVOICE_TRANS_PPN_HD,'0','','0','0','0','0','',''));
                            fputcsv($FH, array('OF','1',$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                            $no = 1;
                            ++$no;
                        }
                    }
                }
                fclose($FH);
            };

            $action = "GENERATE TRX FAKTUR";
            $description = 'Generate List Transaction Faktur '.$dataProject['PROJECT_CODE'].' Periode '.$startDate.' sd '.$endDate;
            $this->saveToLog($action, $description);

            return Response::stream($callback, 200, $headers);
        }
        else
        {
            if ($dataTaxTrans['billing'] == 0)
            {
                return redirect()->route('accounting.tax.listtransaksifaktur')->with('error','Please Choose Document First....');
            }

            $listIDInv = '';
            if (count($dataTaxTrans['billing']) > 0)
            {
                for($i=0;  $i < count($dataTaxTrans['billing']); $i++){
                    if ($i == (count($dataTaxTrans['billing']) - 1))
                    {
                        $listIDInv .= $dataTaxTrans['billing'][$i];
                    }
                    else
                    {
                        $listIDInv .= $dataTaxTrans['billing'][$i].",";
                    }
                }

                $dataTransFaktur = DB::select("exec sp_tax_invoice_withID '".$dataTaxTrans['startDate']."','".$dataTaxTrans['endDate']."','".$project_no."','".$listIDInv."','".$dataTaxTrans['category']."'");

                $headers = [
                    'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
                    ,   'Content-type'        => 'text/csv'
                    ,   'Content-Disposition' => 'attachment; filename='.$dataProject['PROJECT_CODE'].''.$startDate.''.$endDate.'.csv'
                    ,   'Expires'             => '0'
                    ,   'Pragma'              => 'public'
                ];

                # add headers for each column in the CSV download
                $columns = array('FK', 'KD_JENIS_TRANSAKSI', 'FG_PENGGANTI', 'NOMOR_FAKTUR', 'MASA_PAJAK', 'TAHUN_PAJAK', 'TANGGAL_FAKTUR', 'NPWP', 'NAMA', 'ALAMAT_LENGKAP', 'JUMLAH_DPP', 'JUMLAH_PPN', 'JUMLAH_PPNBM', 'ID_KETERANGAN_TAMBAHAN', 'FG_UANG_MUKA', 'UANG_MUKA_DPP', 'UANG_MUKA_PPN', 'UANG_MUKA_PPNBM', 'REFERENSI','KODE_DOKUMEN_PENDUKUNG');
                $columns1 = array('LT', 'NPWP', 'NAMA', 'JALAN', 'BLOK', 'NOMOR', 'RT', 'RW', 'KECAMATAN', 'KELURAHAN', 'KABUPATEN', 'PROPINSI', 'KODE_POS', 'NOMOR_TELEPON');
                $columns2 = array('OF', 'KODE_OBJEK', 'NAMA', 'HARGA_SATUAN', 'JUMLAH_BARANG', 'HARGA_TOTAL', 'DISKON', 'DPP', 'PPN', 'TARIF_PPNBM', 'PPNBM');

                $callback = function() use ($dataTransFaktur, $columns, $columns1, $columns2)
                {
                    $FH = fopen('php://output', 'w');
                    fputcsv($FH, $columns);
                    fputcsv($FH, $columns1);
                    fputcsv($FH, $columns2);

                    $jmlData = count($dataTransFaktur);

                    $no = 1;

                    for($i=0;$i<$jmlData;$i++)
                    {
                        DB::table('INVOICE_TRANS')
                            ->where('INVOICE_TRANS_NOCHAR','=',$dataTransFaktur[$i]->INVOICE_TRANS_NOCHAR)
                            ->update([
                                'IS_EXPORT_FAKTUR'=>1
                            ]);

                        if ($i == 0)
                        {
                            if ($dataTransFaktur[$i]->INVOICE_TRANS_TYPE == 'UT')
                            {
                                fputcsv($FH, array('FK',$dataTransFaktur[$i]->KODE_PAJAK,$dataTransFaktur[$i]->PEMBETULAN_PAJAK,$dataTransFaktur[$i]->INVOICE_FP_NOCHAR,$dataTransFaktur[$i]->BULAN_FAKTUR,$dataTransFaktur[$i]->TAHUN_FAKTUR,$dataTransFaktur[$i]->TGL_SCHEDULE_DATE,$dataTransFaktur[$i]->MD_TENANT_NPWP,$dataTransFaktur[$i]->MD_TENANT_NAME_CHAR,$dataTransFaktur[$i]->MD_TENANT_ADDRESS_TAX,$dataTransFaktur[$i]->INVOICE_TRANS_DPP_HD,$dataTransFaktur[$i]->INVOICE_TRANS_PPN_HD,'0','','0','0','0','0','',''));
                                fputcsv($FH, array('OF','1',$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                                ++$no;
                            }
                            else
                            {
                                fputcsv($FH, array('FK',$dataTransFaktur[$i]->KODE_PAJAK,$dataTransFaktur[$i]->PEMBETULAN_PAJAK,$dataTransFaktur[$i]->INVOICE_FP_NOCHAR,$dataTransFaktur[$i]->BULAN_FAKTUR,$dataTransFaktur[$i]->TAHUN_FAKTUR,$dataTransFaktur[$i]->TGL_SCHEDULE_DATE,$dataTransFaktur[$i]->MD_TENANT_NPWP,$dataTransFaktur[$i]->MD_TENANT_NAME_CHAR,$dataTransFaktur[$i]->MD_TENANT_ADDRESS_TAX,$dataTransFaktur[$i]->INVOICE_TRANS_DPP_HD,$dataTransFaktur[$i]->INVOICE_TRANS_PPN_HD,'0','','0','0','0','0','',''));
                                fputcsv($FH, array('OF','1',$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                                $no = 1;
                            }
                        }
                        else
                        {
                            $j = $i-1;
                            if ($dataTransFaktur[$i]->INVOICE_TRANS_TYPE == 'UT' && $dataTransFaktur[$i]->INVOICE_TRANS_NOCHAR == $dataTransFaktur[$j]->INVOICE_TRANS_NOCHAR)
                            {
                                fputcsv($FH, array('OF',$no,$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                                ++$no;
                            }
                            else
                            {
                                fputcsv($FH, array('FK',$dataTransFaktur[$i]->KODE_PAJAK,$dataTransFaktur[$i]->PEMBETULAN_PAJAK,$dataTransFaktur[$i]->INVOICE_FP_NOCHAR,$dataTransFaktur[$i]->BULAN_FAKTUR,$dataTransFaktur[$i]->TAHUN_FAKTUR,$dataTransFaktur[$i]->TGL_SCHEDULE_DATE,$dataTransFaktur[$i]->MD_TENANT_NPWP,$dataTransFaktur[$i]->MD_TENANT_NAME_CHAR,$dataTransFaktur[$i]->MD_TENANT_ADDRESS_TAX,$dataTransFaktur[$i]->INVOICE_TRANS_DPP_HD,$dataTransFaktur[$i]->INVOICE_TRANS_PPN_HD,'0','','0','0','0','0','',''));
                                fputcsv($FH, array('OF','1',$dataTransFaktur[$i]->INVOICE_TRANS_DESC_CHAR,$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'1',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,'0',$dataTransFaktur[$i]->INVOICE_TRANS_DPP,$dataTransFaktur[$i]->INVOICE_TRANS_PPN,'0','0','','','','','','','','',''));
                                $no = 1;
                                ++$no;
                            }
                        }
                    }
                    fclose($FH);
                };

                $action = "GENERATE TRX FAKTUR";
                $description = 'Generate List Transaction Faktur '.$dataProject['PROJECT_CODE'].' Periode '.$startDate.' sd '.$endDate;
                $this->saveToLog($action, $description);

                return Response::stream($callback, 200, $headers);
            }
        }
    }

//    public function exportDataFaktur(Request $request)
//    {
//        $dataEfHeader = Model\TaxEfHeaderModel::where('TAX_EF_HEADER_ID_INT', '=', $TAX_EF_HEADER_ID_INT)->first();
//        $dataEfDetail = Model\TaxEfDetailModel::where('TAX_EF_HEADER_NOCHAR', '=', $dataEfHeader['TAX_EF_HEADER_NOCHAR'])->get();
//        $dataEfDetail = $this->createFormatTanggal($dataEfDetail);
//
//        $dataProject = ProjectModel::where('PROJECT_NO_CHAR', '=', $dataEfHeader['PROJECT_NO_CHAR'])->first();
//
//        $headers = [
//            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
//            ,   'Content-type'        => 'text/csv'
//            ,   'Content-Disposition' => 'attachment; filename=eFaktur '.$dataProject['PROJECT_DESC'].' '.$dataEfHeader['TAX_EF_HEADER_MASA_PAJAK'].'-'.$dataEfHeader['TAX_EF_HEADER_TAHUN_PAJAK'].'.csv'
//            ,   'Expires'             => '0'
//            ,   'Pragma'              => 'public'
//        ];
//
//
//
//        //dd($dataEfDetail);
//        # add headers for each column in the CSV download
//        //array_unshift($list, array_keys($list[0]));
//        $columns = array('FK', 'KD_JENIS_TRANSAKSI', 'FG_PENGGANTI', 'NOMOR_FAKTUR', 'MASA_PAJAK', 'TAHUN_PAJAK', 'TANGGAL_FAKTUR', 'NPWP', 'NAMA', 'ALAMAT_LENGKAP', 'JUMLAH_DPP', 'JUMLAH_PPN', 'JUMLAH_PPNBM', 'ID_KETERANGAN_TAMBAHAN', 'FG_UANG_MUKA', 'UANG_MUKA_DPP', 'UANG_MUKA_PPN', 'UANG_MUKA_PPNBM', 'REFERENSI');
//        $columns1 = array('LT', 'NPWP', 'NAMA', 'JALAN', 'BLOK', 'NOMOR', 'RT', 'RW', 'KECAMATAN', 'KELURAHAN', 'KABUPATEN', 'PROPINSI', 'KODE_POS', 'NOMOR_TELEPON');
//        $columns2 = array('OF', 'KODE_OBJEK', 'NAMA', 'HARGA_SATUAN', 'JUMLAH_BARANG', 'HARGA_TOTAL', 'DISKON', 'DPP', 'PPN', 'TARIF_PPNBM', 'PPNBM');
//
//        $callback = function() use ($dataEfDetail, $columns, $columns1, $columns2)
//        {
//            $FH = fopen('php://output', 'w');
//            fputcsv($FH, $columns);
//            fputcsv($FH, $columns1);
//            fputcsv($FH, $columns2);
//
//            foreach ($dataEfDetail as $row) {
//                fputcsv($FH, array('FK',"1",'0',$row->TAX_NUMBER_CHAR,$row->TAX_EF_DETAIL_MONTH_FP,$row->TAX_EF_DETAIL_YEAR_FP,$row->TAX_EF_DETAIL_TGL_FP,$row->TAX_EF_DETAIL_NPWP_CHAR,$row->TAX_EF_DETAIL_NIK_CHAR.'#NIK#NAMA#'.$row->TAX_EF_DETAIL_CUST_NAME,$row->TAX_EF_DETAIL_ADDRESS,$row->TAX_EF_DETAIL_DPP,$row->TAX_EF_DETAIL_PPN,'0','','0','0','0','0',''));
//                fputcsv($FH, array('OF','',$row->TAX_EF_DETAIL_DESC,$row->TAX_EF_DETAIL_DPP,'1',$row->TAX_EF_DETAIL_DPP,'0',$row->TAX_EF_DETAIL_DPP,$row->TAX_EF_DETAIL_DPP,'0','0','','','','','','',''));
//            }
//            fclose($FH);
//        };
//
//        return Response::stream($callback, 200, $headers);
//    }

    public function createFormatTanggal($dataEfDetail) {
        //$counter = 1;
        foreach ($dataEfDetail as $EfDetail) {
            //array_add($bookingentry, 'COUNT_BARIS', $counter);
            $date = Carbon::parse($EfDetail['TAX_EF_DETAIL_TGL_FP']);
            //$EfDetail['TAX_EF_DETAIL_TGL_FP'] = Carbon::createFromFormat('d/m/Y',$date->format('d/m/Y'));
            $EfDetail['TAX_EF_DETAIL_TGL_FP'] = $date->format('d/m/Y');
            //dd($EfDetail['TAX_EF_DETAIL_TGL_FP']);
        }
        //dd($dataEfDetail);
        return $dataEfDetail;
    }

    public function saveToLog($action,$description){
        $userName = trim(session('first_name') . ' ' . session('last_name'));
        $submodule = 'List Transaction Faktur';
        $module = 'Tax';
        $by = $userName;
        $table = 'INVOICE_TRANS';

        $logActivity = new LogActivityController;
        $logActivity->createLog(array($action,$module,$submodule,$by,$table,$description));
    }
}
