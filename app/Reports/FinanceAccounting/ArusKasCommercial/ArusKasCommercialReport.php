<?php

namespace App\Reports\FinanceAccounting\ArusKasCommercial;
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

class ArusKasCommercialReport extends \koolreport\KoolReport {
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
        $this->aruskascommercialtable1();
    }

    function aruskascommercialtable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT YEAR(b.TGL_BAYAR_DATE) AS TAHUN, c.INVOICE_TRANS_TYPE_DESC,
            SUM(ISNULL(b.PAID_BILL_AMOUNT, 0)) AS ACTUAL,
            ((SUM(ISNULL(b.PAID_BILL_AMOUNT, 0)) - LAG(SUM(ISNULL(b.PAID_BILL_AMOUNT, 0))) OVER (PARTITION BY c.INVOICE_TRANS_TYPE_DESC ORDER BY YEAR(b.TGL_BAYAR_DATE) ASC))/
            LAG(SUM(ISNULL(b.PAID_BILL_AMOUNT, 0))) OVER (PARTITION BY c.INVOICE_TRANS_TYPE_DESC ORDER BY YEAR(b.TGL_BAYAR_DATE) ASC))*100 AS growth_actual
            FROM INVOICE_TRANS AS a
            LEFT JOIN INVOICE_PAYMENT AS b ON b.INVOICE_TRANS_NOCHAR = a.INVOICE_TRANS_NOCHAR
            LEFT JOIN INVOICE_TRANS_TYPE AS c ON a.INVOICE_TRANS_TYPE = c.INVOICE_TRANS_TYPE
            WHERE b.TGL_BAYAR_DATE >= :start_date AND b.TGL_BAYAR_DATE <= :end_date
            AND a.PROJECT_NO_CHAR = :project AND b.INVOICE_PAYMENT_STATUS_INT = 2
            AND (a.INVOICE_TRANS_TYPE = 'SC' OR a.INVOICE_TRANS_TYPE = 'UT' OR a.INVOICE_TRANS_TYPE = 'OT')
            GROUP BY c.INVOICE_TRANS_TYPE_DESC, YEAR(b.TGL_BAYAR_DATE)
            UNION ALL
            SELECT YEAR(b.TGL_BAYAR_DATE) AS TAHUN, c.INVOICE_TRANS_TYPE_DESC,
            SUM(ISNULL(b.PAID_BILL_AMOUNT, 0)) AS ACTUAL,
            ((SUM(ISNULL(b.PAID_BILL_AMOUNT, 0)) - LAG(SUM(ISNULL(b.PAID_BILL_AMOUNT, 0))) OVER (PARTITION BY c.INVOICE_TRANS_TYPE_DESC ORDER BY YEAR(b.TGL_BAYAR_DATE) ASC))/
            LAG(SUM(ISNULL(b.PAID_BILL_AMOUNT, 0))) OVER (PARTITION BY c.INVOICE_TRANS_TYPE_DESC ORDER BY YEAR(b.TGL_BAYAR_DATE) ASC))*100 AS growth_actual
            FROM INVOICE_TRANS AS a
            LEFT JOIN INVOICE_PAYMENT AS b ON b.INVOICE_TRANS_NOCHAR = a.INVOICE_TRANS_NOCHAR
            LEFT JOIN INVOICE_TRANS_TYPE AS c ON a.INVOICE_TRANS_TYPE = c.INVOICE_TRANS_TYPE
            WHERE b.TGL_BAYAR_DATE >= :start_date AND b.TGL_BAYAR_DATE <= :end_date
            AND a.PROJECT_NO_CHAR = :project AND b.INVOICE_PAYMENT_STATUS_INT = 2
            AND (a.INVOICE_TRANS_TYPE = 'RT' OR a.INVOICE_TRANS_TYPE = 'RS' OR a.INVOICE_TRANS_TYPE = 'CL')
            GROUP BY c.INVOICE_TRANS_TYPE_DESC, YEAR(b.TGL_BAYAR_DATE)
        ")
        ->params(array(
            ":project"=>$this->params['project'],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                'type' => 'string',
                // "prefix" => "Rp. ",
            ),
            "INVOICE_TRANS_TYPE_DESC"=>array(
                'label'=> 'DESCRIPTION',
                // 'type' => 'string',
                // "prefix" => "Rp. ",
            ),
            "ACTUAL"=>array(
                'label'=> 'ACTUAL',
                // 'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "growth_actual"=>array(
                'label'=> 'GROWTH',
                // 'type' => 'number',
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['ACTUAL'] != "" || $row['ACTUAL'] != "NULL" || $row['ACTUAL'] != NULL) {
                    // Dalam Juta
                    $row['ACTUAL'] = $row['ACTUAL'] / 1000000;
                }
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"TAHUN",
                "row"=>"INVOICE_TRANS_TYPE_DESC"
            ),
            "aggregates"=>array(
                "sum"=>"ACTUAL, growth_actual",
            )
        )))
        ->pipe($this->dataStore('finance_accounting_arus_kas_commercial_table1'));
    }
}
