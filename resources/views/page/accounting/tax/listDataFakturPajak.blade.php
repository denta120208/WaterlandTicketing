@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Tax Invoice - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Tax Invoice
@endsection

@section('content')
<script>
    $(function(){
        var table = $('#tax_year_table').DataTable({
            order:[]
        });

        $('#tax_year_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('year_period').value = checkEmptyStringValidation(data[0]);
            $('#taxYearModal').modal('hide');
        });

        $('#year_period').on('click',function(){
            $('#taxYearModal').modal('show');
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
    $('#tax_invoice_table').DataTable({
        pageLength : 25,
        order: [],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: '<?php echo "List Tax Invoice ".$dataProject['PROJECT_CODE']; ?>'
            },
            {
                extend: 'pdfHtml5',
                title: '<?php echo "List Tax Invoice ".$dataProject['PROJECT_CODE']; ?>'
            },
            {
                extend: 'print',
                title: '<?php echo "List Tax Invoice ".$dataProject['PROJECT_CODE']; ?>'
            }
        ]
    });
});
</script>
<script type="text/javascript">

    function delItem(id){
        $.ajax({
            type: "post",
            url: "{{ route('accounting.tax.deletetaxinvoice') }}",
            data: {TAX_MD_FP_ID_INT:id, _token: "{{ csrf_token() }}"},
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

    $(function(){
        $('#update').on('click',function(){
            var year_period = $("#year_period").val();
            var number_code = $("#number_code").val();
            var fp_start_number = $("#fp_start_number").val();
            var fp_end_number = $("#fp_end_number").val();

            if (year_period === '' || number_code === '' || fp_start_number === '' || fp_end_number === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "{{ route('accounting.tax.generatetaxinvoice') }}",
                    data: {
                        TAX_MD_FP_YEAR_CHAR:year_period,
                        TAX_MD_FP_CODE_CHAR:number_code,
                        FP_START_NUMBER:fp_start_number,
                        FP_END_NUMBER:fp_end_number,
                        _token: "{{ csrf_token() }}"},
                    dataType: 'json',
                    cache: false,
                    beforeSend:function(){
                        setTimeout(function(){
                            $(".loader-image").show();
                        }, 1);
                        // please note i have added a delay of 1 millisecond with js timeout function which runs almost same as code with no delay.
                    },
                    success: function (response) {
                        if(response['Success']){
                            document.location.reload(true);
                        }else{
                            alert(response['Error']);
                        }
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
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Tax Year*</label>
                                <input type="text" name="TAX_MD_FP_YEAR_CHAR" id="year_period" class="form-control" placeholder="Year" readonly="yes">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Number Code*</label>
                                <input type="text" name="TAX_MD_FP_CODE_CHAR" id="number_code" class="form-control" placeholder="Number Code">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Start Number*</label>
                                <input type="number" name="FP_START_NUMBER" id="fp_start_number" class="form-control" placeholder="Start Number">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>End Number*</label>
                                <input type="number" name="FP_END_NUMBER" id="fp_end_number" class="form-control" placeholder="End Number">
                                <br>
                                <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="update" style="float: right;">
                                    Generate Number
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="tax_invoice_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th style="width: 10px;">No.</th>
                                    <th>Tax Year</th>
                                    <th>Tax Code</th>
                                    <th>Tax Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;?>
                                @foreach($dataFakturPajak as $data)
                                    <tr>
                                        <td style="text-align: right">{{$i}}</td>
                                        <td style="text-align: center;">{{ $data->TAX_MD_FP_YEAR_CHAR }}</td>
                                        <td style="text-align: center;">{{ $data->TAX_MD_FP_KODE_CHAR }}</td>
                                        <td style="text-align: right">{{ $data->TAX_MD_FP_NOCHAR }}</td>
                                        @if($data->IS_TAKEN == 0)
                                            <td style="text-align: center;">ACTIVE</td>
                                        @elseif($data->IS_TAKEN == 1)
                                            <td style="text-align: center;">USED</td>
                                        @else
                                            <td style="text-align: center;">NONE</td>
                                        @endif
                                        @if($data->IS_TAKEN == 0)
                                        <td style="text-align:center;">
                                            <i class='fa fa-edit' title='Edit Data' onclick='getItem(<?php echo $data->TAX_MD_FP_ID_INT; ?>);'></i>|
                                            <i class='fa fa-trash' title='Delete Data' onclick='delItem(<?php echo $data->TAX_MD_FP_ID_INT; ?>);'></i>
                                        </td>
                                        @else
                                            <td style="text-align:center;"></td>
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

<div class="col-md-3 col-sm-offset-10">
    <div id="taxYearModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tax Year</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact"
                           id="tax_year_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Tahun</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            for($i = $tahun; $i <= $tahun + 5; $i++)
                            {
                        ?>
                            <tr>
                                <td>{{$i}}</td>
                            </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
