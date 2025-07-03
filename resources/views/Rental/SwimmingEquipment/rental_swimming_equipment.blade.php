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
    Form Rental Swimming Equipment - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Rental Swimming Equipment
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('save_rental_swimming_equipment') }}" onsubmit="return validateAmountNotSameForm()" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-4">
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
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>No. Telp <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_NO_TELP" class="form-control" id="TXT_NO_TELP" placeholder="Example : 08878xxxxxxx" required>
                                    <label style="color: red;"><small><b>Example : 08878xxxxxxx</b></small></label>
                                </div>
                            </div>
                        </div>

                        <br />
                        <input type="text" name="TXT_DATATABLE" class="form-control" style="display: none;" id="TXT_DATATABLE" readonly>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>Perlengkapan Renang</label>
                                    <select id="DDL_PERLENGKAPAN_RENANG" name="DDL_PERLENGKAPAN_RENANG" class="form-control select2" style="width: 100%;">
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlDataPerlengkapanRenang as $data)
                                            <?php $currentStock = $data->STOCK_INT - $data->CURRENT_RENT_INT; ?>
                                            <option value="{{ $data->MD_PERLENGKAPAN_RENANG_ID_INT }}" {{ $currentStock <= 0 ? "disabled" : "" }}>
                                                {{ $data->DESC_CHAR }} (Sisa : {{ $currentStock }}) {{ $currentStock <= 0 ? "(Disabled)" : "" }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Price Amount</label>
                                    <input type="text" value="0" name="TXT_HARGA_SATUAN" class="form-control" id="TXT_HARGA_SATUAN" placeholder="Enter Price Amount" readonly>
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
                                            <th>Price</th>
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
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Total <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_HARGA" class="form-control" id="TXT_TOTAL_HARGA" placeholder="Enter Total Price" value="0" readonly required>
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

        cartTable();

        $('select[name="DDL_PERLENGKAPAN_RENANG"]').on('change', function() {
            var subID = $(this).val();
            if(subID.toString()) {
                $.ajax({
                    url: '/get_perlengkapan_renang_price_by_id/' + subID.toString(),
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#TXT_HARGA_SATUAN').val(numberWithCommas(parseInt(data.dataPerlengkapanRentalPrice)));
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

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function cartTable() {
            var t = $('#example1').DataTable();
            const idTable = [];
            $('#BTN_ADD_NEW').on('click', function () {
                var perlengkapanRenangId = $('#DDL_PERLENGKAPAN_RENANG option:selected').val();
                var qty = $("#TXT_QTY").val();
                var priceAmount = $("#TXT_HARGA_SATUAN").val();

                if(perlengkapanRenangId === "" || qty === "" || parseInt(priceAmount) === 0 || priceAmount === "" || parseInt(qty) === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Field cannot be empty!'
                    });
                }
                else {
                    var perlengkapanRenangDesc = $('#DDL_PERLENGKAPAN_RENANG option:selected').text().split("(")[0].trim();
                    var sisaQty = $('#DDL_PERLENGKAPAN_RENANG option:selected').text().split("(")[1].trim().replace("Sisa : ", "").replace(")", "");

                    // Check if qty over than remaining qty
                    if(parseInt(qty) > parseInt(sisaQty)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Qty cannot be over than remaining quantity!'
                        });
                    }
                    else {
                        // Check if a value exists in array
                        if(idTable.indexOf(perlengkapanRenangId) !== -1) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: perlengkapanRenangDesc + ' Already Exists!'
                            });
                        }
                        else {
                            var totalPrice = numberWithCommas(parseInt(priceAmount.toString().replace(/\./g, "")) * qty);
                            t.row.add([
                                perlengkapanRenangId,
                                perlengkapanRenangDesc,
                                priceAmount,
                                qty,
                                totalPrice,
                                "<button id='BTN_REMOVE_ROW' name='BTN_REMOVE_ROW' class='btn btn-danger'>X</button>"
                            ]).draw(false);
                            idTable.push(perlengkapanRenangId);

                            var txtTotalHarga = parseInt($("#TXT_TOTAL_HARGA").val().toString().replace(/\./g, "")) + parseInt(totalPrice.toString().replace(/\./g, ""));
                            $("#TXT_TOTAL_HARGA").val(numberWithCommas(txtTotalHarga));

                            $('select[name="DDL_PERLENGKAPAN_RENANG"]').val(null);
                            $('#TXT_HARGA_SATUAN').val(0);
                            $('#TXT_QTY').val(null);
                            $('select[name="DDL_PERLENGKAPAN_RENANG"]').trigger("change");
                        }
                    }
                }
            });
            $('#example1 tbody').on('click', '#BTN_REMOVE_ROW',function() {
                var rowData = t.row($(this).parents('tr')).data();
                var id = rowData[0];

                var txtTotalHarga = parseInt($("#TXT_TOTAL_HARGA").val().toString().replace(/\./g, "")) - parseInt(rowData[4].toString().replace(/\./g, ""));
                $("#TXT_TOTAL_HARGA").val(numberWithCommas(txtTotalHarga));

                t.row($(this).parents('tr')).remove().draw();

                var index = idTable.indexOf(id);
                if (index >= 0) {
                    idTable.splice(index, 1);
                }
            });
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
            var totalPrice = parseInt($('#TXT_TOTAL_HARGA').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_HARGA').val().toString().replace(/\./g, ""));
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
</script>
@endsection