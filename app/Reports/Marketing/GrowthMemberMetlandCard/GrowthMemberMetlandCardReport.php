<?php

namespace App\Reports\Marketing\GrowthMemberMetlandCard;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use \koolreport\processes\Transpose;
use \koolreport\processes\Transpose2;
use \koolreport\processes\ColumnRename;
use \koolreport\processes\Group;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class GrowthMemberMetlandCardReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    function settings()
    {
        $host = env('DB_HOST3');
        $database = env('DB_DATABASE3');
        $username = env('DB_USERNAME3');
        $password = env('DB_PASSWORD3');
        return array(
            "dataSources" => array(
                "sqlDataSources"=>array(
                    'host' => ''.$host.'',
                    'username' => ''.$username.'',
                    'password' => ''.$password.'',
                    'dbname' => ''.$database.'',
                    'class' => "\koolreport\datasources\SQLSRVDataSource"
                ),
                "arrDataSourcesChart1"=>array(
                    "class"=>'\koolreport\datasources\ArrayDataSource',
                    "data"=>$this->marketinggrowthmembermetlandcardrawdatachart1(),
                    "dataFormat"=>"associate",
                ),
            )
        );
    }

    function setup()
    {
        $this->marketinggrowthmembermetlandcardtable1();
        $this->marketinggrowthmembermetlandcardtable2();
        $this->marketinggrowthmembermetlandcardchart1();
        $this->marketinggrowthmembermetlandcardexcelchart1();
    }

    function marketinggrowthmembermetlandcardtable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT
            CASE
                WHEN b.code = 1803 AND b.seq NOT IN (01) THEN 'Residential'
                ELSE b.[name]
            END as project, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            WHERE a.[status] = 1 AND b.id IN (1,2,10,7,8,13,14,15,16,17,18,19,20) AND a.created_at <= :end_date
            GROUP BY b.[name], b.code, b.seq
        ")
        ->params(array(
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
            '{meta}' => function($meta) {
                return $meta;
            }
        )))
        ->pipe(new ColumnMeta(array(
            "project"=>array(
                'label' => '',
                "footerText"=>"TOTAL"
            ),
            "JUMLAH_MEMBER"=>array(
                'label' => 'MEMBER',
                "footer"=>"sum",
                "footerText"=>"@value",
            ),
        )))
        ->pipe($this->dataStore('marketing_growth_member_metland_card_table1'));
    }

    function marketinggrowthmembermetlandcardtable2() {
        $currentProject = $this->params['project'];
        $dataProject = DB::select("SELECT * FROM MTLA_MALL.dbo.MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = $currentProject");
        $prefixDebtor = $dataProject[0]->PREFIX_DEBTOR;
        $dataProject = DB::select("SELECT * FROM ml_Loyalty.dbo.project AS a WHERE a.project_code = '$prefixDebtor'");

        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT DATEPART(MONTH, a.created_at) AS bulan, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            WHERE a.[status] = 1 AND b.id = :project AND
            a.created_at >= :start_date AND a.created_at <= :end_date
            GROUP BY DATEPART(MONTH, a.created_at)
        ")
        ->params(array(
            ":project"=>$dataProject[0]->id,
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
            '{meta}' => function($meta) {
                return $meta;
            }
        )))
        ->pipe(new ColumnMeta(array(
            "bulan"=>array(
                'label' => 'BULAN',
                "footerText"=>"TOTAL"
            ),
            "JUMLAH_MEMBER"=>array(
                'label' => 'JUMLAH',
                "footer"=>"sum",
                "footerText"=>"@value",
            ),
        )))
        ->pipe($this->dataStore('marketing_growth_member_metland_card_table2'));
    }

    function marketinggrowthmembermetlandcardchart1() {
        $start_date = $this->params["start_date"];
        $cut_off = $this->params["cut_off"];

        $dataRawChart = DB::select("SELECT DATEPART(MONTH, a.created_at) AS bulan,
        CASE
            WHEN b.code = 1802 THEN 'Hotel'
            WHEN b.code = 1803 AND b.seq NOT IN (01) THEN 'Residential'
            ELSE b.[name]
        END as project, COUNT(a.id) AS JUMLAH_MEMBER FROM ml_Loyalty.dbo.member AS a
        LEFT JOIN ml_Loyalty.dbo.project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
        WHERE a.[status] = 1 AND b.id IN (1,2,10,7,8,13,14,15,16,17,18,19,20,21)
        AND a.created_at >= '".$start_date."' AND a.created_at <= '".$cut_off."'
        GROUP BY b.[name], b.code, b.seq, DATEPART(MONTH, a.created_at)");

        $dataChart = array();
        $dataChartTemp = array();
        foreach($dataRawChart as $data) {
            if(array_key_exists($data->bulan, $dataChartTemp)) {
                $dataChartTemp[$data->bulan][0][strval($data->project)] = $data->JUMLAH_MEMBER;
            }
            else {
                $dataChartTemp[$data->bulan][] = array("bulan"=>$data->bulan, strval($data->project)=>$data->JUMLAH_MEMBER);
            }
        }

        foreach($dataRawChart as $data) {
            if(!array_key_exists('Residential', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Residential'] = 0;
            }
            if(!array_key_exists('EVENT', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['EVENT'] = 0;
            }
            if(!array_key_exists('GrandMetropolitan Mall', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['GrandMetropolitan Mall'] = 0;
            }
            if(!array_key_exists('Head Office', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Head Office'] = 0;
            }
            if(!array_key_exists('Hotel', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Hotel'] = 0;
            }
            if(!array_key_exists('Mall Metropolitan Cileungsi', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Mall Metropolitan Cileungsi'] = 0;
            }
            if(!array_key_exists('Metropolitan Mall', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Metropolitan Mall'] = 0;
            }
        }

        foreach($dataChartTemp as $data) {
            $dataChart[] = $data[0];
        }

        session(['growth_member_metland_card_chart1' => $dataChart]);
    }

    function marketinggrowthmembermetlandcardexcelchart1() {
        $this->src('arrDataSourcesChart1')
        ->pipe($this->dataStore('growth_member_metland_card_excel_chart1'));
    }

    function marketinggrowthmembermetlandcardrawdatachart1() {
        $start_date = $this->params["start_date"];
        $cut_off = $this->params["cut_off"];

        $dataRawChart = DB::select("SELECT DATEPART(MONTH, a.created_at) AS bulan,
        CASE
            WHEN b.code = 1802 THEN 'Hotel'
            WHEN b.code = 1803 AND b.seq NOT IN (01) THEN 'Residential'
            ELSE b.[name]
        END as project, COUNT(a.id) AS JUMLAH_MEMBER FROM ml_Loyalty.dbo.member AS a
        LEFT JOIN ml_Loyalty.dbo.project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
        WHERE a.[status] = 1 AND b.id IN (1,2,10,7,8,13,14,15,16,17,18,19,20,21)
        AND a.created_at >= '".$start_date."' AND a.created_at <= '".$cut_off."'
        GROUP BY b.[name], b.code, b.seq, DATEPART(MONTH, a.created_at)");

        $dataChart = array();
        $dataChartTemp = array();
        foreach($dataRawChart as $data) {
            if(array_key_exists($data->bulan, $dataChartTemp)) {
                $dataChartTemp[$data->bulan][0][strval($data->project)] = $data->JUMLAH_MEMBER;
            }
            else {
                $dataChartTemp[$data->bulan][] = array("bulan"=>$data->bulan, strval($data->project)=>$data->JUMLAH_MEMBER);
            }
        }

        foreach($dataRawChart as $data) {
            if(!array_key_exists('Residential', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Residential'] = 0;
            }
            if(!array_key_exists('EVENT', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['EVENT'] = 0;
            }
            if(!array_key_exists('GrandMetropolitan Mall', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['GrandMetropolitan Mall'] = 0;
            }
            if(!array_key_exists('Head Office', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Head Office'] = 0;
            }
            if(!array_key_exists('Hotel', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Hotel'] = 0;
            }
            if(!array_key_exists('Mall Metropolitan Cileungsi', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Mall Metropolitan Cileungsi'] = 0;
            }
            if(!array_key_exists('Metropolitan Mall', $dataChartTemp[$data->bulan][0])) {
                $dataChartTemp[$data->bulan][0]['Metropolitan Mall'] = 0;
            }
        }

        foreach($dataChartTemp as $data) {
            $dataChart[] = $data[0];
        }

        return $dataChart;
    }
}