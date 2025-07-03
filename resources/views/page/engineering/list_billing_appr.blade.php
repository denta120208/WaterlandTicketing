@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Util Billing Approval - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Util Billing Approval
@endsection

@section('content')

<script>
$(document).ready(function()    {
    $('#billingpayment_report_summary').DataTable({
        //dom: 'Bfrtip',
        scrollX : "1500px",
        pageLength : 5,
        order: [],
        // order : [],
        // //pageLength : 25,
        // //scrollX: true,
        scrollY:"500px",
        // scrollCollapse: true,
        paging: false,
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
                            if(column === 7 || column === 8 || column === 9 || column === 10 || column === 11 || column === 12 || column === 16) {
                                return data.replace(/[$,.]/g, '');
                            }
                            else if(column === 0 || column === 17) {
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
        var countSelect = parseInt($("#countSelect").val());

        if (ele.checked) {
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'billing[]', value: billid, class: 'form-control',id: billid}))
            tz.appendTo('#temp-form');

            var totalSelect = countSelect + 1;

            document.getElementById("countSelect").value = totalSelect;
        } else {
            var totalSelect = countSelect - 1;

            document.getElementById("countSelect").value = totalSelect;
            document.getElementById(billid).remove();
        }
    }
</script>
<script type="text/javascript">

    function delRequestBilling(id){
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
                    <form method="POST" action="{{ route('engineering.approveutilbilling') }}">
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover dataTable  display compact" id="billingpayment_report_summary" style="padding: 0.5em;width: 100%">
                                    <thead>
                                    <tr>
                                        <th>Check</th>
                                        <th>Tenant</th>
                                        <th>Formula</th>
                                        <th>Meter</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Start LWBP</th>
                                        <th>End LWBP</th>
                                        <th>Start WBP</th>
                                        <th>End WBP</th>
                                        <th>Billboard Hours</th>
                                        <th>Billboard Days</th>
                                        <th>Handling Fee</th>
                                        <th>BPJU</th>
                                        <th>Lost Factor</th>
                                        <th>Util Amount</th>
                                        <th>Delete</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;?>
                                    @foreach($utilsBilling as $data)
                                        <tr>
                                            <td style="text-align: center;"><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->ID_BILLING;  ?>)" value="<?php echo $data->ID_BILLING;  ?>" id="idbilling"></td>
                                            <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                            <td>{{$data->NAME_U_FORMULA}}</td>
                                            <td>{{$data->UTILS_METER_CHAR}}</td>
                                            <td>{{$data->UTILS_CATEGORY_NAME}}</td>
                                            <td>{{$data->UTILS_TYPE_NAME}}</td>
                                            <td>{{$data->BILLING_DATE}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_START_LWBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_END_LWBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_START_WBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_END_WBP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_BILLBOARD_HOUR,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->BILLING_METER_BILLBOARD_DAY,0,',','.')}}</td>
                                            <td>{{$data->IS_HANDLING}}</td>
                                            <td>{{$data->IS_BPJU}}</td>
                                            <td>{{$data->IS_LOST_FACTOR}}</td>
                                            <td style="text-align: right;">{{number_format($data->UTIL_AMOUNT,0,',','.')}}</td>
                                            <td style="text-align:center;">
                                                <i class='fa fa-trash' title='Edit Data' onclick='delRequestBilling(<?php echo $data->ID_BILLING; ?>);'></i>
                                            </td>
                                            <?php $i++;?>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div id="temp-form">
                                    <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                    <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                    <input type="hidden" name="countSelect" value="0" class="form-control" id="countSelect">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <a href="#confModal" class="btn btn-success" data-toggle="modal" style="float: right;">
                                        Approve Billing
                                    </a>
                                    <div id="confModal" class="modal fade">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Confirmation</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure approve this data ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-info" data-dismiss="modal">No</button>
                                                    <input type="submit" class="btn btn-success" value="Yes">
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
</div>
@endsection
