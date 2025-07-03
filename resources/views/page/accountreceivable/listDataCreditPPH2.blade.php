@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Credit PPh - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Credit PPh
@endsection

@section('content')

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
<style>
    @media screen and (min-width: 676px) {
        .modal-dialog {
            max-width: 800px; /* New width for default modal */
        }
    }
</style>
<script type="text/javascript">
    $(function(){
        var table = $('#type_inv_table').DataTable({
            order:[]
        });

        $('#type_inv_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('type_inv').value = checkEmptyStringValidation(data[0]);
            document.getElementById('type_inv_char').value = checkEmptyStringValidation(data[1]);
            $('#typeinvModal').modal('hide');
        });

        $('#type_inv_char').on('click',function(){
            $('#typeinvModal').modal('show');
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

        $('#tdp_report').DataTable( {
            order : [],
            pageLength : 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: '<?php echo "Data Credit PPh "; ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "Data Credit PPh "; ?>'
                }
            ]
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
                            <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Faktur</th>
                                    <th>Lot</th>
                                    <th>Tenant</th>
                                    <th>Bill Date</th>
                                    <th>Description</th>
                                    <th>DPP</th>
                                    <th>PPN</th>
                                    <th>PPH</th>
                                    <th>Bill Amount</th>
                                    <th>Remaining PPH Amount</th>
                                    <th>Process</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($dataInvoicePT as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->INVOICE_TRANS_NOCHAR}}</td>
                                        <td>{{$data->INVOICE_FP_NOCHAR}}</td>
                                        <td>{{$data->LOT_STOCK_NO}}</td>
                                        <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                        <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                        <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_DPP,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_PPN,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_PPH,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,'','.')}}</td>
                                        <td style="text-align: right;">{{number_format($data->SISA_BAYAR,0,'','.')}}</td>
                                        <td class="center">
                                            <a href="#InputBPModal{!!$data->INVOICE_TRANS_ID_INT!!}" class="btn btn-sm btn-warning" data-toggle="modal">
                                                <i>
                                                    Process
                                                </i>
                                            </a>
                                            <div id="InputBPModal{!!$data->INVOICE_TRANS_ID_INT!!}" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Input Credit PPh</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <form method="POST" action="{{ route('invoice.processcreditpph') }}">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-lg-4">
                                                                        <div class="form-group">
                                                                            <label>Invoice</label>
                                                                            <input type="text" name="INVOICE_TRANS_NOCHAR" id="INVOICE_TRANS_NOCHAR" value="<?php echo $data->INVOICE_TRANS_NOCHAR; ?>" class="form-control" readonly>
                                                                            <input type="hidden" name="INVOICE_TRANS_ID_INT" id="INVOICE_TRANS_ID_INT" value="<?php echo $data->INVOICE_TRANS_ID_INT; ?>" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <div class="form-group">
                                                                            <label>Lot</label>
                                                                            <input type="text" name="LOT_STOCK_NO" id="LOT_STOCK_NO" value="<?php echo $data->LOT_STOCK_NO; ?>" class="form-control" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <div class="form-group">
                                                                            <label>Tenant</label>
                                                                            <input type="text" name="MD_TENANT_NAME_CHAR" id="MD_TENANT_NAME_CHAR" value="<?php echo $data->MD_TENANT_NAME_CHAR; ?>" class="form-control" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="form-group">
                                                                            <label>Remaining Amount</label>
                                                                            <input type="text" name="REMAINING_AMOUNT" id="REMAINING_AMOUNT" class="form-control" value="<?php echo number_format($data->SISA_BAYAR, 0, ',', '.'); ?>" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="form-group">
                                                                            <label>No. Credit PPh</label>
                                                                            <input type="text" name="INVOICE_TRANS_BP_NOCHAR" id="INVOICE_TRANS_BP_NOCHAR" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="form-group">
                                                                            <label>Credit PPh Date</label>
                                                                            <input type="date" class="form-control" name="INVOICE_TRANS_BP_DATE" placeholder="Start date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="form-group">
                                                                            <label>Amount</label>
                                                                            <input type="number" class="form-control" name="AMOUNT" placeholder="Input Amount" required />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                <input type="submit" value="Process" class="btn btn-success" name="viewReport" id="viewReport">
                                                            </div>
                                                        </form>
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
