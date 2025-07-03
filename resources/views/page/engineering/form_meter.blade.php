@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Input Meter - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Input Meter
@endsection

@section('content')

<script>

    $(document).ready(function()
    {
        $('#vendor_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });
    } );

</script>
<script type="text/javascript">

    function getItem(id){
        //alert(id);
        $.ajax({
            type: "post",
            url: "{{ route('engineering.getitemutilstenantmeter') }}",
            data: {ID_UTILS_TENANT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    $("#tenant_name").val(data['MD_TENANT_NAME_CHAR']);
                    $("#id_tenant").val(data['MD_TENANT_ID_INT']);
                    $("#psm_nochar").val(data['PSM_TRANS_NOCHAR']);
                    $("#id_utils_tenant").val(data['ID_UTILS_TENANT']);
                    $("#shop_name").val(data['SHOP_NAME_CHAR']);
                    $("#utils_type").val(data['UTILS_TYPE_NAME']);
                    $("#meter_type").val(data['METER_TYPE']);
                    $("#formula_name").val(data['NAME_U_FORMULA']);
                    $("#id_formula").val(data['ID_U_FORMULA']);
                    $("#category_utils_name").val(data['UTILS_CATEGORY_NAME']);
                    $("#id_utils_category").val(data['UTILS_CATEGORY_ID_INT']);
                    $("#meter_name").val(data['UTILS_METER_CHAR']);
                    $("#id_meter").val(data['ID_METER']);
                    $("#start_LWBP").val(data['BILLING_METER_START_LWBP']);
                    $("#start_WBP").val(data['BILLING_METER_START_WBP']);
                    //alert("Data Has Been Found");
                }else{
                    alert(data['msg']);
                }
                $('#loading').modal('hide');
            }
        });
    };

    $(function(){
        $('#update').on('click',function(){
            var tenant_name = $("#tenant_name").val();
            var id_tenant = $("#id_tenant").val();
            var psm_nochar = $("#psm_nochar").val();
            var id_utils_tenant = $("#id_utils_tenant").val();
            var shop_name = $("#shop_name").val();
            var utils_type = $("#utils_type").val();
            var meter_type = $("#meter_type").val();
            var formula_name = $("#formula_name").val();
            var id_formula = $("#id_formula").val();
            var category_utils_name = $("#category_utils_name").val();
            var id_utils_category = $("#id_utils_category").val();
            var meter_name = $("#meter_name").val();
            var id_meter = $("#id_meter").val();
            var handling_fee = $("#handling_fee").val();
            var bpju = $("#bpju").val();
            var lost_factor = $("#lost_factor").val();
            var admin_amt = $("#admin_amt").val();
            var ppju_rate = $("#ppju_rate").val();
            var trans_date = $("#trans_date").val();
            var hours_billboard = $("#hours_billboard").val();
            var days_billboard = $("#days_billboard").val();
            var start_LWBP = parseFloat($("#start_LWBP").val());
            var end_LWBP = parseFloat($("#end_LWBP").val());
            var start_WBP = parseFloat($("#start_WBP").val());
            var end_WBP = parseFloat($("#end_WBP").val());

            //alert(ppju_rate+' '+ admin_amt);
            //alert(start_LWBP+'<=>'+end_LWBP+'<=>'+start_WBP+'<=>'+end_WBP);

            if (tenant_name === '' || shop_name === '' || utils_type === '' || formula_name === '' ||
                category_utils_name === '' || meter_name === '' || hours_billboard === '' ||
                days_billboard === '' || end_LWBP === '' || end_WBP === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                if ((start_LWBP > end_LWBP) )
                {
                    alert('Your End Meter LWBP Lower Than Start Meter LWBP');
                    return false;
                }
                else if (start_WBP > end_WBP)
                {
                    alert('Your End Meter WBP Lower Than Start Meter WBP');
                    return false;
                }
                else
                {
                    $.ajax({
                        type: "post",
                        url: "{{ route('engineering.meterInput') }}",
                        data: {ID_UTILS_TENANT:id_utils_tenant,
                            MD_TENANT_NAME_CHAR:tenant_name,
                            MD_TENANT_ID_INT:id_tenant,
                            PSM_TRANS_NOCHAR:psm_nochar,
                            ID_UTILS_TENANT:id_utils_tenant,
                            SHOP_NAME_CHAR:shop_name,
                            UTILS_TYPE_NAME:utils_type,
                            METER_TYPE:meter_type,
                            NAME_U_FORMULA:formula_name,
                            ID_U_FORMULA:id_formula,
                            UTILS_CATEGORY_NAME:category_utils_name,
                            UTILS_CATEGORY_ID_INT:id_utils_category,
                            UTILS_METER_CHAR:meter_name,
                            ID_METER:id_meter,
                            IS_HANDLING:handling_fee,
                            IS_BPJU:bpju,
                            IS_LOST_FACTOR:lost_factor,
                            IS_ADMIN:admin_amt,
                            IS_PPJU:ppju_rate,
                            BILLING_DATE:trans_date,
                            BILLING_METER_BILLBOARD_HOUR:hours_billboard,
                            BILLING_METER_BILLBOARD_DAY:days_billboard,
                            BILLING_METER_START_LWBP:start_LWBP,
                            BILLING_METER_END_LWBP:end_LWBP,
                            BILLING_METER_START_WBP:start_WBP,
                            BILLING_METER_END_WBP:end_WBP,
                            _token: "{{ csrf_token() }}"},
                        dataType: 'json',
                        cache: false,
                        beforeSend: function(){ $('#loading').modal('show'); },
                        success: function (response) {
                            if(response['Success']) {
                                alert(response['Success']);
                                document.location.reload(true);
                            } else {
                                alert(response['Error']);
                            }
                        },
                        error: function() {
                            alert('Error, Please contact Administrator!');
                        }
                    });
                }
            }
        });
    });
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Tenant*</label>
                                <input type="text" name="MD_TENANT_NAME_CHAR" class="form-control" placeholder="Tenant" id="tenant_name" readonly="readonly">
                                <input type="hidden" name="MD_TENANT_ID_INT" class="form-control" id="id_tenant" readonly="readonly">
                                <input type="hidden" name="PSM_TRANS_NOCHAR" class="form-control" placeholder="Lease Document" id="psm_nochar" readonly="readonly">
                                <input type="hidden" name="ID_UTILS_TENANT" class="form-control" id="id_utils_tenant" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Shop Name*</label>
                                <input type="text" name="SHOP_NAME_CHAR" class="form-control" placeholder="Shop Name" id="shop_name" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Type*</label>
                                <input type="text" name="UTILS_TYPE_NAME" class="form-control" placeholder="Type" id="utils_type" readonly="readonly">
                                <input type="hidden" name="METER_TYPE" class="form-control" placeholder="Type" id="meter_type" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Formula*</label>
                                <input type="text" name="NAME_U_FORMULA" class="form-control" placeholder="Formula" id="formula_name" readonly="readonly">
                                <input type="hidden" name="ID_U_FORMULA" class="form-control" placeholder="Formula" id="id_formula" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Category*</label>
                                <input type="text" name="UTILS_CATEGORY_NAME" class="form-control" placeholder="Category" id="category_utils_name" readonly="yes">
                                <input type="hidden" name="UTILS_CATEGORY_ID_INT" class="form-control" id="id_utils_category" readonly="yes">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Meter*</label>
                                <input type="text" name="UTILS_METER_CHAR" class="form-control" placeholder="Formula" id="meter_name" readonly="yes">
                                <input type="hidden" name="ID_METER" class="form-control" placeholder="Formula" id="id_meter" readonly="yes">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Start Meter LWBP*</label>
                                <input type="text" name="BILLING_METER_START_LWBP" class="form-control" placeholder="Start Meter LWBP" id="start_LWBP" readonly="yes">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>End Meter LWBP*</label>
                                <input type="text" name="BILLING_METER_END_LWBP" class="form-control" placeholder="0" id="end_LWBP">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Start Meter WBP*</label>
                                <input type="text" name="BILLING_METER_START_WBP" class="form-control" placeholder="Start Meter WBP" id="start_WBP" readonly="yes">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>End Meter WBP*</label>
                                <input type="text" name="BILLING_METER_END_WBP" class="form-control" placeholder="0" id="end_WBP">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>PPJU?</label>
                                <select name="IS_PPJU" class="form-control" id="ppju_rate">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Handling Fee?</label>
                                <select name="IS_HANDLING" class="form-control" id="handling_fee">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>BPJU?</label>
                                <select name="IS_BPJU" class="form-control" id="bpju">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Lost Factor?</label>
                                <select name="IS_LOST_FACTOR" class="form-control" id="lost_factor">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Administration?</label>
                                <select name="IS_ADMIN" class="form-control" id="admin_amt">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Transaction Date</label>
                                <input type="date" class="form-control" name="BILLING_DATE" placeholder="Transaction Date" id="trans_date">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Hours Billboard*</label>
                                <input type="text" name="BILLING_METER_BILLBOARD_HOUR" class="form-control" placeholder="0" id="hours_billboard">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Days Billboard*</label>
                                <input type="text" name="BILLING_METER_BILLBOARD_DAY" id="days_billboard" class="form-control" placeholder="0">
                            </div>
                            <br>
                            <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="update" style="float: right;">
                                Submit Data
                            </a>
                        </div>
                    </div>
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tenant</th>
                                    <th>Shop Name</th>
                                    <th>Type</th>
                                    <th>Formula</th>
                                    <th>Category</th>
                                    <th>Meter</th>
                                    <th>Start LWBP</th>
                                    <th>End LWBP</th>
                                    <th>Start WBP</th>
                                    <th>End WBP</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($utils as $data)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->UTILS_TYPE_NAME}}</td>
                                        <td>{{$data->NAME_U_FORMULA}}</td>
                                        <td>{{$data->UTILS_CATEGORY_NAME}}</td>
                                        <td>{{$data->UTILS_METER_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->METER_STAND_START_LWBP,2,',','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->METER_STAND_END_LWBP,2,',','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->METER_STAND_START_WBP,2,',','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->METER_STAND_END_WBP,2,',','.')}}</td>
                                        @if($data->TENANT_STATUS == 1)
                                        <td style="text-align:center;">
                                            <i class='fa fa-edit' title='Edit Data' onclick='getItem(<?php echo $data->ID_UTILS_TENANT; ?>);'></i>
                                        </td>
                                        @else
                                            <td style="text-align:center;">-</td>
                                        @endif
                                        <?php $i++; ?>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


