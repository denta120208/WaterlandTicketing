<?php

namespace App\Http\Controllers\Rental\Report\EquipmentReport;
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

require_once dirname(__FILE__)."/../../../../../../vendor/koolreport/core/autoload.php";

class EquipmentReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;
    use \koolreport\cloudexport\Exportable;

    public $project_param = NULL;
    public $start_date_param = NULL;
    public $end_date_param = NULL;

    function settings()
    {
        $host = env('DB_HOST');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        return array(
            "dataSources"=>array(
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
        $this->project_param = $this->params["project"];
        $this->start_date_param = $this->params["start_date_param"];
        $this->end_date_param = $this->params["end_date_param"];
        $this->equipmenttable1();
        $this->equipmentdetailstable1();
    }

    function equipmenttable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT a.TRANS_RENTAL_EQUIPMENT_ID_INT,
            a.TRANS_EQUIPMENT_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.NO_TELP_CHAR, a.QTY_INT, a.TOTAL_HARGA_FLOAT, a.DEPOSIT_FLOAT,
            a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT), 0) - a.QTY_INT
                END
            AS QTY_FREE_INT,
            ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT, a.REFUND_FLOAT, a.REFUND_DESC_CHAR, a.REFUND_DATE,
            a.RETUR_BY, a.RETUR_AT, (a.TOTAL_PAID_FLOAT + a.DEPOSIT_FLOAT) AS TOTAL_PAID_FINAL_FLOAT
            FROM TRANS_RENTAL_EQUIPMENT AS a
            LEFT JOIN TRANS_PROMO_EQUIPMENT AS b ON b.TRANS_EQUIPMENT_NOCHAR = a.TRANS_EQUIPMENT_NO_CHAR
            LEFT JOIN MD_PROMO_EQUIPMENT AS c ON c.PROMO_EQUIPMENT_ID_INT = b.PROMO_EQUIPMENT_ID_INT
            LEFT JOIN TRANS_RENTAL_EQUIPMENT_DETAILS AS d ON d.TRANS_EQUIPMENT_NO_CHAR = a.TRANS_EQUIPMENT_NO_CHAR
            WHERE a.PROJECT_NO_CHAR = :project AND a.[STATUS] <> 0
            AND CAST(a.RETUR_AT AS DATE) >= :start_date_param AND CAST(a.RETUR_AT AS DATE) <= :end_date_param
            GROUP BY a.TRANS_RENTAL_EQUIPMENT_ID_INT, a.TRANS_EQUIPMENT_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.NO_TELP_CHAR, a.QTY_INT,
            a.TOTAL_HARGA_FLOAT, a.DEPOSIT_FLOAT, a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            a.REFUND_FLOAT, a.REFUND_DESC_CHAR, a.REFUND_DATE, a.RETUR_BY, a.RETUR_AT
            ORDER BY a.TRANS_RENTAL_EQUIPMENT_ID_INT DESC
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_RENTAL_EQUIPMENT_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('equipment_report_table1'));
    }

    function equipmentdetailstable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT b.TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT, b.TRANS_EQUIPMENT_NO_CHAR,
            b.DESC_CHAR, d.MD_EQUIPMENT_CATEGORY_DESC_CHAR, b.HARGA_FLOAT, b.created_at,
            (d.MD_EQUIPMENT_CATEGORY_DESC_CHAR + ' - ' + b.DESC_CHAR) AS MD_EQUIPMENT_WITH_CATEGORY_DESC_CHAR
            FROM TRANS_RENTAL_EQUIPMENT AS a
            INNER JOIN TRANS_RENTAL_EQUIPMENT_DETAILS AS b ON b.TRANS_EQUIPMENT_NO_CHAR = a.TRANS_EQUIPMENT_NO_CHAR
            INNER JOIN MD_EQUIPMENT AS c ON c.MD_EQUIPMENT_ID_INT = b.MD_EQUIPMENT_ID_INT
            INNER JOIN MD_EQUIPMENT_CATEGORY AS d ON d.MD_EQUIPMENT_CATEGORY_ID_INT = c.MD_EQUIPMENT_CATEGORY_ID_INT
            WHERE a.[STATUS] <> 0 AND b.PROJECT_NO_CHAR = :project
            AND CAST(a.RETUR_AT AS DATE) >= :start_date_param AND CAST(a.RETUR_AT AS DATE) <= :end_date_param
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('equipment_details_report_table1'));
    }
}