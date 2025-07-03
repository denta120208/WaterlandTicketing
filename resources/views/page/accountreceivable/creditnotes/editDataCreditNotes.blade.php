@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Edit Credit Notes - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Credit Notes
@endsection

@section('content')

<style>
    th, td {
        padding: 15px;
    }
</style>

<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tdp_report').DataTable( {
            order : [],
            scrollX : "1500px",
            pageLength : 25
        });
    });
</script>
<script>
    $(document).ready(function()
    {
        $('#tdp_report1').DataTable( {
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );
</script>

<script>
    $(function(){
        var table = $('#cn_type_table').DataTable({
            order:[]
        });

        $('#cn_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            if (document.getElementById('invoice_type').value == 'UT')
            {
                if (checkEmptyStringValidation(data[0]) == 'OUTSTANDING')
                {
                    document.getElementById('cn_trans_dtl_type').value = checkEmptyStringValidation(data[0]);
                    document.getElementById('inv_trans_nochar_utils').value = '';
                    document.getElementById('inv_dtl_desc').value = '';
                    document.getElementById('inv_amount').value = '0';
                    document.getElementById('inv_trans_nochar_utils').disabled = true;
                }
                else
                {
                    document.getElementById('cn_trans_dtl_type').value = checkEmptyStringValidation(data[0]);
                    document.getElementById('inv_trans_nochar_utils').value = '';
                    document.getElementById('inv_dtl_desc').value = '';
                    document.getElementById('inv_amount').value = '0';
                    document.getElementById('inv_trans_nochar_utils').disabled = false;
                }
            }
            else
            {
                if (checkEmptyStringValidation(data[0]) == 'OUTSTANDING')
                {
                    document.getElementById('cn_trans_dtl_type').value = checkEmptyStringValidation(data[0]);
                    document.getElementById('inv_trans_nochar').value = '';
                    document.getElementById('inv_dtl_desc').value = '';
                    document.getElementById('inv_dtl_amount').value = '0';
                    document.getElementById('inv_trans_nochar').disabled = true;
                }
                else
                {
                    document.getElementById('cn_trans_dtl_type').value = checkEmptyStringValidation(data[0]);
                    document.getElementById('inv_trans_nochar').value = '';
                    document.getElementById('inv_dtl_desc').value = '';
                    document.getElementById('inv_dtl_amount').value = '0';
                    document.getElementById('inv_trans_nochar').disabled = false;
                }
            }


            $('#cnTypeModal').modal('hide');
        });

        $('#cn_trans_dtl_type').on('click',function(){
            $('#cnTypeModal').modal('show');
        });
    });

    $(function(){
        var table = $('#invoice_table_utils').DataTable({
            order:[]
        });

        $('#invoice_table_utils tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('inv_trans_nochar_utils').value = checkEmptyStringValidation(data[0]);
            document.getElementById('inv_dtl_desc').value = checkEmptyStringValidation(data[1]);
            document.getElementById('inv_amount').value = checkEmptyStringValidation(data[2]);
            document.getElementById('cn_dtl_amount').value = checkEmptyStringValidation(data[2]);
            $('#invoiceModalUtils').modal('hide');
        });

        $('#inv_trans_nochar_utils').on('click',function(){
            $('#invoiceModalUtils').modal('show');
        });
    });

    $(function(){
        var table = $('#invoice_table').DataTable({
            order:[]
        });

        $('#invoice_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
                document.getElementById('inv_trans_nochar').value = checkEmptyStringValidation(data[0]);
                document.getElementById('inv_dtl_desc').value = checkEmptyStringValidation(data[1]);
                document.getElementById('inv_dtl_amount').value = checkEmptyStringValidation(data[2]);
                $('#invoiceModal').modal('hide');
        });

        $('#inv_trans_nochar').on('click',function(){
            $('#invoiceModal').modal('show');
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
        $('#dtl_dpp').on('change',function(){
            var dtl_dpp = parseFloat($("#dtl_dpp").val());
            var dtl_ppn = parseInt(dtl_dpp * 0.11);
            var dtl_total = parseInt(dtl_dpp + dtl_ppn);

            document.getElementById("dtl_ppn").value = dtl_ppn;
            document.getElementById("dtl_total").value = dtl_total;
        });
    });
</script>
<script>
    function delItemCN(id){
        $.ajax({
            type: "post",
            url: "{{ route('creditnotes.deleteitemcreditnotes') }}",
            data: {CN_TRANS_DTL_ID:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function (response) {
                if(response['Success']){
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

    function getItemCN(id){
        $.ajax({
            type: "post",
            url: "{{ route('creditnotes.getitemcreditnotes') }}",
            data: {CN_TRANS_DTL_ID:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    if (document.getElementById('invoice_type').value == 'UT')
                    {
                        $("#cn_trans_dtl_type").val(data['CN_TRANS_DTL_TYPE']);
                        $("#cn_trans_dtl_id").val(data['CN_TRANS_DTL_ID']);
                        $("#inv_trans_nochar_utils").val(data['INVOICE_TRANS_NOCHAR']);
                        $("#inv_dtl_desc").val(data['INVOICE_TRANS_DESC_CHAR']);
                        $("#inv_amount").val(data['INVOICE_AMOUNT']);
                        $("#cn_dtl_amount").val(data['CN_TRANS_DTL_AMOUNT']);
                        $("#insert_id").val('0');
                    }
                    else
                    {
                        $("#cn_trans_dtl_type").val(data['CN_TRANS_DTL_TYPE']);
                        $("#cn_trans_dtl_id").val(data['CN_TRANS_DTL_ID']);
                        $("#inv_trans_nochar").val(data['INVOICE_TRANS_NOCHAR']);
                        $("#inv_dtl_desc").val(data['INVOICE_TRANS_DESC_CHAR']);
                        $("#inv_amount").val(data['INVOICE_AMOUNT']);
                        $("#inv_dtl_amount").val(data['INVOICE_TRANS_AMOUNT']);
                        $("#cn_dtl_amount").val(data['CN_TRANS_DTL_AMOUNT']);
                        $("#insert_id").val('0');
                    }
                }else{
                    alert(data['msg']);
                }
                $('#loading').modal('hide');
            }
        });
    };

    $(function(){
        $('#update').on('click',function(){
            var cn_trans_dtl_id = $("#cn_trans_dtl_id").val();
            var cn_trans_nochar = $("#cn_trans_nochar").val();
            var cn_trans_dtl_type = $("#cn_trans_dtl_type").val();
            var inv_trans_nochar = $("#inv_trans_nochar").val();
            var inv_amount = parseInt($("#inv_amount").val());
            var inv_dtl_amount = parseInt($("#inv_dtl_amount").val());
            var cn_dtl_amount = parseInt($("#cn_dtl_amount").val());
            var insert_id = $("#insert_id").val();
            var cn_count = parseInt($("#cn_count").val());
            var invoice_type = $("#invoice_type").val();

            if (cn_count >= 10)
            {
                alert('You Cannot Process Over 10 Invoice');
                return false;
            }

            if (cn_trans_dtl_type === "" || cn_dtl_amount <= 0 || inv_trans_nochar === "" || inv_dtl_amount <= 0)
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                if (inv_dtl_amount < cn_dtl_amount)
                {
                    alert('Credit Notes Amount Bigger Than Invoice Amount');
                    return false;
                }
                else
                {
                    $.ajax({
                        type: "post",
                        url: "{{ route('creditnotes.insertupdateitemcreditnotes') }}",
                        data: {CN_TRANS_DTL_ID:cn_trans_dtl_id,
                            CN_TRANS_NOCHAR:cn_trans_nochar,
                            CN_TRANS_DTL_TYPE:cn_trans_dtl_type,
                            INVOICE_TRANS_NOCHAR:inv_trans_nochar,
                            INVOICE_AMOUNT:inv_amount,
                            INVOICE_TRANS_AMOUNT:inv_dtl_amount,
                            CN_TRANS_DTL_AMOUNT:cn_dtl_amount,
                            INVOICE_TRANS_TYPE:invoice_type,
                            insert_id:insert_id,
                            _token: "{{ csrf_token() }}"},
                        dataType: 'json',
                        cache: false,
                        beforeSend: function(){ $('#loading').modal('show'); },
                        success: function (response) {
                            if(response['Success']){
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
                }
            }
        });
    });

    $(function(){
        $('#updateUtils').on('click',function(){
            var cn_trans_dtl_id = $("#cn_trans_dtl_id").val();
            var cn_trans_nochar = $("#cn_trans_nochar").val();
            var cn_trans_dtl_type = $("#cn_trans_dtl_type").val();
            var inv_trans_nochar = $("#inv_trans_nochar_utils").val();
            var inv_amount = parseInt($("#inv_amount").val());
            var cn_dtl_amount = parseInt($("#cn_dtl_amount").val());
            var insert_id = $("#insert_id").val();
            var cn_count = parseInt($("#cn_count").val());
            var invoice_type = $("#invoice_type").val();

            if (cn_count >= 10)
            {
                alert('You Cannot Process Over 10 Invoice');
                return false;
            }

            if (cn_trans_dtl_type === "" || cn_dtl_amount <= 0 || inv_trans_nochar === "")
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                if (inv_amount < cn_dtl_amount)
                {
                    alert('Credit Notes Amount Bigger Than Invoice Amount');
                    return false;
                }
                else
                {
                    $.ajax({
                        type: "post",
                        url: "{{ route('creditnotes.insertupdateitemcreditnotes') }}",
                        data: {CN_TRANS_DTL_ID:cn_trans_dtl_id,
                            CN_TRANS_NOCHAR:cn_trans_nochar,
                            CN_TRANS_DTL_TYPE:cn_trans_dtl_type,
                            INVOICE_TRANS_NOCHAR:inv_trans_nochar,
                            INVOICE_AMOUNT:inv_amount,
                            INVOICE_TRANS_AMOUNT:cn_dtl_amount,
                            CN_TRANS_DTL_AMOUNT:cn_dtl_amount,
                            INVOICE_TRANS_TYPE:invoice_type,
                            insert_id:insert_id,
                            _token: "{{ csrf_token() }}"},
                        dataType: 'json',
                        cache: false,
                        beforeSend: function(){ $('#loading').modal('show'); },
                        success: function (response) {
                            if(response['Success']){
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
                }
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
                    @if($roles == '22' || $roles == '9' || $roles == '99')
                    @else
                    <form method="POST" action="{{ route('creditnotes.saveeditcreditnotes') }}">
                    @csrf
                    @endif
                        <fieldset>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Document*</label>
                                        <input type="text" name="CN_TRANS_NOCHAR" id="cn_trans_nochar" class="form-control" placeholder="Document" value="<?php echo $dataCN->CN_TRANS_NOCHAR; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Lease Document*</label>
                                        <input type="text" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" placeholder="Document" value="<?php echo $dataCN->PSM_TRANS_NOCHAR; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Lot</label>
                                        <input type="text" name="LOT_STOCK_NO" id="lot_stock_no" class="form-control" placeholder="Lot" value="<?php echo $lotNo; ?>" readonly="yes">
                                        <input type="hidden" name="LOT_STOCK_ID_INT" id="lot_stock_id" class="form-control" value="<?php echo $lotId; ?>" readonly="yes">
                                        <input type="hidden" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" value="<?php echo $noPSM; ?>" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tenant*</label>
                                        <input type="text" name="MD_TENANT_NAME_CHAR" id="md_tenant_name" class="form-control" placeholder="Tenant" value="<?php echo $tenantName; ?>" readonly="yes">
                                        <input type="hidden" name="MD_TENANT_ID_INT" id="md_tenant_id_int" class="form-control" value="<?php echo $tenantId; ?>" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>SQM</label>
                                        <input type="text" name="LOT_STOCK_SQM" id="lot_stock_sqm" class="form-control" placeholder="SQM" value="<?php echo $sqm; ?>" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Invoice Type*</label>
                                        <input type="text" class="form-control" id="invoice_type_desc" name="INVOICE_TRANS_TYPE_DESC" value="<?php echo $billingDesc; ?>" placeholder="Invoice Type" readonly>
                                        <input type="hidden" class="form-control" id="invoice_type" name="INVOICE_TRANS_TYPE" value="<?php echo $billingCode; ?>" readonly>
                                    </div>
                                </div>
                                @if($roles == '22' || $roles == '9' || $roles == '99')
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Transaction Date*</label>
                                            <input type="date" value="{{$dataCN->CN_TRANS_TRX_DATE}}" class="form-control" name="CN_TRANS_TRX_DATE" placeholder="Transaction Date" readonly="yes">
                                        </div>
                                    </div>
                                @else
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>Transaction Date*</label>
                                            <input type="date" value="{{$dataCN->CN_TRANS_TRX_DATE}}" class="form-control" name="CN_TRANS_TRX_DATE" placeholder="Transaction Date">
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Amount*</label>
                                        <input type="text" name="CN_TRANS_AMOUNT" class="form-control" id="amount" placeholder="0" value="<?php echo number_format($dataCN->CN_TRANS_AMOUNT,0,'','.'); ?>" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                @if($roles == '22' || $roles == '9' || $roles == '99')
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description*</label>
                                        <input type="text" class="form-control" name="CN_TRANS_DESC" id="inv_desc" placeholder="Description" maxlength="100" readonly="yes" value="<?php echo $dataCN->CN_TRANS_DESC; ?>">
                                    </div>
                                </div>
                                @else
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description*</label>
                                        <input type="text" name="CN_TRANS_DESC" id="inv_desc" class="form-control" placeholder="Description" maxlength="100" value="<?php echo $dataCN->CN_TRANS_DESC; ?>">
                                    </div>
                                </div>
                                @endif
                            </div>
                            <br><br>
                            @if($roles == '22' || $roles == '9')
                            @else
                                @if($dataCN->INVOICE_TRANS_TYPE == 'UT')
                                    <h4>Input Transaction</h4>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                            <label>Type</label>
                                            <input type="text" class="form-control" id="cn_trans_dtl_type" name="CN_TRANS_DTL_TYPE" readonly="yes">
                                            <input type="hidden" class="form-control" id="cn_trans_dtl_id" name="CN_TRANS_DTL_ID" value="0" readonly="yes">
                                            <input type="hidden" class="form-control" id="cn_count" name="CN_COUNT" value="<?php echo $countCNDetail; ?>" readonly="yes">
                                            <input type="hidden" class="form-control" id="insert_id" name="INSERT_ID" value="1" readonly="yes">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Invoice</label>
                                                <input type="text" name="INVOICE_TRANS_NOCHAR" id="inv_trans_nochar_utils" class="form-control" readonly="yes">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Invoice Description</label>
                                                <input type="text" name="INVOICE_TRANS_DESC_CHAR" id="inv_dtl_desc" class="form-control" readonly="yes">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Invoice Debt Amount</label>
                                                <input type="number" name="INVOICE_TRANS_AMOUNT" id="inv_amount" class="form-control" value="0" readonly="yes">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Credit Notes Amount</label>
                                                <input type="number" name="CN_TRANS_DTL_AMOUNT" id="cn_dtl_amount" class="form-control" value="0" readonly="yes">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="updateUtils" style="float: right;">
                                                Add/Update
                                            </a>
                                        </div>
                                    </div>
                                    <br><br>
                                @else
                                    <h4>Input Transaction</h4>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <input type="text" name="CN_TRANS_DTL_TYPE" id="cn_trans_dtl_type" class="form-control" readonly>
                                                <input type="hidden" name="CN_TRANS_DTL_ID" id="cn_trans_dtl_id" class="form-control" value="0" readonly>
                                                <input type="hidden" name="CN_COUNT" id="cn_count" class="form-control" value="<?php echo $countCNDetail; ?>" readonly>
                                                <input type="hidden" name="INSERT_ID" id="insert_id" class="form-control" value="1" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Invoice</label>
                                                <input type="text" name="INVOICE_TRANS_NOCHAR" id="inv_trans_nochar" class="form-control" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label>Invoice Description</label>
                                                <input type="text" name="INVOICE_TRANS_DESC_CHAR" id="inv_dtl_desc" class="form-control" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Detail Debt Amount</label>
                                                <input type="number" name="INVOICE_TRANS_AMOUNT" value="0" class="form-control" id="inv_dtl_amount" readonly="yes">
                                                <input type="hidden" name="INVOICE_TRANS_AMOUNT" value="0" class="form-control" id="inv_amount" readonly="yes">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Credit Notes Amount</label>
                                                <input type="number" name="CN_TRANS_DTL_AMOUNT" value="0" class="form-control" id="cn_dtl_amount">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="update" style="float: right;">
                                                Add/Update
                                            </a>
                                        </div>
                                    </div>
                                    <br><br>
                                @endif
                            @endif
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Type</th>
                                            <th>Invoice</th>
                                            <th>Description</th>
                                            @if($dataCN->INVOICE_TRANS_TYPE == 'UT')
                                                {{-- <th>Billing Type</th>
                                                <th>Invoice Debt Amount</th> --}}
                                            @endif
                                            <th>Detail Debt Amount</th>
                                            <th>Credit Notes Amount</th>
                                            @if($roles == '22' || $roles == '9')
                                            @else
                                            <th>Action</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $no = 1;
                                        $totalINV = 0;
                                        $totalINVAmount = 0;
                                        $totalCNAmount = 0;
                                        ?>
                                        @foreach($dataCNDetail as $detail)
                                            <tr>
                                                <td>{{$no}}</td>
                                                <td>{{$detail->CN_TRANS_DTL_TYPE}}</td>
                                                <td>{{$detail->INVOICE_TRANS_NOCHAR}}</td>
                                                <td>{{$detail->INVOICE_TRANS_DESC_CHAR}}</td>
                                                @if($dataCN->INVOICE_TRANS_TYPE == 'UT')
                                                    {{-- <td>{{$detail->UTILS_TYPE_NAME}}</td>
                                                    <td style="text-align: right;">{{number_format($detail->INVOICE_AMOUNT,0)}}</td> --}}
                                                @endif
                                                <td style="text-align: right;">{{number_format($detail->INVOICE_TRANS_AMOUNT,0)}}</td>
                                                <td style="text-align: right;">{{number_format($detail->CN_TRANS_DTL_AMOUNT,0)}}</td>
                                                @if($roles == '22' || $roles == '9')
                                                @else
                                                <td style="text-align:center;">
                                                    <i class='fa fa-edit' title='Edit Data' onclick='getItemCN(<?php echo $detail->CN_TRANS_DTL_ID; ?>);'></i>|
                                                    <i class='fa fa-trash' title='Delete Data' onclick='delItemCN(<?php echo $detail->CN_TRANS_DTL_ID; ?>);'></i>
                                                </td>
                                                @endif
                                            </tr>
                                            <?php
                                            $no += 1;
                                            $totalINV += $detail->INVOICE_AMOUNT;
                                            $totalINVAmount += $detail->INVOICE_TRANS_AMOUNT;
                                            $totalCNAmount += $detail->CN_TRANS_DTL_AMOUNT;
                                            ?>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td><b>TOTAL</b></td>
                                            <td><b>-</b></td>
                                            <td><b>-</b></td>
                                            <td><b>-</b></td>
                                            @if($dataCN->INVOICE_TRANS_TYPE == 'UT')
                                                {{-- <td><b>-</b></td>
                                                <td style="text-align: right;"><b>{{number_format($totalINV,0)}}</b></td> --}}
                                            @endif
                                            <td style="text-align: right;"><b>{{number_format($totalINVAmount,0)}}</b></td>
                                            <td style="text-align: right;"><b>{{number_format($totalCNAmount,0)}}</b></td>
                                            @if($roles == '22' || $roles == '9')
                                            @else
                                                <td style="text-align:center;">
                                                    <b> - </b>
                                                </td>
                                            @endif
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <br><br>
                            <div class="row" style="padding-left: 5px;">
                                @if($roles == '22' || $roles == '9' || $roles == '99')
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <a class="btn btn-sm btn-danger" href="{{ URL('/creditnotes/listdatacreeditnotes/') }}">
                                            <i>
                                                << Back to List
                                            </i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        @if($dataCN->CN_TRANS_STATUS_INT == 1)
                                            <a href="#approveModal{!!$dataCN->CN_TRANS_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal" style="float: right;">
                                                <i>
                                                    Approve Document
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$dataCN->CN_TRANS_ID_INT!!}" class="modal fade">
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
                                                            <a href="{{ URL('/creditnotes/approvedatacreditnotes/'. $dataCN->CN_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
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
                                @endif
                            </div>
                        </fieldset>
                    @if($roles == '22' || $roles == '9' || $roles == '99')
                    @else
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="cnTypeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="cn_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>INVOICE</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($billingCode == 'UT')
    <div class="col-md-3 col-sm-offset-10">
        <div id="invoiceModalUtils" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Invoice</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-hover dataTable  display compact" id="invoice_table_utils" style="padding: 0.5em;">
                            <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Description</th>
                                <th>Invoice Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dataListInvoice as $listInvoice)
                                <tr>
                                    <td>{{$listInvoice->INVOICE_TRANS_NOCHAR}}</td>
                                    <td>{{$listInvoice->INVOICE_TRANS_DESC_CHAR}}</td>
                                    <td>{{number_format($listInvoice->INVOICE_TRANS_AMOUNT,0,',','')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="col-md-3 col-sm-offset-10">
        <div id="invoiceModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Invoice</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-hover dataTable  display compact" id="invoice_table" style="padding: 0.5em;">
                            <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Description</th>
                                <th>Debt Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dataListInvoice as $listInvoice)
                                <tr>
                                    <td>{{$listInvoice->INVOICE_TRANS_NOCHAR}}</td>
                                    <td>{{$listInvoice->INVOICE_TRANS_DESC_CHAR}}</td>
                                    <td>{{number_format($listInvoice->INVOICE_TRANS_AMOUNT,0,',','')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection


