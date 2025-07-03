<?php
use \koolreport\widgets\koolphp\Table;
use \koolreport\widgets\google\BarChart;

?>
<html>
<head>
    <title>AR Report</title>
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
    <div class="row">
        <div class="col-md-12">
<?php
//$this->dataStore("adds"),array('2021/01/01','2021/10/31','2021/10/31','20','2')
Table::create(array(
        "dataSource"=> DB::select("exec sp_piutang_sum_002 '2021/01/01','2021/10/31','2021/10/31','20','2'"), // ->toArray() Ini untuk yang pakai Eloquent
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
            "CUSTOMER_NAME_CHAR"=>array(
                "label"=>"Customer Name",
//                "type"=>"st",
//                "format"=>"d-m-Y",
//                "displayFormat"=>"d-m-Y",
//                "formatValue"=>function($value,$row){
//                    $color = $value>70000?"#718c00":"#e83e8c";
//                    return "<span style='color:$color'>".date('d-m-Y', strtotime($value))."</span>";
//                }
            ),
            "KODE_STOK_UNIQUE_ID_CHAR"=>array(
                "label"=>"Unit",
            ),
            "SALES_TYPE_CHAR"=>array(
                "label"=>"Sales Type",
                "cssStyle"=>"font-weight:bold",
            ),
            "PROGRESS_TECH_INT"=>array(
                "label"=>"Progress %",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>".$value." %</span>";
                }
            ),
            "TOTAL_PRICE"=>array(
                "label"=>"Total Price",
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            ),
            "PAID_BILL_AMOUNT_NUM"=>array(
                "label"=>"Receipt",
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            ),
            "sisaBadDebt"=>array(
                "label"=>"Bad Debt",
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            ),
            "sisa1"=>array(
                "label"=>"AR ".date("Y"),
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            ),
            "sisa2"=>array(
                "label"=>"AR ".date("Y", strtotime('+1 year')),
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            ),
            "sisa3"=>array(
                "label"=>"AR ".date("Y", strtotime('+2 year')),
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            ),
            "sisa5"=>array(
                "label"=>"AR >=".date("Y", strtotime('+3 year')),
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            ),
            "TOTAL_SISA"=>array(
                "label"=>"Total AR",
                "cssStyle"=>"text-align:right",
                "formatValue"=>function($value,$row){
                    $color = $value>70000?"#718c00":"#e83e8c";
                    return "<span style='color:$color'>Rp. ".number_format($value)."</span>";
                }
            )
        )
    )
);
?>
        </div>
    </div>
</div>
</body>
</html>
