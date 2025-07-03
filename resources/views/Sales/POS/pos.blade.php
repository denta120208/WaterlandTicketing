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
    List POS - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    List POS
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
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <a class="form-control btn btn-info" href="{{ route('pos') }}">
                                        POS
                                    </a>
                                </div>
                            </div>
                        </div>

                        <br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Current POS</b></h4>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Customer</th>
                                        <th>Qty</th>
                                        <th>Promo</th>
                                        <th>Qty Free</th>
                                        <th>Discount (%)</th>
                                        <th>Discount Nominal</th>
                                        <th>Price</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Cashier</th>
                                        <th>Transaction Date</th>
                                        <th>Print Receipt</th>
                                        <th>Cancel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransPOS as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_POS_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_POS_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->CUSTOMER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->PROMO_DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_FREE_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{(float) $data->DISCOUNT_PERCENT_FLOAT}}%</td>
                                        <td style="text-align: center;">{{number_format($data->DISCOUNT_NOMINAL_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_PAID_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_CHANGE_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->CASHIER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                        <td style="text-align: center;">
                                            <a href="{{ URL('/print_pos/' . $data->TRANS_POS_ID_INT) }}" class="btn btn-primary" onclick="window.open(this.href, '_blank', 'noreferrer'); return false">
                                                <i>
                                                    Print
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0)" onclick="swalCancelData({{$data->TRANS_POS_ID_INT}})" class="btn btn-danger">
                                                <i>
                                                    Cancel
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <br /><br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Current POS Details</b></h4>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransPOSDetails as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_POS_DETAIL_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_POS_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->MD_PRODUCT_POS_CATEGORY_DESC_CHAR}} - {{$data->DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->HARGA_SATUAN_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                    </tr>
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
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        $("#example2").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
    });

    function swalCancelData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Cancel</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/cancel_pos/" + param1;
            }
        });
    }
</script>
@endsection