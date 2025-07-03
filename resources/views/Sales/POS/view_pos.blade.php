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
    Form POS - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form POS
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

                    <form action="{{ url('save_pos') }}" onsubmit="return validateAmountNotSameForm()" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Cashier Name <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CASHIER_NAME" class="form-control" id="TXT_CASHIER_NAME" placeholder="Enter Cashier Name" value="{{ $cashierName }}" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nama Customer <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CUSTOMER_NAME" class="form-control" id="TXT_CUSTOMER_NAME" placeholder="Enter Nama Customer" required>
                                </div>
                            </div>
                        </div>
                        
                        <br />
                        <input type="text" name="TXT_DATATABLE" class="form-control" style="display: none;" id="TXT_DATATABLE" readonly>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>Product</label>
                                    <select id="DDL_PRODUCT" name="DDL_PRODUCT" class="form-control select2" style="width: 100%;">
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlDataProductPOS as $data)
                                            <option value="{{ $data->MD_PRODUCT_POS_ID_INT }}">
                                                {{ $data->NAMA_PRODUCT }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Unit Price</label>
                                    <input type="text" value="0" name="TXT_HARGA_SATUAN" class="form-control" id="TXT_HARGA_SATUAN" placeholder="Enter Unit Price" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Qty</label>
                                    <input type="number" name="TXT_QTY" class="form-control" id="TXT_QTY" placeholder="Enter Qty">
                                </div>
                            </div>
                            <div class="col-sm-1" style="padding-top: 32px;">
                                <div class="form-group">
                                    <a href="javascript:void(0)" id="BTN_ADD_NEW" name="BTN_ADD_NEW" class="btn btn-primary">+</a>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                                <h5><b>Cart</b></h5>
                                <table id="example1" name="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Description</th>
                                            <th>Unit Price</th>
                                            <th>Qty</th>
                                            <th>Total Price</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: center;">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br /><br />

                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Qty <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_QTY" class="form-control" id="TXT_TOTAL_QTY" placeholder="Enter Qty" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Total <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_PRICE" class="form-control" id="TXT_TOTAL_PRICE" placeholder="Enter Total Price" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Free Qty</label>
                                    <input type="text" name="TXT_FREE_QTY" class="form-control" id="TXT_FREE_QTY" value="0" readonly required>
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
                                    <label>Total After Discounts <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_PRICE_AFTER_DISCOUNT" class="form-control" id="TXT_TOTAL_PRICE_AFTER_DISCOUNT" value="0" readonly required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
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
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Promo (Optional)</label>
                                    <select id="DDL_PROMO" name="DDL_PROMO" class="form-control select2" style="width: 100%;">
                                        <option value="">--- Not Selected ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
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
                            <div class="col-sm-3">
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
                        <a href="{{ route('listPOS') }}" class="btn btn-danger float-right" style="margin-right: 10px;">Back to List</a>
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

        cartTable();

        $('select[name="DDL_PRODUCT"]').on('change', function() {
            var subID = $(this).val();
            if(subID.toString()) {
                $.ajax({
                    url: '/get_product_pos_price_by_id/' + subID.toString(),
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#TXT_HARGA_SATUAN').val(numberWithCommas(parseInt(data.dataProductPOSPrice)));
                        $("#TXT_QTY").trigger("keyup");
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('#TXT_HARGA_SATUAN').val(0);
                $("#TXT_QTY").trigger("keyup");
            }
        });

        function cartTable() {
            var t = $('#example1').DataTable();
            const idTable = [];

            $('#BTN_ADD_NEW').on('click', function () {
                var productPOSId = $('#DDL_PRODUCT option:selected').val();
                var qty = $("#TXT_QTY").val();
                var priceAmount = $("#TXT_HARGA_SATUAN").val();

                if(productPOSId === "" || qty === "" || parseInt(priceAmount) === 0 || priceAmount === "" || parseInt(qty) === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Field cannot be empty!'
                    });
                }
                else {
                    var productPOSDesc = $('#DDL_PRODUCT option:selected').text().trim();

                    if(idTable.indexOf(productPOSId) !== -1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: productPOSDesc + ' Already Exists!'
                        });
                    }
                    else {
                        var totalPrice = numberWithCommas(parseInt(priceAmount.toString().replace(/\./g, "")) * qty);
                        t.row.add([
                            productPOSId,
                            productPOSDesc,
                            priceAmount,
                            qty,
                            totalPrice,
                            "<a href='javascript:void(0)' id='BTN_REMOVE_ROW' name='BTN_REMOVE_ROW' class='btn btn-danger'>X</a>"
                        ]).draw(false);
                        idTable.push(productPOSId);

                        var txtTotalHarga = parseInt($("#TXT_TOTAL_PRICE").val().toString().replace(/\./g, "")) + parseInt(totalPrice.toString().replace(/\./g, ""));
                        var txtTotalQty = parseInt($("#TXT_TOTAL_QTY").val().toString().replace(/\./g, "")) + parseInt(qty.toString().replace(/\./g, ""));
                        $("#TXT_TOTAL_PRICE").val(numberWithCommas(txtTotalHarga));
                        $("#TXT_TOTAL_QTY").val(txtTotalQty);

                        $('select[name="DDL_PRODUCT"]').val(null);
                        $('#TXT_HARGA_SATUAN').val(0);
                        $('#TXT_QTY').val(null);
                        $('select[name="DDL_PRODUCT"]').trigger("change");
                    }
                }

                $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
                $('select[name="DDL_PROMO"]').trigger("change");
            });

            $('#example1 tbody').on('click', '#BTN_REMOVE_ROW',function() {
                var rowData = t.row($(this).parents('tr')).data();
                var id = rowData[0];

                var txtTotalHarga = parseInt($("#TXT_TOTAL_PRICE").val().toString().replace(/\./g, "")) - parseInt(rowData[4].toString().replace(/\./g, ""));
                var txtTotalQty = parseInt($("#TXT_TOTAL_QTY").val().toString().replace(/\./g, "")) - parseInt(rowData[3].toString().replace(/\./g, ""));
                $("#TXT_TOTAL_PRICE").val(numberWithCommas(txtTotalHarga));
                $("#TXT_TOTAL_QTY").val(numberWithCommas(txtTotalQty));

                t.row($(this).parents('tr')).remove().draw();

                var index = idTable.indexOf(id);
                if (index >= 0) {
                    idTable.splice(index, 1);
                }

                $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
                $('select[name="DDL_PROMO"]').trigger("change");
            });
        }

        $('select[id="DDL_PROMO"]').on('change', function() {
            var subID = $(this).val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));

            var arrTableProductPOS = [];
            var t = $('#example1').DataTable();
            var dataTables = t.rows().data();
            for (var i = 0; i < dataTables.length; i++) {
                // Ambil index 0 sampai 4 dari setiap row
                arrTableProductPOS.push(dataTables[i].slice(0, 5));
            }
            var dataTableProductPOS = arrTableProductPOS.length > 0 ? JSON.stringify(arrTableProductPOS) : null;

            if(!isEmpty(subID)) {
                $.ajax({
                    url: '/get_promo_by_id_product_pos/'+subID+'/'+dataTableProductPOS,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#TXT_DISCOUNT').val(numberWithCommas(parseFloat(data.dataPromoDiscountNominal)));

                        // Jika Minimal Qty Lebih Dari Satu Maka Mulai Hitung Product Free (Jika Ada) Menggunakan Kelipatan Dari Minimal Qty
                        var minQty = parseFloat(data.dataPromoMinQty);
                        if(minQty > 0) {
                            var QtyCurr = parseInt(data.qtyCurr);
                            var perkalianKelipatan = parseInt(QtyCurr / minQty);
                            var dataFree = parseFloat(data.dataPromoPOSFree);
                            var posFree = dataFree * perkalianKelipatan;
                            $('#TXT_FREE_QTY').val(posFree);
                        }
                        else {
                            $('#TXT_FREE_QTY').val(data.dataPromoPOSFree);
                        }

                        $('#TXT_DISCOUNT_PERCENT').val(parseFloat(data.dataPromoDiscountPercent));
                        var totalAfterDiscount = totalPrice - (parseFloat(data.dataPromoDiscountNominal) + (totalPrice * (parseFloat(data.dataPromoDiscountPercent) / 100)));
                        $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val(numberWithCommas(totalAfterDiscount));
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('#TXT_DISCOUNT').val(0);
                $('#TXT_FREE_QTY').val(0);
                $('#TXT_DISCOUNT_PERCENT').val(0);
                $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val(numberWithCommas(totalPrice));
            }
        });

        $('select[name="DDL_PAYMENT_METHOD2"]').on('change', function() {
            var subID = $(this).val();
            var paymentMethod1 = $('select[name="DDL_PAYMENT_METHOD1"]').val() === "" ? null : $('select[name="DDL_PAYMENT_METHOD1"]').val();
            
            var arrTableProductPOS = [];
            var t = $('#example1').DataTable();
            var dataTables = t.rows().data();
            for (var i = 0; i < dataTables.length; i++) {
                // Ambil index 0 sampai 4 dari setiap row
                arrTableProductPOS.push(dataTables[i].slice(0, 5));
            }
            var dataTableProductPOS = arrTableProductPOS.length > 0 ? JSON.stringify(arrTableProductPOS) : null;

            var qty = $('#TXT_TOTAL_QTY').val() === "" ? 0 : $('#TXT_TOTAL_QTY').val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));

            if(subID) {
                $.ajax({
                    url: '/get_promo_pos/'+paymentMethod1+'/'+subID+'/'+dataTableProductPOS+'/'+qty+'/'+totalPrice,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.dataPromo, function(index, item) {
                            $('select[name="DDL_PROMO"]').append("<option value='" + item.PROMO_POS_ID_INT + "'>" + item.DESC_CHAR + "</option>");
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
                $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
            }
        });

        $('select[name="DDL_PAYMENT_METHOD1"]').on('change', function() {
            var subID = $(this).val();
            var paymentMethod2 = $('select[name="DDL_PAYMENT_METHOD2"]').val() === "" ? null : $('select[name="DDL_PAYMENT_METHOD2"]').val();

            var arrTableProductPOS = [];
            var t = $('#example1').DataTable();
            var dataTables = t.rows().data();
            for (var i = 0; i < dataTables.length; i++) {
                // Ambil index 0 sampai 4 dari setiap row
                arrTableProductPOS.push(dataTables[i].slice(0, 5));
            }
            var dataTableProductPOS = arrTableProductPOS.length > 0 ? JSON.stringify(arrTableProductPOS) : null;

            var qty = $('#TXT_TOTAL_QTY').val() === "" ? 0 : $('#TXT_TOTAL_QTY').val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));
            
            if(subID) {
                $.ajax({
                    url: '/get_promo_pos/'+subID+'/'+paymentMethod2+'/'+dataTableProductPOS+'/'+qty+'/'+totalPrice,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.dataPromo, function(index, item) {
                            $('select[name="DDL_PROMO"]').append("<option value='" + item.PROMO_POS_ID_INT + "'>" + item.DESC_CHAR + "</option>");
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

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });

    function validateAmountNotSameForm() {
        var t = $('#example1').DataTable();
        var data = t.rows().data();

        if(data.length <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Cart cannot be empty!'
            });
            return false;
        }
        else {
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val().toString().replace(/\./g, ""));
            var paymentAmount1 = parseInt($('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, ""));
            var paymentAmount2 = parseInt($('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, ""));
            if(totalPrice <= (paymentAmount1 + paymentAmount2)) {
                var dataTable = [];
                for (var i = 0; i < data.length; i++) {
                    dataTable.push(data[i]);
                }
                $("#TXT_DATATABLE").val(JSON.stringify(dataTable));

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
    }

    function isEmpty(str) {
        return (!str || str.length === 0 );
    }
</script>
@endsection