@extends('layouts.mainLayouts')

@section('navbar_header')
    Form View / Edit Invoice Revenue Sharing - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form View / Edit Invoice Revenue Sharing
@endsection

@section('content')

<style>
    th, td {
        padding: 15px;
    }
</style>
<script>
    $(document).ready(function()
    {
        $('#cash_book_table').DataTable({
            order : [],
            scrollX: true,
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

    $(function(){
        var table = $('#coa_table').DataTable({
            order:[]
        });

        $('#coa_table tbody').on('click', 'tr', function ()
        {
            var noinvoiced = $('#noinvoiced').val();
            var refnochar = $('#refnochar').val();
            var desc = $('#desc').val();
            var cust_id = $('#cust_id').val();
            var cust_name = $('#cust_name').val();
            var debtoracct = $('#debtoracct').val();
            var data = table.row( this ).data();
            document.getElementById('desc_char').value = desc;
            document.getElementById('acc_no_char').value = checkEmptyStringValidation(data[1]);
            document.getElementById('acc_nop_char').value = checkEmptyStringValidation(data[0]);
            document.getElementById('acc_name_char').value = checkEmptyStringValidation(data[3]);
            $('#coaModal').modal('hide');
        });

        $('#acc_no_char').on('click',function(){
            $('#coaModal').modal('show');
        });
    });
</script>
<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
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
        var table = $('#lot_table').DataTable({
            order:[]
        });

        $('#lot_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('lot_stock_id').value = checkEmptyStringValidation(data[0]);
            document.getElementById('psm_trans_nochar').value = checkEmptyStringValidation(data[1]);
            document.getElementById('lot_stock_no').value = checkEmptyStringValidation(data[2]);
            document.getElementById('md_tenant_name').value = checkEmptyStringValidation(data[3]);
            document.getElementById('lot_stock_sqm').value = checkEmptyStringValidation(data[4]);
            $('#lotModal').modal('hide');
        });

        $('#lot_stock_no').on('click',function(){
            $('#lotModal').modal('show');
        });
    });

    $(function(){
        var table = $('#billing_type_table').DataTable({
            order:[]
        });

        $('#billing_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('invoice_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('invoice_type_desc').value = checkEmptyStringValidation(data[1]);
            $('#billingtypeModal').modal('hide');
        });

        $('#invoice_type_desc').on('click',function(){
            $('#billingtypeModal').modal('show');
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
            var dtl_ppn = parseInt(dtl_dpp * 0.1);
            var dtl_total = parseInt(dtl_dpp + dtl_ppn);

            document.getElementById("dtl_ppn").value = dtl_ppn;
            document.getElementById("dtl_total").value = dtl_total;
        });
    });
