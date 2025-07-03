<?php

namespace App\Reports\FinanceAccounting\PosisiMutasiEscrow;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use \koolreport\datagrid\DataTables;
use \koolreport\widgets\koolphp\Table;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class PosisiMutasiEscrowReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    function settings()
    {
        $host = env('DB_HOST');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        return array(
            "dataSources" => array(
                "sqlDataSources"=>array(
                    'host' => ''.$host.'',
                    'username' => ''.$username.'',
                    'password' => ''.$password.'',
                    'dbname' => ''.$database.'',
                    'class' => "\koolreport\datasources\SQLSRVDataSource"
                ), 
            )
        );
    }

    function setup()
    {
        $this->posisi_mutasi_escrow_table();
        $this->posisi_mutasi_escrow_excel_table();
    }

    function posisi_mutasi_escrow_table() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_piutang_sum_blokir_boc @rt_period = :cut_off, @project_no = :project, @bank_id = :bank_id, @bussines_id = :business_id
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],            
            ":project"=>$this->params["project"],
            ":bank_id"=>"0",
            ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "BANK_NAME_CHAR"=>array(
                "label" => "BANK",
                "type" => "string",
            ),
            "SISA_UPTO_LS"=>array(
                "label" => "Rp",
            ),
            "UNIT_UPTO_LS"=>array(
                "label" => "Unit",
            ),
            "SISA_UPTO"=>array(
                "label" => "Rp",
            ),
            "UNIT_UPTO"=>array(
                "label" => "Unit",
            ),
            "SA_REALISASI_AMOUNT_INT"=>array(
                "label" => "Rp",
            ),
            "UNIT_PAY"=>array(
                "label" => "Unit",
            ),
            "SISA_ESCROW"=>array(
                "label" => "Rp",
            ),
            "UNIT_ESCROW"=>array(
                "label" => "Unit",
            ),
            "BLOKIR_AMOUNT_INT"=>array(
                "label" => "Rp",
            ),
            "UNIT_PROYEKSI"=>array(
                "label" => "Unit",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['BANK_NAME_CHAR'] = "<p style='text-align: left;'>".$row['BANK_NAME_CHAR']."</p>";
                // $row['TITLE'] = "<p style='text-align: left;'>".$row['TITLE']."</p>";
                // if($row['TITLE'] == "TOTAL") {
                //     $row['TITLE'] = "<p><b>".$row['TITLE']."</b></p>";
                //     $row['COLLECTED_BACKWARD1'] = "<p><b>".number_format($row['COLLECTED_BACKWARD1'])."</b></p>";
                //     $row['COLLECTED_CURRENT'] = "<p><b>".number_format($row['COLLECTED_CURRENT'])."</b></p>";
                //     $row['TOTAL_AGING'] = "<p><b>".$row['TOTAL_AGING']."</b></p>";
                //     $row['TARGET_COLLECTED_CURRENT'] = "<p><b>".number_format($row['TARGET_COLLECTED_CURRENT'])."</b></p>";
                //     $row['COLLECTABILITY_CURRENT'] = "<p><b>".$row['COLLECTABILITY_CURRENT']."%</b></p>";
                // }
                // else {
                //     $row['TITLE'] = $row['TITLE'];
                //     $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                //     $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                //     $row['TOTAL_AGING'] = $row['TOTAL_AGING'];
                //     $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                //     $row['COLLECTABILITY_CURRENT'] = $row['COLLECTABILITY_CURRENT']."%";
                // }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_posisi_mutasi_escrow_table'));
    }
    
    function posisi_mutasi_escrow_excel_table() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_piutang_sum_blokir_boc @rt_period = :cut_off, @project_no = :project, @bank_id = :bank_id, @bussines_id = :business_id
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],            
            ":project"=>$this->params["project"],
            ":bank_id"=>"0",
            ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "BANK_NAME_CHAR"=>array(
                "label" => "BANK",
                "type" => "string",
            ),
            "SISA_UPTO_LS"=>array(
                "label" => "Rp",
            ),
            "UNIT_UPTO_LS"=>array(
                "label" => "Unit",
            ),
            "SISA_UPTO"=>array(
                "label" => "Rp",
            ),
            "UNIT_UPTO"=>array(
                "label" => "Unit",
            ),
            "SA_REALISASI_AMOUNT_INT"=>array(
                "label" => "Rp",
            ),
            "UNIT_PAY"=>array(
                "label" => "Unit",
            ),
            "SISA_ESCROW"=>array(
                "label" => "Rp",
            ),
            "UNIT_ESCROW"=>array(
                "label" => "Unit",
            ),
            "BLOKIR_AMOUNT_INT"=>array(
                "label" => "Rp",
            ),
            "UNIT_PROYEKSI"=>array(
                "label" => "Unit",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // $row['BANK_NAME_CHAR'] = "<p style='text-align: left;'>".$row['BANK_NAME_CHAR']."</p>";
                // $row['TITLE'] = "<p style='text-align: left;'>".$row['TITLE']."</p>";
                // if($row['TITLE'] == "TOTAL") {
                //     $row['TITLE'] = "<p><b>".$row['TITLE']."</b></p>";
                //     $row['COLLECTED_BACKWARD1'] = "<p><b>".number_format($row['COLLECTED_BACKWARD1'])."</b></p>";
                //     $row['COLLECTED_CURRENT'] = "<p><b>".number_format($row['COLLECTED_CURRENT'])."</b></p>";
                //     $row['TOTAL_AGING'] = "<p><b>".$row['TOTAL_AGING']."</b></p>";
                //     $row['TARGET_COLLECTED_CURRENT'] = "<p><b>".number_format($row['TARGET_COLLECTED_CURRENT'])."</b></p>";
                //     $row['COLLECTABILITY_CURRENT'] = "<p><b>".$row['COLLECTABILITY_CURRENT']."%</b></p>";
                // }
                // else {
                //     $row['TITLE'] = $row['TITLE'];
                //     $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                //     $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                //     $row['TOTAL_AGING'] = $row['TOTAL_AGING'];
                //     $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                //     $row['COLLECTABILITY_CURRENT'] = $row['COLLECTABILITY_CURRENT']."%";
                // }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_posisi_mutasi_escrow_excel_table'));
    }
}
