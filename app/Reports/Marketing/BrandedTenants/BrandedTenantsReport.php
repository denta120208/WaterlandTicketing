<?php

namespace App\Reports\Marketing\BrandedTenants;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use \koolreport\datagrid\DataTables;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class BrandedTenantsReport extends \koolreport\KoolReport {
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
        $this->brandedtenantstable1();
        $this->brandedtenantstableexcelpdf1();
        $this->brandedtenantstableexcelpdf2();
    }

    function brandedtenantstable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT 'Anchor Tenant (>= 500m)' AS [KATEGORI], SUM(a.LOT_STOCK_SQM) AS TOTAL_LUAS FROM
            (SELECT DISTINCT a.MD_TENANT_NAME_CHAR, ISNULL(c.LOT_STOCK_SQM, 0) AS LOT_STOCK_SQM FROM MTLA_MALL.dbo.MD_TENANT AS a
            LEFT JOIN MTLA_MALL.dbo.PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN MTLA_MALL.dbo.LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM >= 500 AND b.PROJECT_NO_CHAR = :project AND PSM_TRANS_BOOKING_DATE <= :end_date
            AND a.MD_TENANT_BRANDED_INT = 1) AS a
            UNION ALL
            SELECT 'Non Anchor Tenant (< 500m)' AS [KATEGORI], SUM(b.LOT_STOCK_SQM) AS TOTAL_LUAS FROM
            (SELECT DISTINCT a.MD_TENANT_NAME_CHAR, ISNULL(c.LOT_STOCK_SQM, 0) AS LOT_STOCK_SQM FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM < 500 AND b.PROJECT_NO_CHAR = :project AND PSM_TRANS_BOOKING_DATE <= :end_date
            AND a.MD_TENANT_BRANDED_INT = 1) AS b
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // if($row['UNIT_TYPE'] == "RKN") {
                //     $row['UNIT_TYPE'] = 'RUKO';
                // }
                // // Dalam Juta                
                // $row['NET_BEFORE_TAX_NUM'] = $row['NET_BEFORE_TAX_NUM'] / 1000000;
                return array($row);
            },
            '{meta}' => function($meta) {
                return $meta;
            }
        )))
        ->pipe(new ColumnMeta(array(
            "KATEGORI"=>array(
                'label' => '',
                "footerText"=>"<p><b>TOTAL</b></p>"
            ),
            "TOTAL_LUAS"=>array(
                'label' => "TOTAL LUAS",
                "suffix"=>" m2",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe($this->dataStore('marketing_branded_tenants_table1'));

        session(['marketingbrandedtenantstableproject' => $this->params["project"]]);
        session(['marketingbrandedtenantstablecutoff' => $this->params["cut_off"]]);
    }

    function brandedtenantstableexcelpdf1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT DISTINCT a.MD_TENANT_NAME_CHAR, c.LOT_STOCK_SQM FROM MTLA_MALL.dbo.MD_TENANT AS a
            LEFT JOIN MTLA_MALL.dbo.PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN MTLA_MALL.dbo.LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM >= 500 AND b.PROJECT_NO_CHAR = :project AND PSM_TRANS_BOOKING_DATE <= :end_date
            AND a.MD_TENANT_BRANDED_INT = 1
        ")
        ->params(array(
            ":project"=>$this->params["project"],
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
            "MD_TENANT_NAME_CHAR"=>array(
                'label' => 'TENANT',
                "footerText"=>"<p><b>TOTAL</b></p>"
            ),
            "LOT_STOCK_SQM"=>array(
                'label' => "LUAS",
                "suffix"=>" m2",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe($this->dataStore('marketing_branded_tenants_table_excel_pdf1'));
    }

    function brandedtenantstableexcelpdf2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT DISTINCT a.MD_TENANT_NAME_CHAR, c.LOT_STOCK_SQM FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            WHERE c.LOT_STOCK_SQM < 500 AND b.PROJECT_NO_CHAR = :project AND PSM_TRANS_BOOKING_DATE <= :end_date
            AND a.MD_TENANT_BRANDED_INT = 1
        ")
        ->params(array(
            ":project"=>$this->params["project"],
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
            "MD_TENANT_NAME_CHAR"=>array(
                'label' => 'TENANT',
                "footerText"=>"<p><b>TOTAL</b></p>"
            ),
            "LOT_STOCK_SQM"=>array(
                'label' => "LUAS",
                "suffix"=>" m2",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe($this->dataStore('marketing_branded_tenants_table_excel_pdf2'));
    }
}
