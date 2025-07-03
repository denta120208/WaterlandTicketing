@extends('layouts.mainLayouts')

@section('navbar_header')
    @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
        Form View Data Letter Of Intent Addendum Revision Agreement - <b>{{session('current_project_char')}}</b>
    @else
        Form View Data Letter Of Intent Addendum Renewal Agreement - <b>{{session('current_project_char')}}</b>
    @endif
@endsection

@section('header_title')
    @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
        Form View Data Letter Of Intent Addendum Revision Agreement
    @else
        Form View Data Letter Of Intent Addendum Renewal Agreement
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
        $('#schedule_table').DataTable({
            order : []
        });

        $('#data_rentsclot_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });

        $('#data_rentscamt_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    });

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
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <label>Addendum Document*</label>
                                                    <input type="text" name="PSM_TRANS_ADD_NOCHAR" id="psm_trans_nochar" class="form-control" value="<?php echo $dataAddendum->PSM_TRANS_ADD_NOCHAR ?>" readonly>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label>Lease Document*</label>
                                                    <input type="text" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" value="<?php echo $dataPSM->PSM_TRANS_NOCHAR ?>" readonly>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <input type="hidden" name="PSM_TRANS_ID_INT" value="<?php echo $dataPSM->PSM_TRANS_ID_INT; ?>" class="form-control" id="psm_trans_id" readonly="yes">
                                                <input type="hidden" name="ADD_TYPE" value="<?php echo $ADD_TYPE; ?>" class="form-control" readonly="yes">
                                                <div class="col-lg-12" style="padding-bottom: 20px;">
                                                    <label>Lot Number</label>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <table class="table-striped table-hover compact" id="data_rentsclot_table" cellspacing="0" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Lot</th>
                                                                    <th>SQM RT</th>
                                                                    <th>SQM SC</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php $no = 1; ?>
                                                                @foreach($lotData as $data)
                                                                    <tr>
                                                                        <td style="width: 5px;">{{ $no }}</td>
                                                                        <td>{{ $data->LOT_STOCK_NO }}</td>
                                                                        <td>{{ (float) $data->LOT_STOCK_SQM }}</td>
                                                                        <td>{{ (float) $data->LOT_STOCK_SQM_SC }}</td>
                                                                    </tr>
                                                                    <?php $no += 1; ?>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Tenant*</label>
                                                        <input type="text" name="MD_TENANT_NAME_CHAR" value="<?php echo $tenantData->MD_TENANT_NAME_CHAR; ?>" class="form-control" id="tenant_name" placeholder="Tenant" readonly>
                                                        <input type="hidden" name="MD_TENANT_ID_INT" value="<?php echo $tenantData->MD_TENANT_ID_INT; ?>" class="form-control" id="tenant_id" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Name*</label>
                                                        <input type="text" name="SHOP_NAME_CHAR" value="<?php echo $dataAddendum->SHOP_NAME_CHAR; ?>" class="form-control" placeholder="Shop Name" readonly>
                                                    </div>
                                                </div>
                                                @if($dataPSM->PSM_CATEGORY_ID_INT == 0)
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Type*</label>
                                                        <input type="text" class="form-control" id="shop_type" name="PSM_CATEGORY_NAME" placeholder="Shop Type" readonly="yes">
                                                        <input type="hidden" class="form-control" id="shop_type_id" name="PSM_CATEGORY_ID_INT" value="0" placeholder="Shop Type" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Type*</label>
                                                        <input type="text" name="PSM_CATEGORY_NAME" class="form-control" id="shop_type" placeholder="Shop Type" value="<?php echo $categoryData->PSM_CATEGORY_NAME; ?>" readonly="yes">
                                                        <input type="hidden" name="PSM_CATEGORY_ID_INT" class="form-control" id="shop_type_id" placeholder="Shop Type" value="<?php echo $categoryData->PSM_CATEGORY_ID_INT; ?>" readonly="yes">
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Type*</label>
                                                        <input type="text" class="form-control" id="type" name="MD_SALES_TYPE_DESC" value="<?php echo $salesTypedata->MD_SALES_TYPE_DESC; ?>" placeholder="Type" readonly>
                                                        <input type="hidden" class="form-control" id="type_id" name="MD_SALES_TYPE_ID_INT" value="<?php echo $salesTypedata->MD_SALES_TYPE_ID_INT; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Booking Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_BOOKING_DATE}}" class="form-control" id="startDate" name="PSM_TRANS_BOOKING_DATE" placeholder="Booking Date" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Start Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>End Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Virtual Account*</label>
                                                        <input type="number" name="PSM_TRANS_VA" value="<?php echo $dataAddendum->PSM_TRANS_VA; ?>" class="form-control" placeholder="Virtual Account" readonly="yes">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment(%)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERSEN" value="<?php echo number_format($dataAddendum->PSM_TRANS_DP_PERSEN,0); ?>" class="form-control" placeholder="Down Payment(%)" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERIOD" value="<?php echo $dataAddendum->PSM_TRANS_DP_PERIOD; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" value="<?php echo $dataAddendum->PSM_TRANS_TIME_PERIOD_SCHED; ?>" class="form-control" placeholder="Time Period" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                    <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                        <div class="col-lg-12">
                                                            <h6><b>Rent / Service Charge Amount: </b></h6><br>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <table class="table-striped table-hover compact" id="data_rentscamt_table" cellspacing="0" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Year</th>
                                                                    <th>Rent Amt (For Month) / m2</th>
                                                                    <th>SC Amt (For Month) / m2</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php $no = 1; ?>
                                                                @foreach($dataRentSCAmt as $data)
                                                                    <tr>
                                                                        <td style="text-align: right; width: 5px;">{{ $no }}</td>
                                                                        <td>{{ $data->PSM_TRANS_PRICE_YEAR }}</td>
                                                                        <td>{{ number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,',','.') }}</td>
                                                                        <td>{{ number_format($data->PSM_TRANS_PRICE_SC_NUM,0,',','.') }}</td>
                                                                    </tr>
                                                                    <?php $no += 1; ?>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                    <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                        <div class="col-lg-5">
                                                            <div class="form-group">
                                                                <label>Disc (%) *</label>
                                                                <input type="number" class="form-control" placeholder="Enter Disc (%)" id="disc_persen" name="DISC_PERSEN" value="<?php echo (float) $dataPSM->PSM_TRANS_DISKON_PERSEN ?>" readonly="true">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <div class="form-group">
                                                                <label>Disc Amount *</label>
                                                                <input type="number" class="form-control" placeholder="Enter Disc Amount" id="disc_amt" name="DISC_AMT" value="<?php echo (float) $dataPSM->PSM_TRANS_DISKON_NUM ?>" readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Generate Schedule*</label>
                                                        <select name="PSM_TRANS_GENERATE_BILLING" class="form-control" disabled>
                                                            <option value="1" <?php echo ($dataPSM->PSM_TRANS_GENERATE_BILLING == 1) ? 'selected':'';?>>Automatically</option>
                                                            <option value="0" <?php echo ($dataPSM->PSM_TRANS_GENERATE_BILLING == 0) ? 'selected':'';?>>Manual</option>
                                                        </select>
                                                        <input type="hidden" name="PSM_TRANS_GENERATE_BILLING" value="<?php echo $dataAddendum->PSM_TRANS_GENERATE_BILLING;?>" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Price Before Tax*</label>
                                                        <input type="text" name="PSM_TRANS_NET_BEFORE_TAX" value="<?php echo number_format($dataAddendum->PSM_TRANS_NET_BEFORE_TAX,0,'','.'); ?>" class="form-control" id="net_before_tax" placeholder="Price Before Tax" readonly="yes">
                                                        <input type="hidden" name="PSM_TRANS_NET_BEFORE_TAX_REAL" value="<?php echo $dataAddendum->PSM_TRANS_NET_BEFORE_TAX; ?>" class="form-control" id="net_before_tax_real" placeholder="Price Before Tax" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Price Tax*</label>
                                                        <input type="text" name="PSM_TRANS_PPN" id="price_tax" class="form-control" placeholder="Price Tax" value="<?php echo number_format($dataAddendum->PSM_TRANS_PPN,0,'','.'); ?>" readonly="yes">
                                                        <input type="hidden" name="PSM_TRANS_PPN_REAL" id="price_tax_real" class="form-control" placeholder="Price Tax" value="<?php echo $dataAddendum->PSM_TRANS_PPN; ?>" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Price After Tax*</label>
                                                        <input type="text" name="PSM_TRANS_PRICE" id="price_total" class="form-control" placeholder="Price After Tax" value="<?php echo number_format($dataAddendum->PSM_TRANS_PRICE,0,'','.'); ?>" readonly>
                                                        <input type="hidden" name="PSM_TRANS_PRICE_REAL" id="price_total_real" class="form-control" placeholder="Price After Tax" value="<?php echo $dataAddendum->PSM_TRANS_PRICE; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Security Deposit Type</label>
                                                        <select name="PSM_TRANS_DEPOSIT_TYPE" class="form-control" id="PSM_TRANS_DEPOSIT_TYPE" disabled>
                                                            <option value="">Please Choose</option>
                                                            <option value="FO" <?php if($dataPSM->PSM_TRANS_DEPOSIT_TYPE == "FO") { echo "selected"; } ?>>Fit Out</option>
                                                            <option value="SC" <?php if($dataPSM->PSM_TRANS_DEPOSIT_TYPE == "SC") { echo "selected"; } ?>>Service Charge</option>
                                                            <option value="TLP" <?php if($dataPSM->PSM_TRANS_DEPOSIT_TYPE == "TLP") { echo "selected"; } ?>>Telephone</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Security Deposit Amount</label>
                                                        <input type="number" name="PSM_TRANS_DEPOSIT_NUM" id="PSM_TRANS_DEPOSIT_NUM" value="<?php echo $dataAddendum->PSM_TRANS_DEPOSIT_NUM; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Security Deposit Date</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_DEPOSIT_DATE}}" class="form-control" id="startDate" name="PSM_TRANS_DEPOSIT_DATE" placeholder="Security Deposit Date" readonly='yes'>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Grace Period Type</label>
                                                        <select name="PSM_TRANS_GRASS_TYPE" id="PSM_TRANS_GRASS_TYPE" class="form-control" disabled="yes">
                                                            <option value="">Please Choose</option>
                                                            <option value="SOT" <?php if($dataAddendum->PSM_TRANS_GRASS_TYPE == 'SOT'){echo "selected";} ?>>Start of Contract</option>
                                                            <option value="EOT" <?php if($dataAddendum->PSM_TRANS_GRASS_TYPE == 'EOT'){echo "selected";} ?>>End of Contract</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Grace Period (Month)</label>
                                                        <input type="number" name="PSM_TRANS_GRASS_PERIOD" id="PSM_TRANS_GRASS_PERIOD" value="<?php echo $dataAddendum->PSM_TRANS_GRASS_PERIOD; ?>" class="form-control" placeholder="0" readonly="yes">
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
                                                        <input type="number" name="PSM_MIN_AMT" id="PSM_MIN_AMT" value="<?php echo $dataAddendum->PSM_MIN_AMT; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>Low Amount</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_NUM" id="PSM_REVENUE_LOW_NUM" value="<?php echo $dataAddendum->PSM_REVENUE_LOW_NUM; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Low Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_RATE" id="PSM_REVENUE_LOW_RATE" value="<?php echo $dataAddendum->PSM_REVENUE_LOW_RATE; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>High Amount</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_NUM" id="PSM_REVENUE_HIGH_NUM" value="<?php echo $dataAddendum->PSM_REVENUE_HIGH_NUM; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>High Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_RATE" id="PSM_REVENUE_HIGH_RATE" value="<?php echo $dataAddendum->PSM_REVENUE_HIGH_RATE; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <h6><b>Investment :</b></h6>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>Investment Amount</label>
                                                        <input type="number" name="PSM_INVEST_NUM" id="PSM_INVEST_NUM" value="<?php echo $dataAddendum->PSM_INVEST_NUM; ?>" class="form-control" placeholder="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Investment Rate (%)</label>
                                                        <input type="number" name="PSM_INVEST_RATE" id="PSM_INVEST_RATE" value="<?php echo $dataAddendum->PSM_INVEST_RATE; ?>" class="form-control" placeholder="0" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Description Payment*</label>
                                                        <textarea name="PSM_TRANS_DESCRIPTION" id="PSM_TRANS_DESCRIPTION" class="form-control form-control-sm" size="50x3" placeholder="Description" readonly="yes">
                                                            <?php echo $dataAddendum->PSM_TRANS_DESCRIPTION; ?>
                                                        </textarea>
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
                                                        @if($dataAddendum->PSM_TRANS_ADD_STATUS_INT == 1)
                                                            <a href="#approveModal{!!$dataAddendum->PSM_TRANS_ADD_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal" style="float: right;">
                                                                <i>
                                                                    Approve Document
                                                                </i>
                                                            </a>
                                                            <div id="approveModal{!!$dataAddendum->PSM_TRANS_ADD_ID_INT!!}" class="modal fade">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Confirmation</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <p>Are you sure approve this document ?</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                            <a href="{{ URL('/marketing/leaseagreement/approvedataAddendum/'. $dataAddendum->PSM_TRANS_ADD_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
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
@endsection


