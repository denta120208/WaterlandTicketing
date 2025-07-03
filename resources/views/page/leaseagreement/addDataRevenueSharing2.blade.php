@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Add Revenue Sharing - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add Revenue Sharing
@endsection

@section('content')

@if ($errors->any())
<ul class="alert alert-danger">
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

@if (Session::has('message'))
    <div class="alert alert-success" id="success-alert">
        {{ Session::get('message') }}
    </div>
@endif
<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function()
    {
        $('#vendor_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

</script>
<script>
    $(function(){
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

    $(function(){
        var table = $('#shop_type_table').DataTable({
            order:[]
        });

        $('#shop_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('shop_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('shop_type_id').value = checkEmptyStringValidation(data[1]);
            $('#shopTypeModal').modal('hide');
        });

        $('#shop_type').on('click',function(){
            $('#shopTypeModal').modal('show');
        });
    });

    function checkEmptyStringValidation(arr){
        var dataArr;

        if(arr == null){
             return dataArr = "";
        }else{
            return dataArr = arr;
        }
    }

    $(function()
    {
        $('#disk_persen').on('change',function(){
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

    $(function()
    {
        $('#disk_amount').on('change',function(){
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

    Number.prototype.formatMoney = function (c, d, t)
    {
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

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <fieldset>
                        <div class="row" style="padding-left: 5px; padding-right: 5px;">
                            <div class="col-12">
                                <form action="{{ URL::route('marketing.leaseagreement.addrequestrevenuesharing') }}" method="post" enctype="multipart/form-data">
                                <br>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Lot Number*</label>
                                            <input type="text" name="LOT_STOCK_NO" class="form-control" id="stock_no" placeholder="Lot Number" value="<?php echo $lotData; ?>" readonly>
                                            <input type="hidden" name="PSM_TRANS_NOCHAR" class="form-control" id="psm_trans_nochar" value="<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>" readonly>
                                            <input type="hidden" name="PSM_TRANS_ID_INT" class="form-control" id="psm_trans_nochar" value="<?php echo $dataPSM->PSM_TRANS_ID_INT; ?>" readonly>
                                            <input type="hidden" name="PSM_SCHED_DISC_TYPE" class="form-control" value="SERVICE_CHARGE" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Tenant*</label>
                                            <input type="text" name="MD_TENANT_NAME_CHAR" class="form-control" id="tenant_name" placeholder="Tenant" value="<?php echo $tenantData->MD_TENANT_NAME_CHAR; ?>" readonly>
                                            <input type="hidden" name="MD_TENANT_ID_INT" class="form-control" id="tenant_id" value="<?php echo $tenantData->MD_TENANT_ID_INT; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Shop Name*</label>
                                            <input type="text" name="SHOP_NAME_CHAR" class="form-control" placeholder="Shop Name" value="<?php echo $dataPSM->SHOP_NAME_CHAR; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Shop Type*</label>
                                            <input type="text" name="PSM_CATEGORY_NAME" class="form-control" id="shop_type" placeholder="Shop Type" value="<?php echo $categoryData->PSM_CATEGORY_NAME; ?>" readonly>
                                            <input type="hidden" name="PSM_CATEGORY_ID_INT" class="form-control" id="shop_type_id" value="<?php echo $categoryData->PSM_CATEGORY_ID_INT; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Booking Date*</label>
                                            <input type="date" value="{{$dataPSM->PSM_TRANS_BOOKING_DATE}}" class="form-control" id="startDate" name="PSM_TRANS_BOOKING_DATE" placeholder="Booking Date" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Start Date*</label>
                                            <input type="date" value="{{$dataPSM->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>End Date*</label>
                                            <input type="date" value="{{$dataPSM->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Down Payment(%)*</label>
                                            <input type="number" name="PSM_TRANS_DP_PERSEN" id="PSM_TRANS_DP_PERSEN" value="<?php echo $dataPSM->PSM_TRANS_DP_PERSEN; ?>" class="form-control" placeholder="Down Payment(%)" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Down Payment Period (Month)*</label>
                                            <input type="number" name="PSM_TRANS_DP_PERIOD" id="PSM_TRANS_DP_PERIOD" value="<?php echo $dataPSM->PSM_TRANS_DP_PERIOD; ?>" class="form-control" placeholder="0" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Payment Period (Month)*</label>
                                            <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" id="PSM_TRANS_TIME_PERIOD_SCHED" value="<?php echo $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED; ?>" class="form-control" placeholder="Time Period" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Rent Amount / m2*</label>
                                            <input type="number" name="PSM_TRANS_RENT_NUM" id="PSM_TRANS_RENT_NUM" value="<?php echo number_format($dataPSM->PSM_TRANS_RENT_NUM,0,'','.'); ?>" class="form-control" placeholder="Rent Amount / m2" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Service Charge / m2*</label>
                                            <input type="number" name="PSM_TRANS_SC_NUM" id="PSM_TRANS_SC_NUM" value="<?php echo number_format($dataPSM->PSM_TRANS_SC_NUM,0,'','.'); ?>" class="form-control" placeholder="Service Charge / m2" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Price Before Tax*</label>
                                            <input type="text" name="PSM_TRANS_NET_BEFORE_TAX" class="form-control" id="net_before_tax" placeholder="Price Before Tax" value="<?php echo number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX,0,'','.') ?>" readonly>
                                            <input type="hidden" name="PSM_TRANS_NET_BEFORE_TAX_REAL" class="form-control" id="net_before_tax_real" placeholder="Price Before Tax" value="<?php echo $dataPSM->PSM_TRANS_NET_BEFORE_TAX ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Price Tax*</label>
                                            <input type="text" name="PSM_TRANS_PPN" id="price_tax" class="form-control" placeholder="Price Tax" value="<?php echo number_format($dataPSM->PSM_TRANS_PPN,0,'','.'); ?>" readonly="yes">
                                            <input type="hidden" name="PSM_TRANS_PPN_REAL" id="price_tax_real" class="form-control" placeholder="Price Tax" value="<?php echo $dataPSM->PSM_TRANS_PPN; ?>" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Price After Tax*</label>
                                            <input type="text" name="PSM_TRANS_PRICE" class="form-control" id="price_total" placeholder="Price After Tax" value="<?php echo number_format($dataPSM->PSM_TRANS_PRICE,0,'','.') ?>" readonly="yes">
                                            <input type="hidden" name="PSM_TRANS_PRICE_REAL" class="form-control" id="price_total_real" placeholder="Price After Tax" value="<?php echo $dataPSM->PSM_TRANS_PRICE ?>" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Minimum Amount Charge</label>
                                            <input type="number" name="PSM_RS_MIN_AMT" id="PSM_RS_MIN_AMT" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>RS Start Date*</label>
                                            <input type="date" class="form-control" name="PSM_RS_START_DATE" placeholder="Start Date">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>RS End Date*</label>
                                            <input type="date" class="form-control" name="PSM_RS_END_DATE" placeholder="Start Date">
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            <label>Low Amount</label>
                                            <input type="number" name="PSM_RS_LOW_NUM" id="PSM_RS_LOW_NUM" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Low Rate (%)</label>
                                            <input type="number" name="PSM_RS_LOW_RATE" id="PSM_RS_LOW_RATE" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            <label>High Amount</label>
                                            <input type="number" name="PSM_RS_HIGH_NUM" id="PSM_RS_HIGH_NUM" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>High Rate (%)</label>
                                            <input type="number" name="PSM_RS_HIGH_RATE" id="PSM_RS_HIGH_RATE" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Form Revenue Sharing File (PDF File Only)</label><br>
                                            <input type="hidden" name="upload_file" value="None" id="sheet">
                                            <input type="file" name="upload_file" id="sheet">
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <a href="#confModal1" class="btn btn-primary" data-toggle="modal" style="float: right;">
                                                Save Data
                                            </a>
                                            <div id="confModal1" class="modal fade">
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
                                                            <button type="submit" class="btn btn-primary">Save Data</button>
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
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection