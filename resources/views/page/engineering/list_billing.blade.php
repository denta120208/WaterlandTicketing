@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Util Billing - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Util Billing
@endsection

@section('content')

<script>
    $(document).ready(function()    {
        $('#billingpayment_report_summary').DataTable({
            order: [[0, 'asc']],
            //dom: 'Bfrtip',
            scrollX : true,
            scrollY : true,
            pageLength : 5,
            order: [],
            // order : [],
            pageLength : 25,
            // //scrollX: true,
            //scrollY:"500px",
            // scrollCollapse: true,
            paging: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    //footer: true,
                    title: '<?php echo "Data Utils Billing Appr "; ?>',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {              
                                // return column === 7 ? data.replace(/[$,.]/g, '') : data;
                                if(column === 7 || column === 8 || column === 9 || column === 10 || column === 11 || column === 12 || column === 13 || column === 14 || column === 15) {
                                    return data.replace(/[$,.]/g, '');
                                }
                                else if(column === 15) {
                                    return '';
                                }
                                else {
                                    return data;
                                }
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    // footer: true,
                    title: '<?php echo "Data Utils  Billing Appr "; ?>'
                }
            ]
        });
    });
</script>

<script>
$(document).ready(function()    {
    $('#engineering_meter').DataTable({
        order: [[0, 'asc']],
        pageLength : 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                //footer: true,
                title: '<?php echo "Data Utils Billing "; ?>',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {              
                            // return column === 7 ? data.replace(/[$,.]/g, '') : data;
                            if(column === 7 || column === 8 || column === 9 || column === 10 || column === 11 || column === 12 || column === 13 || column === 14 || column === 15) {
                                return data.replace(/[$,.]/g, '');
                            }
                            else if(column === 15) {
                                return '';
                            }
                            else {
                                return data;
                            }
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                // footer: true,
                title: '<?php echo "Data Utils Billing "; ?>'
            }
        ]
    });
});
</script>

<script type="text/javascript">

    function delItem(id){
        $.ajax({
            type: "post",
            url: "{{ route('engineering.deleteutilbilling') }}",
            data: {ID_BILLING:id, _token: "{{ csrf_token() }}"},
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
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover dataTable  display compact" id="billingpayment_report_summary" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tenant</th>
                                        <th>Formula</th>
                                        <th>Meter</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Start LWBP</th>
                                        <th>End LWBP</th>
                                        <th>LWBP Diff</th>
                                        <th>Start WBP</th>
                                        <th>End WBP</th>
                                        <th>WBP Diff</th>
                                        <th>Billboard Hours</th>
                                        <th>Billboard Days</th>
                                        <th>Util Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;?>
                                    @foreach($utilsBilling as $data)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                            <td>{{$data->NAME_U_FORMULA}}</td>
                                            <td>{{$data->UTILS_METER_CHAR}}</td>
                                            <td>{{$data->UTILS_CATEGORY_NAME}}</td>
                                            <td>{{$data->UTILS_TYPE_NAME}}</td>
                                            <td>{{$data->BILLING_DATE}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_START_LWBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_END_LWBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_LWBP_DIFF,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_START_WBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_END_WBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_WBP_DIFF,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_BILLBOARD_HOUR,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_BILLBOARD_DAY,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->UTIL_AMOUNT,0,',','.')}}</td>
                                            <td>{{$data->BILLING_STATUS}}</td>
                                            @if($data->BILLING_STATUS == 'REQUEST')
                                                <td style="text-align:center;">
                                                    <i class='fa fa-trash' title='Delete Data' onclick='delItem(<?php echo $data->ID_BILLING; ?>);'></i>
                                                </td>
                                            @else
                                                <td style="text-align:center;">
                                                    -
                                                </td>
                                            @endif
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
@endsection
