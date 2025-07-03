<?php

namespace App\Reports\Marketing\AnalisaNPV;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use \koolreport\processes\CopyColumn;
use Illuminate\Http\Request;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class AnalisaNPVReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    public $interest_rate = NULL;
    public $npvLand = NULL;
    public $paymentLand = NULL;
    public $npvBuild = NULL;
    public $paymentBuild = NULL;

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
        $this->interest_rate = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = '".$this->params["project"]."'")[0]->INTEREST_RATE_NPV;
        $this->analisanpvtableland();
        $this->analisanpvtablebuild();
        $this->npvLand = $this->params["npvLand"];
        $this->paymentLand = $this->params["paymentLand"];
        $this->npvBuild = $this->params["npvBuild"];
        $this->paymentBuild = $this->params["paymentBuild"];
    }

    function analisanpvtableland() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_analisa_npv2 @rt_period_from = :start_date, @rt_period_to = :end_date, @rt_period_from_backyear = :backyear_start_date, @rt_period_to_backyear = :backyear_end_date, @project_no = :project, @tipe = :tipe
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"],
            ":backyear_start_date"=>$this->params["start_date_backyear"],
            ":backyear_end_date"=>$this->params["end_date_backyear"],
            ":tipe"=>"TANAH"
        ))
        ->pipe(new ColumnMeta(array(
            "SPR_DATE"=>array(
                'type' => 'string',
            ),
            "UNIT_TYPE"=>array(
                'type' => 'string',
            ),
            "NPV_TANAH"=>array(
                'type' => 'number',
            ),
            "ALL_PAYMENT_TANAH"=>array(
                'type' => 'number',
            ),
            "GROWTH_NPV"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "GROWTH_ALL_PAYMENT"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['UNIT_TYPE'] = strtoupper($row['UNIT_TYPE']);
                if($row['UNIT_TYPE'] == "KAVL") {
                    $row['UNIT_TYPE'] = "KAVLING";
                }
                else if($row['UNIT_TYPE'] == "RKN") {
                    $row['UNIT_TYPE'] = "RUKO";
                }
                else if($row['UNIT_TYPE'] == "RMH") {
                    $row['UNIT_TYPE'] = "RUMAH";
                }
                else if($row['UNIT_TYPE'] == "KAVC") {
                    $row['UNIT_TYPE'] = "KAVLING COMMERCIAL";
                }

                $row['NPV_TANAH'] > 0 ? $row['NPV_TANAH'] = $row['NPV_TANAH'] / 1000 : $row['NPV_TANAH'] = 0;
                $row['ALL_PAYMENT_TANAH'] > 0 ? $row['ALL_PAYMENT_TANAH'] = $row['ALL_PAYMENT_TANAH'] / 1000 : $row['ALL_PAYMENT_TANAH'] = 0;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"SPR_DATE",
                "row"=>"UNIT_TYPE"
            ),
            "aggregates"=>array(
                "sum"=>"NPV_TANAH, ALL_PAYMENT_TANAH, GROWTH_NPV, GROWTH_ALL_PAYMENT"
            )
        )))
        ->pipe($this->dataStore('marketing_analisa_npv_table_land'));

        $dataTotalNPV = DB::select("EXEC sp_boc_analisa_npv_total '".$this->params["start_date"]."', '".$this->params["cut_off"]."', '".$this->params["project"]."'");
        session(['npvLand' => "Rp. ".number_format($dataTotalNPV[0]->TOTAL_NPV_TANAH > 0 ? $dataTotalNPV[0]->TOTAL_NPV_TANAH = $dataTotalNPV[0]->TOTAL_NPV_TANAH / 1000 : $dataTotalNPV[0]->TOTAL_NPV_TANAH = 0)]);
        $this->params['npvLand'] = "Rp. ".number_format($dataTotalNPV[0]->TOTAL_NPV_TANAH);
        session(['paymentLand' => "Rp. ".number_format($dataTotalNPV[0]->TOTAL_ALL_PAYMENT_TANAH > 0 ? $dataTotalNPV[0]->TOTAL_ALL_PAYMENT_TANAH = $dataTotalNPV[0]->TOTAL_ALL_PAYMENT_TANAH / 1000 : $dataTotalNPV[0]->TOTAL_ALL_PAYMENT_TANAH = 0)]);
        $this->params['paymentLand'] = "Rp. ".number_format($dataTotalNPV[0]->TOTAL_ALL_PAYMENT_TANAH);
    }

    function analisanpvtablebuild() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_analisa_npv2 @rt_period_from = :start_date, @rt_period_to = :end_date, @rt_period_from_backyear = :backyear_start_date, @rt_period_to_backyear = :backyear_end_date, @project_no = :project, @tipe = :tipe
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"],
            ":backyear_start_date"=>$this->params["start_date_backyear"],
            ":backyear_end_date"=>$this->params["end_date_backyear"],
            ":tipe"=>"BANGUNAN"
        ))
        ->pipe(new ColumnMeta(array(
            "SPR_DATE"=>array(
                'type' => 'string',
            ),
            "UNIT_TYPE"=>array(
                'type' => 'string',
            ),
            "NPV_BANGUNAN"=>array(
                'type' => 'number',
            ),
            "ALL_PAYMENT_BANGUNAN"=>array(
                'type' => 'number',
            ),
            "GROWTH_NPV"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "GROWTH_ALL_PAYMENT"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['UNIT_TYPE'] = strtoupper($row['UNIT_TYPE']);
                if($row['UNIT_TYPE'] == "KAVL") {
                    $row['UNIT_TYPE'] = "KAVLING";
                }
                else if($row['UNIT_TYPE'] == "RKN") {
                    $row['UNIT_TYPE'] = "RUKO";
                }
                else if($row['UNIT_TYPE'] == "RMH") {
                    $row['UNIT_TYPE'] = "RUMAH";
                }
                else if($row['UNIT_TYPE'] == "KAVC") {
                    $row['UNIT_TYPE'] = "KAVLING COMMERCIAL";
                }

                $row['NPV_BANGUNAN'] > 0 ? $row['NPV_BANGUNAN'] = $row['NPV_BANGUNAN'] / 1000 : $row['NPV_BANGUNAN'] = 0;
                $row['ALL_PAYMENT_BANGUNAN'] > 0 ? $row['ALL_PAYMENT_BANGUNAN'] = $row['ALL_PAYMENT_BANGUNAN'] / 1000 : $row['ALL_PAYMENT_BANGUNAN'] = 0;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"SPR_DATE",
                "row"=>"UNIT_TYPE"
            ),
            "aggregates"=>array(
                "sum"=>"NPV_BANGUNAN, ALL_PAYMENT_BANGUNAN, GROWTH_NPV, GROWTH_ALL_PAYMENT"
            )
        )))
        ->pipe($this->dataStore('marketing_analisa_npv_table_build'));

        $dataTotalNPV = DB::select("EXEC sp_boc_analisa_npv_total '".$this->params["start_date"]."', '".$this->params["cut_off"]."', '".$this->params["project"]."'");
        session(['npvBuild' => "Rp. ".number_format($dataTotalNPV[0]->TOTAL_NPV_BANGUNAN > 0 ? $dataTotalNPV[0]->TOTAL_NPV_BANGUNAN = $dataTotalNPV[0]->TOTAL_NPV_BANGUNAN / 1000 : $dataTotalNPV[0]->TOTAL_NPV_BANGUNAN = 0)]);
        $this->params['npvBuild'] = "Rp. ".number_format($dataTotalNPV[0]->TOTAL_NPV_BANGUNAN);
        session(['paymentBuild' => "Rp. ".number_format($dataTotalNPV[0]->TOTAL_ALL_PAYMENT_BANGUNAN > 0 ? $dataTotalNPV[0]->TOTAL_ALL_PAYMENT_BANGUNAN = $dataTotalNPV[0]->TOTAL_ALL_PAYMENT_BANGUNAN / 1000 : $dataTotalNPV[0]->TOTAL_ALL_PAYMENT_BANGUNAN = 0)]);
        $this->params['paymentBuild'] = "Rp. ".number_format($dataTotalNPV[0]->TOTAL_ALL_PAYMENT_BANGUNAN);
    }
}
