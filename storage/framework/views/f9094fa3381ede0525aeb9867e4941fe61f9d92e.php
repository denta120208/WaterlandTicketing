<?php $__env->startSection('navbar_header'); ?>
    Form Add Data Letter Of Intent - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Add Data Letter Of Intent
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        $('#vendor_table').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false
        });

        $('#vendor_table_cl').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false
        });

        $('#vendor_table_dp').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false
        });

        $('#vendor_table_rt').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false
        });

        $('#vendor_table_sc').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false
        });
    } );
</script>
<script>
    $(document).ready(function() {
        $('#commission_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });
    });

    $(document).ready(function() {
        $('#schedule_table').DataTable({
            order : []
        });
    } );

    $(document).ready(function() {
        $('#data_securedep_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

    $(function() {
        var table = $('#trx_table').DataTable({
            order:[]
        });

        $('#trx_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('trx_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('trx_code').value = checkEmptyStringValidation(data[1]);
            $('#trxModal').modal('hide');
        });

        $('#trx_desc').on('click',function(){
            $('#trxModal').modal('show');
        });
    });

    $(function() {
        var table = $('#stock_table').DataTable({
            order:[]
        });

        $('#stock_table tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            document.getElementById('stock_no').value = checkEmptyStringValidation(data[0]);
            document.getElementById('stock_id').value = checkEmptyStringValidation(data[1]);
            document.getElementById('stock_sqm').value = checkEmptyStringValidation(data[2])+' / '+checkEmptyStringValidation(data[3]);
            document.getElementById('stock_sqm_rt').value = checkEmptyStringValidation(data[2]);
            document.getElementById('stock_sqm_sc').value = checkEmptyStringValidation(data[3]);

            var rent_amount = parseInt($("#rent_amount").val());
            var disk_amount = parseInt($("#disk_amount").val());
            var nilaiNetBeforeTax = (rent_amount * checkEmptyStringValidation(data[2]));

            var dpp = nilaiNetBeforeTax - disk_amount;
            var ppn = dpp * 0.11;
            var total = dpp + ppn;

            document.getElementById("net_before_tax_real").value = dpp;
            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax_real").value = ppn;
            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total_real").value = total;
            document.getElementById("price_total").value = total.formatMoney(0);

            $('#stockModal').modal('hide');
        });

        $('#stock_no').on('click',function() {
            $('#stockModal').modal('show');
        });
    });

    $(function() {
        var table = $('#tenant_table').DataTable({
            order:[]
        });

        $('#tenant_table tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            document.getElementById('tenant_name').value = checkEmptyStringValidation(data[0]);
            document.getElementById('tenant_id').value = checkEmptyStringValidation(data[1]);
            $('#tenantModal').modal('hide');
        });

        $('#tenant_name').on('click',function() {
            $('#tenantModal').modal('show');
        });
    });

    $(function() {
        var table = $('#secure_dep_table').DataTable({
            order:[]
        });

        $('#secure_dep_table tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            document.getElementById('deposit_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('deposit_desc').value = checkEmptyStringValidation(data[1]);
            $('#secureDepModal').modal('hide');
        });

        $('#deposit_desc').on('click',function() {
            $('#secureDepModal').modal('show');
        });
    });

    $(function() {
        var table = $('#shop_type_table').DataTable({
            order:[]
        });

        $('#shop_type_table tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            document.getElementById('shop_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('shop_type_id').value = checkEmptyStringValidation(data[1]);
            $('#shopTypeModal').modal('hide');
        });

        $('#shop_type').on('click',function() {
            $('#shopTypeModal').modal('show');
        });
    });

    $(function() {
        var table = $('#sales_type_table').DataTable({
            order:[]
        });

        $('#sales_type_table tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            document.getElementById('sales_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('sales_id').value = checkEmptyStringValidation(data[1]);
            $('#salesModal').modal('hide');
        });

        $('#sales_desc').on('click',function() {
            $('#salesModal').modal('show');
        });
    });

    $(function() {
        var table = $('#address_tax_table').DataTable({
            order:[]
        });

        $('#address_tax_table tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            document.getElementById('tenant_address_tax').value = checkEmptyStringValidation(data[0]);
            document.getElementById('tenant_address_tax_id').value = checkEmptyStringValidation(data[1]);
            $('#addressTaxModal').modal('hide');
        });

        $('#tenant_address_tax').on('click',function() {
            $('#addressTaxModal').modal('show');
        });
    });

    function checkEmptyStringValidation(arr) {
        var dataArr;

        if(arr == null) {
             return dataArr = "";
        } else {
            return dataArr = arr;
        }
    }

    $(function() {
        $('#rent_amount').on('change',function() {
            var rent_amount = parseInt($("#rent_amount").val());
            var stock_sqm_rt = parseFloat($("#stock_sqm_rt").val());
            var disk_amount = parseInt($("#disk_amount").val());

            var nilaiNetBeforeTax = (rent_amount * stock_sqm_rt);

            var dpp = nilaiNetBeforeTax - disk_amount;
            var ppn = dpp * 0.11;
            var total = dpp + ppn;

            document.getElementById("net_before_tax_real").value = dpp;
            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax_real").value = ppn;
            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total_real").value = total;
            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    $(function() {
        $('#disk_persen').on('change',function() {
            var disk_persen = parseInt($("#disk_persen").val());
            var net_before_tax_real = parseInt($("#net_before_tax_real").val());

            var disk_amount = (disk_persen / 100) * net_before_tax_real;

            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("disk_amount").value = disk_amount;

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    $(function() {
        $('#disk_amount').on('change',function() {
            var disk_amount = parseInt($("#disk_amount").val());

            var net_before_tax_real = parseInt($("#net_before_tax_real").val());

            var disk_persen = (disk_amount/net_before_tax_real) * 100;

            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("disk_persen").value = disk_persen.formatMoney(2);

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    Number.prototype.formatMoney = function (c, d, t) {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "." : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
</script>
<script type="text/javascript">
    function delItemSchedule(id) {
        $.ajax({
            type: "post",
            url: "<?php echo e(route('marketing.leaseagreement.deleteitemschedule')); ?>",
            data: {PSM_SCHEDULE_ID_INT:id, _token: "<?php echo e(csrf_token()); ?>"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function (response) {
                if(response['Success']){
                    alert(response['Success']);
                    document.location.reload(true);
                }else{
                    alert(response['Error']);
                }
                $('#loading').modal('hide');
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
    };

    $(function() {
        $('#update').on('click',function() {
            var stDate = $("#stDate").val();
            var enDate = $("#enDate").val();

            var trx_code = $("#trx_code").val();
            var desc_char = $("#desc_char").val();
            var base_amount = $("#base_amount").val();
            var psm_trans_nochar = $("#psm_trans_nochar").val();
            var psm_trans_id = $("#psm_trans_id").val();

            var insert_id = $("#insert_id").val();
            if (stDate === '' || enDate === '' || trx_code === '' ||
                desc_char === '' || base_amount === 0 || base_amount === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "<?php echo e(route('marketing.leaseagreement.insertupdateitemschedule')); ?>",
                    data: {PSM_TRANS_ID_INT:psm_trans_id,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        TRX_CODE:trx_code,
                        DESC_CHAR:desc_char,
                        BASE_AMOUNT_NUM:base_amount,
                        TGL_SCHEDULE_ST_DATE:stDate,
                        TGL_SCHEDULE_EN_DATE:enDate,
                        insert_id:insert_id,
                        _token: "<?php echo e(csrf_token()); ?>"},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){ $('#loading').modal('show'); },
                    success: function (response) {
                        if(response['Success']){
                            alert(response['Success']);
                            document.location.reload(true);
                        }else{
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });
    });
</script>
<script type="text/javascript">
    function delItemSecureDep(id) {
        $.ajax({
            type: "post",
            url: "<?php echo e(route('marketing.leaseagreement.deleteitemsecuredeposito')); ?>",
            data: {PSM_SECURE_DEP_ID_INT:id, _token: "<?php echo e(csrf_token()); ?>"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function (response) {
                if(response['Success']) {
                    document.location.reload(true);
                }else{
                    alert(response['Error']);
                }
                $('#loading').modal('hide');
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
    };

    function getItemSecureDep(id) {
        $.ajax({
            type: "post",
            url: "<?php echo e(route('marketing.leaseagreement.getitemsecuredeposit')); ?>",
            data: {PSM_SECURE_DEP_ID_INT:id, _token: "<?php echo e(csrf_token()); ?>"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    $("#deposit_desc").val(data['PSM_TRANS_DEPOSIT_DESC']);
                    $("#deposit_type").val(data['PSM_TRANS_DEPOSIT_TYPE']);
                    $("#deposit_id").val(data['PSM_SECURE_DEP_ID_INT']);
                    $("#deposit_num").val(data['PSM_TRANS_DEPOSIT_NUM']);
                    $("#deposit_date").val(data['PSM_TRANS_DEPOSIT_DATE']);
                    $("#insert_id").val('0');
                }else{
                    alert(data['msg']);
                }
                $('#loading').modal('hide');
            }
        });
    };

    $(function() {
        $('#updateSecureDep').on('click',function() {
            var deposit_desc = $("#deposit_desc").val();
            var deposit_type = $("#deposit_type").val();
            var deposit_id = $("#deposit_id").val();

            var deposit_num = $("#deposit_num").val();
            var deposit_date = $("#deposit_date").val();
            var psm_trans_nochar = $("#psm_trans_nochar").val();
            var insert_id = $("#insert_id").val();

            if (deposit_desc === '' || deposit_num === '' || deposit_date === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "<?php echo e(route('marketing.leaseagreement.insertupdatesecuredeposit')); ?>",
                    data: {PSM_SECURE_DEP_ID_INT:deposit_id,
                        PSM_TRANS_DEPOSIT_DESC:deposit_desc,
                        PSM_TRANS_DEPOSIT_TYPE:deposit_type,
                        PSM_TRANS_DEPOSIT_NUM:deposit_num,
                        PSM_TRANS_DEPOSIT_DATE:deposit_date,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        insert_id:insert_id,
                        _token: "<?php echo e(csrf_token()); ?>"},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){ $('#loading').modal('show'); },
                    success: function (response) {
                        if(response['Success']){
                            document.location.reload(true);
                        }else{
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });
    });
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <fieldset>
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-info card-tabs" >
                                    <div class="card-header p-0 pt-1">
                                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                                href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                                aria-selected="true">Data Header Transaction</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content" id="custom-tabs-one-tabContent" style="padding-left: 5px; padding-right: 5px;">
                                        
                                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                            <form action="<?php echo e(URL::route('marketing.leaseagreement.adddatapsm')); ?>" method="post">
                                            <?php echo csrf_field(); ?>
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <label for="Document*">Document*</label>
                                                    <input type="text" name="PSM_TRANS_NOCHAR" value="NONAME" class="form-control" id="psm_trans_nochar" readonly="yes">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="Company Name*">Company Name*</label>
                                                        <input type="text" name="MD_TENANT_NAME_CHAR" class="form-control" id="tenant_name" placeholder="Tenant" readonly="yes">
                                                        <input type="hidden" name="MD_TENANT_ID_INT" class="form-control" id="tenant_id" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="Tenant Name*">Tenant Name*</label>
                                                        <input type="text" name="SHOP_NAME_CHAR" class="form-control" placeholder="Shop Name">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="Tenant Category*">Tenant Category*</label>
                                                        <input type="text" name="PSM_CATEGORY_NAME" class="form-control" id="shop_type" placeholder="Shop Type" readonly="yes">
                                                        <input type="hidden" name="PSM_CATEGORY_ID_INT" value="0" class="form-control" id="shop_type_id" placeholder="Shop Type" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="Type*">Type*</label>
                                                        <input type="text" name="MD_SALES_TYPE_DESC" class="form-control" id="sales_desc" placeholder="Type" readonly="yes">
                                                        <input type="hidden" name="MD_SALES_TYPE_ID_INT" class="form-control" id="sales_id" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="Booking Date*">Booking Date*</label>
                                                        <input type="date" value="" class="form-control" id="startDate" name="PSM_TRANS_BOOKING_DATE" placeholder="Booking Date">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="Start Date*">Start Date*</label>
                                                        <input type="date" value="" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="End Date*">End Date*</label>
                                                        <input type="date" value="" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="Virtual Account*">Virtual Account*</label>
                                                        <input type="number" name="PSM_TRANS_VA" class="form-control" placeholder="Virtual Account">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="Down Payment(%)*">Down Payment(%)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERSEN" value="0" class="form-control" placeholder="Down Payment(%)">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="Down Payment Period (Month)*">Down Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERIOD" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="Payment Period (Month)*">Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" value="0" class="form-control" placeholder="Time Period">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="Generate Schedule*">Generate Schedule*</label>
                                                        <select name="PSM_TRANS_GENERATE_BILLING" class="form-control">
                                                        <option value="1">Automatically</option>
                                                        <option value="0">Manual</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="Grace Period Type">Grace Period Type</label>
                                                        <select name="PSM_TRANS_GRASS_TYPE" class="form-control">
                                                        <option value=" ">Please Choose</option>
                                                        <option value="SOT">Start of Contract</option>
                                                        <option value="EOT">End of Contract</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="Grace Period (Month)">Grace Period (Month)</label>
                                                        <input type="number" name="PSM_TRANS_GRASS_PERIOD" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="Bank Garansi">Bank Garansi</label>
                                                        <input type="text" name="PSM_BANK_GARANSI_NOCHAR" class="form-control" placeholder="Bank Garansi">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="Bank Garansi Amount">Bank Garansi Amount</label>
                                                        <input type="number" name="PSM_BANK_GARANSI" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <br><br>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <h6><b>Especially for the type of Revenue Sharing :</b></h6><br>
                                                    <h6><b>Revenue Sharing Rate :</b></h6>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="Minimum Amount Charge">Minimum Amount Charge</label>
                                                        <input type="number" name="PSM_MIN_AMT" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label for="Low Amount">Low Amount</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_NUM" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="Low Rate (%)">Low Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_RATE" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label for="High Amount">High Amount</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_NUM" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="High Rate (%)">High Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_RATE" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <h6><b>Investment :</b></h6>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label for="Investment Amount">Investment Amount</label>
                                                        <input type="number" name="PSM_INVEST_NUM" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="Investment Rate (%)">Investment Rate (%)</label>
                                                        <input type="number" name="PSM_INVEST_RATE" value="0" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="Description Payment*">Description Payment*</label>
                                                        <textarea name="PSM_TRANS_DESCRIPTION" class="form-control form-control-sm" rows="3" placeholder="Description"></textarea>
                                                        <script>
                                                            CKEDITOR.replace('PSM_TRANS_DESCRIPTION');
                                                        </script>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-danger" href="<?php echo e(URL('/marketing/leaseagreement/viewlistdatanew/')); ?>">
                                                            <i>
                                                                << Back to List
                                                            </i>
                                                        </a>
                                                        <a href="#confModal" class="btn btn-primary" data-toggle="modal" style="float: right;">
                                                            Save Data
                                                        </a>
                                                        <div id="confModal" class="modal fade">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Confirmation</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Are you sure save this data ?</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                                                        <input type="submit" value="Save Data" class="btn btn-primary">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="salesModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Sales Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="sales_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Sales Type</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $dataSalesType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($salesType->MD_SALES_TYPE_DESC); ?></td>
                                <td><?php echo e($salesType->MD_SALES_TYPE_ID_INT); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-offset-10">
    <div id="shopTypeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="shop_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $dataCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data->PSM_CATEGORY_NAME); ?></td>
                                <td><?php echo e($data->PSM_CATEGORY_ID_INT); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-offset-10">
    <div id="stockModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Lot Stock</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="stock_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Lot No.</th>
                            <th>ID</th>
                            <th>SQM RT</th>
                            <th>SQM SC</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $dataLot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                <td><?php echo e($data->LOT_STOCK_ID_INT); ?></td>
                                <td><?php echo e($data->LOT_STOCK_SQM); ?></td>
                                <td><?php echo e($data->LOT_STOCK_SQM_SC); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-offset-10">
    <div id="tenantModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tenant</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="tenant_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $dataTenant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($tenant->MD_TENANT_NAME_CHAR); ?></td>
                                <td><?php echo e($tenant->MD_TENANT_ID_INT); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/leaseagreement/addDataLeaseAgreement2.blade.php ENDPATH**/ ?>