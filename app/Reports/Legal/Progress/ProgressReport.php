<?php

namespace App\Reports\Legal\Progress;
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

class ProgressReport extends \koolreport\KoolReport {
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
        $this->progress_table1();
        $this->progress_table2();
    }

    function progress_table1() {
        // $project_id = $this->params["project"];
        // $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            select a.PROJECT_NO_CHAR,
            b.UNIT_TYPE,
            c.DESC_SALESTYPE_CHAR
            ,COUNT(CASE WHEN a.AJB_NO_CHAR !='' then 1 ELSE NULL END) as sudah_ajb,
            COUNT(CASE WHEN a.AJB_NO_CHAR is null or a.AJB_NO_CHAR = '' then 1 ELSE NULL END) as blm_ajb,
            COUNT(CASE WHEN (a.AJB_NO_CHAR is null or a.AJB_NO_CHAR = '') and a.IS_FULL_PAID=1 then 1 ELSE NULL END) as blm_ajb_lunas,
            COUNT(CASE WHEN (a.AJB_NO_CHAR is null or a.AJB_NO_CHAR = '') and a.IS_FULL_PAID=0 then 1 ELSE NULL END) as blm_ajb_blm_lunas
            from SA_BOOKINGENTRY a inner join MD_STOCK b
            on a.KODE_STOK_UNIQUE_ID_CHAR=b.NOUNIT_CHAR
            inner join MD_SALESTYPE c on a.SALES_TYPE_CHAR=c.ID_SALESTYPE_INT
            where a.PROJECT_NO_CHAR=:project
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by a.PROJECT_NO_CHAR,c.DESC_SALESTYPE_CHAR,b.UNIT_TYPE
        ")
        ->params(array(
            ":project"=>$this->params["project"],
        ))
        ->pipe(new ColumnMeta(array(
            "UNIT_TYPE"=>array(
                "label" => "TIPE UNIT",
                'type' => 'string',
                "footerText"=>"<p><b>TOTAL</b></p>"
            ),
            "DESC_SALESTYPE_CHAR"=>array(
                "label" => "METODE PEMBAYARAN",
                "footerText"=>"-"
            ),
            "sudah_ajb"=>array(
                "label" => "SUDAH AJB",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>"
            ),
            "blm_ajb"=>array(
                "label" => "BELUM AJB",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>"
            ),
            "blm_ajb_lunas"=>array(
                "label" => "BELUM AJB SUDAH LUNAS",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>"
            ),
            "blm_ajb_blm_lunas"=>array(
                "label" => "BELUM AJB BELUM LUNAS",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>"
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                if($row['UNIT_TYPE'] == "KAVL") {
                    $row['UNIT_TYPE'] = "KAVLING";
                }
                else if($row['UNIT_TYPE'] == "KAVC") {
                    $row['UNIT_TYPE'] = "KAVLING COMMERCIAL";
                }
                else if($row['UNIT_TYPE'] == "RKN") {
                    $row['UNIT_TYPE'] = "RUKO";
                }
                else if($row['UNIT_TYPE'] == "RMH") {
                    $row['UNIT_TYPE'] = "RUMAH";
                }
                else {
                    $row['UNIT_TYPE'] = "RUMAH";
                }
                // $row['UNIT_TYPE'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('legal_progress_table1'));
    }

    function progress_table2() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_sales_ajb @rt_period = :cut_off, @project_no = :project, @business_id = :business_id
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "tahun"=>array(
                "label" => "TAHUN",
                'type' => 'string',
                "footerText"=>"<p><b>TOTAL</b></p>"
            ),
            "ajb"=>array(
                "label" => "SUDAH AJB",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "blm_ajb"=>array(
                "label" => "BELUM AJB",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('legal_progress_table2'));
    }
}
