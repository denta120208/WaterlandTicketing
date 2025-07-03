<?php $__env->startSection('navbar_header'); ?>
    Form Generate Invoice - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Generate Invoice
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(Session::has('message')): ?>
    <div class="alert alert-success" id="success-alert">
        <?php echo e(Session::get('message')); ?>

    </div>
<?php elseif(Session::has('error')): ?>
    <div class="alert alert-danger" id="success-alert">
        <?php echo e(Session::get('error')); ?>

    </div>
<?php endif; ?>

<style>
th, td {
    padding: 5px;
}

.vertical-text {
    float: left;
    transform: rotate(270deg);
    padding: 1em;
    font-size: 1em;
    color: black;
}
</style>

<script type="text/javascript">
    function checkEmptyStringValidation(arr){
        var dataArr;

        if(arr == null){
             return dataArr = "";
        }else{
            return dataArr = arr;
        }
    }
</script>
<script type="text/javascript">
    $(function() {
        $('#dt_transaksi').on('change',function(){
            var x = new Date($('#dt_transaksi').val());
            var currMonth = $('#monthdate').val();
            var currYear = $('#yeardate').val();
            var rqsMonth = x.getMonth() + 1;
            var rqsYear = x.getFullYear();

            if (currMonth < 10)
            {
                var currDate = currYear+'0'+currMonth;
            }
            else
            {
                var currDate = currYear+currMonth;
            }

            var currDateInt = parseInt(currDate);

            if (rqsMonth < 10)
            {
                var rqsDate = rqsYear+'0'+rqsMonth;
            }
            else
            {
                var rqsDate = rqsYear+''+rqsMonth;
            }

            var rqsDateInt = parseInt(rqsDate);
            if(rqsDateInt < currDateInt)
            {
                document.getElementById("backdate").value = null;
            }
            else
            {
                document.getElementById("backdate").value = 'Proses';
            }
        });
    });

    function filterColumn ( i )
    {
         $('#billingpayment_sales_report').DataTable().column( i ).search(
                $('#col'+i+'_filter').val()
                ).draw();
    }

     $(function()
    {
        $( "#startDate" ).datepicker({
              dateFormat: "yy-mm-dd",
              changeMonth: true,
              changeYear: true,
              onClick : function(date){
               document.getElementById('startDate').value = date;
              }
        });
    });

    $(function()
    {
        $( "#endDate" ).datepicker({
             dateFormat: "yy-mm-dd",
              changeMonth: true,
              changeYear: true,
               onClick : function(date){
               document.getElementById('endDate').value = date;
              }

        });
    });

    $(document).ready(function() {
       $.fn.DataTable.ext.search.push(
            function(settings, data, dataindex) {
                var startDate = Date.parse($('#startDate').val());
                var endDate = Date.parse($('#endDate').val());
                var dateColumn = Date.parse( data[0] ) || 0; // use data for the date column

                if ( ( isNaN( startDate ) && isNaN( endDate ) ) ||
                    ( isNaN( startDate ) && dateColumn <= endDate ) ||
                    ( startDate <= dateColumn   && isNaN( endDate ) ) ||
                    ( startDate <= dateColumn   && dateColumn <= endDate ) )
                {
                    return true;
                }
                    return false;
            }
        );

       $('#tdp_report').DataTable( {
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
               {
                   extend: 'excelHtml5',
                   title: '<?php echo "Data Generate Invoice "; ?>'
               },
               {
                   extend: 'pdfHtml5',
                   footer: true,
                   title: '<?php echo "Data Generate Invoice "; ?>'
               }
            ]
       });

        $('#tdp_report1').DataTable( {
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
} );
</script>
<script>
    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
            document.getElementById('all').remove();
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'selectall', value: 'all', class: 'form-control',id: 'all'}))
            tz.appendTo('#temp-form');
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                console.log(i)
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
            document.getElementById('all').remove();
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'selectall', value: 'none', class: 'form-control',id: 'all'}))
            tz.appendTo('#temp-form');
        }
    }

    function selected(ele,billid){
        if (ele.checked) {
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'billing[]', value: billid, class: 'form-control',id: billid}))
            tz.appendTo('#temp-form');
        } else {
            document.getElementById(billid).remove();
        }
    }
</script>

