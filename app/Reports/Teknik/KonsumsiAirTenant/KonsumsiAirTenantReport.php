<?php

namespace App\Reports\Teknik\KonsumsiAirTenant;
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

class KonsumsiAirTenantReport extends \koolreport\KoolReport {
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
        $this->teknikkonsumsiairtenanttable1();
        $this->teknikkonsumsiairtenantchart1();
        $this->teknikkonsumsiairtenantchart2();
    }

    function teknikkonsumsiairtenanttable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            IF YEAR(:end_date) <= '2021'
            BEGIN
                SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
                SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM, SUM(a.NOMINAL_M3_NUM) AS NOMINAL_M3_NUM FROM HIST_AIR AS a
                WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
                AND a.ID_TYPE_AIR_INT = 2
                GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
            END
            ELSE
            BEGIN
                SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
                SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM, SUM(a.NOMINAL_M3_NUM) AS NOMINAL_M3_NUM FROM HIST_AIR AS a
                WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
                AND a.ID_TYPE_AIR_INT = 2
                GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
                UNION ALL
                SELECT YEAR(a.BILLING_DATE) AS TAHUN, MONTH(a.BILLING_DATE) AS BULAN,
                SUM(a.BILLING_AMOUNT_LWBP) AS NOMINAL_RP_NUM,
                SUM(a.BILLING_METER_LWBP_DIFF) AS NOMINAL_M3_NUM
                FROM UTILS_BILLING AS a
                WHERE a.PROJECT_NO_CHAR = :project AND a.BILLING_DATE >= '2022-01-01' AND a.BILLING_DATE <= :end_date
                AND a.BILLING_TYPE = 3
                AND a.BILLING_STATUS = 4
                GROUP BY YEAR(a.BILLING_DATE), MONTH(a.BILLING_DATE)
            END
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
            "NOMINAL_M3_NUM"=>array(
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['NOMINAL_RP_NUM'] = $row['NOMINAL_RP_NUM'] / 1000;
                // $row['NOMINAL_M3_NUM'] = $row['NOMINAL_M3_NUM'] / 1000;
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
                "sum"=>"NOMINAL_RP_NUM, NOMINAL_M3_NUM",
            )
        )))
        ->pipe($this->dataStore('teknik_konsumsi_air_tenant_table1'));
    }

    function teknikkonsumsiairtenantchart1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            IF YEAR(:end_date) <= '2021'
            BEGIN
                SELECT BULAN AS BULAN, 
                [:year3], [:year2], [:year1]
                FROM
                (
                    SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
                    SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM FROM HIST_AIR AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
                    AND a.ID_TYPE_AIR_INT = 2
                    GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
                ) AS SourceTable
                PIVOT
                (
                SUM(NOMINAL_RP_NUM)
                FOR TAHUN IN ([:year3], [:year2], [:year1])
                ) AS PivotTable;
            END
            ELSE
            BEGIN
                SELECT BULAN AS BULAN, 
                [:year3], [:year2], [:year1]
                FROM
                (
                    SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
                    SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM FROM HIST_AIR AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
                    AND a.ID_TYPE_AIR_INT = 2
                    GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
                    UNION ALL
                    SELECT YEAR(a.BILLING_DATE) AS TAHUN, MONTH(a.BILLING_DATE) AS BULAN,
                    SUM(a.BILLING_AMOUNT_LWBP) AS NOMINAL_RP_NUM
                    FROM UTILS_BILLING AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND a.BILLING_DATE >= '2022-01-01' AND a.BILLING_DATE <= :end_date
                    AND a.BILLING_TYPE = 3
                    AND a.BILLING_STATUS = 4
                    GROUP BY YEAR(a.BILLING_DATE), MONTH(a.BILLING_DATE)
                ) AS SourceTable
                PIVOT
                (
                SUM(NOMINAL_RP_NUM)
                FOR TAHUN IN ([:year3], [:year2], [:year1])
                ) AS PivotTable;
            END
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
        ->pipe($this->dataStore('teknik_konsumsi_air_tenant_chart1'));

        session(['konsumsiAirTenantYear1Chart1' => (int) date('Y', strtotime($this->params["cut_off"]))]);
        session(['konsumsiAirTenantYear2Chart1' => (int) (date('Y', strtotime($this->params["cut_off"]))-1)]);
        session(['konsumsiAirTenantYear3Chart1' => (int) (date('Y', strtotime($this->params["cut_off"]))-2)]);
    }

    function teknikkonsumsiairtenantchart2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            IF YEAR(:end_date) <= '2021'
            BEGIN
                SELECT BULAN AS BULAN, 
                [:year3], [:year2], [:year1]
                FROM
                (
                    SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
                    SUM(a.NOMINAL_M3_NUM) AS NOMINAL_M3_NUM FROM HIST_AIR AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
                    AND a.ID_TYPE_AIR_INT = 2
                    GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
                ) AS SourceTable
                PIVOT
                (
                SUM(NOMINAL_M3_NUM)
                FOR TAHUN IN ([:year3], [:year2], [:year1])
                ) AS PivotTable;
            END
            ELSE
            BEGIN
                SELECT BULAN AS BULAN, 
                [:year3], [:year2], [:year1]
                FROM
                (
                    SELECT YEAR(a.PERIODE_DATE) AS TAHUN, MONTH(a.PERIODE_DATE) AS BULAN,
                    SUM(a.NOMINAL_M3_NUM) AS NOMINAL_M3_NUM FROM HIST_AIR AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND a.PERIODE_DATE >= :start_date AND a.PERIODE_DATE <= :end_date
                    AND a.ID_TYPE_AIR_INT = 2
                    GROUP BY YEAR(a.PERIODE_DATE), MONTH(a.PERIODE_DATE)
                    UNION ALL
                    SELECT YEAR(a.BILLING_DATE) AS TAHUN, MONTH(a.BILLING_DATE) AS BULAN,
                    SUM(a.BILLING_METER_LWBP_DIFF) AS NOMINAL_M3_NUM
                    FROM UTILS_BILLING AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND a.BILLING_DATE >= '2022-01-01' AND a.BILLING_DATE <= :end_date
                    AND a.BILLING_TYPE = 3
                    AND a.BILLING_STATUS = 4
                    GROUP BY YEAR(a.BILLING_DATE), MONTH(a.BILLING_DATE)
                ) AS SourceTable
                PIVOT
                (
                SUM(NOMINAL_M3_NUM)
                FOR TAHUN IN ([:year3], [:year2], [:year1])
                ) AS PivotTable;
            END
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
                $row[$year1] = $row[$year1] / 1;
                $row[$year2] = $row[$year2] / 1;
                $row[$year3] = $row[$year3] / 1;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_konsumsi_air_tenant_chart2'));

        session(['konsumsiAirTenantYear1Chart2' => (int) date('Y', strtotime($this->params["cut_off"]))]);
        session(['konsumsiAirTenantYear2Chart2' => (int) (date('Y', strtotime($this->params["cut_off"]))-1)]);
        session(['konsumsiAirTenantYear3Chart2' => (int) (date('Y', strtotime($this->params["cut_off"]))-2)]);
    }
}