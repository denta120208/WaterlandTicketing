@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Approve Discount Schedule - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Approve Discount Schedule
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
                            {{--General--}}
                            <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab" style="padding-left: 5px; padding-right: 5px;">
                                <form action="{{ URL::route('marketing.leaseagreement.uploadfilediscount') }}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <br>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Lot Number*</label>
                                            <input type="text" name="LOT_STOCK_NO" class="form-control" id="stock_no" placeholder="Lot Number" readonly value="<?php echo $lotData; ?>">
                                            <input type="hidden" name="PSM_TRANS_NOCHAR" class="form-control" id="psm_trans_nochar" readonly value="<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Tenant*</label>
                                            <input type="text" name="MD_TENANT_NAME_CHAR" class="form-control" id="tenant_name" placeholder="Tenant" readonly value="<?php echo $tenantData->MD_TENANT_NAME_CHAR; ?>">
                                            <input type="hidden" name="MD_TENANT_ID_INT" class="form-control" id="tenant_id" readonly value="<?php echo $tenantData->MD_TENANT_ID_INT; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Shop Name*</label>
                                            <input type="text" name="SHOP_NAME_CHAR" class="form-control" placeholder="Shop Name" readonly value="<?php echo $dataPSM->SHOP_NAME_CHAR; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Shop Type*</label>
                                            <input type="text" name="PSM_CATEGORY_NAME" class="form-control" id="shop_type" placeholder="Shop Type" readonly value="<?php echo $categoryData->PSM_CATEGORY_NAME; ?>">
                                            <input type="hidden" name="PSM_CATEGORY_ID_INT" class="form-control" id="shop_type_id" placeholder="Shop Type" readonly value="<?php echo $categoryData->PSM_CATEGORY_ID_INT; ?>">
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
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Down Payment(%)*</label>
                                            <input type="number" name="PSM_TRANS_DP_PERSEN" class="form-control" placeholder="Down Payment(%)" value="{{ $dataPSM->PSM_TRANS_DP_PERSEN }}" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Down Payment Period (Month)*</label>
                                            <input type="number" name="PSM_TRANS_DP_PERIOD" value="<?php echo $dataPSM->PSM_TRANS_DP_PERIOD; ?>" class="form-control" placeholder="0" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Payment Period (Month)*</label>
                                            <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" class="form-control" placeholder="Time Period" value="<?php echo $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED; ?>" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Rent Amount / m2*</label>
                                            <input type="number" name="PSM_TRANS_RENT_NUM" value="<?php echo number_format($dataPSM->PSM_TRANS_RENT_NUM,0,'','.') ?>" class="form-control" placeholder="Rent Amount / m2" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Service Charge / m2*</label>
                                            <input type="number" name="PSM_TRANS_SC_NUM" class="form-control" placeholder="Service Charge / m2" value="<?php echo number_format($dataPSM->PSM_TRANS_SC_NUM,0,'','.'); ?>" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Disc(%)*</label>
                                            <input type="text" name="PSM_TRANS_DISKON_PERSEN" class="form-control" id="disk_persen" placeholder="Discount (%)" readonly value="<?php echo number_format($dataPSM->PSM_TRANS_DISKON_PERSEN,2,'.',''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Disc. Amount*</label>
                                            <input type="text" name="PSM_TRANS_DISKON_NUM" class="form-control" id="disk_amount" placeholder="Discount Amount" readonly value="<?php echo $dataPSM->PSM_TRANS_DISKON_NUM ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Price Before Tax*</label>
                                            <input type="text" name="PSM_TRANS_NET_BEFORE_TAX" class="form-control" id="net_before_tax" placeholder="Price Before Tax" value="<?php echo number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX,0,'','.'); ?>" readonly>
                                            <input type="hidden" name="PSM_TRANS_NET_BEFORE_TAX_REAL" class="form-control" id="net_before_tax_real" value="<?php echo $dataPSM->PSM_TRANS_NET_BEFORE_TAX; ?>" readonly>
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
                                            <input type="text" name="PSM_TRANS_PRICE" class="form-control" id="price_total" placeholder="Price After Tax" readonly value="<?php echo number_format($dataPSM->PSM_TRANS_PRICE,0,'','.'); ?>">
                                            <input type="hidden" name="PSM_TRANS_PRICE_REAL" class="form-control" id="price_total_real" placeholder="Price After Tax" readonly value="<?php echo $dataPSM->PSM_TRANS_PRICE; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Disc. Start Date*</label>
                                            <input type="date" value="{{$dataSchedDiscount->PSM_SCHED_DISC_START_DATE}}" class="form-control" name="PSM_SCHED_DISC_START_DATE" placeholder="Start Date" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Disc. End Date*</label>
                                            <input type="date" value="{{$dataSchedDiscount->PSM_SCHED_DISC_END_DATE}}" class="form-control" name="PSM_SCHED_DISC_END_DATE" placeholder="Start Date" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Disc. Schedule Type*</label>
                                            <input type="text" class="form-control" value="{{$dataSchedDiscount->PSM_TRANS_DISC_TYPE}}" name="PSM_TRANS_DISC_TYPE" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Disc. Schedule Amount*</label>
                                            <input type="text" class="form-control" value="{{$dataSchedDiscount->PSM_SCHED_DISC_AMT}}" name="PSM_SCHED_DISC_AMT" placeholder="0" readonly="yes">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
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
                                        </table>
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Form Discount Approved File (PDF File Only)</label><br>
                                            <input type="hidden" name="upload_file" id="sheet" value="None">
                                            <input type="file" name="upload_file" id="sheet">
                                            <input type="hidden" class="form-control" name="PSM_SCHED_DISC_ID_INT" value="{{$dataSchedDiscount->PSM_SCHED_DISC_ID_INT}}" id="sched_disc_id" readonly>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <a href="#confModal" class="btn btn-primary" data-toggle="modal" style="float: right;">
                                                Process Discount
                                            </a>
                                            <div id="confModal" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure process this data ?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="submit" class="btn btn-primary">Process Discount</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


