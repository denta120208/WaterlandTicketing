<?php

namespace App\Reports\FinanceAccounting\RescheduleCutiPembayaran;
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

class RescheduleCutiPembayaranReport extends \koolreport\KoolReport {
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
        $this->reschedule_cuti_pembayaran_table();
        $this->reschedule_cuti_pembayaran_table_excel();
        $this->reschedule_cuti_pembayaran_table_pdf();
    }

    function reschedule_cuti_pembayaran_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_sales_reschedule @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "TITLE"=>array(
                "label" => "DARI PENJUALAN",
            ),
            "UNIT_BEFORE"=>array(
                "label" => "UNIT",
            ),
            "PAID_BILL_AMOUNT_NUM_HIST"=>array(
                "label" => "COLLECTION RP",
            ),
            "UNIT_AFTER"=>array(
                "label" => "UNIT",
            ),
            "PAID_BILL_AMOUNT_NUM"=>array(
                "label" => "COLLECTION RP",
            ),
            "PERSEN"=>array(
                "label" => "%",
                "type" => "number",
                "suffix"=> "%",
            ),
            "DIFF_AMOUNT"=>array(
                "label" => "RP",
            ),
            "UNIT_REQ"=>array(
                "label" => "UNIT",
            ),
            "AMOUNT_REQ"=>array(
                "label" => "HARGA JUAL RP",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['TITLE'] = "<p style='text-align: left;'>".$row['TITLE']."</p>";
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
        ->pipe($this->dataStore('finance_accounting_reschedule_cuti_pembayaran_table'));
    }

    function reschedule_cuti_pembayaran_table_excel() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_sales_reschedule @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "TITLE"=>array(
                "label" => "DARI PENJUALAN",
            ),
            "UNIT_BEFORE"=>array(
                "label" => "UNIT",
            ),
            "PAID_BILL_AMOUNT_NUM_HIST"=>array(
                "label" => "COLLECTION RP (JUTA)",
            ),
            "UNIT_AFTER"=>array(
                "label" => "UNIT",
            ),
            "PAID_BILL_AMOUNT_NUM"=>array(
                "label" => "COLLECTION RP (JUTA)",
            ),
            "PERSEN"=>array(
                "label" => "%",
                "type" => "number",
                "suffix"=> "%",
            ),
            "DIFF_AMOUNT"=>array(
                "label" => "RP (JUTA)",
            ),
            "UNIT_REQ"=>array(
                "label" => "UNIT",
            ),
            "AMOUNT_REQ"=>array(
                "label" => "HARGA JUAL RP (JUTA)",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_reschedule_cuti_pembayaran_table_excel'));
    }

    function reschedule_cuti_pembayaran_table_pdf() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_sales_reschedule @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "TITLE"=>array(
                "label" => "DARI PENJUALAN",
            ),
            "UNIT_BEFORE"=>array(
                "label" => "UNIT",
            ),
            "PAID_BILL_AMOUNT_NUM_HIST"=>array(
                "label" => "COLLECTION RP (JUTA)",
            ),
            "UNIT_AFTER"=>array(
                "label" => "UNIT",
            ),
            "PAID_BILL_AMOUNT_NUM"=>array(
                "label" => "COLLECTION RP (JUTA)",
            ),
            "PERSEN"=>array(
                "label" => "%",
                "type" => "number",
                "suffix"=> "%",
            ),
            "DIFF_AMOUNT"=>array(
                "label" => "RP (JUTA)",
            ),
            "UNIT_REQ"=>array(
                "label" => "UNIT",
            ),
            "AMOUNT_REQ"=>array(
                "label" => "HARGA JUAL RP (JUTA)",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['TITLE'] = "<p style='text-align: left;'>".$row['TITLE']."</p>";
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_reschedule_cuti_pembayaran_table_pdf'));
    }
}
