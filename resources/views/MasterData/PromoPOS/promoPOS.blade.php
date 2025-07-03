<?php
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\drilldown\LegacyDrillDown;
    use \koolreport\drilldown\DrillDown;
    use \koolreport\widgets\google\LineChart;
    use \koolreport\barcode\QRCode;
?>

<style>
    td {
        white-space: nowrap;
    }
</style>

@extends('layouts.mainLayouts')

@section('navbar_header')
    List Promo POS - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    List Promo POS
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div style="padding-left: 5px;">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('warning'))
                            <div class="alert alert-warning alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('info'))
                            <div class="alert alert-info alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('info') }}</strong>
                            </div>
                        @endif
                    </div>

                    <form>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <a class="form-control btn btn-info" href="{{ route('add_new_promo_pos') }}">
                                        Add New Promo POS
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">                            
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Category</th>
                                        <th>Product Name</th>
                                        <th>Description</th>
                                        <th>Free Qty</th>
                                        <th>Discount (%)</th>
                                        <th>Discount Nominal</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Payment Method</th>
                                        <th>Minimal Qty</th>
                                        <th>Minimal Payment</th>
                                        <th>Maximal Transaction Number</th>
                                        <th>Request By</th>
                                        <th>Request At</th>
                                        <th>Status</th>
                                        <th>Approve SMM</th>
                                        <th>Approve GM</th>
                                        <th>Edit</th>
                                        <th>Cancel</th>
                                        <th>Terminate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($dataPromoPOS as $data)
                                    <tr>
                                        <td style="text-align: center;">{{ $i }}</td>
                                        <td style="text-align: left;">{{ $data->MD_PRODUCT_POS_CATEGORY_DESC_CHAR }}</td>
                                        <td style="text-align: left;">{{ $data->MD_PRODUCT_POS_DESC_CHAR }}</td>
                                        <td style="text-align: left;">{{ $data->DESC_CHAR }}</td>
                                        <td style="text-align: center;">{{ $data->QTY_FREE_INT }}</td>
                                        <td style="text-align: center;">{{ (float) $data->DISCOUNT_PERCENT_FLOAT }}%</td>
                                        <td style="text-align: center;">{{number_format($data->DISCOUNT_NOMINAL_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{ date('Y-m-d H:i', strtotime($data->START_PROMO_DATE)) }}</td>
                                        <td style="text-align: center;">{{ date('Y-m-d H:i', strtotime($data->END_PROMO_DATE)) }}</td>
                                        <td style="text-align: center;">{{ $data->PAYMENT_METHOD_DESC_CHAR }}</td>
                                        <td style="text-align: center;">{{ $data->MIN_QTY }}</td>
                                        <td style="text-align: center;">{{number_format($data->MIN_PAYMENT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{ $data->MAX_TRX_NUMBER }}</td>
                                        <td style="text-align: center;">{{ $data->created_by }}</td>
                                        <td style="text-align: center;">{{ date('Y-m-d H:i:s', strtotime($data->created_at)) }}</td>
                                        <td style="text-align: center;">{{ $data->DESC_CHAR_STATUS }}</td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            @if($data->STATUS == 1 && (session('level') == '99' || session('level') == '11'))
                                            <a href="javascript:void(0)" onclick="swalApprSMMData({{$data->PROMO_POS_ID_INT}})" class="btn btn-primary">
                                            @elseif($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 2 && (session('level') == '99' || session('level') == '11'))
                                            <a href="javascript:void(0)" onclick="swalUnapprSMMData({{$data->PROMO_POS_ID_INT}})" class="btn btn-danger">
                                            @else
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            @endif
                                                @if($data->STATUS <= 1)
                                                <i>Approve</i>
                                                @else
                                                <i>Unapprove</i>
                                                @endif
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            @if($data->STATUS == 2 && (session('level') == '99' || session('level') == '9'))
                                            <a href="javascript:void(0)" onclick="swalApprGMData({{$data->PROMO_POS_ID_INT}})" class="btn btn-primary">
                                            @elseif($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 3 && (session('level') == '99' || session('level') == '9'))
                                            <a href="javascript:void(0)" onclick="swalUnapprGMData({{$data->PROMO_POS_ID_INT}})" class="btn btn-danger">
                                            @else
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            @endif
                                                @if($data->STATUS <= 2)
                                                <i>Approve</i>
                                                @else
                                                <i>Unapprove</i>
                                                @endif
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            @if($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 1)
                                            <a href="{{ URL('/edit_view_promo_pos/' . base64_encode($data->PROMO_POS_ID_INT)) }}" class="btn btn-primary">
                                            @else
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            @endif
                                                <i>
                                                    Edit
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            @if($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 1)
                                            <a href="javascript:void(0)" onclick="swalDeleteData({{$data->PROMO_POS_ID_INT}})" class="btn btn-danger">
                                            @else
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            @endif
                                                <i>
                                                    Cancel
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            @if($data->STATUS == 3 && date('Y-m-d H:i:s') <= date('Y-m-d H:i:s', strtotime($data->END_PROMO_DATE)) && (session('level') == '99' || session('level') == '9' || session('level') == '11'))
                                            <a href="javascript:void(0)" onclick="swalTerminateData({{$data->PROMO_POS_ID_INT}})" class="btn btn-danger">
                                            @else
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            @endif
                                                <i>
                                                    Terminate
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#example1").DataTable({
            // "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    function swalDeleteData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Cancel</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/delete_promo_pos/" + param1;
            }
        });
    }

    function swalTerminateData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Terminate</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/terminate_promo_pos/" + param1;
            }
        });
    }

    function swalApprSMMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Approve</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/appr_smm_promo_pos/" + param1;
            }
        });
    }

    function swalApprGMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Approve</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/appr_gm_promo_pos/" + param1;
            }
        });
    }

    function swalUnapprSMMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Unapprove</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/unappr_smm_promo_pos/" + param1;
            }
        });
    }

    function swalUnapprGMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Unapprove</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/unappr_gm_promo_pos/" + param1;
            }
        });
    }
</script>
@endsection