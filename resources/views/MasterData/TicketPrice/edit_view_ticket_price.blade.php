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
    Form Edit Ticket Price - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Ticket Price
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('edit_ticket_price') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="TXT_ID" class="form-control" id="TXT_ID" value="{{ $dataTicketPrice->MD_PRICE_TICKET_ID_INT }}" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group <span style="color: red;">*</span></label>
                                    <select id="DDL_GROUP" name="DDL_GROUP" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlTicketGroup as $data)
                                        <option value="{{ $data->MD_GROUP_TICKET_ID_INT }}" {{ $dataTicketPrice->MD_GROUP_TICKET_ID_INT == $data->MD_GROUP_TICKET_ID_INT ? "selected" : "" }}>
                                            {{ $data->MD_GROUP_TICKET_DESC }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Description <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_DESC" class="form-control" id="TXT_DESC" placeholder="Enter Description" value="{{ $dataTicketPrice->MD_PRICE_TICKET_DESC }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Single Ticket Price (Include PB1 & PPH) <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_TICKET" class="form-control" id="TXT_TICKET" placeholder="Enter Price Ticket" value="{{ $dataTicketPrice->MD_PRICE_TICKET_NUM }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>PB1 (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_PB1" class="form-control" id="TXT_PB1" placeholder="Enter PB1 (%)" value="{{ (float) $dataTicketPrice->MD_PRICE_TICKET_PB1_PERCENT_INT }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>PPH (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_PPH" class="form-control" id="TXT_PPH" placeholder="Enter PPH (%)" value="{{ (float) $dataTicketPrice->MD_PRICE_TICKET_PPH_PERCENT_INT }}" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_SUBMIT" name="BTN_SUBMIT" class="btn btn-primary float-right">Edit</button>
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