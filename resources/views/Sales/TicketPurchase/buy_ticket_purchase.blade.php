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
    Form Ticket Purchase - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Ticket Purchase
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    @if(session()->has('urlRedirect'))
                        <script>
                            window.open('{{session()->get('urlRedirect')}}', "_blank", 'noreferrer').print();
                        </script>

                        @if(session()->has('urlRedirectReceipt'))
                        <script>
                            window.open('{{session()->get('urlRedirectReceipt')}}', "_blank", 'noreferrer').print();
                        </script>
                        @endif
                        
                        @if(session()->has('change'))
                        <script>
                            $(document).ready(function() {
                                Swal.fire({
                                    title: 'Change',
                                    allowOutsideClick: false,
                                    html: "<h4><b>Rp. {{session()->get('change')}}</b></h4>"
                                });
                            });
                        </script>
                        @endif
                    @endif

                    <form action="{{ url('save_ticket_purchase') }}" onsubmit="return validateAmountNotSameForm()" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6" style="display: none;">
                                <div class="form-group">
                                    <label>Nama Customer <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CUSTOMER_NAME" class="form-control" id="TXT_CUSTOMER_NAME" placeholder="Enter Nama Customer">
                                    <input type="hidden" name="HOLIDAY_ID_INT" class="form-control" id="HOLIDAY_ID_INT" value="{{ $HOLIDAY_ID_INT }}" readonly>
                                    <input type="hidden" name="IS_HOLIDAY" class="form-control" id="IS_HOLIDAY" value="{{ $IS_HOLIDAY }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6" style="display: none;">
                                <div class="form-group">
                                    <label>Identity Customer</label>
                                    <input type="text" name="TXT_ID_CUSTOMER" class="form-control" id="TXT_ID_CUSTOMER" placeholder="Enter KTP / Kartu Pelajar / Passport">
                                </div>
                            </div>
                            <div class="col-sm-6" style="display: none;">
                                <div class="form-group">
                                    <label>Email address</label>
                                    <input type="email" name="TXT_EMAIL" class="form-control" id="TXT_EMAIL" placeholder="Enter email">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Cashier Name <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CASHIER_NAME" class="form-control" id="TXT_CASHIER_NAME" placeholder="Enter Cashier Name" value="{{ $cashierName }}" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Ticket Type <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_TYPE" name="DDL_TICKET_TYPE" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlDataTicketGroup as $data)
                                        <option value="{{ $data->MD_GROUP_TICKET_ID_INT }}">
                                            {{ $data->MD_GROUP_TICKET_DESC }} ({{ $data->MD_GROUP_TICKET_PERSON }} Orang)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Ticket Price <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_PRICE" name="DDL_TICKET_PRICE" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Price Amount Ticket <span style="color: red;">*</span></label>
                                    <input type="text" value="0" name="TXT_PRICE_TICKET" class="form-control" id="TXT_PRICE_TICKET" placeholder="Enter Price Ticket" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Qty Ticket <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_QTY_TICKET" class="form-control" id="TXT_QTY_TICKET" placeholder="Enter Qty Ticket" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Promo (Optional)</label>
                                    <select id="DDL_PROMO" name="DDL_PROMO" class="form-control select2" style="width: 100%;">
                                        <option value="">--- Not Selected ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Free Ticket</label>
                                    <input type="text" name="TXT_FREE_TICKET" class="form-control" id="TXT_FREE_TICKET" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Discount (%)</label>
                                    <input type="text" name="TXT_DISCOUNT_PERCENT" class="form-control" id="TXT_DISCOUNT_PERCENT" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Discount Nominal</label>
                                    <input type="text" name="TXT_DISCOUNT" class="form-control" id="TXT_DISCOUNT" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Total <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_PRICE" class="form-control" id="TXT_TOTAL_PRICE" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Total After Discounts <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_PRICE_AFTER_DISCOUNT" class="form-control" id="TXT_TOTAL_PRICE_AFTER_DISCOUNT" value="0" readonly required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
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
                            <div class="col-sm-6" style="display: none;">
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
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Card / Voucher Number 1</label>
                                    <input type="text" name="TXT_NUMBER1" class="form-control" id="TXT_NUMBER1" placeholder="Kosongkan Jika Tidak Ada">
                                </div>
                            </div>
                            <div class="col-sm-6" style="display: none;">
                                <div class="form-group">
                                    <label>Card / Voucher Number 2 (Optional)</label>
                                    <input type="text" name="TXT_NUMBER2" class="form-control" id="TXT_NUMBER2" placeholder="Kosongkan Jika Tidak Ada">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Payment Amount 1 <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_PAYMENT_AMOUNT1" class="form-control" id="TXT_PAYMENT_AMOUNT1" placeholder="Enter Payment Method Amount 1" required>
                                </div>
                            </div>
                            <div class="col-sm-6" style="display: none;">
                                <div class="form-group">
                                    <label>Payment Amount 2 (Optional)</label>
                                    <input type="number" name="TXT_PAYMENT_AMOUNT2" class="form-control" id="TXT_PAYMENT_AMOUNT2" placeholder="Kosongkan Jika Tidak Ada">
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_PAY" name="BTN_PAY" class="btn btn-primary float-right">Save</button>
                        <a href="{{ route('ticket_purchase') }}" class="btn btn-danger float-right" style="margin-right: 10px;">Back to List</a>
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

        $('select[id="DDL_PROMO"]').on('change', function() {
            var subID = $(this).val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));
            if(!isEmpty(subID)) {
                $.ajax({
                    url: '/get_promo_by_id_ticket_purchase/' + subID,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#TXT_DISCOUNT').val(numberWithCommas(parseFloat(data.dataPromoDiscountNominal)));

                        // Jika Minimal Qty Lebih Dari Satu Maka Mulai Hitung Ticket Free (Jika Ada) Menggunakan Kelipatan Dari Minimal Qty
                        var minQty = parseFloat(data.dataPromoMinQty);
                        if(minQty > 0) {
                            var QtyTicketCurr = parseInt($('#TXT_QTY_TICKET').val());
                            var perkalianKelipatan = parseInt(QtyTicketCurr / minQty);
                            var dataTicketFree = parseFloat(data.dataPromoTicketFree);
                            var ticketFree = dataTicketFree * perkalianKelipatan;
                            $('#TXT_FREE_TICKET').val(ticketFree);
                        }
                        else {
                            $('#TXT_FREE_TICKET').val(data.dataPromoTicketFree);
                        }

                        $('#TXT_DISCOUNT_PERCENT').val(parseFloat(data.dataPromoDiscountPercent));
                        var totalAfterDiscount = totalPrice - (parseFloat(data.dataPromoDiscountNominal) + (totalPrice * (parseFloat(data.dataPromoDiscountPercent) / 100)));
                        $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val(numberWithCommas(Math.round(totalAfterDiscount)));
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('#TXT_DISCOUNT').val(0);
                $('#TXT_FREE_TICKET').val(0);
                $('#TXT_DISCOUNT_PERCENT').val(0);
                $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val(numberWithCommas(totalPrice));
            }
        });

        $('select[name="DDL_PAYMENT_METHOD2"]').on('change', function() {
            var subID = $(this).val();
            var paymentMethod1 = $('select[name="DDL_PAYMENT_METHOD1"]').val() === "" ? null : $('select[name="DDL_PAYMENT_METHOD1"]').val();
            var ticketType = $('select[name="DDL_TICKET_TYPE"]').val() === "" ? null : $('select[name="DDL_TICKET_TYPE"]').val();
            var ticketPrice = $('select[name="DDL_TICKET_PRICE"]').val() === "" ? null : $('select[name="DDL_TICKET_PRICE"]').val();
            var qty = $('#TXT_QTY_TICKET').val() === "" ? 0 : $('#TXT_QTY_TICKET').val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));

            if(subID) {
                $.ajax({
                    url: '/get_promo_ticket_purchase/'+paymentMethod1+'/'+subID+'/'+ticketType+'/'+ticketPrice+'/'+qty+'/'+totalPrice,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.dataPromo, function(index, item) {
                            $('select[name="DDL_PROMO"]').append("<option value='" + item.PROMO_TICKET_PURCHASE_ID_INT + "'>" + item.DESC_CHAR + "</option>");
                        });
                        $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
            }
        });

        $('select[name="DDL_PAYMENT_METHOD1"]').on('change', function() {
            var subID = $(this).val();
            var paymentMethod2 = $('select[name="DDL_PAYMENT_METHOD2"]').val() === "" ? null : $('select[name="DDL_PAYMENT_METHOD2"]').val();
            var ticketType = $('select[name="DDL_TICKET_TYPE"]').val() === "" ? null : $('select[name="DDL_TICKET_TYPE"]').val();
            var ticketPrice = $('select[name="DDL_TICKET_PRICE"]').val() === "" ? null : $('select[name="DDL_TICKET_PRICE"]').val();
            var qty = $('#TXT_QTY_TICKET').val() === "" ? 0 : $('#TXT_QTY_TICKET').val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));

            if(subID) {
                $.ajax({
                    url: '/get_promo_ticket_purchase/'+subID+'/'+paymentMethod2+'/'+ticketType+'/'+ticketPrice+'/'+qty+'/'+totalPrice,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.dataPromo, function(index, item) {
                            $('select[name="DDL_PROMO"]').append("<option value='" + item.PROMO_TICKET_PURCHASE_ID_INT + "'>" + item.DESC_CHAR + "</option>");
                        });
                        $('select[name="DDL_PROMO"]').trigger("change");
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                $('select[name="DDL_PROMO"]').trigger("change");
            }
        });

        $("#TXT_QTY_TICKET").keyup(function() {
            var hargaTicket = parseInt($('#TXT_PRICE_TICKET').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PRICE_TICKET').val().toString().replace(/\./g, ""));

            var hargaFinal = ($("#TXT_QTY_TICKET").val() === "" ? 0 : $("#TXT_QTY_TICKET").val()) * hargaTicket;
            $("#TXT_TOTAL_PRICE").val(numberWithCommas(hargaFinal));
            $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
            $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
            $('select[name="DDL_PROMO"]').trigger("change");
        });

        $('select[name="DDL_TICKET_PRICE"]').on('change', function() {
            var subID = $(this).val();
            if(subID) {
                $.ajax({
                    url: '/get_ticket_price_by_id_price/' + subID,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#TXT_PRICE_TICKET').val(numberWithCommas(data.dataTicketPrice.MD_PRICE_TICKET_NUM));
                        $("#TXT_QTY_TICKET").trigger("keyup");
                        $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                        $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
                        $('select[name="DDL_PROMO"]').trigger("change");
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('#TXT_PRICE_TICKET').val(0);
                $("#TXT_QTY_TICKET").trigger("keyup");
                $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
                $('select[name="DDL_PROMO"]').trigger("change");
            }
        });

        $('select[name="DDL_TICKET_TYPE"]').on('change', function() {
            var subID = $(this).val();
            if(subID) {
                $.ajax({
                    url: '/get_ticket_price_by_id_group/' + subID,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        const dateNow = new Date().getDay();
                        $('select[name="DDL_TICKET_PRICE"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.ddlDataTicketPrice, function(index, item)
                        {
                            const isHoliday = parseInt($('#IS_HOLIDAY').val());
                            const dayStrNow = item.MD_PRICE_TICKET_DESC.toUpperCase();
                            if(isHoliday === 1) { // Jika Hari Ini Adalah Holiday
                                if(dayStrNow.includes('WEEKDAY')) { // Jika hari weekday tapi ini holiday maka tombol disabled
                                    $('select[name="DDL_TICKET_PRICE"]').append("<option value='" + item.MD_PRICE_TICKET_ID_INT + "' disabled>" + item.MD_PRICE_TICKET_DESC + " (Disabled)</option>");
                                }
                                else { // Jika hari bukan weekday tapi hari ini holiday maka tombol enabled
                                    $('select[name="DDL_TICKET_PRICE"]').append("<option value='" + item.MD_PRICE_TICKET_ID_INT + "'>" + item.MD_PRICE_TICKET_DESC + "</option>");
                                }
                            }
                            else { // Jika Hari Ini Bukan Hari Holiday
                                if(dateNow == 0 || dateNow == 6) { // Hari Weekend (Sabtu Minggu)
                                    if(dayStrNow.includes('WEEKDAY')) { // Jika hari weekend tapi ini weekday maka tombol disabled
                                        $('select[name="DDL_TICKET_PRICE"]').append("<option value='" + item.MD_PRICE_TICKET_ID_INT + "' disabled>" + item.MD_PRICE_TICKET_DESC + " (Disabled)</option>");
                                    }
                                    else { // Jika hari weekend tapi bukan weekday maka tombol enabled
                                        $('select[name="DDL_TICKET_PRICE"]').append("<option value='" + item.MD_PRICE_TICKET_ID_INT + "'>" + item.MD_PRICE_TICKET_DESC + "</option>");
                                    }
                                }
                                else {
                                    if(dayStrNow.includes('WEEKEND')) { // Jika hari weekday tapi ini weekend maka tombol disabled
                                        $('select[name="DDL_TICKET_PRICE"]').append("<option value='" + item.MD_PRICE_TICKET_ID_INT + "' disabled>" + item.MD_PRICE_TICKET_DESC + " (Disabled)</option>");
                                    }
                                    else { // Jika hari weekday tapi bukan weekend maka tombol enabled
                                        $('select[name="DDL_TICKET_PRICE"]').append("<option value='" + item.MD_PRICE_TICKET_ID_INT + "'>" + item.MD_PRICE_TICKET_DESC + "</option>");
                                    }
                                }
                            }
                        });
                        $('select[name="DDL_TICKET_PRICE"]').trigger("change");
                        $("#TXT_QTY_TICKET").trigger("keyup");
                        $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                        $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
                        $('select[name="DDL_PROMO"]').trigger("change");

                        // Jika Jumlah Orang Dari Ticket Group Lebih Dari 1 Maka Qty Otomatis Keisi
                        if(parseInt(data.dataTicketGroup.MD_GROUP_TICKET_PERSON) > 1) {
                            $("#TXT_QTY_TICKET").val(parseInt(data.dataTicketGroup.MD_GROUP_TICKET_PERSON));
                            document.getElementById('TXT_QTY_TICKET').readOnly = true;
                        }
                        else {
                            $("#TXT_QTY_TICKET").val(null);
                            document.getElementById('TXT_QTY_TICKET').readOnly = false;
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('select[name="DDL_TICKET_PRICE"]').empty().append('<option value="">--- Not Selected ---</option>');
                $('select[name="DDL_TICKET_PRICE"]').trigger("change");
                $("#TXT_QTY_TICKET").trigger("keyup");
                $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
                $('select[name="DDL_PROMO"]').trigger("change");

                $("#TXT_QTY_TICKET").val(null);
                document.getElementById('TXT_QTY_TICKET').readOnly = false;
            }
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });

    function validateAmountNotSameForm() {
        var totalPriceAfterDiscount = parseInt($('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val().toString().replace(/\./g, ""));
        var paymentAmount1 = parseInt($('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, ""));
        var paymentAmount2 = parseInt($('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, ""));
        if(totalPriceAfterDiscount <= (paymentAmount1 + paymentAmount2)) {
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