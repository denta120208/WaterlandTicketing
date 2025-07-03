<?php

namespace App\Reports\Legal\IMBSertifikat;
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

class IMBSertifikatReport extends \koolreport\KoolReport {
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
        $this->imb_sertifikat_table1();
        $this->imb_sertifikat_table2();
    }

    function imb_sertifikat_table1() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_sales_imb @rt_period = :cut_off, @project_no = :project, @business_id = :business_id
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
            "IMB"=>array(
                "label" => "SUDAH IMB",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>"
            ),
            "blm_IMB"=>array(
                "label" => "BELUM IMB",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>"
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('legal_imb_sertifikat_table1'));
    }

    function imb_sertifikat_table2() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_sales_sertifikat @rt_period = :cut_off, @project_no = :project, @business_id = :business_id
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
            "sudah_sertifikat"=>array(
                "label" => "SUDAH SERTIFIKAT",
                'type' => 'number',
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "blm_sertifikat"=>array(
                "label" => "BELUM SERTIFIKAT",
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
        ->pipe($this->dataStore('legal_imb_sertifikat_table2'));
    }
}
