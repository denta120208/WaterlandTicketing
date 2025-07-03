<?php
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\drilldown\LegacyDrillDown;
    use \koolreport\drilldown\DrillDown;
    use \koolreport\widgets\google\LineChart;
    use Illuminate\Support\Str;
?>

@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Rental Locker - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Rental Locker
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('save_rental_locker') }}" onsubmit="return validateAmountNotSameForm()" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Cashier Name <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CASHIER_NAME" class="form-control" id="TXT_CASHIER_NAME" placeholder="Enter Cashier Name" value="{{ $cashierName }}" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Nama Customer <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CUSTOMER_NAME" class="form-control" id="TXT_CUSTOMER_NAME" placeholder="Enter Nama Customer" required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>No. Telp <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_NO_TELP" class="form-control" id="TXT_NO_TELP" placeholder="Enter No. Telp" required>
                                    <label style="color: red;"><small><b>Example : 08878xxxxxxx</b></small></label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Locker <span style="color: red;">*</span></label>
                                    <select id="DDL_LOCKER" name="DDL_LOCKER[]" class="form-control select2" multiple="multiple" data-placeholder="Select Locker" style="width: 100%;" required>                                        
                                        @foreach($ddlDataLocker as $data)
                                        <option value="{{ $data->MD_LOCKER_ID_INT }}" {{ $data->IS_RENT == 1 ? "disabled" : "" }}>
                                            {{ $data->DESC_CHAR }} {{ $data->IS_RENT == 1 ? "(Disabled)" : "" }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Total <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_PRICE" class="form-control" id="TXT_TOTAL_PRICE" placeholder="Enter Total Price" value="0" readonly required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Method 1 <span style="color: red;">*</span></label>
                                    <select id="DDL_PAYMENT_METHOD1" name="DDL_PAYMENT_METHOD1" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlDataPaymentMethod as $data)
                                        <option value="{{ $data->PAYMENT_METHOD_ID_INT }}">
                                            {{ $data->PAYMENT_METHOD_DESC_CHAR }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Method 2 (Optional)</label>
                                    <select id="DDL_PAYMENT_METHOD2" name="DDL_PAYMENT_METHOD2" class="form-control select2" style="width: 100%;">
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlDataPaymentMethod as $data)
                                        <option value="{{ $data->PAYMENT_METHOD_ID_INT }}">
                                            {{ $data->PAYMENT_METHOD_DESC_CHAR }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Card / Voucher Number 1</label>
                                    <input type="text" name="TXT_NUMBER1" class="form-control" id="TXT_NUMBER1" placeholder="Kosongkan Jika Tidak Ada">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Card / Voucher Number 2 (Optional)</label>
                                    <input type="text" name="TXT_NUMBER2" class="form-control" id="TXT_NUMBER2" placeholder="Kosongkan Jika Tidak Ada">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Amount 1 <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_PAYMENT_AMOUNT1" class="form-control" id="TXT_PAYMENT_AMOUNT1" placeholder="Enter Payment Method Amount 1" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Amount 2 (Optional)</label>
                                    <input type="number" name="TXT_PAYMENT_AMOUNT2" class="form-control" id="TXT_PAYMENT_AMOUNT2" placeholder="Kosongkan Jika Tidak Ada">
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_PAY" name="BTN_PAY" class="btn btn-primary float-right">Save</button>
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

        $('select[id="DDL_LOCKER"]').on('change', function() {
            var subID = $(this).val();
            if(!isEmpty(subID)) {
                $.ajax({
                    url: '/get_locker_price_by_id/' + subID,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $("#TXT_TOTAL_PRICE").val(numberWithCommas(parseInt(data.dataLockerPrice)));
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('#TXT_TOTAL_PRICE').val(0);
            }
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });

    function validateAmountNotSameForm() {
        var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));
        var paymentAmount1 = parseInt($('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, ""));
        var paymentAmount2 = parseInt($('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, ""));
        if(totalPrice <= (paymentAmount1 + paymentAmount2)) {
            return true;
        }
        else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Payment amount cannot be less than the bill!'
            });
            return false;
        }
    }

    function isEmpty(str) {
        return (!str || str.length === 0 );
    }
</script>
@endsection