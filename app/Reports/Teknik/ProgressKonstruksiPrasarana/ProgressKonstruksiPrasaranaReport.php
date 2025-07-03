<?php

namespace App\Reports\Teknik\ProgressKonstruksiPrasarana;
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

class ProgressKonstruksiPrasaranaReport extends \koolreport\KoolReport {
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
        $this->progress_konstruksi_prasarana_table();
        $this->progress_konstruksi_prasarana_excel_table();
    }

    function progress_konstruksi_prasarana_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT mv.MD_VENDOR_NAME_CHAR,  pk.NAMA_PEKERJAAN, sp.SPK_TYPE_NAME_CHAR, MAX(ss.MD_TERMIN_PROGRESS_INT) as Target,
            MAX(ss.MD_TERMIN_BAYAR_INT) as Realisasi, (MAX(ss.MD_TERMIN_BAYAR_INT) - MAX(ss.MD_TERMIN_PROGRESS_INT)) as Target_x_Realisasi,
            st.SPK_TRANS_START_DATE, st.SPK_TRANS_END_DATE, st.SPK_TRANS_APPROVE_INT, mp.PROJECT_NAME,st.SPK_TRANS_NOCHAR
            FROM SPK_TRANS st INNER JOIN MD_VENDOR mv
            ON st.MD_VENDOR_ID_INT = mv.MD_VENDOR_ID_INT INNER JOIN PL_MASTER_PEKERJAAN pk
            ON st.ID_PEKERJAAN = pk.ID_PEKERJAAN INNER JOIN SPK_TYPE sp
            ON st.SPK_TRANS_TYPE = sp.SPK_TYPE_ID_INT INNER JOIN SPK_SERTIFIKAT ss
            ON st.SPK_TRANS_NOCHAR = ss.SPK_TRANS_NOCHAR INNER JOIN MD_PROJECT mp
            ON st.PROJECT_NO_CHAR = mp.PROJECT_NO_CHAR
            WHERE st.THN_BUDGET = :cut_off AND
            st.SPK_TRANS_APPROVE_INT > '0' AND
            st.PROJECT_NO_CHAR <> '1' AND
            st.PROJECT_NO_CHAR = :project AND pk.PROJECT_NO_CHAR = :project
            GROUP BY mv.MD_VENDOR_NAME_CHAR,  pk.NAMA_PEKERJAAN, sp.SPK_TYPE_NAME_CHAR, st.SPK_TRANS_START_DATE, st.SPK_TRANS_END_DATE
            , st.SPK_TRANS_APPROVE_INT, mp.PROJECT_NAME, st.SPK_TRANS_NOCHAR
            ORDER BY mp.PROJECT_NAME
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
        ))
        ->pipe(new ColumnMeta(array(
            "MD_VENDOR_NAME_CHAR"=>array(
                "label" => "KONTRAKTOR",
                "type" => "string",
                // "footerText"=>"<p><b>NET BACKLOG</b></p>"
            ),
            "NAMA_PEKERJAAN"=>array(
                "label" => "PEKERJAAN",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "Target"=>array(
                "label" => "TARGET",
                "suffix" => "%",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "Realisasi"=>array(
                "label" => "R ".$this->params["cut_off"],
                "suffix" => "%",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "Target_x_Realisasi"=>array(
                "label" => "+/-",
                // "suffix" => "%",
                "formatValue"=>function($value, $row){
                    $color = number_format($value)<0?"red":"black";
                    return "<p style='color:$color;'>".number_format($value)."%</p>";
                }
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "SPK_TRANS_END_DATE"=>array(
                "label" => "TARGET SELESAI",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['MD_VENDOR_NAME_CHAR'] = "<p style='text-align: left;'>".strtoupper($row['MD_VENDOR_NAME_CHAR'])."</p>";
                $row['NAMA_PEKERJAAN'] = "<p style='text-align: left;'>".$row['NAMA_PEKERJAAN']."</p>";
                $row['SPK_TRANS_END_DATE'] = date('M-Y', strtotime($row['SPK_TRANS_END_DATE']));
                // $row['TYPE_RETENSI'] = strtoupper($row['TYPE_RETENSI']);
                // $row['TITLE'] = "<p style='text-align: left;'>".$row['TITLE']."</p>";
                // if($row['TITLE'] == "TOTAL") {
                //     $row['TITLE'] = "<p><b>".$row['TITLE']."</b></p>";
                //     $row['COLLECTED_BACKWARD1'] = "<p><b>".number_format($row['COLLECTED_BACKWARD1'])."</b></p>";
                //     $row['COLLECTED_CURRENT'] = "<p><b>".number_format($row['COLLECTED_CURRENT'])."</b></p>";
                //     $row['TOTAL_AGING'] = "<p><b>".$row['TOTAL_AGING']."</b></p>";
                //     $row['TARGET_COLLECTED_CURRENT'] = "<p><b>".number_format($row['TARGET_COLLECTED_CURRENT'])."</b></p>";
                //     $row['COLLECTABILITY_CURRENT'] = "<p><b>".$row['COLLECTABILITY_CURRENT']."%</b></p>";
                // }
                // else {
                //     $row['TITLE'] = $row['TITLE'];
                //     $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                //     $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                //     $row['TOTAL_AGING'] = $row['TOTAL_AGING'];
                //     $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                //     $row['COLLECTABILITY_CURRENT'] = $row['COLLECTABILITY_CURRENT']."%";
                // }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_progress_konstruksi_prasarana_table'));
    }

    function progress_konstruksi_prasarana_excel_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT mv.MD_VENDOR_NAME_CHAR,  pk.NAMA_PEKERJAAN, sp.SPK_TYPE_NAME_CHAR, MAX(ss.MD_TERMIN_PROGRESS_INT) as Target,
            MAX(ss.MD_TERMIN_BAYAR_INT) as Realisasi, (MAX(ss.MD_TERMIN_BAYAR_INT) - MAX(ss.MD_TERMIN_PROGRESS_INT)) as Target_x_Realisasi,
            st.SPK_TRANS_START_DATE, st.SPK_TRANS_END_DATE, st.SPK_TRANS_APPROVE_INT, mp.PROJECT_NAME,st.SPK_TRANS_NOCHAR
            FROM SPK_TRANS st INNER JOIN MD_VENDOR mv
            ON st.MD_VENDOR_ID_INT = mv.MD_VENDOR_ID_INT INNER JOIN PL_MASTER_PEKERJAAN pk
            ON st.ID_PEKERJAAN = pk.ID_PEKERJAAN INNER JOIN SPK_TYPE sp
            ON st.SPK_TRANS_TYPE = sp.SPK_TYPE_ID_INT INNER JOIN SPK_SERTIFIKAT ss
            ON st.SPK_TRANS_NOCHAR = ss.SPK_TRANS_NOCHAR INNER JOIN MD_PROJECT mp
            ON st.PROJECT_NO_CHAR = mp.PROJECT_NO_CHAR
            WHERE st.THN_BUDGET = :cut_off AND
            st.SPK_TRANS_APPROVE_INT > '0' AND
            st.PROJECT_NO_CHAR <> '1' AND
            st.PROJECT_NO_CHAR = :project AND pk.PROJECT_NO_CHAR = :project
            GROUP BY mv.MD_VENDOR_NAME_CHAR,  pk.NAMA_PEKERJAAN, sp.SPK_TYPE_NAME_CHAR, st.SPK_TRANS_START_DATE, st.SPK_TRANS_END_DATE
            , st.SPK_TRANS_APPROVE_INT, mp.PROJECT_NAME, st.SPK_TRANS_NOCHAR
            ORDER BY mp.PROJECT_NAME
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
        ))
        ->pipe(new ColumnMeta(array(
            "MD_VENDOR_NAME_CHAR"=>array(
                "label" => "KONTRAKTOR",
                "type" => "string",
                // "footerText"=>"<p><b>NET BACKLOG</b></p>"
            ),
            "NAMA_PEKERJAAN"=>array(
                "label" => "PEKERJAAN",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "Target"=>array(
                "label" => "TARGET",
                "suffix" => "%",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "Realisasi"=>array(
                "label" => "R ".$this->params["cut_off"],
                "suffix" => "%",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "Target_x_Realisasi"=>array(
                "label" => "+/-",
                // "suffix" => "%",
                "formatValue"=>function($value, $row){
                    $color = number_format($value)<0?"red":"black";
                    return "<p style='color:$color;'>".number_format($value)."%</p>";
                }
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
            "SPK_TRANS_END_DATE"=>array(
                "label" => "TARGET SELESAI",
                "type" => "string",
                // "footer"=>"sum",
                // "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['MD_VENDOR_NAME_CHAR'] = strtoupper($row['MD_VENDOR_NAME_CHAR']);
                $row['SPK_TRANS_END_DATE'] = date('M-Y', strtotime($row['SPK_TRANS_END_DATE']));
                // $row['TYPE_RETENSI'] = strtoupper($row['TYPE_RETENSI']);
                // $row['TITLE'] = "<p style='text-align: left;'>".$row['TITLE']."</p>";
                // if($row['TITLE'] == "TOTAL") {
                //     $row['TITLE'] = "<p><b>".$row['TITLE']."</b></p>";
                //     $row['COLLECTED_BACKWARD1'] = "<p><b>".number_format($row['COLLECTED_BACKWARD1'])."</b></p>";
                //     $row['COLLECTED_CURRENT'] = "<p><b>".number_format($row['COLLECTED_CURRENT'])."</b></p>";
                //     $row['TOTAL_AGING'] = "<p><b>".$row['TOTAL_AGING']."</b></p>";
                //     $row['TARGET_COLLECTED_CURRENT'] = "<p><b>".number_format($row['TARGET_COLLECTED_CURRENT'])."</b></p>";
                //     $row['COLLECTABILITY_CURRENT'] = "<p><b>".$row['COLLECTABILITY_CURRENT']."%</b></p>";
                // }
                // else {
                //     $row['TITLE'] = $row['TITLE'];
                //     $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                //     $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                //     $row['TOTAL_AGING'] = $row['TOTAL_AGING'];
                //     $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                //     $row['COLLECTABILITY_CURRENT'] = $row['COLLECTABILITY_CURRENT']."%";
                // }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_progress_konstruksi_prasarana_excel_table'));
    }
}
