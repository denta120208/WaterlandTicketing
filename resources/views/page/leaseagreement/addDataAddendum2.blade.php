@extends('layouts.mainLayouts')

@section('navbar_header')
    @if($ADD_TYPE == 'RVS')
        Form Add Data Letter Of Intent Addendum Revision Agreement - <b>{{session('current_project_char')}}</b>
    @else
        Form Add Data Letter Of Intent Addendum Renewal Agreement - <b>{{session('current_project_char')}}</b>
    @endif
@endsection

@section('header_title')
    @if($ADD_TYPE == 'RVS')
        Form Add Data Letter Of Intent Addendum Revision Agreement
    @else
        Form Add Data Letter Of Intent Addendum Renewal Agreement
    @endif
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
            scrollY:"700px",
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });
    });
</script>
<script>
    $(document).ready(function()
    {
        $('#commission_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });
    });

    $(document).ready(function()
    {
        var table = $('#tenant_table').DataTable({
            order:[]
        });

        $('#tenant_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('tenant_name').value = checkEmptyStringValidation(data[0]);
            document.getElementById('tenant_id').value = checkEmptyStringValidation(data[1]);
            $('#tenantModal').modal('hide');
        });
        
        $('#tenant_name').on('click',function(){
            $('#tenantModal').modal('show');
        });

        $('#schedule_table').DataTable({
            order : []
        });

        $('#data_rentsclot_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

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
        var table = $('#sales_type_table').DataTable({
            order:[]
        });

        $('#sales_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('sales_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('sales_id').value = checkEmptyStringValidation(data[1]);
            $('#salesModal').modal('hide');
        });

        $('#sales_desc').on('click',function(){
            $('#salesModal').modal('show');
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
        $('#price_rent_meter').on('change',function(){
            var price_rent_meter = parseFloat($("#price_rent_meter").val());
            var disk_amount = parseFloat($("#disk_amount").val());
            var sqm_rent = parseFloat($("#sqm_rent").val());

            var total_price_rent = (price_rent_meter * sqm_rent);

            var net_before_tax_real = parseInt(total_price_rent);

            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);
            document.getElementById("net_before_tax_real").value = net_before_tax_real.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);
            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

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
                                        {{--General--}}
                                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                            <form action="{{ URL::route('marketing.leaseagreement.adddataaddendum') }}" method="post">
                                            @csrf
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <label>Document*</label>
                                                    <input type="text" name="PSM_TRANS_NOCHAR" class="form-control" id="psm_trans_nochar" value="<?php echo ($dataPSM->PSM_TRANS_NOCHAR); ?>" readonly>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <input type="hidden" name="PSM_TRANS_ID_INT" class="form-control" id="psm_trans_id" value="<?php echo ($dataPSM->PSM_TRANS_ID_INT); ?>" readonly>
                                                <input type="hidden" name="ADD_TYPE" class="form-control" value="<?php echo ($ADD_TYPE); ?>" readonly>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Tenant*</label>
                                                        <input type="text" name="MD_TENANT_NAME_CHAR" class="form-control" id="tenant_name" placeholder="Tenant" value="<?php echo ($tenantData->MD_TENANT_NAME_CHAR); ?>" readonly>
                                                        <input type="hidden" name="MD_TENANT_ID_INT" class="form-control" id="tenant_id" value="<?php echo ($tenantData->MD_TENANT_ID_INT); ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Name*</label>
                                                        <input type="text" name="SHOP_NAME_CHAR" class="form-control" placeholder="Shop Name" value="<?php echo ($dataPSM->SHOP_NAME_CHAR); ?>">
                                                    </div>
                                                </div>
                                                @if($dataPSM->PSM_CATEGORY_ID_INT == 0)
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Type*</label>
                                                        <input type="text" name="PSM_CATEGORY_NAME" class="form-control" id="shop_type" placeholder="Shop Type" readonly>
                                                        <input type="hidden" name="PSM_CATEGORY_ID_INT" class="form-control" id="shop_type_id" placeholder="Shop Type" value="0" readonly>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Type*</label>
                                                        <input type="text" name="PSM_CATEGORY_NAME" id="shop_type" value="<?php echo $categoryData->PSM_CATEGORY_NAME; ?>" class="form-control" placeholder="Shop Type" readonly>
                                                        <input type="hidden" name="PSM_CATEGORY_ID_INT" id="shop_type_id" value="<?php echo $categoryData->PSM_CATEGORY_ID_INT; ?>" class="form-control" placeholder="Shop Type" readonly>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($ADD_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Type*</label>
                                                        <input type="text" name="MD_SALES_TYPE_DESC" id="type" value="<?php echo $salesTypedata->MD_SALES_TYPE_DESC; ?>" class="form-control" placeholder="Type" readonly>
                                                        <input type="hidden" name="MD_SALES_TYPE_ID_INT" id="type_id" value="<?php echo $salesTypedata->MD_SALES_TYPE_ID_INT; ?>" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Type*</label>
                                                        <input type="text" name="MD_SALES_TYPE_DESC" id="sales_desc" value="<?php echo $salesTypedata->MD_SALES_TYPE_DESC; ?>" class="form-control" placeholder="Type" readonly>
                                                        <input type="hidden" name="MD_SALES_TYPE_ID_INT" id="sales_id" value="<?php echo $salesTypedata->MD_SALES_TYPE_ID_INT; ?>" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Booking Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_BOOKING_DATE}}" class="form-control" id="startDate" name="PSM_TRANS_BOOKING_DATE" placeholder="Booking Date" readonly="yes">
                                                    </div>
                                                </div>
                                                @if($ADD_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Start Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Start Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date">
                                                    </div>
                                                </div>
                                                @endif
                                                @if($ADD_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>End Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>End Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date">
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Virtual Account*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_VA}}" class="form-control" name="PSM_TRANS_VA" placeholder="Virtual Account">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @if($ADD_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Down Payment(%)*</label>
                                                        <input type="number" value="{{number_format($dataPSM->PSM_TRANS_DP_PERSEN,0)}}" class="form-control" name="PSM_TRANS_DP_PERSEN" placeholder="Down Payment(%)" readonly>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Down Payment(%)*</label>
                                                        <input type="number" value="{{number_format($dataPSM->PSM_TRANS_DP_PERSEN,0)}}" class="form-control" name="PSM_TRANS_DP_PERSEN" placeholder="Down Payment(%)">
                                                    </div>
                                                </div>
                                                @endif
                                                @if($ADD_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Down Payment Period (Month)*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_DP_PERIOD}}" class="form-control" name="PSM_TRANS_DP_PERIOD" placeholder="0" readonly>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Down Payment Period (Month)*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_DP_PERIOD}}" class="form-control" name="PSM_TRANS_DP_PERIOD" placeholder="0">
                                                    </div>
                                                </div>
                                                @endif
                                                @if($ADD_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_TIME_PERIOD_SCHED}}" class="form-control" name="PSM_TRANS_TIME_PERIOD_SCHED" placeholder="Time Period" readonly>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_TIME_PERIOD_SCHED}}" class="form-control" name="PSM_TRANS_TIME_PERIOD_SCHED" placeholder="Time Period">
                                                    </div>
                                                </div>
                                                @endif
                                                @if($ADD_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Generate Schedule*</label>
                                                        <select name="PSM_TRANS_GENERATE_BILLING" id="PSM_TRANS_GENERATE_BILLING" class="form-control" disabled="yes">
                                                            <option value="1" <?php if($dataPSM->PSM_TRANS_GENERATE_BILLING == "1"){ echo 'selected';} ?>>Automatically</option>
                                                            <option value="0" <?php if($dataPSM->PSM_TRANS_GENERATE_BILLING == "0"){ echo 'selected';} ?>>Manual</option>
                                                        </select>
                                                        <input type="hidden" name="PSM_TRANS_GENERATE_BILLING" value="<?php echo $dataPSM->PSM_TRANS_GENERATE_BILLING; ?>" class="form-control" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Generate Schedule*</label>
                                                        <select name="PSM_TRANS_GENERATE_BILLING" id="PSM_TRANS_GENERATE_BILLING" class="form-control">
                                                            <option value="1" <?php if($dataPSM->PSM_TRANS_GENERATE_BILLING == "1"){ echo 'selected';} ?>>Automatically</option>
                                                            <option value="0" <?php if($dataPSM->PSM_TRANS_GENERATE_BILLING == "0"){ echo 'selected';} ?>>Manual</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Grace Period Type</label>
                                                        <select name="PSM_TRANS_GRASS_TYPE" id="PSM_TRANS_GRASS_TYPE" class="form-control">
                                                            <option value="">Please Choose</option>
                                                            <option value="SOT" <?php if($dataPSM->PSM_TRANS_GRASS_TYPE == "SOT"){ echo 'selected';} ?>>Start of Contract</option>
                                                            <option value="EOT" <?php if($dataPSM->PSM_TRANS_GRASS_TYPE == "EOT"){ echo 'selected';} ?>>End of Contract</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Grace Period (Month)</label>
                                                        <input type="number" name="PSM_TRANS_GRASS_PERIOD" id="PSM_TRANS_GRASS_PERIOD" class="form-control" placeholder="0" value="<?php echo $dataPSM->PSM_TRANS_GRASS_PERIOD; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <h6><b>Especially for the type of Revenue Sharing :</b></h6><br>
                                                    <h6><b>Revenue Sharing Rate :</b></h6>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Minimum Amount Charge</label>
                                                        <input type="number" name="PSM_MIN_AMT" id="PSM_MIN_AMT" class="form-control" placeholder="0" value="<?php echo $dataPSM->PSM_MIN_AMT; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>Low Amount</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_NUM" id="PSM_REVENUE_LOW_NUM" class="form-control" placeholder="0" value="<?php echo $dataPSM->PSM_REVENUE_LOW_NUM; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Low Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_RATE" id="PSM_REVENUE_LOW_RATE" class="form-control" placeholder="0" value="<?php echo $dataPSM->PSM_REVENUE_LOW_RATE; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>High Amount</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_NUM" id="PSM_REVENUE_HIGH_NUM" class="form-control" placeholder="0" value="<?php echo $dataPSM->PSM_REVENUE_HIGH_NUM; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>High Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_RATE" id="PSM_REVENUE_HIGH_RATE" value="<?php echo $dataPSM->PSM_REVENUE_HIGH_RATE; ?>" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <h6><b>Investment :</b></h6>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>Investment Amount</label>
                                                        <input type="number" name="PSM_INVEST_NUM" value="<?php echo $dataPSM->PSM_INVEST_NUM ?>" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Investment Rate (%)</label>
                                                        <input type="number" name="PSM_INVEST_RATE" value="<?php echo $dataPSM->PSM_INVEST_RATE ?>" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Description Payment*</label>
                                                        <textarea name="PSM_TRANS_DESCRIPTION" class="form-control form-control-sm" size="50x3" placeholder="Description"><?php echo $dataPSM->PSM_TRANS_DESCRIPTION; ?></textarea>
                                                        <script>
                                                            CKEDITOR.replace('PSM_TRANS_DESCRIPTION');
                                                        </script>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-danger" href="{{ URL('/marketing/leaseagreement/viewlistdatanew/') }}">
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
                        @foreach($dataSalesType as $salesType)
                            <tr>
                                <td>{{$salesType->MD_SALES_TYPE_DESC}}</td>
                                <td>{{$salesType->MD_SALES_TYPE_ID_INT}}</td>
                            </tr>
                        @endforeach
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
                        @foreach($dataTenant as $tenant)
                            <tr>
                                <td>{{$tenant->MD_TENANT_NAME_CHAR}}</td>
                                <td>{{$tenant->MD_TENANT_ID_INT}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


