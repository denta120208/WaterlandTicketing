@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Util Tenant - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Util Tenant
@endsection

@section('content')
<script>
    $(function(){
        var table = $('#psm_table').DataTable({
            order:[]
        });

        $('#psm_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            //alert(data);
            document.getElementById('shop_name').value = checkEmptyStringValidation(data[2]);
            document.getElementById('psm_trans_nochar').value = checkEmptyStringValidation(data[4]);
            document.getElementById('tenant_name').value = checkEmptyStringValidation(data[1]);
            document.getElementById('tenant_id').value = checkEmptyStringValidation(data[0]);
            $('#psmModal').modal('hide');
        });

        $('#tenant_name').on('click',function(){
            $('#psmModal').modal('show');
        });
    });

    $(function(){
        var table = $('#formula_table').DataTable({
            order:[]
        });

        $('#formula_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            //alert(data);
            document.getElementById('utils_formula_name').value = checkEmptyStringValidation(data[1]);
            document.getElementById('id_formula').value = checkEmptyStringValidation(data[0]);
            $('#formulaModal').modal('hide');
        });

        $('#utils_formula_name').on('click',function(){
            $('#formulaModal').modal('show');
        });
    });

    $(function(){
        var table = $('#meter_table').DataTable({
            order:[]
        });

        $('#meter_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            //alert(data);
            document.getElementById('utils_meter').value = checkEmptyStringValidation(data[1]);
            document.getElementById('id_meter').value = checkEmptyStringValidation(data[0]);
            $('#meterModal').modal('hide');
        });

        $('#utils_meter').on('click',function(){
            $('#meterModal').modal('show');
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
$(document).ready(function()    {
    $('#engineering_table').DataTable({
        pageLength : 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                footer: true,
                title: '<?php echo "Utils Tenant ".$dataProject['PROJECT_CODE']; ?>'
            }
        ]
    });
});
</script>
<script type="text/javascript">

    function delItem(id){
        $.ajax({
            type: "post",
            url: "{{ route('engineering.deleteitemutilstenant') }}",
            data: {ID_UTILS_TENANT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function (response) {
                if(response['Success']){
                    //alert(response['Success']);
                    document.location.reload(true);
                    //$('#ptCash_trans_table').data.reload();
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

    function getItem(id){
        //alert('test');

        $.ajax({
            type: "post",
            url: "{{ route('engineering.getitemutilstenant') }}",
            data: {ID_UTILS_TENANT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    $("#tenant_name").val(data['MD_TENANT_NAME_CHAR']);
                    $("#psm_trans_nochar").val(data['PSM_TRANS_NOCHAR']);

                    $("#tenant_id").val(data['ID_TENANT']);
                    $("#shop_name").val(data['SHOP_NAME_CHAR']);
                    $("#id_utils_tenant").val(data['ID_UTILS_TENANT']);
                    $("#utils_formula_name").val(data['NAME_U_FORMULA']);
                    $("#id_formula").val(data['ID_U_FORMULA']);

                    $("#utils_meter").val(data['UTILS_METER_CHAR']);
                    $("#id_meter").val(data['ID_METER']);
                    $("#id_meter_old").val(data['ID_METER']);

                    $("#insert_id").val('0');
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
            var id_utils_tenant = $("#id_utils_tenant").val();
            var psm_trans_nochar = $("#psm_trans_nochar").val();
            var tenant_id = $("#tenant_id").val();
            var id_formula = $("#id_formula").val();
            var id_meter = $("#id_meter").val();
            var id_meter_old = $("#id_meter_old").val();
            var insert_id = $("#insert_id").val();

            // alert(id_utils_tenant+'<=>'+psm_trans_nochar+'<=>'+tenant_id+'<=>'+id_formula+'<=>'+id_meter+
            // '<=>'+insert_id);
            if (psm_trans_nochar === '' || tenant_id === '' ||
                id_formula === '' || id_meter === '' )
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "{{ route('engineering.tenant.save') }}",
                    data: {ID_UTILS_TENANT:id_utils_tenant,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        ID_TENANT:tenant_id,
                        ID_U_FORMULA:id_formula,
                        ID_METER:id_meter,
                        ID_METER_OLD:id_meter_old,
                        insert_id:insert_id,
                        _token: "{{ csrf_token() }}"},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){ $('#loading').modal('show'); },
                    success: function (response) {
                        if(response['Success']){
                            //alert(response['Success']);
                            document.location.reload(true);
                            //$('#ptCash_trans_table').data.reload();
                        }else{
                            alert(response['Error']);
                        }
                        //$('#loading').modal('hide');
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });
    });
</script>
<style>
    @media screen and (min-width: 676px) {
        .modal-dialog {
            max-width: 700px; /* New width for default modal */
        }
    }
</style>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Tenant*</label>
                                <input type="text" name="MD_TENANT_NAME_CHAR" id="tenant_name" class="form-control" placeholder="Tenant" readonly>
                                <input type="hidden" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" readonly>
                                <input type="hidden" name="ID_TENANT" id="tenant_id" class="form-control" readonly>
                                <input type="hidden" name="SHOP_NAME_CHAR" id="shop_name" class="form-control" readonly>
                                <input type="hidden" name="ID_UTILS_TENANT" id="id_utils_tenant" class="form-control" value="0" readonly>
                                <input type="hidden" name="insert_id" id="insert_id" class="form-control" value="1" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Utils Formula*</label>
                                <input type="text" name="NAME_U_FORMULA" id="utils_formula_name" class="form-control" placeholder="Utils Formula" readonly>
                                <input type="hidden" name="ID_U_FORMULA" id="id_formula" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Utils Meter*</label>
                                <input type="text" name="UTILS_METER_CHAR" id="utils_meter" class="form-control" placeholder="Utils Meter" readonly>
                                <input type="hidden" name="ID_METER" id="id_meter" class="form-control" readonly>
                                <input type="hidden" name="ID_METER_OLD" id="id_meter_old" class="form-control" readonly>
                                <br>
                                <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="update" style="float: right;">
                                    Insert/Update
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="engineering_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tenant</th>
                                    <th>Shop Name</th>
                                    <th>Utils Formula</th>
                                    <th>Utils Meter</th>
                                    <th>Type</th>
                                    <th>View/Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;?>
                                @foreach($utils as $data)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{ $data->MD_TENANT_NAME_CHAR }}</td>
                                        <td>{{ $data->SHOP_NAME_CHAR }}</td>
                                        <td>{{ $data->NAME_U_FORMULA }}</td>
                                        <td>{{ $data->UTILS_METER_CHAR }}</td>
                                        <td>{{ $data->UTILS_TYPE_NAME }}</td>
                                        <td style="text-align:center;">
                                            <i class='fa fa-edit' title='Edit Data' onclick='getItem(<?php echo $data->ID_UTILS_TENANT; ?>);'></i>|
                                            <i class='fa fa-trash' title='Delete Data' onclick='delItem(<?php echo $data->ID_UTILS_TENANT; ?>);'></i>
                                        </td>
                                        <?php $i++;?>
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

<div class="col-md-3 col-sm-offset-10">
    <div id="formulaModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Formula</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="formula_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Formula</th>
                            <th>Type</th>
                            <th>Catgory</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($uForm as $formula)
                            <tr>
                                <td>{{$formula->ID_U_FORMULA}}</td>
                                <td>{{$formula->NAME_U_FORMULA}}</td>
                                <td>{{$formula->UTILS_TYPE_NAME}}</td>
                                <td>{{$formula->UTILS_CATEGORY_NAME}}</td>
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
    <div id="meterModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Meters</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="meter_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Meters</th>
                            <th>Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($meter as $meters)
                            <tr>
                                <td>{{$meters->ID_METER}}</td>
                                <td>{{$meters->UTILS_METER_CHAR}}</td>
                                <td>{{$meters->UTILS_TYPE_NAME}}</td>
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
    <div id="psmModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tenant</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="psm_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tenant</th>
                            <th>Shop Name</th>
                            <th>Lot</th>
                            <th>Lease Doc.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($util_types as $psm)
                            <tr>
                                <td>{{$psm->MD_TENANT_ID_INT}}</td>
                                <td>{{$psm->MD_TENANT_NAME_CHAR}}</td>
                                <td>{{$psm->SHOP_NAME_CHAR}}</td>
                                <td>{{$psm->LOT_STOCK_NO}}</td>
                                <td>{{$psm->PSM_TRANS_NOCHAR}}</td>
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
