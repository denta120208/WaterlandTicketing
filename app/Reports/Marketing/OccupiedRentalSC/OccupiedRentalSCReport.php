<?php

namespace App\Reports\Marketing\OccupiedRentalSC;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class OccupiedRentalSCReport extends \koolreport\KoolReport {
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
        $this->occupiedrentalsctable1();
        $this->occupiedrentalsctable2();
    }

    function occupiedrentalsctable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT DISTINCT YEAR(b.PSM_TRANS_BOOKING_DATE) AS TAHUN,
            'Anchor Tenant (>= 500m)' AS [KATEGORI],
            ISNULL(SUM(c.LOT_STOCK_SQM), 0) AS LEASED_AREA,
            ISNULL(SUM(b.PSM_TRANS_NET_BEFORE_TAX), 0) AS RENTAL_REVENUE,
            (ISNULL(SUM(b.PSM_TRANS_NET_BEFORE_TAX), 0) / ISNULL(SUM(c.LOT_STOCK_SQM), 0)) AS ARR
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM >= 500 AND
            b.PROJECT_NO_CHAR = :project AND
            b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date AND
            c.ON_RENT_STAT_INT = 1
            GROUP BY YEAR(b.PSM_TRANS_BOOKING_DATE)
            UNION ALL
            SELECT DISTINCT YEAR(b.PSM_TRANS_BOOKING_DATE) AS TAHUN,
            'Non Anchor Tenant (< 500m)' AS [KATEGORI],
            ISNULL(SUM(c.LOT_STOCK_SQM), 0) AS LEASED_AREA,
            ISNULL(SUM(b.PSM_TRANS_NET_BEFORE_TAX), 0) AS RENTAL_REVENUE,
            (ISNULL(SUM(b.PSM_TRANS_NET_BEFORE_TAX), 0) / ISNULL(SUM(c.LOT_STOCK_SQM), 0)) AS ARR
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM < 500 AND
            b.PROJECT_NO_CHAR = :project AND
            b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date AND
            c.ON_RENT_STAT_INT = 1
            GROUP BY YEAR(b.PSM_TRANS_BOOKING_DATE)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                'type' => 'string',                
            )
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
                $row['RENTAL_REVENUE'] = $row['RENTAL_REVENUE'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"TAHUN",
                "row"=>"KATEGORI"
            ),
            "aggregates"=>array(
                "sum"=>"LEASED_AREA, RENTAL_REVENUE, ARR",
            )
        )))
        ->pipe($this->dataStore('marketing_occupied_rental_sc_table1'));
    }

    function occupiedrentalsctable2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT DISTINCT YEAR(b.PSM_TRANS_BOOKING_DATE) AS TAHUN,
            'Anchor Tenant (>= 500m)' AS [KATEGORI],
            ISNULL(SUM(c.LOT_STOCK_SQM_SC), 0) AS LEASED_AREA,
            ISNULL(SUM(b.PSM_TRANS_SC_NUM * c.LOT_STOCK_SQM_SC), 0) AS SC_REVENUE,
            (ISNULL(SUM(b.PSM_TRANS_SC_NUM * c.LOT_STOCK_SQM_SC), 0) / ISNULL(SUM(c.LOT_STOCK_SQM_SC), 0)) AS ARR
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM_SC >= 500 AND
            b.PROJECT_NO_CHAR = :project AND
            b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date AND
            c.ON_RENT_STAT_INT = 1
            GROUP BY YEAR(b.PSM_TRANS_BOOKING_DATE)
            UNION ALL
            SELECT DISTINCT YEAR(b.PSM_TRANS_BOOKING_DATE) AS TAHUN,
            'Non Anchor Tenant (< 500m)' AS [KATEGORI],
            ISNULL(SUM(c.LOT_STOCK_SQM_SC), 0) AS LEASED_AREA,
            ISNULL(SUM(b.PSM_TRANS_SC_NUM * c.LOT_STOCK_SQM_SC), 0) AS SC_REVENUE,
            (ISNULL(SUM(b.PSM_TRANS_SC_NUM * c.LOT_STOCK_SQM_SC), 0) / ISNULL(SUM(c.LOT_STOCK_SQM_SC), 0)) AS ARR
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM_SC < 500 AND
            b.PROJECT_NO_CHAR = :project AND
            b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date AND
            c.ON_RENT_STAT_INT = 1
            GROUP BY YEAR(b.PSM_TRANS_BOOKING_DATE)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                'type' => 'string',                
            )
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
                $row['SC_REVENUE'] = $row['SC_REVENUE'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"TAHUN",
                "row"=>"KATEGORI"
            ),
            "aggregates"=>array(
                "sum"=>"LEASED_AREA, SC_REVENUE, ARR",
            )
        )))
        ->pipe($this->dataStore('marketing_occupied_rental_sc_table2'));
    }
}
