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
    Form Membership - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Membership
<?php $__env->stopSection(); ?>
 <style>
    .disabled-form {
      opacity: 0.5;
    }
  </style>
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

                        <?php if(session()->has('urlRedirectReceipt')): ?>
                        <script>
                            window.open('<?php echo e(session()->get('urlRedirectReceipt')); ?>', "_blank", 'noreferrer').print();
                        </script>
                        <?php endif; ?>
                        
                        <?php if(session()->has('change')): ?>
                         <style>
                            .read-only {
                              background-color: #f5f5f5; /* Warna latar belakang untuk menunjukkan status read-only */
                            }
                        </style>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form action="<?php echo e(url('store')); ?>" onsubmit="return validateAmountNotSameForm()" method="post">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <div class="col-sm-6" style="display: none;">
                                <div class="form-group">
                                    <label>Nama Customer <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_CUSTOMER_NAME" class="form-control" id="TXT_CUSTOMER_NAME" placeholder="Enter Nama Customer">
                                    <input type="hidden" name="HOLIDAY_ID_INT" class="form-control" id="HOLIDAY_ID_INT" value="" readonly>
                                    <input type="hidden" name="IS_HOLIDAY" class="form-control" id="IS_HOLIDAY" value="" readonly>
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
                                    <label>Price <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_PRICE" name="DDL_MEM_PRICE" class="form-control select2" style="width: 100%;" required>
                                    <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataPriceMembership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_PRICE_MEMBERSHIP_ID_INT); ?>">
                                            <?php echo e($data->HARGA_FLOAT); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </select>
                                </div>
                              </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Group <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_GROUP" name="DDL_MEM_GROUP" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option> 
                                        <?php $__currentLoopData = $ddlDataGroupMembership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_GROUP_MEMBERSHIP_ID_INT); ?>">
                                            <?php echo e($data->DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Qty <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_QTY_TICKET" class="form-control" id="TXT_QTY_TICKET" placeholder="Enter Qty" required>
                                </div>
                            </div>
                              <div class="col-sm-2">
                                <div class="form-group">
                                  <label>Type <span style="color: red;">*</span></label>
                                  <select id="DDL_TICKET_TYPE" name="DDL_MEM_TYPE" class="form-control select2" style="width: 100%;" required onchange="handleTicketTypeChange()">
                                    <option value="">--- Not Selected ---</option>
                                    <?php $__currentLoopData = $ddlDataTypeMembership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($data->MD_GROUP_TYPE_MEMBERSHIP_ID_INT); ?>">
                                      <?php echo e($data->DESC_CHAR); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </select>
                                </div>
                              </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Periode <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_PRICE" name="DDL_TICKET_PRICE" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataPeriodeMembership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_PERIODE_MEMBERSHIP_ID_INT); ?>">
                                            <?php echo e($data->DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Start Periode <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="PROMO_END_DTTIME" class="form-control" id="PROMO_END_DTTIME" placeholder="Enter End Promo" required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>End Periode</label>
                                    <input type="datetime-local" name="PROMO_END_DTTIME" class="form-control" id="PROMO_END_DTTIME" placeholder="Enter End Promo" required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Harga</label>
                                    <input type="text" name="TXT_FREE_TICKET" class="form-control" id="TXT_FREE_TICKET" value="0" readonly required>
                                </div>
                            </div>
                        </div>
                        
                        <hr style="border-top: 1px solid black">    
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Project <span style="color: red;">*</span></label>
                                    <select id="TXT_PROJECT" name="TXT_PROJECT" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="Project1">Project1</option>
                                        <option value="Project2">Project2</option>
                                    </select>
                                </div>
                            </div>
                              <div class="col-sm-2">
                                <div class="form-group">
                                  <label>Unit <span style="color: red;">*</span></label>
                                  <select id="TXT_UNIT" name="TXT_UNIT" class="form-control select2" style="width: 100%;" required>
                                    <option value="">--- Not Selected ---</option>
                                    <option value="Unit1">Unit1</option>
                                    <option value="Unit2">Unit2</option>
                                  </select>
                                </div>
                              </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Nama <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_NAME" class="form-control" id="TXT_NAME" placeholder="Enter Your Name" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>KTP <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_KTP" class="form-control" id="TXT_KTP" placeholder="Enter Your KTP" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>No. Telp <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_NO_TELP" class="form-control" id="TXT_NO_TELP" placeholder="Enter Telephone Number" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Address <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_ADDRESS" class="form-control" id="TXT_ADDRESS" placeholder="Enter Your Address" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>No. Metland Card <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_METLAND_CARD" class="form-control" id="TXT_METLAND_CARD" placeholder="Enter Your Metland Card" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_ADD_NEW" name="BTN_ADD_NEW" class="btn btn-success float-right">+</button>
                        <br><br>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Project</th>
                                            <th>Unit</th>
                                            <th>Nama</th>
                                            <th>KTP</th>
                                            <th>No. Telp</th>
                                            <th>Address</th>
                                            <th>No. Metland Card</th>
                                            <th>Action</th> <!-- Add this column for the delete button -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr style="border-top: 1px solid black">
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Promo <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_PROMO" name="DDL_TICKET_PROMO" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Disc % <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_QTY_TICKET" class="form-control" id="TXT_QTY_TICKET" placeholder="Enter Your Disc %" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Disc Num <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_QTY_TICKET" class="form-control" id="TXT_QTY_TICKET" placeholder="Enter Your Disc Nominal" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Harga Setelah Promo <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_QTY_TICKET" class="form-control" id="TXT_QTY_TICKET" placeholder="Enter Your Price" required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Periode <span style="color: red;">*</span></label>
                                     <select id="DDL_TICKET_PERIODE" name="DDL_TICKET_PERIODE" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option> 
                                     </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Periode Start <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="PROMO_END_DTTIME" class="form-control" id="PROMO_END_DTTIME" placeholder="Enter End Promo" required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Periode End <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="PROMO_END_DTTIME" class="form-control" id="PROMO_END_DTTIME" placeholder="Enter End Promo" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_PAY" name="BTN_PAY" class="btn btn-primary float-right">Save</button>
                        <a href="<?php echo e(route('membership')); ?>" class="btn btn-danger float-right" style="margin-right: 10px;">Back to List</a>
                </div>
            </div> 
                      
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


 <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
 <script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#example1').DataTable({
            columnDefs: [
                {
                    targets: -1, // Last column
                    data: null,
                    defaultContent:
                        '<button class="btn btn-warning btn-sm edit-btn">Edit</button> <button class="btn btn-danger btn-sm delete-btn">Delete</button>',
                },
            ],
        });

        // Add New button click event
        $('#BTN_ADD_NEW').on('click', function () {
            // Get input values
            var project = $('#TXT_PROJECT').val();
            var unit = $('#TXT_UNIT').val();
            var name = $('#TXT_NAME').val();
            var ktp = $('#TXT_KTP').val();
            var noTelp = $('#TXT_NO_TELP').val();
            var address = $('#TXT_ADDRESS').val();
            var metlandCard = $('#TXT_METLAND_CARD').val();

            // Add a new row to the DataTable
            table.row
                .add([
                    table.rows().count() + 1, // No
                    project,
                    unit,
                    name,
                    ktp,
                    noTelp,
                    address,
                    metlandCard,
                    '<button class="btn btn-warning btn-sm edit-btn">Edit</button> <button class="btn btn-danger btn-sm delete-btn">Delete</button>',
                ])
                .draw();

            // Clear input fields
            $('#TXT_PROJECT, #TXT_UNIT, #TXT_NAME, #TXT_KTP, #TXT_NO_TELP, #TXT_ADDRESS, #TXT_METLAND_CARD').val('');
        });

        // Edit button click event
        $('#example1 tbody').on('click', '.edit-btn', function () {
            var row = table.row($(this).parents('tr')).data();

            // Populate the form with the selected row's data
            $('#TXT_PROJECT').val(row[1]);
            $('#TXT_UNIT').val(row[2]);
            $('#TXT_NAME').val(row[3]);
            $('#TXT_KTP').val(row[4]);
            $('#TXT_NO_TELP').val(row[5]);
            $('#TXT_ADDRESS').val(row[6]);
            $('#TXT_METLAND_CARD').val(row[7]);

            // Remove the selected row from the DataTable
            table.row($(this).parents('tr')).remove().draw();

            // Update row numbers
            updateRowNumbers();
        });

        // Delete button click event
        $('#example1 tbody').on('click', '.delete-btn', function () {
            var row = table.row($(this).parents('tr'));
            table.row(row).remove().draw();

            // Update row numbers
            updateRowNumbers();
        });

        // Function to update row numbers
        function updateRowNumbers() {
            table
                .column(0)
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        }

        // Save button click event
        $('#BTN_PAY').on('click', function () {
            // Add logic to save data from the form if needed
            // This is where you would send the data to the server or perform any other necessary action
        });
    });
</script>




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
                        $('#TXT_TOTAL_PRICE_AFTER_DISCOUNT').val(numberWithCommas(totalAfterDiscount));
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/MasterData/Membership/add_new_membership.blade.php ENDPATH**/ ?>