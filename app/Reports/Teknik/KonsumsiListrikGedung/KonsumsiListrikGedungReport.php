<?php

namespace App\Reports\Teknik\KonsumsiListrikGedung;
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

class KonsumsiListrikGedungReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    function settings()
    {
        $host = env('DB_HOST2');
        $database = env('DB_DATABASE2');
        $username = env('DB_USERNAME2');
        $password = env('DB_PASSWORD2');
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
        $this->teknikkonsumsilistrikgedungtable1();
        $this->teknikkonsumsilistrikgedungchart1();
        $this->teknikkonsumsilistrikgedungchart2();
    }

    function teknikkonsumsilistrikgedungtable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
            SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM, SUM(a.NOMINAL_KWH_NUM) AS NOMINAL_KWH_NUM FROM HIST_LISTRIK AS a
            WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
            AND a.ID_TYPE_LISTRIK_INT = 1
            GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                'type' => 'string',
            ),
            "BULAN"=>array(
                'type' => 'string',
            ),
            "NOMINAL_RP_NUM"=>array(
                'type' => 'number',
            ),
            "NOMINAL_KWH_NUM"=>array(
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['NOMINAL_RP_NUM'] = $row['NOMINAL_RP_NUM'] / 1000000;
                $row['NOMINAL_KWH_NUM'] = $row['NOMINAL_KWH_NUM'] / 1000;
                $row['BULAN'] = sprintf("%02d", $row['BULAN']);
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"BULAN",
                "row"=>"TAHUN"
            ),
            "aggregates"=>array(
                "sum"=>"NOMINAL_RP_NUM, NOMINAL_KWH_NUM",
            )
        )))
        ->pipe($this->dataStore('teknik_konsumsi_listrik_gedung_table1'));
    }

    function teknikkonsumsilistrikgedungchart1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT BULAN AS BULAN, 
            [:year3], [:year2], [:year1]
            FROM
            (
            SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
            SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM FROM HIST_LISTRIK AS a
            WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
            AND a.ID_TYPE_LISTRIK_INT = 1
            GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
            ) AS SourceTable
            PIVOT
            (
            SUM(NOMINAL_RP_NUM)
            FOR TAHUN IN ([:year3], [:year2], [:year1])
            ) AS PivotTable;
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"],
            ":year1"=>(int) date('Y', strtotime($this->params["cut_off"])),
            ":year2"=>(int) (date('Y', strtotime($this->params["cut_off"]))-1),
            ":year3"=>(int) (date('Y', strtotime($this->params["cut_off"]))-2)
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $year1 = (int) date('Y', strtotime($this->params["cut_off"]));
                $year2 = (int) (date('Y', strtotime($this->params["cut_off"]))-1);
                $year3 = (int) (date('Y', strtotime($this->params["cut_off"]))-2);
                $row[$year1] = $row[$year1] / 1000000;
                $row[$year2] = $row[$year2] / 1000000;
                $row[$year3] = $row[$year3] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_konsumsi_listrik_gedung_chart1'));

        session(['konsumsiListrikGedungYear1Chart1' => (int) date('Y', strtotime($this->params["cut_off"]))]);
        session(['konsumsiListrikGedungYear2Chart1' => (int) (date('Y', strtotime($this->params["cut_off"]))-1)]);
        session(['konsumsiListrikGedungYear3Chart1' => (int) (date('Y', strtotime($this->params["cut_off"]))-2)]);
    }

    function teknikkonsumsilistrikgedungchart2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT BULAN AS BULAN, 
            [:year3], [:year2], [:year1]
            FROM
            (
            SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
            SUM(a.NOMINAL_KWH_NUM) AS NOMINAL_KWH_NUM FROM HIST_LISTRIK AS a
            WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
            AND a.ID_TYPE_LISTRIK_INT = 1
            GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
            ) AS SourceTable
            PIVOT
            (
            SUM(NOMINAL_KWH_NUM)
            FOR TAHUN IN ([:year3], [:year2], [:year1])
            ) AS PivotTable;
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"],
            ":year1"=>(int) date('Y', strtotime($this->params["cut_off"])),
            ":year2"=>(int) (date('Y', strtotime($this->params["cut_off"]))-1),
            ":year3"=>(int) (date('Y', strtotime($this->params["cut_off"]))-2)
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $year1 = (int) date('Y', strtotime($this->params["cut_off"]));
                $year2 = (int) (date('Y', strtotime($this->params["cut_off"]))-1);
                $year3 = (int) (date('Y', strtotime($this->params["cut_off"]))-2);
                $row[$year1] = $row[$year1] / 1000;
                $row[$year2] = $row[$year2] / 1000;
                $row[$year3] = $row[$year3] / 1000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_konsumsi_listrik_gedung_chart2'));

        session(['konsumsiListrikGedungYear1Chart2' => (int) date('Y', strtotime($this->params["cut_off"]))]);
        session(['konsumsiListrikGedungYear2Chart2' => (int) (date('Y', strtotime($this->params["cut_off"]))-1)]);
        session(['konsumsiListrikGedungYear3Chart2' => (int) (date('Y', strtotime($this->params["cut_off"]))-2)]);
    }
}