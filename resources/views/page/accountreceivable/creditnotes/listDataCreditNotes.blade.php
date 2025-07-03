@extends('layouts.mainLayouts')

@section('navbar_header')
    Form List Credit Notes - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form List Credit Notes
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
           scrollX : "1500px",
           pageLength : 25,
           dom: 'Bfrtip',
           buttons: [
               {
                   extend: 'excelHtml5',
                   title: '<?php echo "List Data Credit Notes "; ?>'
               },
               {
                   extend: 'pdfHtml5',
                   footer: true,
                   title: '<?php echo "List Data Credit Notes "; ?>'
               }
           ]
       });
    });
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-lg-3">
                            <a class="btn btn-success" href="{!! URL::route('creditnotes.viewadddatacreditnotes') !!}" role="button">
                                Add Data Credit Notes
                            </a>
                        </div>
                    </div>
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Document</th>
                                    <th>Lease</th>
                                    <th>Lot</th>
                                    <th>Tenant</th>
                                    <th>Shop Name</th>
                                    <th>Description</th>
                                    <th>Bill Amount</th>
                                    <th>Status</th>
                                    <th>View/Edit</th>
                                    <th>Cancel</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                @foreach($listDataCreditNotes as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$i}}</td>
                                        <td>{{$data->CN_TRANS_NOCHAR}}</td>
                                        <td>{{$data->PSM_TRANS_NOCHAR}}</td>
                                        <td>{{$data->LOT_STOCK_NO}}</td>
                                        <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->CN_TRANS_DESC}}</td>
                                        <td style="text-align: right;">{{number_format($data->CN_TRANS_AMOUNT,0,'','')}}</td>
                                        <td>{{$data->CN_TRANS_STATUS_INT}}</td>
                                        <td class="center">
                                            <a class="btn btn-sm btn-warning" href="{{ URL('/creditnotes/vieweditdatacreditnotes/' . $data->CN_TRANS_ID_INT. '/' . $data->DOC_TYPE) }}">
                                                <i>
                                                    View/Edit
                                                </i>
                                            </a>
                                        </td>
                                        @if($data->CN_TRANS_STATUS_INT == 'REQUEST')
                                            <td class="center">
                                                <a href="#cancelModal{!!$data->CN_TRANS_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                    <i>
                                                        Cancel
                                                    </i>
                                                </a>
                                                <div id="cancelModal{!!$data->CN_TRANS_ID_INT!!}" class="modal fade">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Confirmation</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <p>Are you sure cancel this document ?</p>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                <a href="{{ URL('/creditnotes/canceldatacreditnotes/'. $data->CN_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @else
                                            <td class="center">
                                                <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                    <i>
                                                        Cancel
                                                    </i>
                                                </a>
                                            </td>
                                        @endif
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
