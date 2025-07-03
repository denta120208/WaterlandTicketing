@extends('layouts.mainLayouts')

@section('navbar_header')
    Form List Invoice Approve - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form List Invoice Approve
@endsection

@section('content')

@if (Session::has('message'))
    <div class="alert alert-success" id="success-alert">
        {{ Session::get('message') }}
    </div>
@elseif (Session::has('errorFailed'))
    <div class="alert alert-danger" id="success-alert">
        {{ Session::get('errorFailed') }}
    </div>
@endif

<style>
th, td {
    padding: 5px;
}

.vertical-text {
    float: left;
    transform: rotate(270deg);
    padding: 1em;
    font-size: 1em;
    color: black;
}
</style>
<script type="text/javascript">
    function checkEmptyStringValidation(arr){
        var dataArr;

        if(arr == null){
             return dataArr = "";
        }else{
            return dataArr = arr;
        }
    }
</script>
<script type="text/javascript">

    function filterColumn ( i )
    {
         $('#billingpayment_sales_report').DataTable().column( i ).search(
                $('#col'+i+'_filter').val()
                ).draw();
    }

     $(function()
    {
        $( "#startDate" ).datepicker({
              dateFormat: "yy-mm-dd",
              changeMonth: true,
              changeYear: true,
              onClick : function(date){
               document.getElementById('startDate').value = date;
              }
        });
    });

    $(function()
    {
        $( "#endDate" ).datepicker({
             dateFormat: "yy-mm-dd",
              changeMonth: true,
              changeYear: true,
               onClick : function(date){
               document.getElementById('endDate').value = date;
              }

        });
    });

    $(document).ready(function() {
       $.fn.DataTable.ext.search.push(
            function(settings, data, dataindex) {
                var startDate = Date.parse($('#startDate').val());
                var endDate = Date.parse($('#endDate').val());
                var dateColumn = Date.parse( data[0] ) || 0; // use data for the date column

                if ( ( isNaN( startDate ) && isNaN( endDate ) ) ||
                    ( isNaN( startDate ) && dateColumn <= endDate ) ||
                    ( startDate <= dateColumn   && isNaN( endDate ) ) ||
                    ( startDate <= dateColumn   && dateColumn <= endDate ) )
                {
                    return true;
                }
                    return false;
            }
        );

       $('#rental_payment').DataTable( {
           order : [],
           pageLength : 10,
           scrollX: true,
       });

        $('#sc_payment').DataTable( {
            order : [],
            pageLength : 10,
            scrollX: true,
        });

        $('#ut_payment').DataTable( {
            order : [],
            pageLength : 10,
            scrollX: true,
        });

        $('#cl_payment').DataTable( {
            order : [],
            pageLength : 10,
            scrollX: true,
        });

        $('#rs_payment').DataTable( {
            order : [],
            pageLength : 10,
            scrollX: true,
        });

        $('#ot_payment').DataTable( {
            order : [],
            pageLength : 10,
            scrollX: true,
        });

        $('#rb_payment').DataTable( {
            order : [],
            pageLength : 10,
            scrollX: true,
        });

        $('#sd_payment').DataTable( {
            order : [],
            pageLength : 10,
            scrollX: true,
        });
} );
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Rental Payment</h4>
                            <table class="table-striped table-hover compact" id="rental_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceRENT as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Service Charge</h4>
                            <table class="table-striped table-hover compact" id="sc_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceSC as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Utility</h4>
                            <table class="table-striped table-hover compact" id="ut_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceUT as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Casual Leasing</h4>
                            <table class="table-striped table-hover compact" id="cl_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceCL as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Revenue Sharing</h4>
                            <table class="table-striped table-hover compact" id="rs_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceRS as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Others</h4>
                            <table class="table-striped table-hover compact" id="ot_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceOT as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Reimbursement</h4>
                            <table class="table-striped table-hover compact" id="rb_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceRB as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; padding-left: 5px;">
                        <div class="col-md-12">
                            <h4>Security Deposit</h4>
                            <table class="table-striped table-hover compact" id="sd_payment" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Shop</th>
                                    <th>Description</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Payment Account</th>
                                    <th>Inv. Amount</th>
                                    <th>Fine Amount</th>
                                    <th>Payment Amount</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoiceSD as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td>{{$data->TGL_BAYAR_DATE}}</td>
                                        <td>{{$data->PAYMENT_METHOD}}</td>
                                        <td>{{$data->ACC_NAME_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_DENDA,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->PAID_BILL_AMOUNT,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/approveinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Reject
                                                </i>
                                            </a>
                                            <div id="rejectModal{!!$data->INVOICE_PAYMENT_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure reject this transaction ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="{{ URL('/invoice/rejectinvoicepayment/'. $data->INVOICE_PAYMENT_ID_INT) }}" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
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
