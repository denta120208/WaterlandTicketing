@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Add Discount Schedule - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add Discount Schedule
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
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-info card-tabs" >
                                    <div class="card-header p-0 pt-1">
                                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                                href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                                aria-selected="true">Schedule and Service Charge</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content" id="custom-tabs-one-tabContent" style="padding-left: 5px; padding-right: 5px;">
                                        {{--Schedule--}}
                                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                            <form action="{{ URL::route('marketing.leaseagreement.addscheddiscount') }}" method="post">
                                            @csrf
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Lot Number*</label>
                                                        <input type="text" class="form-control" id="stock_no" name="LOT_STOCK_NO" placeholder="Lot Number" value="<?php echo $lotData; ?>" readonly>
                                                        <input type="hidden" class="form-control" id="psm_trans_nochar" name="PSM_TRANS_NOCHAR" value="<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>" readonly>
                                                        <input type="hidden" class="form-control" name="PSM_SCHED_DISC_TYPE" value="SCHEDULE" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Tenant*</label>
                                                        <input type="text" class="form-control" id="tenant_name" name="MD_TENANT_NAME_CHAR" placeholder="Tenant" value="<?php echo $tenantData->MD_TENANT_NAME_CHAR; ?>" readonly>
                                                        <input type="hidden" class="form-control" id="tenant_id" name="MD_TENANT_ID_INT" value="<?php echo $tenantData->MD_TENANT_ID_INT; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Name*</label>
                                                        <input type="text" name="SHOP_NAME_CHAR" value="<?php echo $dataPSM->SHOP_NAME_CHAR; ?>" class="form-control" placeholder="Shop Name" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Type*</label>
                                                        <input type="text" class="form-control" id="shop_type" name="PSM_CATEGORY_NAME" placeholder="Shop Type" value="<?php echo $categoryData->PSM_CATEGORY_NAME; ?>" readonly>
                                                        <input type="hidden" class="form-control" id="shop_type_id" name="PSM_CATEGORY_ID_INT" value="<?php echo $categoryData->PSM_CATEGORY_ID_INT; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Booking Date*</label>
                                                        <input type="date" value="<?php echo $dataPSM->PSM_TRANS_BOOKING_DATE; ?>" class="form-control" id="startDate" name="PSM_TRANS_BOOKING_DATE" placeholder="Booking Date" readonly="yes">
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
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment(%)*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_DP_PERSEN}}" class="form-control" name="PSM_TRANS_DP_PERSEN" placeholder="Down Payment(%)" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment Period (Month)*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_DP_PERIOD}}" class="form-control" name="PSM_TRANS_DP_PERIOD" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" value="{{$dataPSM->PSM_TRANS_TIME_PERIOD_SCHED}}" class="form-control" name="PSM_TRANS_TIME_PERIOD_SCHED" placeholder="Time Period" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Rent Amount / m2*</label>
                                                        <input type="number" value="<?php echo number_format($dataPSM->PSM_TRANS_RENT_NUM,0,'','.'); ?>" class="form-control" name="PSM_TRANS_RENT_NUM" placeholder="Rent Amount / m2" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Service Charge / m2*</label>
                                                        <input type="number" name="PSM_TRANS_SC_NUM" value="{{ number_format($dataPSM->PSM_TRANS_SC_NUM,0,'','.') }}" class="form-control" placeholder="Service Charge / m2" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Disc(%)*</label>
                                                        <input type="text" name="PSM_TRANS_DISKON_PERSEN" value="{{number_format($dataPSM->PSM_TRANS_DISKON_PERSEN,2,'.','')}}" class="form-control" id="disk_persen" placeholder="Discount (%)" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Disc. Amount*</label>
                                                        <input type="text" class="form-control" id="disk_amount" name="PSM_TRANS_DISKON_NUM" value="{{ $dataPSM->PSM_TRANS_DISKON_NUM }}" placeholder="Discount Amount" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>Price Before Tax*</label>
                                                        <input type="text" name="PSM_TRANS_NET_BEFORE_TAX" value="<?php echo number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX, 0, '', '.'); ?>" class="form-control" id="net_before_tax" placeholder="Price Before Tax" readonly="yes">
                                                        <input type="hidden" name="PSM_TRANS_NET_BEFORE_TAX_REAL" value="<?php echo $dataPSM->PSM_TRANS_NET_BEFORE_TAX; ?>" class="form-control" id="net_before_tax_real" placeholder="Price Before Tax" readonly="yes">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>Price Tax*</label>
                                                        <input type="text" name="PSM_TRANS_PPN" class="form-control" id="price_tax" placeholder="Price Tax" readonly value="<?php echo number_format($dataPSM->PSM_TRANS_PPN,0,'','.'); ?>">
                                                        <input type="hidden" name="PSM_TRANS_PPN_REAL" class="form-control" id="price_tax_real" placeholder="Price Tax" readonly value="<?php echo $dataPSM->PSM_TRANS_PPN; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>Price After Tax*</label>
                                                        <input type="text" name="PSM_TRANS_PRICE" class="form-control" id="price_total" placeholder="Price After Tax" readonly value="<?php echo number_format($dataPSM->PSM_TRANS_PRICE,0,'','.');?>">
                                                        <input type="hidden" name="PSM_TRANS_PRICE_REAL" class="form-control" id="price_total_real" placeholder="Price After Tax" readonly value="<?php echo $dataPSM->PSM_TRANS_PRICE;?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Disc. Schedule Type*</label>
                                                        <select name="PSM_TRANS_DISC_TYPE" class="form-control">
                                                            <option value="">Please Choose</option>
                                                            <option value="Percentation">Percentation</option>
                                                            <option value="Amount">Amount</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Disc. Schedule Amount*</label>
                                                        <input type="text" name="PSM_SCHED_DISC_AMT" class="form-control" placeholder="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                                        <thead>
                                                        <tr>
                                                            <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                                            <th style="text-align: center;">No.</th>
                                                            <th style="text-align: center;">Date</th>
                                                            <th style="text-align: center;">Trx. Code</th>
                                                            <th style="text-align: center;">Description</th>
                                                            <th style="text-align: center;">Base Amount</th>
                                                            <th style="text-align: center;">Discount</th>
                                                            <th style="text-align: center;">Tax Amount</th>
                                                            <th style="text-align: center;">Invoice Amount</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $i = 1;
                                                        $totalBase = 0;
                                                        $totalDisc = 0;
                                                        $totalTax = 0;
                                                        $totalInv = 0;
                                                        ?>
                                                        @foreach($scheduleData as $data)
                                                            <tr>
                                                                <th><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>)" value="<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>" id="idbilling"></th>
                                                                <td>{{$i}}</td>
                                                                <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                                                <td>{{$data->TRX_CODE}}</td>
                                                                <td>{{$data->DESC_CHAR}}</td>
                                                                <td style="text-align: right;">{{number_format($data->BASE_AMOUNT_NUM,0,'','.')}}</td>
                                                                <td style="text-align: right;">{{number_format($data->DISC_NUM,0,'','.')}}</td>
                                                                <td style="text-align: right;">{{number_format($data->PPN_PRICE_NUM,0,'','.')}}</td>
                                                                <td style="text-align: right;">{{number_format($data->BILL_AMOUNT,0,'','.')}}</td>
                                                            </tr>
                                                            <?php
                                                            $i += 1;
                                                            $totalBase += $data->BASE_AMOUNT_NUM;
                                                            $totalDisc += $data->DISC_NUM;
                                                            $totalTax += $data->PPN_PRICE_NUM;
                                                            $totalInv += $data->BILL_AMOUNT;
                                                            ?>
                                                        @endforeach
                                                        </tbody>
                                                        <tfooter>
                                                            <tr>
                                                                <td></td>
                                                                <td><b>TOTAL</b></td>
                                                                <td><b>-</b></td>
                                                                <td><b>-</b></td>
                                                                <td><b>-</b></td>
                                                                <td style="text-align: right;"><b>{{number_format($totalBase,0,'','.')}}</b></td>
                                                                <td style="text-align: right;"><b>{{number_format($totalDisc,0,'','.')}}</b></td>
                                                                <td style="text-align: right;"><b>{{number_format($totalTax,0,'','.')}}</b></td>
                                                                <td style="text-align: right;"><b>{{number_format($totalInv,0,'','.')}}</b></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfooter>
                                                    </table>
                                                    <div id="temp-form">
                                                        <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                                        <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                                    </div>
                                                </div>
                                            </div>
                                            <br><br>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
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
                                                                        <button type="submit" name="submit" class="btn btn-primary">Save Data</button>
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
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


