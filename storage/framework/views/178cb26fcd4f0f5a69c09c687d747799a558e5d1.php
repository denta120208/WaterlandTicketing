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



<?php $__env->startSection('navbar_header'); ?>
    Form Rental Equipment - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Rental Equipment
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <?php if(session()->has('urlRedirect')): ?>
                        <script>
                            window.open('<?php echo e(session()->get('urlRedirect')); ?>', "_blank", 'noreferrer').print();
                        </script>
                        <?php if(session()->has('change')): ?>
                        <script>
                            $(document).ready(function() {
                                Swal.fire({
                                    title: 'Change',
                                    allowOutsideClick: false,
                                    html: "<h4><b>Rp. <?php echo e(session()->get('change')); ?></b></h4>"
                                });
                            });
                        </script>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form action="<?php echo e(url('save_rental_equipment')); ?>" onsubmit="return validateAmountNotSameForm()" method="post">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Cashier Name <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CASHIER_NAME" class="form-control" id="TXT_CASHIER_NAME" placeholder="Enter Cashier Name" value="<?php echo e($cashierName); ?>" readonly required>
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
                                    <label>Equipment <span style="color: red;">*</span></label>
                                    <select id="DDL_EQUIPMENT" name="DDL_EQUIPMENT[]" class="form-control select2" multiple="multiple" data-placeholder="Select Equipment" style="width: 100%;" required>                                        
                                        <?php $__currentLoopData = $ddlDataEquipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_EQUIPMENT_ID_INT); ?>" <?php echo e($data->IS_RENT == 1 ? "disabled" : ""); ?>>
                                            <?php echo e($data->MD_EQUIPMENT_CATEGORY_DESC_CHAR); ?> - <?php echo e($data->EQUIPMENT_ASSET_NUMBER); ?> <?php echo e($data->IS_RENT == 1 ? "(Disabled)" : ""); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Qty <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_QTY" class="form-control" id="TXT_QTY" placeholder="Enter Qty" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Total <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_PRICE" class="form-control" id="TXT_TOTAL_PRICE" placeholder="Enter Total Price" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Total After Deposit <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_AF_DEPO" class="form-control" id="TXT_TOTAL_AF_DEPO" placeholder="Enter Total After Deposit" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Deposit <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_DEPOSIT" class="form-control" id="TXT_DEPOSIT" placeholder="Enter Total Deposit" value="0" required>
                                    <label style="color: red;"><small><b>Isi 0 Jika Tidak Ada</b></small></label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Free Qty</label>
                                    <input type="text" name="TXT_FREE_QTY" class="form-control" id="TXT_FREE_QTY" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Discount (%)</label>
                                    <input type="text" name="TXT_DISCOUNT_PERCENT" class="form-control" id="TXT_DISCOUNT_PERCENT" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Discount Nominal</label>
                                    <input type="text" name="TXT_DISCOUNT" class="form-control" id="TXT_DISCOUNT" value="0" readonly required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Total After Discounts <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_TOTAL_PRICE_AFTER_DISCOUNT" class="form-control" id="TXT_TOTAL_PRICE_AFTER_DISCOUNT" value="0" readonly required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                                    <label>Payment Method 1 <span style="color: red;">*</span></label>
                                    <select id="DDL_PAYMENT_METHOD1" name="DDL_PAYMENT_METHOD1" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataPaymentMethod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->PAYMENT_METHOD_ID_INT); ?>">
                                            <?php echo e($data->PAYMENT_METHOD_DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" style="display: none;">
                                <div class="form-group">
                                    <label>Payment Method 2 (Optional)</label>
                                    <select id="DDL_PAYMENT_METHOD2" name="DDL_PAYMENT_METHOD2" class="form-control select2" style="width: 100%;">
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataPaymentMethod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->PAYMENT_METHOD_ID_INT); ?>">
                                            <?php echo e($data->PAYMENT_METHOD_DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <a href="<?php echo e(route('rentEquipment')); ?>" class="btn btn-danger float-right" style="margin-right: 10px;">Back to List</a>
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
            var totalDepo = parseInt($('#TXT_DEPOSIT').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_DEPOSIT').val().toString().replace(/\./g, ""));
            var category = isEmpty($('select[id="DDL_EQUIPMENT"]').val()) ? null : $('select[id="DDL_EQUIPMENT"]').val();
            if(!isEmpty(subID)) {
                $.ajax({
                    url: '/get_promo_by_id_equipment/'+subID+'/'+category,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#TXT_DISCOUNT').val(numberWithCommas(parseFloat(data.dataPromoDiscountNominal)));

                        // Jika Minimal Qty Lebih Dari Satu Maka Mulai Hitung Ticket Free (Jika Ada) Menggunakan Kelipatan Dari Minimal Qty
                        var minQty = parseFloat(data.dataPromoMinQty);
                        if(minQty > 0) {
                            var QtyCurr = parseInt(data.valueCategoryFree);
                            var perkalianKelipatan = parseInt(QtyCurr / minQty);
                            var dataFree = parseFloat(data.dataPromoEquipmentFree);
                            var equipmentFree = dataFree * perkalianKelipatan;
                            $('#TXT_FREE_QTY').val(equipmentFree);
                        }
                        else {
                            $('#TXT_FREE_QTY').val(data.dataPromoEquipmentFree);
                        }

                        $('#TXT_DISCOUNT_PERCENT').val(parseFloat(data.dataPromoDiscountPercent));
                        var totalAfterDiscount = (totalPrice - (parseFloat(data.dataPromoDiscountNominal) + (totalPrice * (parseFloat(data.dataPromoDiscountPercent) / 100)))) + totalDepo;
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
                $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val(numberWithCommas(totalPrice + totalDepo));
            }
        });

        $('select[name="DDL_PAYMENT_METHOD2"]').on('change', function() {
            var subID = $(this).val();
            var paymentMethod1 = $('select[name="DDL_PAYMENT_METHOD1"]').val() === "" ? null : $('select[name="DDL_PAYMENT_METHOD1"]').val();
            var category = isEmpty($('select[id="DDL_EQUIPMENT"]').val()) ? null : $('select[id="DDL_EQUIPMENT"]').val();
            var qty = $('#TXT_QTY').val() === "" ? 0 : $('#TXT_QTY').val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));

            if(subID) {
                $.ajax({
                    url: '/get_promo_equipment/'+paymentMethod1+'/'+subID+'/'+category+'/'+qty+'/'+totalPrice,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.dataPromo, function(index, item) {
                            $('select[name="DDL_PROMO"]').append("<option value='" + item[0].PROMO_EQUIPMENT_ID_INT + "'>" + item[0].MD_EQUIPMENT_CATEGORY_DESC_CHAR + " - " + item[0].DESC_CHAR + "</option>");
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
            var category = isEmpty($('select[id="DDL_EQUIPMENT"]').val()) ? null : $('select[id="DDL_EQUIPMENT"]').val();
            var qty = $('#TXT_QTY').val() === "" ? 0 : $('#TXT_QTY').val();
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));

            if(subID) {
                $.ajax({
                    url: '/get_promo_equipment/'+subID+'/'+paymentMethod2+'/'+category+'/'+qty+'/'+totalPrice,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="DDL_PROMO"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.dataPromo, function(index, item) {
                            $('select[name="DDL_PROMO"]').append("<option value='" + item[0].PROMO_EQUIPMENT_ID_INT + "'>" + item[0].MD_EQUIPMENT_CATEGORY_DESC_CHAR + " - " + item[0].DESC_CHAR + "</option>");
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

        $("#TXT_DEPOSIT").keyup(function() {
            var totalPrice = parseInt($('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE').val().toString().replace(/\./g, ""));
            var deposit = $("#TXT_DEPOSIT").val() === "" ? 0 : $("#TXT_DEPOSIT").val();
            var totalPriceFinal = parseInt(totalPrice) + parseInt(deposit);
            $("#TXT_TOTAL_AF_DEPO").val(numberWithCommas(totalPriceFinal));
            $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
            $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
            $('select[name="DDL_PROMO"]').trigger("change");
        });

        $('select[id="DDL_EQUIPMENT"]').on('change', function() {
            var subID = $(this).val();
            if(!isEmpty(subID)) {
                $.ajax({
                    url: '/get_equipment_price_by_id/' + subID,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $("#TXT_TOTAL_PRICE").val(numberWithCommas(parseInt(data.dataEquipmentPrice)));
                        $("#TXT_QTY").val(numberWithCommas(parseInt(data.dataEquipmentCount)));
                        $("#TXT_DEPOSIT").trigger("keyup");
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
                $('#TXT_TOTAL_PRICE').val(0);
                $("#TXT_DEPOSIT").trigger("keyup");
                $('select[name="DDL_PAYMENT_METHOD1"]').trigger("change");
                $('select[name="DDL_PAYMENT_METHOD2"]').trigger("change");
                $('select[name="DDL_PROMO"]').trigger("change");
            }
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });

    function validateAmountNotSameForm() {
        var totalPrice = parseInt($('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val().toString().replace(/\./g, ""));
        var paymentAmount1 = parseInt($('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT1').val().toString().replace(/\./g, ""));
        var paymentAmount2 = parseInt($('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, "") === "" ? 0 : $('#TXT_PAYMENT_AMOUNT2').val().toString().replace(/\./g, ""));
        var deposit = parseInt($('#TXT_DEPOSIT').val() === "" ? 0 : $('#TXT_DEPOSIT').val());
        if((totalPrice - deposit) <= ((paymentAmount1 + paymentAmount2) - deposit)) {
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/Rental/Equipment/rental_equipment.blade.php ENDPATH**/ ?>