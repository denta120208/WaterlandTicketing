<?php

namespace App\Reports\Marketing\DemografiUsiaPekerjaan;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use \koolreport\datagrid\DataTables;
use \koolreport\widgets\koolphp\Table;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class DemografiUsiaPekerjaanReport extends \koolreport\KoolReport {
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
                "arrDataSourcesPekerjaan"=>array(
                    "class"=>'\koolreport\datasources\ArrayDataSource',
                    "data"=>$this->demografi_usia_pekerjaan_chart_pekerjaan_excel_raw_data(),
                    "dataFormat"=>"associate",
                ),
            )
        );
    }

    function setup()
    {
        $this->demografi_usia_pekerjaan_chart_pekerjaan();
        $this->demografi_usia_pekerjaan_chart_usia();
        $this->demografi_usia_pekerjaan_chart_pekerjaan_excel();
        $this->demografi_usia_pekerjaan_chart_usia_excel();
    }

    function demografi_usia_pekerjaan_chart_pekerjaan() {
        $project_id = $this->params["project"];
        $cut_off = $this->params["cut_off"];

        $dataRawChart = DB::select("EXEC sp_boc_customer_profile_by_age_job @rt_period = '".$cut_off."', @project_no = $project_id, @group = 'JOB'");

        $dataChart = array();
        $dataChartTemp = array();
        foreach($dataRawChart as $data) {
            if(array_key_exists($data->PEKERJAAN_CHAR, $dataChartTemp)) {
                array_push($dataChartTemp[$data->PEKERJAAN_CHAR][0], $data->JUMLAHUNIT);
            }
            else {
                $dataChartTemp[$data->PEKERJAAN_CHAR][] = array("PEKERJAAN_CHAR"=>$data->PEKERJAAN_CHAR, strval($data->TAHUN)=>$data->JUMLAHUNIT);
            }
        }

        foreach($dataChartTemp as $data) {
            $dataChart[] = $data[0];
        }

        session(['demografi_usia_pekerjaan_chart_pekerjaan' => $dataChart]);
    }
    
    function demografi_usia_pekerjaan_chart_pekerjaan_excel() {
        $this->src('arrDataSourcesPekerjaan')
        ->pipe($this->dataStore('demografi_usia_pekerjaan_chart_pekerjaan_excel'));
    }

    function demografi_usia_pekerjaan_chart_pekerjaan_excel_raw_data() {
        $project_id = $this->params["project"];
        $cut_off = $this->params["cut_off"];

        $dataRawChart = DB::select("EXEC sp_boc_customer_profile_by_age_job @rt_period = '".$cut_off."', @project_no = $project_id, @group = 'JOB'");

        $dataChart = array();
        $dataChartTemp = array();
        foreach($dataRawChart as $data) {
            if(array_key_exists($data->PEKERJAAN_CHAR, $dataChartTemp)) {
                array_push($dataChartTemp[$data->PEKERJAAN_CHAR][0], $data->JUMLAHUNIT);
            }
            else {
                $dataChartTemp[$data->PEKERJAAN_CHAR][] = array("PEKERJAAN_CHAR"=>$data->PEKERJAAN_CHAR, strval($data->TAHUN)=>$data->JUMLAHUNIT);
            }
        }

        foreach($dataChartTemp as $data) {
            $dataChart[] = $data[0];
        }

        return $dataChart;
    }

    function demografi_usia_pekerjaan_chart_usia() {
        $project_id = $this->params["project"];
        $cut_off = $this->params["cut_off"];

        $dataChart = DB::select("EXEC sp_boc_customer_profile_by_age_job @rt_period = '".$cut_off."', @project_no = $project_id, @group = 'AGE'");

        $this->params['demografi_usia_pekerjaan_chart_cut_off'] = $cut_off;
        session(['demografi_usia_pekerjaan_chart_cut_off' => $cut_off]);
        session(['demografi_usia_pekerjaan_chart_age' => $dataChart]);
    }

    function demografi_usia_pekerjaan_chart_usia_excel() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_customer_profile_by_age_job @rt_period = :cut_off, @project_no = :project, @group = :group
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":group"=>"AGE"
        ))
        ->pipe($this->dataStore('demografi_usia_pekerjaan_chart_age_excel'));
    }
}
