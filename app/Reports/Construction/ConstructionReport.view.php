<?php
use \koolreport\widgets\koolphp\Table;
use \koolreport\widgets\google\BarChart;

?>
<html>
<head>
    <title>Construction Report</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo URL::asset('/css/bootstrap.css')?>" rel="stylesheet">
    <link href="<?php echo URL::asset('/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet">
    <link href="<?php echo URL::asset('/css/timeline.css')?>" rel="stylesheet">
    <link href="<?php echo URL::asset('/css/metisMenu.min.css')?>" rel="stylesheet">
    <link href="<?php echo URL::asset('/css/sb-admin-2.css')?>" rel="stylesheet">
    <link href="<?php echo URL::asset('/css/morris.css')?>" rel="stylesheet">
    <link href="<?php echo URL::asset('/css/style.css')?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URL::asset('/css/jquery-ui.css')?>">
    <!-- Custom Fonts -->
    <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.20/datatables.min.css"/>

    <!-- jQuery -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.js"></script>

    <script src="<?php echo URL::asset('/js/jquery-ui.js')?>"></script>
    <script type="text/javascript" src="<?php echo URL::asset('/js/bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo URL::asset('/js/morris.min.js')?>"></script>
    <script type="text/javascript" src="<?php echo URL::asset('/js/raphael-min.js')?>"></script>
    <script type="text/javascript" src="<?php echo URL::asset('/js/jquery.stickytableheaders.min.js')?>"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
</head>
<body>
<div class="col-lg-12">
    <div class="text-center">
        <h1>Progres Konstruksi Prasarana</h1>
        <p class="lead">
            Teknik & Quantity Surveyor
        </p>
    </div>
    <div class="row">
        <div class="col-md-12">
<?php
$project = 20;
$query = DB::select("SELECT mv.MD_VENDOR_NAME_CHAR,  pk.NAMA_PEKERJAAN, sp.SPK_TYPE_NAME_CHAR, MAX(ss.MD_TERMIN_PROGRESS_INT) as Target,
MAX(ss.MD_TERMIN_BAYAR_INT) as Realisasi, st.SPK_TRANS_START_DATE, st.SPK_TRANS_END_DATE, st.SPK_TRANS_APPROVE_INT, mp.PROJECT_NAME,st.SPK_TRANS_NOCHAR
FROM SPK_TRANS st INNER JOIN MD_VENDOR mv
ON st.MD_VENDOR_ID_INT = mv.MD_VENDOR_ID_INT INNER JOIN PL_MASTER_PEKERJAAN pk
ON st.ID_PEKERJAAN = pk.ID_PEKERJAAN INNER JOIN SPK_TYPE sp
ON st.SPK_TRANS_TYPE = sp.SPK_TYPE_ID_INT INNER JOIN SPK_SERTIFIKAT ss
ON st.SPK_TRANS_NOCHAR = ss.SPK_TRANS_NOCHAR INNER JOIN MD_PROJECT mp
ON st.PROJECT_NO_CHAR = mp.PROJECT_NO_CHAR
WHERE st.THN_BUDGET = '2021' AND
st.SPK_TRANS_APPROVE_INT > '0' AND
st.PROJECT_NO_CHAR <> '1' AND
st.PROJECT_NO_CHAR = '".$project."' AND pk.PROJECT_NO_CHAR = '".$project."'
GROUP BY mv.MD_VENDOR_NAME_CHAR,  pk.NAMA_PEKERJAAN, sp.SPK_TYPE_NAME_CHAR, st.SPK_TRANS_START_DATE, st.SPK_TRANS_END_DATE
, st.SPK_TRANS_APPROVE_INT, mp.PROJECT_NAME, st.SPK_TRANS_NOCHAR
ORDER BY mp.PROJECT_NAME");

Table::create(array(
        "dataSource"=> $query, // ->toArray() Ini untuk yang pakai Eloquent
        "cssClass"=>array(
            "table"=>"table-bordered table-striped table-hover",
        ),
        "paging"=>array(
            "pageSize"=>25,
            "pageIndex"=>0,
        ),
        "columns"=>array(
            "#"=>array(
                "label"=>"No",
                "start"=>1,
            ),
            "MD_VENDOR_NAME_CHAR"=>array(
                "label"=>"Contractor Name",
//                "type"=>"st",
//                "format"=>"d-m-Y",
//                "displayFormat"=>"d-m-Y",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>".date('d-m-Y', strtotime($value))."</span>";
//                }
            ),
            "NAMA_PEKERJAAN"=>array(
                "label"=>"Work Name",
            ),
            "SPK_TYPE_NAME_CHAR"=>array(
                "label"=>"Work Type",
                "cssStyle"=>"font-weight:bold",
            ),
            "Target"=>array(
                "label"=>"Target %",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>".number_format($value)." %</span>";
                }
            ),
            "Realisasi"=>array(
                "label"=>"Realisasi %",
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>".number_format($value)." %</span>";
                }
            ),
            "SPK_TRANS_END_DATE"=>array(
                "label"=>"Target Realisasi",
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $nilai = strtotime($value) - strtotime(date('Y-m-d'));
                    if($nilai > 1){
                        $color = $value>70000?"#718c00":"#e83e8c";
                        return "<span style='color:$color'>".date('d-m-Y', strtotime($value))."</span>";
                    }else{
                        return '';
                    }
                },
            ),
//            "sisaBadDebt"=>array(
//                "label"=>"Bad Debt",
//                "cssStyle"=>"text-align:right",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
//                }
//            ),
//            "sisa1"=>array(
//                "label"=>"AR ".date("Y"),
//                "cssStyle"=>"text-align:right",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
//                }
//            ),
//            "sisa2"=>array(
//                "label"=>"AR ".date("Y", strtotime('+1 year')),
//                "cssStyle"=>"text-align:right",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
//                }
//            ),
//            "sisa3"=>array(
//                "label"=>"AR ".date("Y", strtotime('+2 year')),
//                "cssStyle"=>"text-align:right",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
//                }
//            ),
//            "sisa5"=>array(
//                "label"=>"AR >=".date("Y", strtotime('+3 year')),
//                "cssStyle"=>"text-align:right",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
//                }
//            ),
//            "TOTAL_SISA"=>array(
//                "label"=>"Total AR",
//                "cssStyle"=>"text-align:right",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
//                }
//            )
        )
    )
);
?>
        </div>
    </div>
<!--    <div class="row">-->
<!--        <div class="col-md-12">-->
<!---->
<!--        </div>-->
<!--    </div>-->
</div>
</body>
</html>
