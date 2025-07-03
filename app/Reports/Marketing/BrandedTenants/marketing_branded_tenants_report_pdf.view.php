<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
?>
<html>
    <style>
        .pivot-column-header, .pivot-data-header {
            font-weight: bold;
            text-align: center;
        }
        .table {
            text-align: center;
            min-width: 100%;
            width: 100%;
        }
        th {
            background-color: #081c5c;
            color: white;
        }
        .pivot-column {
            background-color: #081c5c;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 1%;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Marketing Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Branded Tenants</h1>
        </div>
        <hr/>

        <h3><b>Branded Tenants (All Category)</b></h3>
        <?php
            Table::create(array(
                "dataSource"=> $this->dataStore('marketing_branded_tenants_table1'),
                "themeBase"=>"bs4",
                "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                )
            ));
        ?>
        <div class="page-break"></div>
        <h3><b>Branded Tenants (Anchor Tenant >= 500m)</b></h3>
        <?php
            Table::create(array(
                "dataSource"=> $this->dataStore('marketing_branded_tenants_table_excel_pdf1'),
                "themeBase"=>"bs4",
                "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                )
            ));
        ?>
        <div class="page-break"></div>
        <h3><b>Branded Tenants (Non Anchor Tenant < 500m)</b></h3>
        <?php
            Table::create(array(
                "dataSource"=> $this->dataStore('marketing_branded_tenants_table_excel_pdf2'),
                "themeBase"=>"bs4",
                "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                )
            ));
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>