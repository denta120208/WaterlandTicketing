<?php $__env->startSection('content_report'); ?>

<html>
    <header>
        <style>
            @page  { margin: 10mm;}
            table, th, td {
                border: 0px black;
            }
            th, td {
                padding: 1px;
            }
            div.headeratas{
                padding-left: 2em;
                margin-left: 9em;
                text-align:center;
                margin-top:-1em;
                font-size: 10px
            }
            div.headeratas5{
                padding-left: 2em;
                margin-left: 9em;
                text-align:center;
                margin-top:5em;
                font-size: 10px
            }
            div.headeratastengah{
                padding-left: 2em;
                /*margin-left: 8em;*/
                text-align:left;
                margin-top:-1em;
                margin-left:20em;
                font-size: 10px
            }
            div.headerbawahtengah{
                padding-left: 2em;
                margin-left: -1em;
                text-align:center;
                margin-top: 1em;
                font-size: 12px;
            }
            div.headerbawahtengah2{
                margin-top: 1em;
                font-size: 12px;
            }
            div.headerbawahtengah3{
                margin-top: 1em;
                font-size: 14px;
            }
            div.headerataskiri{
                text-align:left;
                margin-right:-7em;
                margin-top: -1em;
            }
            div.headerataskiri5{
                text-align:left;
                margin-right:-7em;
                margin-top: -30px;
                font-size: 9px;
            }
            div.headeratastengah5{
                padding-left: 2em;
                text-align:left;
                margin-top: -30px;
                margin-left:20em;
                font-size: 9px;
            }
            div.headerataskanan5{
                margin-top:-30px;
                text-align:right;
                font-size: 9px;
            }
            div.headerbawahtengah5{
                margin-left: 5em;
                text-align:center;
                margin-top: 1em;
                font-size: 12px;
            }
            div.headerbawahkiri{
                text-align:left; 
            }
            div.headerataskanan{
                margin-top:2em;
                text-align:right;
                font-size: 8px;
            }
            div.headerbawahkanan{
                margin-top:-3em;
                text-align:right;
                font-size: 8px;
            }
            table.kananatas{
                align:right;
            }
            div.tanggalbawah{
                 text-align: left;
                 font-size: 11px;
            }
            div.ttdatas{
               margin-top:0em;
               text-align: left;
               font-size: 11px;
            }
            div.ttdbawah{
               margin-top:2em;
               text-align: left;
            }
            div.salesbawah{
               margin-top:0em;
               margin-right:8em;
               margin-bottom:5em;
               text-align: right;
            }
            div.bawah{
               margin-right:6em;
               text-align: right;
            }
            div.tableCustomer{
                text-align:left;
                margin-left: 2em;
                padding-left: 2em;
            }
            div.termAndConditionAtas{
                text-align:left;
                font-size: 10px;
                margin-bottom:-1em;
            }
            div.termAndConditionBawah{
                 text-align:left;
                 margin-left: 3em;
                 font-size: 10px;
            }
            div.termAndConditionNextPage{
                 text-align:left;
                 padding: 1em;
                 font-size: 12px;
                 margin-top:0em;
            }
            div.rekeningAtasTermConditions{
                 text-align:center;
                 margin-left: 2em;
                 font-size: 12px;
            }
            div.rekeningBawahTermConditions{
                 text-align:center;
                 margin-left: -2em;
                 font-size: 12px;
            }
            div.Customer{
                padding-left: 3em;
                font-size: 11px;
            }
            div.dataBookingEntry{
                 font-size: 11px;
                 margin-top: -5em;
            }
            div.page-break {
                page-break-after: always;
            }
            div.tabelCicilan{
                font-size: 13px;
                text-align: center;
                padding: 1em;
                padding-bottom: 1em;
            }
            div.parafTermCondition{
                 text-align: right;
                 font-size: 11px;
            }
            thead{
                font-size: 11px;
                text-align: center;
                margin-bottom: 1em;  
            }
            tbody{
                font-size: 11px;
                text-align: left;
            }
            table {
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid black;
                padding: 7px;
            }
            #watermark p {
                position: absolute;
                top: 0;
                left: 0;
                color: #0c0c0c;
                margin-top: 300;
                margin-left: 200;
                font-size: 130;
                font-family: "Arial Black", "Arial Bold";
                opacity: 0.15;
                pointer-events: none;
            }
        </style>
    </header>
    <body>
        <div class="row">
            <div id="watermark">
                <b><p>Metland</p></b>
            </div>
        </div>
        <div class="row">
            <div>
                <center>
                    <div class="headerbawahtengah">
                        <h1><b><label>Visitors By Time</label></b></h1>
                        <?php
                            $dataProject = DB::select("SELECT * FROM MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = '".$project_no."'");
                            $userName = trim(session('first_name') . ' ' . session('last_name'));
                        ?>
                        <h3><b><?php echo e($dataProject[0]->PROJECT_NAME); ?></b></h3>
                        <h3><b><?php echo e(date('d/m/Y', strtotime($report->start_date_param))); ?> - <?php echo e(date('d/m/Y', strtotime($report->end_date_param))); ?></b></h3>
                        <h3><b><?php echo e($report->kategori_param); ?></b></h3>
                        <h3><b>Printed by <?php echo e($userName); ?> at <?php echo e(date('d/m/Y H:i')); ?></b></h3>
                    </div>
                </center>
            </div>
            <hr />
        </div>
        <br>
        <table class="stripe hover compact" id="data_bill_payment" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th rowspan="2">YEAR</th>
                    <th colspan="2">JAN</th>
                    <th colspan="2">FEB</th>
                    <th colspan="2">MAR</th>
                    <th colspan="2">APR</th>
                    <th colspan="2">MAY</th>
                    <th colspan="2">JUN</th>
                    <th colspan="2">JUL</th>
                    <th colspan="2">AUG</th>
                    <th colspan="2">SEP</th>
                    <th colspan="2">OCT</th>
                    <th colspan="2">NOV</th>
                    <th colspan="2">DEC</th>
                    <th colspan="3">TOTAL</th>
                </tr>
                <tr>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>PAID (AVG)</th>
                </tr>
            </thead>
            <tbody>
            <?php if(count($dataVisitorsByTime) > 0): ?>
            <?php
                $JanTicket = 0; $JanPaid = 0;
                $FebTicket = 0; $FebPaid = 0;
                $MarTicket = 0; $MarPaid = 0;
                $AprTicket = 0; $AprPaid = 0;
                $MayTicket = 0; $MayPaid = 0;
                $JunTicket = 0; $JunPaid = 0;
                $JulTicket = 0; $JulPaid = 0;
                $AugTicket = 0; $AugPaid = 0;
                $SepTicket = 0; $SepPaid = 0;
                $OctTicket = 0; $OctPaid = 0;
                $NovTicket = 0; $NovPaid = 0;
                $DecTicket = 0; $DecPaid = 0;
                $totalTicket = 0; $totalPaid = 0;
            ?>
            <?php $__currentLoopData = $dataVisitorsByTime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align: left"><?php echo e($data['YEAR']); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['JAN_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['JAN_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['FEB_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['FEB_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['MAR_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['MAR_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['APR_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['APR_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['MAY_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['MAY_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['JUN_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['JUN_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['JUL_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['JUL_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['AUG_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['AUG_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['SEP_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['SEP_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['OCT_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['OCT_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['NOV_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['NOV_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['DEC_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['DEC_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['TOTAL_TICKET'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['TOTAL_PAID'],0,',','.')); ?></td>
                    <td style='text-align: right'><?php echo e(number_format($data['TOTAL_PAID (AVG)'],0,',','.')); ?></td>
                </tr>
                <?php
                    $JanTicket += $data['JAN_TICKET']; $JanPaid += $data['JAN_PAID'];
                    $FebTicket += $data['FEB_TICKET']; $FebPaid += $data['FEB_PAID'];
                    $MarTicket += $data['MAR_TICKET']; $MarPaid += $data['MAR_PAID'];
                    $AprTicket += $data['APR_TICKET']; $AprPaid += $data['APR_PAID'];
                    $MayTicket += $data['MAY_TICKET']; $MayPaid += $data['MAY_PAID'];
                    $JunTicket += $data['JUN_TICKET']; $JunPaid += $data['JUN_PAID'];
                    $JulTicket += $data['JUL_TICKET']; $JulPaid += $data['JUL_PAID'];
                    $AugTicket += $data['AUG_TICKET']; $AugPaid += $data['AUG_PAID'];
                    $SepTicket += $data['SEP_TICKET']; $SepPaid += $data['SEP_PAID'];
                    $OctTicket += $data['OCT_TICKET']; $OctPaid += $data['OCT_PAID'];
                    $NovTicket += $data['NOV_TICKET']; $NovPaid += $data['NOV_PAID'];
                    $DecTicket += $data['DEC_TICKET']; $DecPaid += $data['DEC_PAID'];
                    $totalTicket += $data['TOTAL_TICKET']; $totalPaid += $data['TOTAL_PAID'];
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="text-align: center; font-size: 120%;"><b>TOTAL</b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($JanTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($JanPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($FebTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($FebPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($MarTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($MarPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($AprTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($AprPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($MayTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($MayPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($JunTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($JunPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($JulTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($JulPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($AugTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($AugPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($SepTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($SepPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($OctTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($OctPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($NovTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($NovPaid,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($DecTicket,0,',','.')); ?></b></td>
                <td style='text-align: right; font-size: 120%;'><b><?php echo e(number_format($DecPaid,0,',','.')); ?></b></td>
                <td style="text-align: right; font-size: 120%;"><b><?php echo e(number_format($totalTicket,0,',','.')); ?></b></td>
                <td style="text-align: right; font-size: 120%;"><b><?php echo e(number_format($totalPaid,0,',','.')); ?></b></td>
                <td style="text-align: right; font-size: 120%;"><b>-</b></td>
            </tr>
            <?php else: ?>
            <tr>
                <td style="text-align: center" colspan="28"><b>No data available</b></td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </body>
</html>
<?php echo $__env->make('Sales.Report.visitorsByTime.pdfcetak', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/watergroup/public_html/metland_water/resources/views/Sales/Report/visitorsByTime/pdfReportVisitorsByTimePerbulan.blade.php ENDPATH**/ ?>