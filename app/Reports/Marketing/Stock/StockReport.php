<?php

namespace App\Reports\Marketing\Stock;
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

class StockReport extends \koolreport\KoolReport {
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
        $this->stock_table();
        $this->stock_excel_table();
        $this->stock_pdf_table();
    }

    function stock_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_stok_marketing_report @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "HARGA_JUAL"=>array(
                "label"=>"HARGA JUAL",
                'type' => 'string',
                // "prefix" => "Rp. ",
            ),
            "LT"=>array(
                'type' => 'string',
            ),
            "LB"=>array(
                'type' => 'string',
            ),
            "UNIT"=>array(
                'type' => 'string',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['CLUSTER'] == "KAVL") {
                    $row['CLUSTER'] = "<td style='background-color:yellow;'><b>"."KAVLING"."</b></td>";
                    $row['UNIT'] = "<td style='background-color:yellow;'><b>".$row['UNIT']."</b></td>";
                    $row['LT'] = "<td style='background-color:yellow;'>".number_format($row['LT'])."</td>";
                    $row['LB'] = "<td style='background-color:yellow;'>".number_format($row['LB'])."</td>";
                    $row['HARGA_JUAL'] = "<td style='background-color:yellow;'><b>".number_format($row['HARGA_JUAL'])."</b></td>";
                }
                else if($row['CLUSTER'] == "KAVC") {
                    $row['CLUSTER'] = "<td style='background-color:yellow;'><b>"."KAVLING COMMERCIAL"."</b></td>";
                    $row['UNIT'] = "<td style='background-color:yellow;'><b>".$row['UNIT']."</b></td>";
                    $row['LT'] = "<td style='background-color:yellow;'>".number_format($row['LT'])."</td>";
                    $row['LB'] = "<td style='background-color:yellow;'>".number_format($row['LB'])."</td>";
                    $row['HARGA_JUAL'] = "<td style='background-color:yellow;'><b>".number_format($row['HARGA_JUAL'])."</b></td>";
                }
                else if($row['CLUSTER'] == "RKN") {
                    $row['CLUSTER'] = "<td style='background-color:yellow;'><b>"."RUKO"."</b></td>";
                    $row['UNIT'] = "<td style='background-color:yellow;'><b>".$row['UNIT']."</b></td>";
                    $row['LT'] = "<td style='background-color:yellow;'>".number_format($row['LT'])."</td>";
                    $row['LB'] = "<td style='background-color:yellow;'>".number_format($row['LB'])."</td>";
                    $row['HARGA_JUAL'] = "<td style='background-color:yellow;'><b>".number_format($row['HARGA_JUAL'])."</b></td>";
                }
                else if($row['CLUSTER'] == "RMH") {
                    $row['CLUSTER'] = "<td style='background-color:yellow;'><b>"."RUMAH"."</b></td>";
                    $row['UNIT'] = "<td style='background-color:yellow;'><b>".$row['UNIT']."</b></td>";
                    $row['LT'] = "<td style='background-color:yellow;'>".number_format($row['LT'])."</td>";
                    $row['LB'] = "<td style='background-color:yellow;'>".number_format($row['LB'])."</td>";
                    $row['HARGA_JUAL'] = "<td style='background-color:yellow;'><b>".number_format($row['HARGA_JUAL'])."</b></td>";
                }
                else if($row['CLUSTER'] == "TOTAL") {
                    $row['CLUSTER'] = "<td style='background-color:#081c5c; color: white;'><b>".$row['CLUSTER']."</b></td>";
                    $row['UNIT'] = "<td style='background-color:#081c5c; color: white;'><b>".$row['UNIT']."</b></td>";
                    $row['LT'] = "<td style='background-color:#081c5c; color: white;'><b>".number_format($row['LT'])."</b></td>";
                    $row['LB'] = "<td style='background-color:#081c5c; color: white;'><b>".number_format($row['LB'])."</b></td>";
                    $row['HARGA_JUAL'] = "<td style='background-color:#081c5c; color: white;'><b>".number_format($row['HARGA_JUAL'])."</b></td>";
                }
                else {
                    $row['CLUSTER'] = $row['CLUSTER'];
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('marketing_stock_table'));
    }

    function stock_excel_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_stok_marketing_report @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "HARGA_JUAL"=>array(
                "label"=>"HARGA JUAL",
                'type' => 'string',
                // "prefix" => "Rp. ",
            ),
            "LT"=>array(
                'type' => 'string',
            ),
            "LB"=>array(
                'type' => 'string',
            ),
            "UNIT"=>array(
                'type' => 'string',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['CLUSTER'] == "KAVL") {
                    $row['CLUSTER'] = "KAVLING";
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                else if($row['CLUSTER'] == "KAVC") {
                    $row['CLUSTER'] = "KAVLING COMMERCIAL";
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                else if($row['CLUSTER'] == "RKN") {
                    $row['CLUSTER'] = "RUKO";
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                else if($row['CLUSTER'] == "RMH") {
                    $row['CLUSTER'] = "RUMAH";
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                else if($row['CLUSTER'] == "TOTAL") {
                    $row['CLUSTER'] = "TOTAL";
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                else {
                    $row['CLUSTER'] = $row['CLUSTER'];
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('marketing_stock_excel_table'));
    }

    function stock_pdf_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_stok_marketing_report @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "HARGA_JUAL"=>array(
                "label"=>"HARGA JUAL",
                'type' => 'string',
                // "prefix" => "Rp. ",
            ),
            "LT"=>array(
                'type' => 'string',
            ),
            "LB"=>array(
                'type' => 'string',
            ),
            "UNIT"=>array(
                'type' => 'string',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['CLUSTER'] == "KAVL") {
                    $row['CLUSTER'] = "<b>KAVLING</b>";
                    $row['UNIT'] = "<b>".$row['UNIT']."</b>";
                    $row['LT'] = "<b>".number_format($row['LT'])."</b>";
                    $row['LB'] = "<b>".number_format($row['LB'])."</b>";
                    $row['HARGA_JUAL'] = "<b>".number_format($row['HARGA_JUAL'])."</b>";
                }
                else if($row['CLUSTER'] == "KAVC") {
                    $row['CLUSTER'] = "<b>KAVLING COMMERCIAL</b>";
                    $row['UNIT'] = "<b>".$row['UNIT']."</b>";
                    $row['LT'] = "<b>".number_format($row['LT'])."</b>";
                    $row['LB'] = "<b>".number_format($row['LB'])."</b>";
                    $row['HARGA_JUAL'] = "<b>".number_format($row['HARGA_JUAL'])."</b>";
                }
                else if($row['CLUSTER'] == "RKN") {
                    $row['CLUSTER'] = "<b>RUKO</b>";
                    $row['UNIT'] = "<b>".$row['UNIT']."</b>";
                    $row['LT'] = "<b>".number_format($row['LT'])."</b>";
                    $row['LB'] = "<b>".number_format($row['LB'])."</b>";
                    $row['HARGA_JUAL'] = "<b>".number_format($row['HARGA_JUAL'])."</b>";
                }
                else if($row['CLUSTER'] == "RMH") {
                    $row['CLUSTER'] = "<b>RUMAH</b>";
                    $row['UNIT'] = "<b>".$row['UNIT']."</b>";
                    $row['LT'] = "<b>".number_format($row['LT'])."</b>";
                    $row['LB'] = "<b>".number_format($row['LB'])."</b>";
                    $row['HARGA_JUAL'] = "<b>".number_format($row['HARGA_JUAL'])."</b>";
                }
                else if($row['CLUSTER'] == "TOTAL") {
                    $row['CLUSTER'] = "<b>TOTAL</b>";
                    $row['UNIT'] = "<b>".$row['UNIT']."</b>";
                    $row['LT'] = "<b>".number_format($row['LT'])."</b>";
                    $row['LB'] = "<b>".number_format($row['LB'])."</b>";
                    $row['HARGA_JUAL'] = "<b>".number_format($row['HARGA_JUAL'])."</b>";
                }
                else {
                    $row['CLUSTER'] = $row['CLUSTER'];
                    $row['UNIT'] = $row['UNIT'];
                    $row['LT'] = number_format($row['LT']);
                    $row['LB'] = number_format($row['LB']);
                    $row['HARGA_JUAL'] = number_format($row['HARGA_JUAL']);
                }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('marketing_stock_pdf_table'));
    }
}