<div class="modal fade" id="edit-desc-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Edit Description</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('gi.generateInvoiceEditDesc')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-5">Description *</label>
                    <div class="col-sm-12">
                        <input type="hidden" class="form-control" name="SCHEDULE_ID_EDIT" id="SCHEDULE_ID_EDIT" style="width: 100%;" placeholder="Schedule ID" readonly required />
                        <input type="hidden" class="form-control" name="CUT_OFF_POST" id="CUT_OFF_POST" style="width: 100%;" placeholder="Cut Off" value="<?php echo e(empty($dateCutOffReal) ? NULL : $dateCutOffReal); ?>" readonly required />
                        <input type="hidden" class="form-control" name="TYPE_POST" id="TYPE_POST" style="width: 100%;" placeholder="Type" value="<?php echo e(empty($dataCatgory) ? NULL : $dataCatgory); ?>" readonly required />
                        <input type="text" class="form-control" name="DESCRIPTION_EDIT" id="DESCRIPTION_EDIT" style="width: 100%;" placeholder="Description" required />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Edit</button>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('invoice.viewlistgenerateinvoice')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Cut Off Date</label>
                                    <input type="date" class="form-control" name="cutOffDate" placeholder="Cut Off Date">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="category" class="form-control">
                                        <option value="CasualLeasing">Casual Leasing</option>
                                        <option value="Rental">Rental</option>
                                        <option value="SecurityDeposit">Security Deposit</option>
                                        <option value="ServiceCharge">Service Charge</option>
                                        <option value="Utility">Utility</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 mt-4" style="padding-top: 8px; padding-left: 15px;">
                                <div class="form-group row">
                                    <button type="submit" class="btn btn-primary">View Data</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-sm-12">
                            <b>Cut Off Date : <?php echo e($dateCutOff); ?></b><br>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <?php if($dataGenerateInv <> 0 && ($dataCatgory == 'Rental')): ?>
                                <form action="<?php echo e(URL::route('invoice.generateinvoicerental')); ?>" method="post">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="cutoff" value="<?php echo $cutoff; ?>" class="form-control" readonly>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Doc. Date*</label>
                                                <input type="date" class="form-control" name="docDate" placeholder="Doc Date" id="dt_transaksi">
                                                <input type="hidden" name="monthdate" id="monthdate" value="<?php echo $dataProject['MONTH_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="yeardate" id="yeardate" value="<?php echo $dataProject['YEAR_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="backdate" id="backdate" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Due Date*</label>
                                                <input type="date" class="form-control" name="dueDate" placeholder="Due Date">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Transaction Code (Tax)*</label>
                                                <select name="TRANS_CODE" class="form-control">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                            <th>No.</th>
                                            <th>Tenant</th>
                                            <th>Shop Name</th>
                                            <th>Lot</th>
                                            <th>Bill Date</th>
                                            <th>Description</th>
                                            <th>PPH Status</th>
                                            <th>Discount</th>
                                            <th>DPP</th>
                                            <th>PPN</th>
                                            <th>Bill Amount</th>
                                            <th>Print Proforma</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; ?>
                                        <?php $__currentLoopData = $dataInvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>)" value="<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>" id="idbilling"></td>
                                                <td style="text-align: center;"><?php echo e($i); ?></td>
                                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                                <td><?php echo e($data->TGL_SCHEDULE_DATE); ?></td>
                                                <td><a href="javascript:void(0)" onclick="editDataDesc('<?php echo $data->PSM_SCHEDULE_ID_INT ?>','<?php echo $data->DESC_CHAR ?>')"><?php echo e($data->DESC_CHAR); ?></a></td>
                                                <?php if($data->MD_TENANT_PPH_INT == 1): ?>
                                                    <td>Potong Tenant</td>
                                                <?php elseif($data->MD_TENANT_PPH_INT == 0): ?>
                                                    <td>Potong Sendiri</td>
                                                <?php else: ?>
                                                    <td>NONE</td>
                                                <?php endif; ?>
                                                <td style="text-align: right;"><?php echo e(number_format($data->DISC_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BASE_AMOUNT_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->PPN_PRICE_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILL_AMOUNT,0,'','')); ?></td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="<?php echo e(URL('/invoice/printinvoiceperforma/' . $data->PSM_SCHEDULE_ID_INT.'/'.$cutoff)); ?>" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print Proforma
                                                        </i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <div id="temp-form">
                                        <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                        <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                    </div>
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Generate Data
                                        </a>
                                        <div id="confModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure generate this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                        <input type="submit" value="Yes" class="btn btn-success">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php elseif($dataGenerateInv <> 0 && ($dataCatgory == 'SecurityDeposit')): ?>
                                <form action="<?php echo e(URL::route('invoice.generateinvoicesecuritydesposit')); ?>" method="post">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="cutoff" value="<?php echo $cutoff; ?>" class="form-control" readonly>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Doc. Date*</label>
                                                <input type="date" class="form-control" name="docDate" placeholder="Doc Date" id="dt_transaksi">
                                                <input type="hidden" name="monthdate" id="monthdate" value="<?php echo $dataProject['MONTH_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="yeardate" id="yeardate" value="<?php echo $dataProject['YEAR_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="backdate" id="backdate" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Due Date*</label>
                                                <input type="date" class="form-control" name="dueDate" placeholder="Due Date">
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                            <th>No.</th>
                                            <th>Tenant</th>
                                            <th>Shop Name</th>
                                            <th>Lot</th>
                                            <th>Bill Date</th>
                                            <th>Description</th>
                                            <th>Bill Amount</th>
                                            <th>Print Proforma</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; ?>
                                        <?php $__currentLoopData = $dataInvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>)" value="<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>" id="idbilling"></td>
                                                <td style="text-align: center;"><?php echo e($i); ?></td>
                                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                                <td><?php echo e($data->TGL_SCHEDULE_DATE); ?></td>
                                                <td><a href="javascript:void(0)" onclick="editDataDesc('<?php echo $data->PSM_SCHEDULE_ID_INT ?>','<?php echo $data->DESC_CHAR ?>')"><?php echo e($data->DESC_CHAR); ?></a></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILL_AMOUNT,0,'','')); ?></td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="<?php echo e(URL('/invoice/printinvoiceperforma/' . $data->PSM_SCHEDULE_ID_INT.'/'.$cutoff)); ?>" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print Proforma
                                                        </i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                            $i ++;
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <div id="temp-form">
                                        <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                        <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                    </div>
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Generate Data
                                        </a>
                                        <div id="confModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure generate this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                        <input type="submit" value="Yes" class="btn btn-success">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php elseif($dataGenerateInv <> 0 && ($dataCatgory == 'ServiceCharge')): ?>
                                <form action="<?php echo e(URL::route('invoice.generateinvoiceservicecharge')); ?>" method="post">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="cutoff" value="<?php echo $cutoff; ?>" class="form-control" readonly>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Doc. Date*</label>
                                                <input type="date" class="form-control" name="docDate" placeholder="Doc Date" id="dt_transaksi">
                                                <input type="hidden" name="monthdate" id="monthdate" value="<?php echo $dataProject['MONTH_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="yeardate" id="yeardate" value="<?php echo $dataProject['YEAR_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="backdate" id="backdate" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Due Date</label>
                                                <input type="date" class="form-control" name="dueDate" placeholder="Due Date">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Transaction Code (Tax)*</label>
                                                <select name="TRANS_CODE" class="form-control">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                            <th>No.</th>
                                            <th>Tenant</th>
                                            <th>Shop Name</th>
                                            <th>Lot</th>
                                            <th>Bill Date</th>
                                            <th>Description</th>
                                            <th>PPH Status</th>
                                            <th>Discount</th>
                                            <th>DPP</th>
                                            <th>PPN</th>
                                            <th>Bill Amount</th>
                                            <th>Print Proforma</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; ?>
                                        <?php $__currentLoopData = $dataInvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>)" value="<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>" id="idbilling"></td>
                                                <td style="text-align: center;"><?php echo e($i); ?></td>
                                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                                <td><?php echo e($data->TGL_SCHEDULE_DATE); ?></td>
                                                <td><a href="javascript:void(0)" onclick="editDataDesc('<?php echo $data->PSM_SCHEDULE_ID_INT ?>','<?php echo $data->DESC_CHAR ?>')"><?php echo e($data->DESC_CHAR); ?></a></td>
                                                <?php if($data->MD_TENANT_PPH_INT == 1): ?>
                                                    <td>Potong Tenant</td>
                                                <?php elseif($data->MD_TENANT_PPH_INT == 0): ?>
                                                    <td>Potong Sendiri</td>
                                                <?php else: ?>
                                                    <td>NONE</td>
                                                <?php endif; ?>
                                                <td style="text-align: right;"><?php echo e(number_format($data->DISC_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BASE_AMOUNT_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->PPN_PRICE_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILL_AMOUNT,0,'','')); ?></td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="<?php echo e(URL('/invoice/printinvoiceperformaservicecharge/' . $data->PSM_SCHEDULE_ID_INT.'/'.$cutoff)); ?>" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print Proforma
                                                        </i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                            $i ++;
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <div id="temp-form">
                                        <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                        <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                    </div>
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Generate Data
                                        </a>
                                        <div id="confModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure generate this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                        <input type="submit" value="Yes" class="btn btn-success" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php elseif($dataGenerateInv <> 0 && $dataCatgory == 'Utility'): ?>
                                <form action="<?php echo e(URL::route('invoice.generateinvoiceutility')); ?>" method="post">
                                    <input type="hidden" name="cutoff" value="<?php echo $cutoff; ?>" class="form-control" readonly>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Doc. Date*</label>
                                                <input type="date" class="form-control" name="docDate" placeholder="Doc Date" id="dt_transaksi">
                                                <input type="hidden" name="monthdate" id="monthdate" class="form-control" value="<?php echo $dataProject['MONTH_PERIOD']?>" readonly>
                                                <input type="hidden" name="yeardate" id="yeardate" class="form-control" value="<?php echo $dataProject['YEAR_PERIOD']?>" readonly>
                                                <input type="hidden" name="backdate" id="backdate" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Due Date</label>
                                                <input type="date" class="form-control" name="dueDate" placeholder="Due Date">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Transaction Code (Tax)*</label>
                                                <select name="TRANS_CODE" class="form-control">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-striped table-hover compact" id="tdp_report1" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                            <th>No.</th>
                                            <th>Tenant</th>
                                            <th>Shop Name</th>
                                            <th>Lot</th>
                                            <th>Bill Date</th>
                                            <th>Description</th>
                                            <th>PPH Status</th>
                                            <th>Start LWBP</th>
                                            <th>End LWBP</th>
                                            <th>Start WBP</th>
                                            <th>End WBP</th>
                                            <th>DPP</th>
                                            <th>PPN</th>
                                            <th>Bill Amount</th>
                                            <th>Print Performa</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; ?>
                                        <?php $__currentLoopData = $dataInvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->ID_BILLING;  ?>)" value="<?php echo $data->ID_BILLING;  ?>" id="idbilling"></td>
                                                <td style="text-align: center;"><?php echo e($i); ?></td>
                                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                                <td><?php echo e($data->BILLING_DATE); ?></td>
                                                <td><?php echo e($data->UTILS_TYPE_NAME); ?></td>
                                                <?php if($data->MD_TENANT_PPH_INT == 1): ?>
                                                    <td>Potong Tenant</td>
                                                <?php elseif($data->MD_TENANT_PPH_INT == 0): ?>
                                                    <td>Potong Sendiri</td>
                                                <?php else: ?>
                                                    <td>NONE</td>
                                                <?php endif; ?>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILLING_METER_START_LWBP,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILLING_METER_END_LWBP,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILLING_METER_START_WBP,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILLING_METER_END_WBP,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->DPP,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->PPN,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->TOTAL,0,'','')); ?></td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="<?php echo e(URL('/invoice/printinvoiceperformautility/' . $data->ID_BILLING.'/'.$cutoff)); ?>" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print Performa
                                                        </i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <div id="temp-form">
                                        <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                        <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                    </div>
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Generate Data
                                        </a>
                                        <div id="confModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure generate this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                        <input type="submit" value="Yes" class="btn btn-success">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php elseif($dataGenerateInv <> 0 && ($dataCatgory == 'CasualLeasing')): ?>
                                <form action="<?php echo e(URL::route('invoice.generateinvoicecasual')); ?>" method="post">
                                    <input type="hidden" name="cutoff" value="<?php echo $cutoff; ?>" class="form-control" readonly>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Doc. Date*</label>
                                                <input type="date" class="form-control" name="docDate" placeholder="Doc Date" id="dt_transaksi">
                                                <input type="hidden" name="monthdate" id="monthdate" value="<?php echo $dataProject['MONTH_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="yeardate" id="yeardate" value="<?php echo $dataProject['YEAR_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="backdate" id="backdate" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Due Date*</label>
                                                <input type="date" class="form-control" name="dueDate" placeholder="Due Date">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Transaction Code (Tax)*</label>
                                                <select name="TRANS_CODE" class="form-control">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                            <th>No.</th>
                                            <th>Tenant</th>
                                            <th>Shop Name</th>
                                            <th>Lot</th>
                                            <th>Bill Date</th>
                                            <th>Description</th>
                                            <th>PPH Status</th>
                                            <th>Discount</th>
                                            <th>DPP</th>
                                            <th>PPN</th>
                                            <th>Bill Amount</th>
                                            <th>Print Proforma</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; ?>
                                        <?php $__currentLoopData = $dataInvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>)" value="<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>" id="idbilling"></td>
                                                <td style="text-align: center;"><?php echo e($i); ?></td>
                                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                                <td><?php echo e($data->TGL_SCHEDULE_DATE); ?></td>
                                                <td><a href="javascript:void(0)" onclick="editDataDesc('<?php echo $data->PSM_SCHEDULE_ID_INT ?>','<?php echo $data->DESC_CHAR ?>')"><?php echo e($data->DESC_CHAR); ?></a></td>
                                                <?php if($data->MD_TENANT_PPH_INT == 1): ?>
                                                    <td>Potong Tenant</td>
                                                <?php elseif($data->MD_TENANT_PPH_INT == 0): ?>
                                                    <td>Potong Sendiri</td>
                                                <?php else: ?>
                                                    <td>NONE</td>
                                                <?php endif; ?>
                                                <td style="text-align: right;"><?php echo e(number_format($data->DISC_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BASE_AMOUNT_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->PPN_PRICE_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILL_AMOUNT,0,'','')); ?></td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="<?php echo e(URL('/invoice/printinvoiceperforma/' . $data->PSM_SCHEDULE_ID_INT.'/'.$cutoff)); ?>" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print Proforma
                                                        </i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <div id="temp-form">
                                        <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                        <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                    </div>
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Generate Data
                                        </a>
                                        <div id="confModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure generate this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                        <input type="submit" value="Yes" class="btn btn-success">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php elseif($dataGenerateInv <> 0 && ($dataCatgory == 'Others')): ?>
                                <form action="<?php echo e(URL::route('invoice.generateinvoiceothers')); ?>" method="post">
                                    <input type="hidden" name="cutoff" value="<?php echo $cutoff; ?>" class="form-control" readonly>
                                    <br><br>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Doc. Date*</label>
                                                <input type="date" class="form-control" name="docDate" placeholder="Doc Date" id="dt_transaksi">
                                                <input type="hidden" name="monthdate" id="monthdate" value="<?php echo $dataProject['MONTH_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="yeardate" id="yeardate" value="<?php echo $dataProject['YEAR_PERIOD'] ?>" class="form-control" readonly>
                                                <input type="hidden" name="backdate" id="backdate" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>Due Date*</label>
                                                <input type="date" class="form-control" name="dueDate" placeholder="Due Date">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Transaction Code (Tax)*</label>
                                                <select name="TRANS_CODE" class="form-control">
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                            <th>No.</th>
                                            <th>Tenant</th>
                                            <th>Shop Name</th>
                                            <th>Lot</th>
                                            <th>Bill Date</th>
                                            <th>Description</th>
                                            <th>PPH Status</th>
                                            <th>Discount</th>
                                            <th>DPP</th>
                                            <th>PPN</th>
                                            <th>Bill Amount</th>
                                            <th>Print Proforma</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; ?>
                                        <?php $__currentLoopData = $dataInvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>)" value="<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>" id="idbilling"></td>
                                                <td style="text-align: center;"><?php echo e($i); ?></td>
                                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                                <td><?php echo e($data->TGL_SCHEDULE_DATE); ?></td>
                                                <td><a href="javascript:void(0)" onclick="editDataDesc('<?php echo $data->PSM_SCHEDULE_ID_INT ?>','<?php echo $data->DESC_CHAR ?>')"><?php echo e($data->DESC_CHAR); ?></a></td>
                                                <?php if($data->MD_TENANT_PPH_INT == 1): ?>
                                                    <td>Potong Tenant</td>
                                                <?php elseif($data->MD_TENANT_PPH_INT == 0): ?>
                                                    <td>Potong Sendiri</td>
                                                <?php else: ?>
                                                    <td>NONE</td>
                                                <?php endif; ?>
                                                <td style="text-align: right;"><?php echo e(number_format($data->DISC_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BASE_AMOUNT_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->PPN_PRICE_NUM,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->BILL_AMOUNT,0,'','')); ?></td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="<?php echo e(URL('/invoice/printinvoiceperforma/' . $data->PSM_SCHEDULE_ID_INT.'/'.$cutoff)); ?>" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print Proforma
                                                        </i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <div id="temp-form">
                                        <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                        <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                    </div>
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Generate Data
                                        </a>
                                        <div id="confModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure generate this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                        <input type="submit" value="Yes" class="btn btn-success">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editDataDesc(PSM_SCHEDULE_ID_INT, DESC_CHAR) {
        $("#SCHEDULE_ID_EDIT").val(PSM_SCHEDULE_ID_INT);
        $("#DESCRIPTION_EDIT").val(DESC_CHAR);
        $('#edit-desc-modal').modal('show');
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/accountreceivable/listDataGenerateInv.blade.php ENDPATH**/ ?>