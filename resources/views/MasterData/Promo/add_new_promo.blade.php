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
    Form Add New Promo Ticket - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add New Promo Ticket
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('save_promo') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Description <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_DESC" class="form-control" id="TXT_DESC" placeholder="Enter Description" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Free Ticket <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_TICKET_FREE" class="form-control" id="TXT_TICKET_FREE" placeholder="Enter Ticket Free" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_DISCOUNT_PERCENT" class="form-control" id="TXT_DISCOUNT_PERCENT" placeholder="Enter Discount %" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount Nominal <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_DISCOUNT_NOMINAL" class="form-control" id="TXT_DISCOUNT_NOMINAL" placeholder="Enter Discount Nominal" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Start Promo <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="TXT_START_PROMO" class="form-control" id="TXT_START_PROMO" placeholder="Enter Start Promo" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>End Promo <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="TXT_END_PROMO" class="form-control" id="TXT_END_PROMO" placeholder="Enter End Promo" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Ticket Group <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_GROUP" name="DDL_TICKET_GROUP" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="ALL">ALL</option>
                                        @foreach($ddlDataTicketGroup as $data)
                                        <option value="{{ $data->MD_GROUP_TICKET_ID_INT }}">
                                            {{ $data->MD_GROUP_TICKET_DESC }} ({{ $data->MD_GROUP_TICKET_PERSON }} Orang)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Ticket Price <span style="color: red;">*</span></label>
                                    <select id="DDL_TICKET_PRICE" name="DDL_TICKET_PRICE" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="ALL">ALL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Method <span style="color: red;">*</span></label>
                                    <select id="DDL_PAYMENT_METHOD" name="DDL_PAYMENT_METHOD" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="ALL">ALL</option>
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
                                    <label>Minimal Ticket Qty <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MIN_TICKET_QTY" class="form-control" id="TXT_MIN_TICKET_QTY" placeholder="Enter Minimal Ticket Qty" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Minimal Ticket Payment <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MIN_TICKET_PAYMENT" class="form-control" id="TXT_MIN_TICKET_PAYMENT" placeholder="Enter Minimal Ticket Payment" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Maximal Transaction Number <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MAX_TRX_NUM" class="form-control" id="TXT_MAX_TRX_NUM" placeholder="Enter Maximal Transaction Number" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_SUBMIT" name="BTN_SUBMIT" class="btn btn-primary float-right">Save</button>
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

        $('select[name="DDL_TICKET_GROUP"]').on('change', function() {
            var subID = $(this).val();
            if(subID) {
                if(subID === 'ALL') {
                    $('select[name="DDL_TICKET_PRICE"]').empty().append('<option value="">--- Not Selected ---</option>');
                    $('select[name="DDL_TICKET_PRICE"]').append('<option value="ALL">ALL</option>');
                }
                else {
                    $.ajax({
                        url: '/get_ticket_price_promo/' + subID,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $('select[name="DDL_TICKET_PRICE"]').empty().append('<option value="">--- Not Selected ---</option>');
                            $('select[name="DDL_TICKET_PRICE"]').append('<option value="ALL">ALL</option>');
                            $.each(data.ddlDataTicketPrice, function(index, item)
                            {
                                $('select[name="DDL_TICKET_PRICE"]').append("<option value='" + item.MD_PRICE_TICKET_ID_INT + "'>" + item.MD_PRICE_TICKET_DESC + "</option>");
                            });
                        },
                        error: function() {
                            alert('Error, Please contact Administrator!');
                        }
                    });
                }
            }
            else {
                $('select[name="DDL_TICKET_PRICE"]').empty().append('<option value="">--- Not Selected ---</option>');
                $('select[name="DDL_TICKET_PRICE"]').append('<option value="ALL">ALL</option>');
            }
        });
    });

    function isNumberWithDecimal(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && ((charCode < 48 && charCode !== 46) || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
@endsection