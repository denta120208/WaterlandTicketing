<?php

namespace App\Reports\Marketing\OccupiedTenantRentalPeriod;
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

class OccupiedTenantRentalPeriodReport extends \koolreport\KoolReport {
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
        $dataRawPersen = $this->getRawPersenTenantRentalPeriodTable1();
        $this->occupiedtenantrentalperiodtable1($dataRawPersen);
    }

    function occupiedtenantrentalperiodtable1($dataRawPersen) {
        $this->params['dataRawPersen'] = $dataRawPersen;

        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT '< 3 TAHUN' AS [PERIODE_SEWA], COUNT(a.MD_TENANT_ID_INT) AS [JUMLAH_TENANT],
            ISNULL(SUM(c.LOT_STOCK_SQM), 0) AS LEASED_AREA
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE b.PROJECT_NO_CHAR = :project
            AND b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date
            AND c.ON_RENT_STAT_INT = 1
            AND DATEDIFF(DAY, b.PSM_TRANS_START_DATE, b.PSM_TRANS_END_DATE) < 1095
            UNION ALL
            SELECT '3 TAHUN' AS [PERIODE_SEWA], COUNT(a.MD_TENANT_ID_INT) AS [JUMLAH_TENANT],
            ISNULL(SUM(c.LOT_STOCK_SQM), 0) AS LEASED_AREA
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE b.PROJECT_NO_CHAR = :project
            AND b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date
            AND c.ON_RENT_STAT_INT = 1
            AND DATEDIFF(DAY, b.PSM_TRANS_START_DATE, b.PSM_TRANS_END_DATE) = 1095
            UNION ALL
            SELECT '5 TAHUN' AS [PERIODE_SEWA], COUNT(a.MD_TENANT_ID_INT) AS [JUMLAH_TENANT],
            ISNULL(SUM(c.LOT_STOCK_SQM), 0) AS LEASED_AREA
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE b.PROJECT_NO_CHAR = :project
            AND b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date
            AND c.ON_RENT_STAT_INT = 1
            AND DATEDIFF(DAY, b.PSM_TRANS_START_DATE, b.PSM_TRANS_END_DATE) = 1825
            UNION ALL
            SELECT '> 5 TAHUN' AS [PERIODE_SEWA], COUNT(a.MD_TENANT_ID_INT) AS [JUMLAH_TENANT],
            ISNULL(SUM(c.LOT_STOCK_SQM), 0) AS LEASED_AREA
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE b.PROJECT_NO_CHAR = :project
            AND b.PSM_TRANS_BOOKING_DATE <= :end_date AND b.PSM_TRANS_BOOKING_DATE >= :start_date
            AND c.ON_RENT_STAT_INT = 1
            AND DATEDIFF(DAY, b.PSM_TRANS_START_DATE, b.PSM_TRANS_END_DATE) > 1825
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new CalculatedColumn(array(
            "total_tenant_rental_period_percent"=>function($row){
                if($this->params['dataRawPersen'] == 0) {
                    $persen = round((($row["LEASED_AREA"] / 1) * 100), 2);
                } else {
                    $persen = round((($row["LEASED_AREA"] / $this->params['dataRawPersen']) * 100), 2);
                }
                return $persen;
            }
        )))
        ->pipe(new ColumnMeta(array(
            "PERIODE_SEWA"=>array(
                "label" => "PERIODE SEWA",
                'type' => 'string',
                "footerText"=>"TOTAL"
            ),
            "JUMLAH_TENANT"=>array(
                "label" => "JUMLAH TENANT",
                "footer"=>"sum",
                "footerText"=>"@value"
            ),
            "LEASED_AREA"=>array(
                "label" => "LEASED AREA (m2)",
                "footer"=>"sum",
                "footerText"=>"@value"
            ),
            "total_tenant_rental_period_percent"=>array(
                "label" => "%",
                "suffix" => "%",
                "footer"=>"sum",
                "footerText"=>"@value"
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
                // $row['RENTAL_REVENUE'] = $row['RENTAL_REVENUE'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('marketing_occupied_tenant_rental_period_table1'));
    }

    function getRawPersenTenantRentalPeriodTable1() {
        // Untuk perhitungan persen
        $dataPersen = DB::select("SELECT ISNULL(SUM(c.LOT_STOCK_SQM), 0) AS TOTAL_LEASED_AREA
        FROM MTLA_MALL.dbo.MD_TENANT AS a
        LEFT JOIN MTLA_MALL.dbo.PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
        LEFT JOIN MTLA_MALL.dbo.LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
        WHERE b.PROJECT_NO_CHAR = '".$this->params["project"]."'
        AND b.PSM_TRANS_BOOKING_DATE <= '".$this->params["cut_off"]."' AND b.PSM_TRANS_BOOKING_DATE >= '".$this->params["start_date"]."'
        AND c.ON_RENT_STAT_INT = 1
        AND (DATEDIFF(DAY, b.PSM_TRANS_START_DATE, b.PSM_TRANS_END_DATE) <= 1095
        OR DATEDIFF(DAY, b.PSM_TRANS_START_DATE, b.PSM_TRANS_END_DATE) >= 1825)");

        return $dataPersen[0]->TOTAL_LEASED_AREA;
    }
}
