<?php

namespace App\Reports\FinanceAccounting\PosisiEscrowTahunBank;
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

class PosisiEscrowTahunBankReport extends \koolreport\KoolReport {
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
        $this->posisi_escrow_tahun_bank_table1();
        $this->posisi_escrow_tahun_bank_table2();
        $this->posisi_escrow_tahun_bank_excel_table1();
        $this->posisi_escrow_tahun_bank_excel_table2();
    }

    function posisi_escrow_tahun_bank_table1() {
        // $project_id = $this->params["project"];
        // $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_posisi_escrow_1 @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],            
            ":project"=>$this->params["project"]
            // ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                "label" => "TH ESCROW",
                "type" => "string",
            ),
            "SISA_BUILDING"=>array(
                "label" => "BUILDING",
            ),
            "SISA_CERTIFICATE"=>array(
                "label" => "CERTIFICATE",
            ),
            "TOTAL_NOMINAL"=>array(
                "label" => "Rp",
            ),
            "TOTAL_UNIT"=>array(
                "label" => "Unit",
            ),
            "RENCANA_PENCAIRAN_SELANJUTNYA"=>array(
                "label" => "RENCANA PENCAIRAN SELANJUTNYA",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['RENCANA_PENCAIRAN_SELANJUTNYA'] = "<p style='text-align: left;'>".$row['RENCANA_PENCAIRAN_SELANJUTNYA']."</p>";
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
        ->pipe($this->dataStore('finance_accounting_posisi_escrow_tahun_bank_table1'));
    }

    function posisi_escrow_tahun_bank_table2() {
        // $project_id = $this->params["project"];
        // $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_posisi_escrow_2 @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],            
            ":project"=>$this->params["project"]
            // ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "BANK"=>array(
                "label" => "BANK",
                "type" => "string",
            ),
            "SISA_BUILDING"=>array(
                "label" => "BUILDING",
            ),
            "SISA_CERTIFICATE"=>array(
                "label" => "CERTIFICATE",
            ),
            "TOTAL_NOMINAL"=>array(
                "label" => "Rp",
            ),
            "TOTAL_UNIT"=>array(
                "label" => "Unit",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
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
        ->pipe($this->dataStore('finance_accounting_posisi_escrow_tahun_bank_table2'));
    }

    function posisi_escrow_tahun_bank_excel_table1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_posisi_escrow_1 @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],            
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                "label" => "TH ESCROW",
                "type" => "string",
            ),
            "SISA_BUILDING"=>array(
                "label" => "BUILDING",
            ),
            "SISA_CERTIFICATE"=>array(
                "label" => "CERTIFICATE",
            ),
            "TOTAL_NOMINAL"=>array(
                "label" => "Rp",
            ),
            "TOTAL_UNIT"=>array(
                "label" => "Unit",
            ),
            "RENCANA_PENCAIRAN_SELANJUTNYA"=>array(
                "label" => "RENCANA PENCAIRAN SELANJUTNYA",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['RENCANA_PENCAIRAN_SELANJUTNYA'] = $row['RENCANA_PENCAIRAN_SELANJUTNYA'];
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_posisi_escrow_tahun_bank_excel_table1'));
    }

    function posisi_escrow_tahun_bank_excel_table2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_posisi_escrow_2 @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],            
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "BANK"=>array(
                "label" => "BANK",
                "type" => "string",
            ),
            "SISA_BUILDING"=>array(
                "label" => "BUILDING",
            ),
            "SISA_CERTIFICATE"=>array(
                "label" => "CERTIFICATE",
            ),
            "TOTAL_NOMINAL"=>array(
                "label" => "Rp",
            ),
            "TOTAL_UNIT"=>array(
                "label" => "Unit",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_posisi_escrow_tahun_bank_excel_table2'));
    }
}