</script>
<script>
    function delItemInv(id){
        $.ajax({
            type: "post",
            url: "{{ route('invoice.deleteiteminv') }}",
            data: {INVOICE_TRANS_DTL_ID_INT:id, _token: "{{ csrf_token() }}"},
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

    function getItemInv(id){
        $.ajax({
            type: "post",
            url: "{{ route('invoice.getitemgltransrs') }}",
            data: {INVOICE_TRANS_DTL_ID_INT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    $("#dtl_desc").val(data['INVOICE_TRANS_DTL_DESC']);
                    $("#dtl_revenue").val(data['INVOICE_TRANS_REVENUE_AMT']);
                    $("#inv_dtl_id_int").val(data['INVOICE_TRANS_DTL_ID_INT']);
                    $("#insert_id").val('0');
                }else{
                    alert(data['msg']);
                }
                $('#loading').modal('hide');
            }
        });
    };

    $(function(){
        $('#update').on('click',function(){
            var invoice_trans_nochar = $("#invoice_trans_nochar").val();
            var dtl_desc = $("#dtl_desc").val();
            var dtl_revenue = $("#dtl_revenue").val();
            var inv_dtl_id_int = $("#inv_dtl_id_int").val();
            var insert_id = $("#insert_id").val();

            if (dtl_desc === "" || dtl_revenue === "")
            {
                alert("Cannot Save Empty Data/Field");
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "{{ route('invoice.insertupdateiteminvoicers') }}",
                    data: {INVOICE_TRANS_NOCHAR:invoice_trans_nochar,
                        INVOICE_TRANS_DTL_ID_INT:inv_dtl_id_int,
                        INVOICE_TRANS_DTL_DESC:dtl_desc,
                        INVOICE_TRANS_REVENUE_AMT:dtl_revenue,
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
                    <form method="POST" action="{{ route('invoice.saveeditinvoicemanual') }}">
                        @csrf
                        <fieldset>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Document*</label>
                                        <input type="text" name="INVOICE_TRANS_NOCHAR" id="invoice_trans_nochar" class="form-control" placeholder="Lot" value="<?php echo $dataInvoice->INVOICE_TRANS_NOCHAR; ?>" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Lot*</label>
                                        <input type="text" name="LOT_STOCK_NO" id="lot_stock_no" class="form-control" placeholder="Lot" value="<?php echo $lotNo; ?>" readonly="yes">
                                        <input type="hidden" name="LOT_STOCK_ID_INT" id="lot_stock_id" class="form-control" value="<?php echo $lotId; ?>" readonly="yes">
                                        <input type="hidden" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" value="<?php echo $dataInvoice->PSM_TRANS_NOCHAR; ?>" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tenant*</label>
                                        <input type="text" name="MD_TENANT_NAME_CHAR" id="md_tenant_name" class="form-control" placeholder="Tenant" value="<?php echo $dataTenant->MD_TENANT_NAME_CHAR; ?>" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>SQM*</label>
                                        <input type="text" name="LOT_STOCK_SQM" id="lot_stock_sqm" class="form-control" placeholder="SQM" value="<?php echo $sqm; ?>" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                @if($dataInvoice->INVOICE_AUTOMATION_INT == 0 && $dataInvoice->JOURNAL_STATUS_INT == 0)
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Transaction Date*</label>
                                        <input type="date" value="{{$dataInvoice->TGL_SCHEDULE_DATE}}" class="form-control" name="TGL_SCHEDULE_DATE" placeholder="Transaction Date">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Due Date*</label>
                                        <input type="date" value="{{$dataInvoice->TGL_SCHEDULE_DUE_DATE}}" class="form-control" name="TGL_SCHEDULE_DUE_DATE" placeholder="Due Date">
                                    </div>
                                </div>
                                @else
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Transaction Date*</label>
                                        <input type="date" value="{{$dataInvoice->TGL_SCHEDULE_DATE}}" class="form-control" name="TGL_SCHEDULE_DATE" placeholder="Transaction Date" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Due Date*</label>
                                        <input type="date" value="{{$dataInvoice->TGL_SCHEDULE_DUE_DATE}}" class="form-control" name="TGL_SCHEDULE_DUE_DATE" placeholder="Due Date" readonly="yes">
                                    </div>
                                </div>
                                @endif
                                @if($dataInvoice->INVOICE_AUTOMATION_INT == 0 && $dataInvoice->JOURNAL_STATUS_INT == 0)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Invoice Type*</label>
                                        <input type="text" class="form-control" id="invoice_type_desc" name="INVOICE_TRANS_TYPE_DESC" value="<?php echo $dataBillingType->INVOICE_TRANS_TYPE_DESC; ?>" placeholder="Invoice Type" readonly>
                                        <input type="hidden" class="form-control" id="invoice_type" name="INVOICE_TRANS_TYPE" value="<?php echo $dataBillingType->INVOICE_TRANS_TYPE; ?>" readonly>
                                    </div>
                                </div>
                                @else
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Invoice Type*</label>
                                        <input type="text" name="INVOICE_TRANS_TYPE_DESC" class="form-control" id="invoice_type_desc" value="<?php echo $dataInvoice->INVOICE_TRANS_TYPE; ?>" placeholder="Invoice Type" readonly>
                                        <input type="hidden" name="INVOICE_TRANS_TYPE" class="form-control" id="invoice_type" value="<?php echo $dataInvoice->INVOICE_TRANS_TYPE; ?>" readonly>
                                    </div>
                                </div>
                                @endif
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Amount*</label>
                                        <input type="text" name="INVOICE_TRANS_TOTAL" id="amount" class="form-control" placeholder="0" value="<?php echo number_format($dataInvoice->INVOICE_TRANS_TOTAL,0,'','.'); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description*</label>
                                        <input type="text" name="INVOICE_TRANS_DESC_CHAR" id="inv_desc" class="form-control" placeholder="Description" maxlength="100" value="<?php echo $dataInvoice->INVOICE_TRANS_DESC_CHAR; ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            @if($dataInvoice->INVOICE_AUTOMATION_INT == 0 && $dataInvoice->JOURNAL_STATUS_INT == 0)
                            <h4>Input Transaction</h4>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Detail Description</label>
                                        <input type="text" name="INVOICE_TRANS_DTL_DESC" id="dtl_desc" class="form-control">
                                        <input type="hidden" name="INVOICE_TRANS_DTL_ID_INT" id="inv_dtl_id_int" class="form-control" value="0">
                                        <input type="hidden" name="INSERT_ID" id="insert_id" class="form-control" value="1">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Revenue Amt.</label>
                                        <input type="number" name="INVOICE_TRANS_REVENUE_AMT" id="dtl_revenue" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="update" style="float: right;">
                                        Add/Update
                                    </a>
                                </div>
                            </div>
                            @endif
                            <br><br>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <table class="table-striped table-hover compact" id="cash_book_table" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Description</th>
                                            <th>Revenue Amt.</th>
                                            <th>Base Amount</th>
                                            <th>Tax Amount</th>
                                            <th>Total Amount</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $no = 1; ?>
                                        @foreach($dataInvoiceDetail as $detail)
                                            <tr>
                                                <td>{{$no}}</td>
                                                <td>{{$detail->INVOICE_TRANS_DTL_DESC}}</td>
                                                <td style="text-align: right;">{{number_format($detail->INVOICE_TRANS_REVENUE_AMT,0,',','.')}}</td>
                                                <td style="text-align: right;">{{number_format($detail->INVOICE_TRANS_DTL_DPP,0,',','.')}}</td>
                                                <td style="text-align: right;">{{number_format($detail->INVOICE_TRANS_DTL_PPN,0,',','.')}}</td>
                                                <td style="text-align: right;">{{number_format($detail->INVOICE_TRANS_DTL_TOTAL,0,',','.')}}</td>
                                                <td style="text-align:center;">
                                                    <i class='fa fa-edit' title='Edit Data' onclick='getItemInv(<?php echo $detail->INVOICE_TRANS_DTL_ID_INT; ?>);'></i>|
                                                    <i class='fa fa-trash' title='Delete Data' onclick='delItemInv(<?php echo $detail->INVOICE_TRANS_DTL_ID_INT; ?>);'></i>
                                                </td>
                                            </tr>
                                            <?php $no += 1; ?>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <br><br>
                            <div class="row" style="padding-left: 5px;">
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
                                                        <input type="submit" value="Save Data" class="btn btn-primary">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="lotModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Lot</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="lot_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Lease Doc.</th>
                            <th>Lot</th>
                            <th>Tenant</th>
                            <th>SQM</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataLot as $data)
                            <tr>
                                <td>{{$data->LOT_STOCK_ID_INT}}</td>
                                <td>{{$data->PSM_TRANS_NOCHAR}}</td>
                                <td>{{$data->LOT_STOCK_NO}}</td>
                                <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                <td style="text-align: right;">{{number_format($data->LOT_STOCK_SQM,0,'','.')}}</td>
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


