@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Paid - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Paid
@endsection

@section('content')

@if ($errors->any())
<ul class="alert alert-danger">
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

@if (Session::has('message'))
    <div class="alert alert-success" id="success-alert">
        {{ Session::get('message') }}
    </div>
@endif
<style>
    th, td {
        padding: 15px;
    }
</style>
<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script>

    $(document).ready(function()
    {
        $('#tdp_report1').DataTable( {
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

    $(document).ready(function()
    {
        $('#payment_report1').DataTable( {
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

</script>
<script>
    $(function()
    {

        $('#dt_transaksi').on('change',function(){
            var x = new Date($('#dt_transaksi').val());
            var currMonth = $('#monthdate').val();
            var currYear = $('#yeardate').val();
            var rqsMonth = x.getMonth() + 1;
            var rqsYear = x.getFullYear();

            if (currMonth < 10)
            {
                var currDate = currYear+'0'+currMonth;
            }
            else
            {
                var currDate = currYear+currMonth;
            }

            var currDateInt = parseInt(currDate);

            if (rqsMonth < 10)
            {
                var rqsDate = rqsYear+'0'+rqsMonth;
            }
            else
            {
                var rqsDate = rqsYear+''+rqsMonth;
            }

            var rqsDateInt = parseInt(rqsDate);
            if(rqsDateInt < currDateInt)
            {
                document.getElementById("backdate").value = null;
            }
            else
            {
                document.getElementById("backdate").value = 'Proses';
            }
        });
    });

    $(function(){
        var table = $('#coa_table').DataTable({
            order:[]
        });

        $('#coa_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('acc_no_char').value = checkEmptyStringValidation(data[0]);
            document.getElementById('acc_nop_char').value = checkEmptyStringValidation(data[1]);
            document.getElementById('acc_name_char').value = checkEmptyStringValidation(data[2]);
            $('#coaModal').modal('hide');
        });

        $('#acc_name_char').on('click',function(){
            $('#coaModal').modal('show');
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
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    @if($dataInvoice->INVOICE_STATUS_INT <> 4)
                    <form method="POST" action="{{ route('invoice.saveinvoicepayment') }}">
                    @csrf
                    @endif
                    <fieldset>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Invoice*</label>
                                    <input type="text" name="INVOICE_TRANS_NOCHAR" id="invoice_nochar" class="form-control" value="<?php echo $dataInvoice->INVOICE_TRANS_NOCHAR; ?>" placeholder="Invoice" readonly>
                                    <input type="hidden" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" value="<?php echo $dataInvoice->PSM_TRANS_NOCHAR; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Lot*</label>
                                    <input type="text" name="LOT_STOCK_NO" id="lot_stock_no" class="form-control" value="<?php echo $dataInvoice->LOT_STOCK_NO; ?>" placeholder="Lot" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Tenant*</label>
                                    <input type="text" name="MD_TENANT_NAME_CHAR" id="md_tenant_name_char" class="form-control" value="<?php echo $namaTenant; ?>" placeholder="Tenant" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Shop Name*</label>
                                    <input type="text" name="SHOP_NAME_CHAR" id="shop_name_char" class="form-control" value="<?php echo $shopName; ?>" placeholder="Shop Name" readonly>
                                </div>
                            </div>
                            @if($dataInvoice->MD_TENANT_PPH_INT == 1)
                                @if($dataInvoice->TGL_SCHEDULE_DATE <= '2022-03-31')
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Stamp Amount*</label>
                                            <input type="text" name="DUTY_STAMP" class="form-control" id="duty_stamp" placeholder="Stamp" value="<?php echo number_format($dataInvoice->DUTY_STAMP,0,',','.'); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Inv. Amount Before Stamp*</label>
                                            <input type="text" name="INVOICE_AMOUNT_BEFORE_STAMP" class="form-control" id="invoice_amount_before_stamp" placeholder="Invoice Amount Before Stamp" value="<?php echo number_format($dataInvoice->INVOICE_TRANS_DPP,0,',','.'); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Inv. Amount*</label>
                                            <input type="text" name="INVOICE_TRANS_TOTAL" class="form-control" id="amount" placeholder="Amount" value="<?php echo number_format($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->DUTY_STAMP,0,',','.'); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Inv. Payment*</label>
                                            <input type="text" name="INVOICE_PAYMENT" id="amount" class="form-control" placeholder="Amount" value="<?php echo number_format($dataSumPaidInvoice,0,',','.'); ?>" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Credit Notes*</label>
                                            <input type="text" name="CREDIT_NOTES" id="amount" class="form-control" placeholder="Amount" value="<?php echo number_format($sumCN,0,',','.'); ?>" readonly="yes">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Debt. Amount*</label>
                                            <input type="text" name="INVOICE_DEBT_AMOUNT" id="amount" class="form-control" placeholder="Amount" value="<?php echo number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->DUTY_STAMP) - ($dataSumPaidInvoice + $sumCN)),0,',','.'); ?>" readonly="yes">
                                        </div>
                                    </div>
                                @else
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Stamp Amount*</label>
                                            <input type="text" name="DUTY_STAMP" value="<?php echo number_format($dataInvoice->DUTY_STAMP,0,',','.'); ?>" class="form-control" id="duty_stamp" placeholder="Stamp" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Inv. Amount Before Stamp*</label>
                                            <input type="text" name="INVOICE_AMOUNT_BEFORE_STAMP" value="<?php echo number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH),0,',','.'); ?>" class="form-control" id="invoice_amount_before_stamp" placeholder="Invoice Amount Before Stamp" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Inv. Amount*</label>
                                            <input type="text" name="INVOICE_TRANS_TOTAL" value="<?php echo number_format(((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH) + $dataInvoice->DUTY_STAMP),0,',','.'); ?>" class="form-control" id="amount" placeholder="Amount" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Inv. Payment*</label>
                                            <input type="text" name="INVOICE_PAYMENT" value="<?php echo number_format($dataSumPaidInvoice,0,',','.'); ?>" class="form-control" id="amount" placeholder="Amount" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Credit Notes*</label>
                                            <input type="text" name="CREDIT_NOTES" value="<?php echo number_format($sumCN,0,',','.'); ?>" class="form-control" id="amount" placeholder="Amount" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Debt. Amount*</label>
                                            <input type="text" name="INVOICE_DEBT_AMOUNT" id="amount" class="form-control" value="<?php echo number_format((((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH) + $dataInvoice->DUTY_STAMP) - ($dataSumPaidInvoice + $sumCN)),0,',','.') ?>" placeholder="Amount" readonly>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Stamp Amount*</label>
                                        <input type="text" name="DUTY_STAMP" id="duty_stamp" class="form-control" placeholder="Stamp" value="<?php echo number_format($dataInvoice->DUTY_STAMP, 0, ",", "."); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Inv. Amount Before Stamp*</label>
                                        <input type="text" name="INVOICE_AMOUNT_BEFORE_STAMP" id="invoice_amount_before_stamp" class="form-control" placeholder="Invoice Amount Before Stamp" value="<?php echo number_format($dataInvoice->INVOICE_TRANS_TOTAL, 0, ",", "."); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Inv. Amount*</label>
                                        <input type="text" name="INVOICE_TRANS_TOTAL" id="amount" class="form-control" placeholder="Amount" value="<?php echo number_format($dataInvoice->INVOICE_TRANS_TOTAL + $dataInvoice->DUTY_STAMP, 0, ",", "."); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Inv. Payment*</label>
                                        <input type="text" name="INVOICE_PAYMENT" id="amount" class="form-control" placeholder="Amount" value="<?php echo number_format($dataSumPaidInvoice,0,',','.'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Credit Notes*</label>
                                        <input type="text" name="CREDIT_NOTES" id="amount" class="form-control" placeholder="Amount" value="<?php echo number_format($sumCN,0,',','.'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Debt. Amount*</label>
                                        <input type="text" name="INVOICE_DEBT_AMOUNT" id="amount" class="form-control" placeholder="Amount" value="<?php echo number_format((($dataInvoice->INVOICE_TRANS_TOTAL + $dataInvoice->DUTY_STAMP) - ($dataSumPaidInvoice + $sumCN)),0,',','.'); ?>" readonly>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row" style="padding-left: 5px;">
                            <br><br>
                            <h5>Detail Invoice</h5>
                            <div class="col-lg-12">
                                <table class="table-striped table-hover compact" id="tdp_report1" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Bill Date</th>
                                        <th>Description</th>
                                        <th>DPP</th>
                                        <th>PPN</th>
                                        <th>PPH</th>
                                        <th>Bill Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $no = 1;
                                        $dpp = 0;
                                        $ppn = 0;
                                        $pph = 0;
                                        $total = 0;
                                    ?>
                                    @foreach($dataDetailInv as $data)
                                        <tr>
                                            <td>{{$no}}</td>
                                            <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                            <td>{{$data->INVOICE_TRANS_DESC_CHAR}}</td>
                                            <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_DPP,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_PPN,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_PPH,0,',','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->INVOICE_TRANS_TOTAL,0,',','.')}}</td>
                                        </tr>
                                        <?php
                                        $no += 1;
                                        $dpp += $data->INVOICE_TRANS_DPP;
                                        $ppn += $data->INVOICE_TRANS_PPN;
                                        $pph += $data->INVOICE_TRANS_PPH;
                                        $total += $data->INVOICE_TRANS_TOTAL;
                                        ?>
                                    @endforeach
                                    </tbody>
                                    <tfooter>
                                        <tr>
                                            <td><b>TOTAL</b></td>
                                            <td><b>-</b></td>
                                            <td><b>-</b></td>
                                            <td style="text-align: right;"><b>{{number_format($dpp,0,',','.')}}</b></td>
                                            <td style="text-align: right;"><b>{{number_format($ppn,0,',','.')}}</b></td>
                                            <td style="text-align: right;"><b>{{number_format($pph,0,',','.')}}</b></td>
                                            <td style="text-align: right;"><b>{{number_format($total,0,',','.')}}</b></td>
                                        </tr>
                                    </tfooter>
                                </table>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 25px; padding-left: 5px;">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Transaction Date*</label>
                                    <input type="date" value="" class="form-control" name="TGL_BAYAR_DATE" placeholder="Transaction Date" id="dt_transaksi">
                                    <input type="hidden" value="<?php echo $dataProject['MONTH_PERIOD']; ?>" class="form-control" name="monthdate" id="monthdate" readonly>
                                    <input type="hidden" value="<?php echo $dataProject['YEAR_PERIOD']; ?>" class="form-control" name="yeardate" id="yeardate" readonly>
                                    <input type="hidden" class="form-control" name="backdate" id="backdate" readonly>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Account Payment*</label>
                                    <input type="text" name="ACC_NAME_CHAR" class="form-control" id="acc_name_char" placeholder="Account Payment" readonly>
                                    <input type="hidden" name="ACC_NOP_CHAR" class="form-control" id="acc_nop_char" placeholder="Account Payment" readonly>
                                    <input type="hidden" name="ACC_NO_CHAR" class="form-control" id="acc_no_char" placeholder="Account Payment" readonly>
                                </div>
                            </div>
                            @if($dataInvoice->INVOICE_STATUS_INT <> 4)
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Payment Method*</label>
                                    <select name="PAYMENT_METHOD" class="form-control">
                                        <option value="">Please Choose</option>
                                        <option value="Cash">Cash</option>
                                        <option value="EDC">EDC</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                </div>
                            </div>
                            @else
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Payment Method*</label>
                                    <select name="PAYMENT_METHOD" class="form-control" id="PAYMENT_METHOD" disabled>
                                    <option value="">Please Choose</option>
                                    <option value="cash">Cash</option>
                                    <option value="EDC">EDC</option>
                                    <option value="Transfer">Transfer</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Money Fine</label>
                                    <input type="number" name="PAID_BILL_DENDA" id="PAID_BILL_DENDA" value="0" class="form-control" readonly="yes">
                                </div>
                            </div>
                            @if($dataInvoice->INVOICE_STATUS_INT <> 4)
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Payment Input*</label>
                                    <input type="number" name="PAID_BILL_AMOUNT" class="form-control" value="0">
                                </div>
                            </div>
                            @else
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Payment Input*</label>
                                    <input type="number" name="PAID_BILL_AMOUNT" id="PAID_BILL_AMOUNT" value="0" class="form-control" readonly>
                                </div>
                            </div>
                            @endif
                            @if($dataInvoice->INVOICE_STATUS_INT <> 4)
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Is Payment Stamp?*</label>
                                    <select name="PAYMENT_STAMP" class="form-control">
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            @else
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Is Payment Stamp?*</label>
                                    <select name="PAYMENT_STAMP" id="PAYMENT_STAMP" class="form-control" disabled>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        @if($dataInvoice->INVOICE_STATUS_INT <> 4)
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                        Save Data
                                    </a>
                                    <div id="confModal" class="modal fade">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Confirmation</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure save this data ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" id="submit" class="btn btn-primary">Save Data</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row" style="padding-left: 5px;">
                            <br><br>
                            <h5>Payment Invoice</h5>
                            <div class="col-lg-12">
                                <table class="table-striped table-hover compact" id="payment_report1" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Payment date</th>
                                        <th>Account Payment</th>
                                        <th>Payment Amount</th>
                                        <th>Receipt</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $no = 1; ?>
                                    @foreach($dataInvPayment as $dataPayment)
                                        <tr>
                                            <td>{{$no}}</td>
                                            <td>{{$dataPayment->TGL_BAYAR_DATE}}</td>
                                            <td>{{$dataPayment->ACC_NAME_CHAR}}</td>
                                            <td style="text-align: right;">{{number_format($dataPayment->PAID_BILL_AMOUNT,0,',','.')}}</td>
                                            <td class="center">
                                                <a class="btn btn-sm btn-info" href="{{ URL('/invoice/printkwitansireceipt/' . $dataPayment->INVOICE_PAYMENT_ID_INT) }}" onclick="window.open(this.href).print(); return false">
                                                    <i>
                                                        Receipt
                                                    </i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        $no += 1;
                                        ?>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    @if($dataInvoice->INVOICE_STATUS_INT <> 4)
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="coaModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Account Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="coa_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Account</th>
                            <th>Parent</th>
                            <th>Account Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataCOABank as $data)
                            <tr>
                                <td>{{$data->ACC_NO_CHAR}}</td>
                                <td>{{$data->ACC_NOP_CHAR}}</td>
                                <td>{{$data->ACC_NAME_CHAR}}</td>
                            </tr>
                        @endforeach
                        @foreach($dataCoaOthers as $data1)
                            <tr>
                                <td>{{$data1->ACC_NO_CHAR}}</td>
                                <td>{{$data1->ACC_NOP_CHAR}}</td>
                                <td>{{$data1->ACC_NAME_CHAR}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


