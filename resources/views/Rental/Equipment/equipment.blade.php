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
    List Rental Equipment - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    List Rental Equipment
@endsection

@section('content')
<div class="modal fade" id="modal-retur">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('retur_rental_equipment') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Retur Equipment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="TXT_ID" class="form-control" id="TXT_ID" placeholder="Enter ID" readonly required>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Total Deposit</label>
                                <input type="text" name="TXT_DEPOSIT" class="form-control" id="TXT_DEPOSIT" placeholder="Enter Total Deposit" value="0" readonly required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Total Refund <span style="color: red;">*</span></label>
                                <input type="number" name="TXT_REFUND" class="form-control" id="TXT_REFUND" placeholder="Enter Total Refund" required>
                                <span style="color: red;"><b>* Isi 0 Jika Deposit Tidak Dikembalikan</b></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Refund Description</label>
                                <input type="text" name="TXT_REFUND_DESC" class="form-control" id="TXT_REFUND_DESC" placeholder="Enter Refund Description">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer right-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Retur</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <a class="form-control btn btn-info" href="{{ route('rental_equipment') }}">
                                        Rental Equipment
                                    </a>
                                </div>
                            </div>
                        </div>

                        <br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Current Rental</b></h4>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Customer</th>
                                        <th>Telp</th>
                                        <th>Qty</th>
                                        <th>Promo</th>
                                        <th>Qty Free</th>
                                        <th>Discount (%)</th>
                                        <th>Discount Nominal</th>
                                        <th>Price</th>
                                        <th>Deposit</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Cashier</th>
                                        <th>Transaction Date</th>
                                        <th>Print Receipt</th>
                                        <th>Retur</th>
                                        <th>Cancel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransEquipmentRent as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_EQUIPMENT_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_EQUIPMENT_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->CUSTOMER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->NO_TELP_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->PROMO_DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_FREE_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{(float) $data->DISCOUNT_PERCENT_FLOAT}}%</td>
                                        <td style="text-align: center;">{{number_format($data->DISCOUNT_NOMINAL_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->DEPOSIT_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_PAID_FLOAT + $data->DEPOSIT_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_CHANGE_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->CASHIER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                        <td style="text-align: center;">
                                            <a href="{{ URL('/print_rental_equipment/' . $data->TRANS_RENTAL_EQUIPMENT_ID_INT) }}" class="btn btn-primary" onclick="window.open(this.href, '_blank', 'noreferrer'); return false">
                                                <i>
                                                    Print
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0)" onclick="showModalRetur({{ $data->TRANS_RENTAL_EQUIPMENT_ID_INT }}, '{{ number_format($data->DEPOSIT_FLOAT, 0, ',', '.') }}')" class="btn btn-success">
                                                <i>
                                                    Retur
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0)" onclick="swalCancelData({{$data->TRANS_RENTAL_EQUIPMENT_ID_INT}})" class="btn btn-danger">
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
                            <h4><b>Current Rental Details</b></h4>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransEquipmentRentDetails as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_EQUIPMENT_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->MD_EQUIPMENT_CATEGORY_DESC_CHAR}} - {{$data->DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <br /><br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Rental Retur (Today)</b></h4>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Customer</th>
                                        <th>Telp</th>
                                        <th>Qty</th>
                                        <th>Promo</th>
                                        <th>Qty Free</th>
                                        <th>Discount (%)</th>
                                        <th>Discount Nominal</th>
                                        <th>Price</th>
                                        <th>Deposit</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Refund</th>
                                        <th>Refund Desc</th>
                                        <th>Refund Date</th>
                                        <th>Cashier</th>
                                        <th>Transaction Date</th>
                                        <th>Retur By</th>
                                        <th>Retur Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransEquipmentRetur as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_EQUIPMENT_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_EQUIPMENT_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->CUSTOMER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->NO_TELP_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->PROMO_DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_FREE_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{(float) $data->DISCOUNT_PERCENT_FLOAT}}%</td>
                                        <td style="text-align: center;">{{number_format($data->DISCOUNT_NOMINAL_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->DEPOSIT_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_PAID_FLOAT + $data->DEPOSIT_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_CHANGE_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->REFUND_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->REFUND_DESC_CHAR == NULL ? "-" : $data->REFUND_DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d', strtotime($data->REFUND_DATE))}}</td>
                                        <td style="text-align: center;">{{$data->CASHIER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                        <td style="text-align: center;">{{$data->RETUR_BY}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->RETUR_AT))}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <br /><br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Rental Retur Details (Today)</b></h4>
                            <table id="example4" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransEquipmentReturDetails as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_EQUIPMENT_DETAIL_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_EQUIPMENT_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->MD_EQUIPMENT_CATEGORY_DESC_CHAR}} - {{$data->DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->HARGA_FLOAT, 0, ",", ".")}}</td>
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

        $("#example3").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');

        $("#example4").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
    });

    function showModalRetur(param1, param2) {
        $('#TXT_ID').val(param1);
        $('#TXT_DEPOSIT').val(param2);
        $('#modal-retur').modal('show');
    }

    function swalReturData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Retur</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/retur_rental_equipment/" + param1;
            }
        });
    }

    function swalCancelData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Cancel</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/cancel_rental_equipment/" + param1;
            }
        });
    }
</script>
@endsection