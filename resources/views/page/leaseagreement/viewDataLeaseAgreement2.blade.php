@extends('layouts.mainLayouts')

@section('navbar_header')
    Form View Data Letter Of Intent - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form View Data Letter Of Intent
@endsection

@section('content')
@if($errors->any())
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

<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function()
    {
        $('#vendor_table').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });

        $('#vendor_table_cl').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });

        $('#vendor_table_dp').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });

        $('#vendor_table_rt').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });

        $('#vendor_table_sc').DataTable({
            order: [[0, 'asc']],
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });

        $('#addendum_table').DataTable({
            order : [],
            // scrollY:"700px",
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Addendum ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Addendum ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });
    } );

</script>
<script>
    $(document).ready(function()
    {
        $('#commission_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });
    });

    $(document).ready(function()
    {
        $('#schedule_table').DataTable({
            order : []
        });
    } );

    $(document).ready(function()
    {
        $('#data_securedep_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });

        $('#data_rentscamt_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });

        $('#data_rentsclot_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

    $(function(){
        var table = $('#trx_table').DataTable({
            order:[]
        });

        $('#trx_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('trx_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('trx_code').value = checkEmptyStringValidation(data[1]);
            $('#trxModal').modal('hide');
        });

        $('#trx_desc').on('click',function(){
            $('#trxModal').modal('show');
        });
    });

    $(function(){
        var table = $('#secure_dep_table').DataTable({
            order:[]
        });

        $('#secure_dep_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('deposit_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('deposit_desc').value = checkEmptyStringValidation(data[1]);
            $('#secureDepModal').modal('hide');
        });

        $('#deposit_desc').on('click',function(){
            $('#secureDepModal').modal('show');
        });
    });

    $(function(){
        var table = $('#shop_type_table').DataTable({
            order:[]
        });

        $('#shop_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('shop_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('shop_type_id').value = checkEmptyStringValidation(data[1]);
            $('#shopTypeModal').modal('hide');
        });

        $('#shop_type').on('click',function(){
            $('#shopTypeModal').modal('show');
        });
    });

    $(function(){
        var table = $('#address_tax_table').DataTable({
            order:[]
        });

        $('#address_tax_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('tenant_address_tax').value = checkEmptyStringValidation(data[0]);
            document.getElementById('tenant_address_tax_id').value = checkEmptyStringValidation(data[1]);
            $('#addressTaxModal').modal('hide');
        });

        $('#tenant_address_tax').on('click',function(){
            $('#addressTaxModal').modal('show');
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

    $(function()
    {
        $('#disk_persen').on('change',function(){
            var disk_persen = parseInt($("#disk_persen").val());
            var net_before_tax_real = parseInt($("#net_before_tax_real").val());

            var disk_amount = (disk_persen / 100) * net_before_tax_real;


            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("disk_amount").value = disk_amount;

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    $(function()
    {
        $('#disk_amount').on('change',function(){
            var disk_amount = parseInt($("#disk_amount").val());

            var net_before_tax_real = parseInt($("#net_before_tax_real").val());

            var disk_persen = (disk_amount/net_before_tax_real) * 100;

            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("disk_persen").value = disk_persen.formatMoney(2);

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    Number.prototype.formatMoney = function (c, d, t)
    {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "." : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
</script>
<script type="text/javascript">
    function delItemSchedule(id){
        $.ajax({
            type: "post",
            url: "{{ route('marketing.leaseagreement.deleteitemschedule') }}",
            data: {PSM_SCHEDULE_ID_INT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function (response) {
                if(response['Success']){
                    alert(response['Success']);
                    document.location.reload(true);
                }else{
                    alert(response['Error']);
                }
                $('#loading').modal('hide');
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
    };

    $(function(){
        $('#update').on('click',function(){

            var stDate = $("#stDate").val();
            var enDate = $("#enDate").val();

            var trx_code = $("#trx_code").val();
            var desc_char = $("#desc_char").val();
            var base_amount = $("#base_amount").val();
            var psm_trans_nochar = $("#psm_trans_nochar").val();
            var psm_trans_id = $("#psm_trans_id").val();

            var insert_id = $("#insert_id").val();

            if (stDate === '' || enDate === '' || trx_code === '' ||
                desc_char === '' || base_amount === 0 || base_amount === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "{{ route('marketing.leaseagreement.insertupdateitemschedule') }}",
                    data: {PSM_TRANS_ID_INT:psm_trans_id,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        TRX_CODE:trx_code,
                        DESC_CHAR:desc_char,
                        BASE_AMOUNT_NUM:base_amount,
                        TGL_SCHEDULE_ST_DATE:stDate,
                        TGL_SCHEDULE_EN_DATE:enDate,
                        insert_id:insert_id,
                        _token: "{{ csrf_token() }}"},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){ $('#loading').modal('show'); },
                    success: function (response) {
                        if(response['Success']){
                            alert(response['Success']);
                            document.location.reload(true);
                        }else{
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });
    });
</script>
<script>
    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
            document.getElementById('all').remove();
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'selectall', value: 'all', class: 'form-control',id: 'all'}))
            tz.appendTo('#temp-form');
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                console.log(i)
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
            document.getElementById('all').remove();
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'selectall', value: 'none', class: 'form-control',id: 'all'}))
            tz.appendTo('#temp-form');
        }
    }

    function selected(ele,billid){
        if (ele.checked) {
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'billing[]', value: billid, class: 'form-control',id: billid}))
            tz.appendTo('#temp-form');
        } else {
            document.getElementById(billid).remove();
        }
    }
</script>
<script type="text/javascript">
    function delItemSecureDep(id){
        $.ajax({
            type: "post",
            url: "{{ route('marketing.leaseagreement.deleteitemsecuredeposito') }}",
            data: {PSM_SECURE_DEP_ID_INT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function (response) {
                if(response['Success']){
                    document.location.reload(true);
                }else{
                    alert(response['Error']);
                }
                $('#loading').modal('hide');
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
    };

    function delItemRentSCAmt(id){
        $.ajax({
            type: "post",
            url: "{{ route('marketing.leaseagreement.deleteitemrentscamt') }}",
            data: {
                PSM_TRANS_PRICE_ID_INT:id,
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                $('#loading').modal('show');
            },
            success: function (response) {
                if(response['Success']) {
                    document.location.reload(true);
                } else {
                    alert(response['Error']);
                }
                $('#loading').modal('hide');
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
    };

    function getItemSecureDep(id){
        $.ajax({
            type: "post",
            url: "{{ route('marketing.leaseagreement.getitemsecuredeposit') }}",
            data: {PSM_SECURE_DEP_ID_INT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    $("#deposit_desc").val(data['PSM_TRANS_DEPOSIT_DESC']);
                    $("#deposit_type").val(data['PSM_TRANS_DEPOSIT_TYPE']);
                    $("#deposit_id").val(data['PSM_SECURE_DEP_ID_INT']);
                    $("#deposit_num").val(data['PSM_TRANS_DEPOSIT_NUM']);
                    $("#deposit_date").val(data['PSM_TRANS_DEPOSIT_DATE']);
                    $("#insert_id").val('0');
                }else{
                    alert(data['msg']);
                }
                $('#loading').modal('hide');
            }
        });
    };

    $(function(){
        $('#saveLot').on('click',function() {
            var psm_trans_id_int = "<?php echo $dataPSM->PSM_TRANS_ID_INT; ?>";
            var psm_trans_nochar = "<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>";
            var lot = $("#LOT_STOCK_NO").val().toString();

            if(lot == '') {
                alert('Input Failed, Select Lot Number Correctly');
                return false;
            }
            else {
                $.ajax({
                    type: "post",
                    url: "{{ route('marketing.leaseagreement.insertrentsclot') }}",
                    data: {
                        PSM_TRANS_ID_INT:psm_trans_id_int,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        LOT_STOCK_ID_INT:lot,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                        $('#loading').modal('show');
                    },
                    success: function (response) {
                        if(response['Success']) {
                            document.location.reload(true);
                        } else {
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });

        $('#saveDisc').on('click',function() {
            var psm_trans_id_int = "<?php echo $dataPSM->PSM_TRANS_ID_INT; ?>";
            var psm_trans_nochar = "<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>";
            var disc_persen = parseFloat($("#disc_persen").val());
            var disc_amt = parseFloat($("#disc_amt").val());

            if (isNaN(disc_persen) || isNaN(disc_amt)) {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else {
                $.ajax({
                    type: "post",
                    url: "{{ route('marketing.leaseagreement.insertrentscdisc') }}",
                    data: {
                        PSM_TRANS_DISKON_PERSEN:disc_persen,
                        PSM_TRANS_DISKON_NUM:disc_amt,
                        PSM_TRANS_ID_INT:psm_trans_id_int,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                        $('#loading').modal('show');
                    },
                    success: function (response) {
                        if(response['Success']) {
                            document.location.reload(true);
                        } else {
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });

        $('#disc_persen').on('keyup',function() {
            var disc_persen = parseFloat($("#disc_persen").val()) / 100;
            var net_before_tax_real = parseFloat($("#net_before_tax_real").val());
            var disc_amt = net_before_tax_real * disc_persen;
            $("#disc_amt").val(disc_amt);
        });

        $('#disc_amt').on('keyup',function() {
            var disc_amt = parseFloat($("#disc_amt").val());
            var net_before_tax_real = parseFloat($("#net_before_tax_real").val());
            var disc_persen = (disc_amt / net_before_tax_real) * 100;
            $("#disc_persen").val(disc_persen);
        });

        $('#saveRentScAmt').on('click',function() {
            var tableArr = $('#data_rentscamt_table').DataTable().rows().data().toArray();
            var tableArrLength = $('#data_rentscamt_table').DataTable().rows().data().toArray().length;
            var isExists = false;
            for(i = 0; i < tableArrLength; i++) {
                if(parseInt(tableArr[i][1]) == parseInt($("#year_int").val())) {
                    isExists = true;
                    break;
                }
            }

            if(isExists) {
                alert('Input Failed, Tahun sudah ada. Silahkan input tahun yang lain!');
            }
            else {
                var psm_trans_id_int = "<?php echo $dataPSM->PSM_TRANS_ID_INT; ?>";
                var psm_trans_nochar = "<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>";
                var year_int = parseInt($("#year_int").val());
                var rent_amount = parseFloat($("#rent_amount").val());
                var sc_amount = parseFloat($("#sc_amount").val());
                
                var start_date = new Date("<?php echo $dataPSM->PSM_TRANS_START_DATE; ?>");
                var end_date = new Date("<?php echo $dataPSM->PSM_TRANS_END_DATE; ?>");

                if(isNaN(year_int) || isNaN(rent_amount) || isNaN(sc_amount)) {
                    alert('Input Failed, Enter All Data Correctly');
                    return false;
                }
                else if(year_int < parseInt(start_date.getFullYear()) || year_int > parseInt(end_date.getFullYear())) {
                    alert('Input Failed, Year tidak bisa lewat dari Start Date & End Date');
                    return false;
                }
                else {
                    $.ajax({
                        type: "post",
                        url: "{{ route('marketing.leaseagreement.insertrentscamt') }}",
                        data: {
                            PSM_TRANS_ID_INT:psm_trans_id_int,
                            PSM_TRANS_NOCHAR:psm_trans_nochar,
                            PSM_TRANS_PRICE_YEAR:year_int,
                            PSM_TRANS_PRICE_RENT_NUM:rent_amount,
                            PSM_TRANS_PRICE_SC_NUM:sc_amount,
                            PSM_TRANS_START_DATE:"<?php echo $dataPSM->PSM_TRANS_START_DATE; ?>",
                            PSM_TRANS_END_DATE:"<?php echo $dataPSM->PSM_TRANS_END_DATE; ?>",
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: 'json',
                        cache: false,
                        beforeSend: function() {
                            $('#loading').modal('show');
                        },
                        success: function (response) {
                            if(response['Success']) {
                                document.location.reload(true);
                            } else {
                                alert(response['Error']);
                            }
                        },
                        error: function() {
                            alert('Error, Please contact Administrator!');
                        }
                    });
                }
            }
        });
        
        $('#updateSecureDep').on('click',function() {
            var deposit_desc = $("#deposit_desc").val();
            var deposit_type = $("#deposit_type").val();
            var deposit_id = $("#deposit_id").val();

            var deposit_num = $("#deposit_num").val();
            var deposit_date = $("#deposit_date").val();
            var psm_trans_nochar = $("#psm_trans_nochar").val();
            var insert_id = $("#insert_id").val();

            if (deposit_desc === '' || deposit_num === '' || deposit_date === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "{{ route('marketing.leaseagreement.insertupdatesecuredeposit') }}",
                    data: {PSM_SECURE_DEP_ID_INT:deposit_id,
                        PSM_TRANS_DEPOSIT_DESC:deposit_desc,
                        PSM_TRANS_DEPOSIT_TYPE:deposit_type,
                        PSM_TRANS_DEPOSIT_NUM:deposit_num,
                        PSM_TRANS_DEPOSIT_DATE:deposit_date,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        insert_id:insert_id,
                        _token: "{{ csrf_token() }}"},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){ $('#loading').modal('show'); },
                    success: function (response) {
                        if(response['Success']){
                            document.location.reload(true);
                        }else{
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });
    });
</script>

<style>
    @media screen and (min-width: 676px) {
        .modal-dialog {
            max-width: 700px; /* New width for default modal */
        }
    }
</style>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                <fieldset>
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-info card-tabs" >
                                <div class="card-header p-0 pt-1">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                               href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                               aria-selected="true">Data Header Transaction</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                               href="#custom-tabs-one-profile" role="tab"
                                               aria-controls="custom-tabs-one-profile" aria-selected="false">Schedule Payment</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                               href="#custom-tabs-two-profile" role="tab"
                                               aria-controls="custom-tabs-one-profile" aria-selected="false">Data Addendum</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                                               href="#custom-tabs-three-profile" role="tab"
                                               aria-controls="custom-tabs-three-profile" aria-selected="false">Administration Document</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="custom-tabs-one-tabContent" style="padding-left: 5px; padding-right: 5px;">
                                    {{--General--}}
                                    <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                        <form action="{{ URL::route('marketing.leaseagreement.editdatapsm') }}" method="post">
                                        @csrf
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <label>Document*</label>
                                                <input type="text" name="PSM_TRANS_NOCHAR" value="{{$dataPSM->PSM_TRANS_NOCHAR}}" class="form-control" id="psm_trans_nochar" readonly>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <input type="hidden" name="PSM_TRANS_ID_INT" value="<?php echo $dataPSM->PSM_TRANS_ID_INT ?>" class="form-control" id="psm_trans_id" readonly="yes">
                                            @if($dataPSM->LOT_STOCK_NO == NULL)
                                            <div class="col-lg-11">
                                                <div class="form-group">
                                                    <label>Lot Number*</label>
                                                    <select id="LOT_STOCK_NO" name="LOT_STOCK_NO[]" class="form-control select2" multiple="multiple" data-placeholder="Select Lot" style="width: 100%;">                                        
                                                        @foreach($lotData as $data)
                                                        <option value="{{ $data->LOT_STOCK_ID_INT }}">
                                                            {{ $data->LOT_STOCK_NO }} - SQM RT : {{ (float) $data->LOT_STOCK_SQM }} - SQM SC : {{ (float) $data->LOT_STOCK_SQM_SC }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-1" style="padding-right: 25px; padding-top: 31px;">
                                                <div class="form-group">
                                                    <a href="javascript:void(0)" class="btn btn-info" name="btnsavelot" id="saveLot" style="float: right; margin-bottom: 20px;">
                                                        Save
                                                    </a>
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-12" style="padding-bottom: 20px;">
                                                <label>Lot Number</label>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <table class="table-striped table-hover compact" id="data_rentsclot_table" cellspacing="0" width="100%">
                                                            <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Lot</th>
                                                                <th>SQM RT</th>
                                                                <th>SQM SC</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php $no = 1; ?>
                                                            @foreach($lotData as $data)
                                                                <tr>
                                                                    <td style="width: 5px;">{{ $no }}</td>
                                                                    <td>{{ $data->LOT_STOCK_NO }}</td>
                                                                    <td>{{ (float) $data->LOT_STOCK_SQM }}</td>
                                                                    <td>{{ (float) $data->LOT_STOCK_SQM_SC }}</td>
                                                                </tr>
                                                                <?php $no += 1; ?>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Tenant*</label>
                                                    <input type="text" class="form-control" value="{{$tenantData->MD_TENANT_NAME_CHAR}}" id="tenant_name" name="MD_TENANT_NAME_CHAR" placeholder="Tenant" readonly>
                                                    <input type="hidden" class="form-control" value="{{$tenantData->MD_TENANT_ID_INT}}" name="MD_TENANT_ID_INT" id="tenant_id" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Shop Name*</label>
                                                    <input type="text" class="form-control" value="{{$dataPSM->SHOP_NAME_CHAR}}" name="SHOP_NAME_CHAR" placeholder="Shop Name">
                                                </div>
                                            </div>
                                            @if($dataPSM->PSM_CATEGORY_ID_INT == 0)
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Shop Type*</label>
                                                    <input type="text" class="form-control" id="shop_type" name="PSM_CATEGORY_NAME" placeholder="Shop Type" readonly="yes">
                                                    <input type="hidden" class="form-control" id="shop_type_id" name="PSM_CATEGORY_ID_INT" value="0" readonly="yes">
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Shop Type*</label>
                                                    <input type="text" name="PSM_CATEGORY_NAME" value="<?php echo $categoryData->PSM_CATEGORY_NAME; ?>" class="form-control" id="shop_type" placeholder="Shop Type" readonly>
                                                    <input type="hidden" name="PSM_CATEGORY_ID_INT" value="<?php echo $categoryData->PSM_CATEGORY_ID_INT; ?>" class="form-control" id="shop_type_id" placeholder="Shop Type" readonly>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Type*</label>
                                                    <input type="text" class="form-control" id="sales_desc" placeholder="Type" readonly value="<?php echo $salesTypedata->MD_SALES_TYPE_DESC?>" name="MD_SALES_TYPE_DESC">
                                                    <input type="hidden" class="form-control" id="sales_id" readonly value="<?php echo $salesTypedata->MD_SALES_TYPE_ID_INT?>" name="MD_SALES_TYPE_ID_INT">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Booking Date*</label>
                                                    <input type="date" value="{{$dataPSM->PSM_TRANS_BOOKING_DATE}}" class="form-control" id="startDate" name="PSM_TRANS_BOOKING_DATE" placeholder="Booking Date" readonly="yes">
                                                </div>
                                            </div>
                                            @if($dataPSM->PSM_TRANS_BILLING_INT == 0)
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Start Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date" <?php echo count($dataRentSCAmt) > 0 ? "readonly" : "" ?>>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Start Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date" readonly="yes">
                                                    </div>
                                                </div>
                                            @endif
                                            @if($dataPSM->PSM_TRANS_BILLING_INT == 0)
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>End Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date" <?php echo count($dataRentSCAmt) > 0 ? "readonly" : "" ?>>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>End Date*</label>
                                                        <input type="date" value="{{$dataPSM->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date" readonly="yes">
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Virtual Account*</label>
                                                    <input type="number" class="form-control" name="PSM_TRANS_VA" id="PSM_TRANS_VA" placeholder="Virtual Account" value="<?php echo $dataPSM->PSM_TRANS_VA; ?>" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if($dataPSM->PSM_TRANS_DP_BILLING_INT == 0)
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Down Payment(%)*</label>
                                                    <input type="text" name="PSM_TRANS_DP_PERSEN" value="<?php echo number_format($dataPSM->PSM_TRANS_DP_PERSEN,2,'.',''); ?>" class="form-control" placeholder="Down Payment(%)">
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Down Payment(%)*</label>
                                                    <input type="text" name="PSM_TRANS_DP_PERSEN" value="<?php echo number_format($dataPSM->PSM_TRANS_DP_PERSEN,2,'.','.'); ?>" class="form-control" placeholder="Down Payment(%)" readonly="yes">
                                                </div>
                                            </div>
                                            @endif
                                            @if($dataPSM->PSM_TRANS_DP_BILLING_INT == 0)
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Down Payment Period (Month)*</label>
                                                    <input type="number" name="PSM_TRANS_DP_PERIOD" value="<?php echo $dataPSM->PSM_TRANS_DP_PERIOD; ?>" class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Down Payment Period (Month)*</label>
                                                    <input type="number" name="PSM_TRANS_DP_PERIOD" value="<?php echo $dataPSM->PSM_TRANS_DP_PERIOD; ?>" class="form-control" placeholder="0" readonly="yes">
                                                </div>
                                            </div>
                                            @endif
                                            @if($dataPSM->PSM_TRANS_BILLING_INT == 0)
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" value="<?php echo $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED; ?>" class="form-control" placeholder="Time Period">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" value="<?php echo $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED; ?>" class="form-control" placeholder="Time Period" readonly>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                    <div class="col-lg-12">
                                                        <h6><b>Rent / Service Charge Amount: </b></h6><br>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Year *</label>
                                                            <input type="number" name="YEAR_INT" class="form-control" placeholder="Enter Year" id="year_int" value="<?php echo date('Y'); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Rent Amount (For Month) / m2 *</label>
                                                            <input type="number" name="RENT_AMOUNT" class="form-control" placeholder="Enter Rent Amount" id="rent_amount" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Service Charge Amount (For Month) / m2 *</label>
                                                            <input type="number" name="SC_AMOUNT" value="0" class="form-control" placeholder="Enter Service Charge Amount" id="sc_amount">
                                                        </div>
                                                        <a href="javascript:void(0)" class="btn btn-info" name="btnsaverentscamt" id="saveRentScAmt" style="float: right; margin-bottom: 20px;">
                                                            Insert
                                                        </a>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <table class="table-striped table-hover compact" id="data_rentscamt_table" cellspacing="0" width="100%">
                                                            <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Year</th>
                                                                <th>Rent Amt (For Month) / m2</th>
                                                                <th>SC Amt (For Month) / m2</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php $no = 1; ?>
                                                            @foreach($dataRentSCAmt as $data)
                                                                <tr>
                                                                    <td style="text-align: right; width: 5px;">{{ $no }}</td>
                                                                    <td>{{ $data->PSM_TRANS_PRICE_YEAR }}</td>
                                                                    <td>{{ number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,',','.') }}</td>
                                                                    <td>{{ number_format($data->PSM_TRANS_PRICE_SC_NUM,0,',','.') }}</td>
                                                                    <td style="text-align: center;">
                                                                        <i class='fa fa-trash' title='Delete Data' onclick='delItemRentSCAmt(<?php echo $data->PSM_TRANS_PRICE_ID_INT; ?>);'></i>
                                                                    </td>
                                                                </tr>
                                                                <?php $no += 1; ?>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                    <div class="col-lg-5">
                                                        <div class="form-group">
                                                            <label>Disc (%) *</label>
                                                            <input type="number" class="form-control" name="DISC_PERSEN" placeholder="Enter Disc (%)" id="disc_persen" value="<?php echo (float) $dataPSM->PSM_TRANS_DISKON_PERSEN; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5">
                                                        <div class="form-group">
                                                            <label>Disc Amount *</label>
                                                            <input type="number" name="DISC_AMT" value="{{ (float) $dataPSM->PSM_TRANS_DISKON_NUM }}" class="form-control" placeholder="Enter Disc Amount" id="disc_amt">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1" style="padding-right: 25px; padding-top: 31px;">
                                                        <div class="form-group">
                                                            <a href="javascript:void(0)" class="btn btn-info" name="btnsavedisc" id="saveDisc" style="float: right; margin-bottom: 20px;">
                                                                Save
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Generate Schedule*</label>
                                                    <select name="PSM_TRANS_GENERATE_BILLING" class="form-control" id="PSM_TRANS_GENERATE_BILLING" disabled>
                                                        <option value="1" <?php if ($dataPSM->PSM_TRANS_GENERATE_BILLING == 1) {echo "selected";} ?>>Automatically</option>
                                                        <option value="0" <?php if ($dataPSM->PSM_TRANS_GENERATE_BILLING == 0) {echo "selected";} ?>>Manual</option>
                                                    </select>
                                                    <input type="hidden" name="PSM_TRANS_GENERATE_BILLING" value="<?php echo $dataPSM->PSM_TRANS_GENERATE_BILLING; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Price Before Tax*</label>
                                                    <input type="text" name="PSM_TRANS_NET_BEFORE_TAX" value="<?php echo number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX,0,'','.'); ?>" class="form-control" id="net_before_tax" placeholder="Price Before Tax" readonly>
                                                    <input type="hidden" name="PSM_TRANS_NET_BEFORE_TAX_REAL" value="<?php echo $dataPSM->PSM_TRANS_NET_BEFORE_TAX; ?>" class="form-control" id="net_before_tax_real" placeholder="Price Before Tax" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Price Tax*</label>
                                                    <input type="text" class="form-control" id="price_tax" name="PSM_TRANS_PPN" placeholder="Price Tax" value="<?php echo number_format($dataPSM->PSM_TRANS_PPN,0,'','.'); ?>" readonly="yes">
                                                    <input type="hidden" class="form-control" id="price_tax_real" name="PSM_TRANS_PPN_REAL" value="<?php echo $dataPSM->PSM_TRANS_PPN; ?>" readonly="yes">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Price After Tax*</label>
                                                <input type="text" class="form-control" id="price_total" name="PSM_TRANS_PRICE" placeholder="Price After Tax" value="<?php echo number_format($dataPSM->PSM_TRANS_PRICE,0,'','.'); ?>" readonly>
                                                <input type="hidden" class="form-control" id="price_total_real" name="PSM_TRANS_PRICE_REAL" placeholder="Price After Tax" value="<?php echo $dataPSM->PSM_TRANS_PRICE; ?>" readonly>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Grace Period Type</label>
                                                    <select class="form-control" name="PSM_TRANS_GRASS_TYPE">
                                                        <option value="">Please Choose</option>
                                                        <option value="SOT" {{$dataPSM->PSM_TRANS_GRASS_TYPE == "SOT" ? "selected" : ""}}>Start of Contract</option>
                                                        <option value="EOT" {{$dataPSM->PSM_TRANS_GRASS_TYPE == "EOT" ? "selected" : ""}}>End of Contract</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Grace Period (Month)</label>
                                                    <input type="number" name="PSM_TRANS_GRASS_PERIOD" value="<?php echo $dataPSM->PSM_TRANS_GRASS_PERIOD; ?>" class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Bank Garansi</label>
                                                    <input type="text" name="PSM_BANK_GARANSI_NOCHAR" class="form-control" placeholder="Bank Garansi" value="<?php echo $dataPSM->PSM_BANK_GARANSI_NOCHAR; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Bank Garansi Amount</label>
                                                    <input type="number" name="PSM_BANK_GARANSI" value="<?php echo $dataPSM->PSM_BANK_GARANSI; ?>" class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                            @if($dataPSM->MD_TENANT_TAX_ID_INT == 0)
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Address Tax*</label>
                                                    <input type="text" class="form-control" id="tenant_address_tax" name="MD_TENANT_ADDRESS_TAX" readonly="yes">
                                                    <input type="hidden" class="form-control" id="tenant_address_tax_id" name="MD_TENANT_TAX_ID_INT" value="0" readonly="yes">
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Address Tax*</label>
                                                    <input type="text" class="form-control" name="MD_TENANT_ADDRESS_TAX" id="tenant_address_tax" value="<?php echo $addressTaxData->MD_TENANT_ADDRESS_TAX; ?>" readonly>
                                                    <input type="hidden" class="form-control" name="MD_TENANT_TAX_ID_INT" id="tenant_address_tax_id" value="<?php echo $addressTaxData->MD_TENANT_TAX_ID_INT; ?>" readonly>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h6><b>Security Deposit: </b></h6><br>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Security Deposit</label>
                                                    <input type="text" class="form-control" placeholder="Security Deposit Type" id="deposit_desc" readonly>
                                                    <input type="hidden" class="form-control" id="deposit_type" readonly>
                                                    <input type="hidden" class="form-control" id="insert_id" readonly value="1">
                                                    <input type="hidden" class="form-control" id="deposit_id" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Security Deposit Amount</label>
                                                    <input type="number" name="PSM_TRANS_DEPOSIT_NUM" class="form-control" placeholder="0" id="deposit_num">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label for="Security Deposit Date">Security Deposit Date</label>
                                                    <input type="date" value="" class="form-control" id="deposit_date" name="PSM_TRANS_DEPOSIT_DATE" placeholder="Security Deposit Date">
                                                </div>
                                                <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="updateSecureDep" style="float: right;margin-bottom: 10px;">
                                                    Insert/Update
                                                </a>
                                            </div>
                                            <div class="col-lg-12">
                                                <table class="table-striped table-hover compact" id="data_securedep_table" cellspacing="0" width="100%">
                                                    <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Security Deposit</th>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $no = 1; ?>
                                                    @foreach($dataSecureDep as $secureDep)
                                                        <tr>
                                                            <td style="text-align: right; width: 5px;">{{$no}}</td>
                                                            <td>{{$secureDep->PSM_SECURE_DEP_TYPE_DESC}}</td>
                                                            <td>{{$secureDep->PSM_TRANS_DEPOSIT_DATE}}</td>
                                                            <td>{{number_format($secureDep->PSM_TRANS_DEPOSIT_NUM,0,',','.')}}</td>
                                                            @if($secureDep->INVOICE_STATUS_INT == 0)
                                                            <td style="text-align:center;">
                                                                <i class='fa fa-edit' title='Edit Data' onclick='getItemSecureDep(<?php echo $secureDep->PSM_SECURE_DEP_ID_INT; ?>);'></i>|
                                                                <i class='fa fa-trash' title='Delete Data' onclick='delItemSecureDep(<?php echo $secureDep->PSM_SECURE_DEP_ID_INT; ?>);'></i>
                                                            </td>
                                                            @else
                                                                <td style="text-align:center;"></td>
                                                            @endif
                                                        </tr>
                                                        <?php $no += 1; ?>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <br><br>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h6><b>Especially for the type of Revenue Sharing :</b></h6><br>
                                                <h6><b>Revenue Sharing Rate :</b></h6>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Minimum Amount Charge</label>
                                                    <input type="number" name="PSM_MIN_AMT" value="<?php echo $dataPSM->PSM_MIN_AMT; ?>" class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="form-group">
                                                    <label>Low Amount</label>
                                                    <input type="number" name="PSM_REVENUE_LOW_NUM" class="form-control" placeholder="0" value="<?php echo $dataPSM->PSM_REVENUE_LOW_NUM; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Low Rate (%)</label>
                                                    <input type="number" name="PSM_REVENUE_LOW_RATE" class="form-control" placeholder="0" value="<?php echo $dataPSM->PSM_REVENUE_LOW_RATE; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-group">
                                                    <label>High Amount</label>
                                                    <input type="number" name="PSM_REVENUE_HIGH_NUM" value="<?php echo $dataPSM->PSM_REVENUE_HIGH_NUM; ?>" class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>High Rate (%)</label>
                                                    <input type="number" name="PSM_REVENUE_HIGH_RATE" value="<?php echo $dataPSM->PSM_REVENUE_HIGH_RATE; ?>" class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <h6><b>Investment :</b></h6>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-group">
                                                    <label>Investment Amount</label>
                                                    <input type="number" name="PSM_INVEST_NUM" value="<?php echo $dataPSM->PSM_INVEST_NUM; ?>" class="form-control" placeholder="0" id="PSM_INVEST_NUM">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Investment Rate (%)</label>
                                                    <input type="number" name="PSM_INVEST_RATE" value="<?php echo $dataPSM->PSM_INVEST_RATE; ?>" class="form-control" placeholder="0" id="PSM_INVEST_RATE">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Description Payment*</label>
                                                    <textarea class="form-control form-control-sm" name="PSM_TRANS_DESCRIPTION" id="PSM_TRANS_DESCRIPTION" placeholder="Description" readonly>
                                                        <?php echo $dataPSM->PSM_TRANS_DESCRIPTION; ?>
                                                    </textarea>
                                                    <script>
                                                        CKEDITOR.replace('PSM_TRANS_DESCRIPTION');
                                                    </script>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <a class="btn btn-sm btn-danger" href="{{ URL('/marketing/leaseagreement/viewlistdatanew/') }}">
                                                        <i>
                                                            << Back to List
                                                        </i>
                                                    </a>
                                                    <a href="#confModal" class="btn btn-primary" data-toggle="modal" style="float: right;">
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
                                                                    <input type="submit" value="Save Data" class="btn btn-primary">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                    {{--Schedule--}}
                                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                        <br>
                                        @if($dataPSM->PSM_TRANS_NET_BEFORE_TAX > 0)
                                        <div class="row">
                                            @if($dataPSM->MD_SALES_TYPE_ID_INT == 1) {{--Rental--}}
                                                @if($dataPSM->PSM_TRANS_DP_PERSEN > 0 && $dataPSM->PSM_TRANS_DP_BILLING_INT == 0)
                                                    <div class="col-lg-2">
                                                        <a href="#generateModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="btn btn-sm btn-warning" data-toggle="modal">
                                                            <i>
                                                                Generate Schedule DP
                                                            </i>
                                                        </a>
                                                        <div id="generateModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="modal fade">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Confirmation</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <p>Are you sure generate schedule DP?</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                        <a href="{{ URL('/marketing/leaseagreement/generatescheddatapsmdp/'. $dataPSM->PSM_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    @if($dataPSM->PSM_TRANS_BILLING_INT == 0)
                                                        <div class="col-lg-3">
                                                        <a href="#generateModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="btn btn-sm btn-success" data-toggle="modal">
                                                            <i>
                                                                Generate Schedule Rental
                                                            </i>
                                                        </a>
                                                        <div id="generateModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="modal fade">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Confirmation</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <p>Are you sure generate schedule ?</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                        <a href="{{ URL('/marketing/leaseagreement/generatescheddatapsm/'. $dataPSM->PSM_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endif
                                                @if($cekBayarSchedDP == 0)
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <a href="#delSchedModalDP{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                                <i>
                                                                    Delete Schedule DP
                                                                </i>
                                                            </a>
                                                            <div id="delSchedModalDP{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="modal fade">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Confirmation</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <p>Are you sure delete schedule DP?</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                            <a href="{{ URL('/marketing/leaseagreement/deletescheddatapsmdp/'. $dataPSM->PSM_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($cekBayarSched == 0)
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <a href="#delSchedModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                                <i>
                                                                    Delete Schedule
                                                                </i>
                                                            </a>
                                                            <div id="delSchedModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="modal fade">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Confirmation</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <p>Are you sure delete schedule ?</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                            <a href="{{ URL('/marketing/leaseagreement/deletescheddatapsm/'. $dataPSM->PSM_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-warning" href="{{ URL('/marketing/leaseagreement/viewscheddiscount/' . $dataPSM->PSM_TRANS_ID_INT) }}">
                                                            <i>
                                                                Add Data Discount
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @if(count($dataReqRevenueSharing) <= 0)
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-warning" href="{{ URL('/marketing/leaseagreement/viewrequestrevenuesharing/' . $dataPSM->PSM_TRANS_ID_INT) }}">
                                                            <i>
                                                                Add Request Revenue Sharing
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @endif
                                            @elseif($dataPSM->MD_SALES_TYPE_ID_INT == 3) {{--Casual Leasing--}}
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-warning" href="{{ URL('/marketing/leaseagreement/viewscheddiscount/' . $dataPSM->PSM_TRANS_ID_INT) }}">
                                                            <i>
                                                                Add Data Discount
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <form action="{{ URL::route('marketing.leaseagreement.uploadbillingschedule') }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6><b>Input Data Schedule</b></h6>
                                                <br>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Start Date*</label>
                                                    <input type="date" id="stDate" value="" class="form-control" name="TGL_SCHEDULE_ST_DATE" placeholder="Start Date">
                                                    <input type="hidden" class="form-control" placeholder="Description" id="insert_id" readonly value="1" name="insert_id">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>End Date*</label>
                                                    <input type="date" id="enDate" value="" class="form-control" name="TGL_SCHEDULE_EN_DATE" placeholder="End Date">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Trx. Type*</label>
                                                    <input type="text" class="form-control" placeholder="Trx. Type" id="trx_desc" readonly>
                                                    <input type="hidden" class="form-control" placeholder="Trx. Type" id="trx_code" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Description*</label>
                                                    <input type="text" name="DESC_CHAR" class="form-control" placeholder="Description" id="desc_char">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="Amount*">Amount*</label>
                                                    <input type="number" name="BASE_AMOUNT_NUM" class="form-control" placeholder="Amount" id="base_amount">
                                                </div>
                                                <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="update" style="float: right;">
                                                    Insert Schedule
                                                </a>
                                            </div>
                                        </div>
                                        </form>
                                        <br>
                                        @endif
                                        <div class="row">
                                            <div class="col-12 col-sm-12">
                                                <div class="card card-primary card-tabs">
                                                    <div class="card-header p-0 pt-1">
                                                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" id="all-tab" data-toggle="pill" href="#all" role="tab" aria-controls="all" aria-selected="true">All</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="casualLeasing-tab" data-toggle="pill" href="#casualLeasing" role="tab" aria-controls="casualLeasing" aria-selected="false">Casual Leasing</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="downPayment-tab" data-toggle="pill" href="#downPayment" role="tab" aria-controls="downPayment" aria-selected="false">Down Payment</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="rental-tab" data-toggle="pill" href="#rental" role="tab" aria-controls="rental" aria-selected="false">Rental</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="serviceCharge-tab" data-toggle="pill" href="#serviceCharge" role="tab" aria-controls="serviceCharge" aria-selected="false">Service Charge</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="tab-content" id="custom-tabs-one-tabContent">
                                                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <form action="{{ URL::route('marketing.leaseagreement.voidschedule') }}" method="post">
                                                                        @csrf
                                                                        <input type="hidden" name="PSM_TRANS_NOCHAR1" class="form-control" id="psm_trans_nochar" value="<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>" readonly>
                                                                        <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                                                            <thead>
                                                                            <tr>
                                                                                <th style="text-align: center;">No.</th>
                                                                                <th style="text-align: center;">Check</th>
                                                                                <th style="text-align: center;">Date</th>
                                                                                <th style="text-align: center;">Trx. Code</th>
                                                                                <th style="text-align: center;">Description</th>
                                                                                <th style="text-align: center;">Base Amount</th>
                                                                                <th style="text-align: center;">Discount</th>
                                                                                <th style="text-align: center;">DPP</th>
                                                                                <th style="text-align: center;">Tax Amount</th>
                                                                                <th style="text-align: center;">Invoice Amount</th>
                                                                                <th style="text-align: center;">Status</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php
                                                                            $i = 1;
                                                                            $totalBase = 0;
                                                                            $totalDisc = 0;
                                                                            $totalDPP = 0;
                                                                            $totalTax = 0;
                                                                            $totalInv = 0;
                                                                            ?>
                                                                            @foreach($scheduleData as $data)
                                                                                <tr>
                                                                                    <td>{{$i}}</td>
                                                                                    @if($data->SCHEDULE_STATUS_INT == 0 || $data->SCHEDULE_STATUS_INT > 1)
                                                                                        <td style="text-align: center;"></td>
                                                                                    @else
                                                                                        <td style="text-align: center;"><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>)" value="<?php echo $data->PSM_SCHEDULE_ID_INT;  ?>" id="idbilling"></td>
                                                                                    @endif
                                                                                    <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                                                                    <td>{{$data->TRX_CODE}}</td>
                                                                                    <td><a href="javascript:void(0)" onclick="editDataDesc('<?php echo $data->PSM_SCHEDULE_ID_INT ?>','<?php echo $data->DESC_CHAR ?>')">{{$data->DESC_CHAR}}</a></td>
                                                                                    <td style="text-align: right;">{{number_format($data->BASE_AMOUNT_NUM,0,'','.')}}</td>
                                                                                    <td style="text-align: right;">{{number_format($data->DISC_NUM,0,'','.')}}</td>
                                                                                    <td style="text-align: right;">{{number_format(($data->BASE_AMOUNT_NUM - $data->DISC_NUM),0,'','.')}}</td>
                                                                                    <td style="text-align: right;">{{number_format($data->PPN_PRICE_NUM,0,'','.')}}</td>
                                                                                    <td style="text-align: right;">{{number_format($data->BILL_AMOUNT,0,'','.')}}</td>
                                                                                    <td style="text-align: center;">
                                                                                        @if($data->SCHEDULE_STATUS_INT == 0)
                                                                                            VOID
                                                                                        @elseif($data->SCHEDULE_STATUS_INT == 1)
                                                                                            ACTIVE
                                                                                        @elseif($data->SCHEDULE_STATUS_INT == 2)
                                                                                            INVOICE
                                                                                        @elseif($data->SCHEDULE_STATUS_INT == 3)
                                                                                            PAID
                                                                                        @else
                                                                                            ''
                                                                                        @endif
                                                                                    </td>
                                                                                    @if($data->SCHEDULE_STATUS_INT == 1)

                                                                                    @endif
                                                                                </tr>
                                                                                <?php
                                                                                $i += 1;
                                                                                $totalBase += $data->BASE_AMOUNT_NUM;
                                                                                $totalDisc += $data->DISC_NUM;
                                                                                $totalDPP += ($data->BASE_AMOUNT_NUM - $data->DISC_NUM);
                                                                                $totalTax += $data->PPN_PRICE_NUM;
                                                                                $totalInv += $data->BILL_AMOUNT;
                                                                                ?>
                                                                            @endforeach
                                                                            </tbody>
                                                                            <tfooter>
                                                                                <tr>
                                                                                    <td><b>TOTAL</b></td>
                                                                                    <td><b>-</b></td>
                                                                                    <td><b>-</b></td>
                                                                                    <td><b>-</b></td>
                                                                                    <td><b>-</b></td>
                                                                                    <td style="text-align: right;"><b>{{number_format($totalBase,0,'','.')}}</b></td>
                                                                                    <td style="text-align: right;"><b>{{number_format($totalDisc,0,'','.')}}</b></td>
                                                                                    <td style="text-align: right;"><b>{{number_format($totalDPP,0,'','.')}}</b></td>
                                                                                    <td style="text-align: right;"><b>{{number_format($totalTax,0,'','.')}}</b></td>
                                                                                    <td style="text-align: right;"><b>{{number_format($totalInv,0,'','.')}}</b></td>
                                                                                    <td></td>
                                                                                </tr>
                                                                            </tfooter>
                                                                        </table>
                                                                        <div id="temp-form">
                                                                            <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                                                            <input type="hidden" name="billing" value="0" class="form-control" id="all">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            <a class="btn btn-sm btn-danger" href="{{ URL('/marketing/leaseagreement/viewlistdatanew/') }}">
                                                                                <i>
                                                                                    << Back to List
                                                                                </i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    @if($dataPSM->PSM_TRANS_NET_BEFORE_TAX > 0)
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            <a href="#confModal1" class="btn btn-primary" data-toggle="modal" style="float: right;">
                                                                                Void Data
                                                                            </a>
                                                                            <div id="confModal1" class="modal fade">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">Confirmation</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <p>Are you sure void this data ?</p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                                            <button class="btn btn-success" type="submit">Yes</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="casualLeasing" role="tabpanel" aria-labelledby="casualLeasing-tab">
                                                                <table class="table-striped table-hover compact" id="vendor_table_cl" cellspacing="0" width="100%">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="text-align: center;">No.</th>
                                                                        <th style="text-align: center;">Date</th>
                                                                        <th style="text-align: center;">Trx. Code</th>
                                                                        <th style="text-align: center;">Description</th>
                                                                        <th style="text-align: center;">Base Amount</th>
                                                                        <th style="text-align: center;">Discount</th>
                                                                        <th style="text-align: center;">DPP</th>
                                                                        <th style="text-align: center;">Tax Amount</th>
                                                                        <th style="text-align: center;">Invoice Amount</th>
                                                                        <th style="text-align: center;">Status</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $i = 1;
                                                                    $totalBase = 0;
                                                                    $totalDisc = 0;
                                                                    $totalDPP = 0;
                                                                    $totalTax = 0;
                                                                    $totalInv = 0;
                                                                    ?>
                                                                    @foreach($scheduleDataCL as $data)
                                                                        <tr>
                                                                            <td>{{$i}}</td>
                                                                            <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                                                            <td>{{$data->TRX_CODE}}</td>
                                                                            <td>{{$data->DESC_CHAR}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BASE_AMOUNT_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->DISC_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format(($data->BASE_AMOUNT_NUM - $data->DISC_NUM),0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->PPN_PRICE_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BILL_AMOUNT,0,'','.')}}</td>
                                                                            <td style="text-align: center;">
                                                                                @if($data->SCHEDULE_STATUS_INT == 0)
                                                                                    VOID
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 1)
                                                                                    ACTIVE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 2)
                                                                                    INVOICE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 3)
                                                                                    PAID
                                                                                @else
                                                                                    ''
                                                                                @endif
                                                                            </td>
                                                                            @if($data->SCHEDULE_STATUS_INT == 1)

                                                                            @endif
                                                                        </tr>
                                                                        <?php
                                                                        $i += 1;
                                                                        $totalBase += $data->BASE_AMOUNT_NUM;
                                                                        $totalDisc += $data->DISC_NUM;
                                                                        $totalDPP += ($data->BASE_AMOUNT_NUM - $data->DISC_NUM);
                                                                        $totalTax += $data->PPN_PRICE_NUM;
                                                                        $totalInv += $data->BILL_AMOUNT;
                                                                        ?>
                                                                    @endforeach
                                                                    </tbody>
                                                                    <tfooter>
                                                                        <tr>
                                                                            <td><b>TOTAL</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalBase,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDisc,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDPP,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalTax,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalInv,0,'','.')}}</b></td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tfooter>
                                                                </table>
                                                            </div>
                                                            <div class="tab-pane fade" id="downPayment" role="tabpanel" aria-labelledby="downPayment-tab">
                                                                <table class="table-striped table-hover compact" id="vendor_table_dp" cellspacing="0" width="100%">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="text-align: center;">No.</th>
                                                                        <th style="text-align: center;">Date</th>
                                                                        <th style="text-align: center;">Trx. Code</th>
                                                                        <th style="text-align: center;">Description</th>
                                                                        <th style="text-align: center;">Base Amount</th>
                                                                        <th style="text-align: center;">Discount</th>
                                                                        <th style="text-align: center;">DPP</th>
                                                                        <th style="text-align: center;">Tax Amount</th>
                                                                        <th style="text-align: center;">Invoice Amount</th>
                                                                        <th style="text-align: center;">Status</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $i = 1;
                                                                    $totalBase = 0;
                                                                    $totalDisc = 0;
                                                                    $totalDPP = 0;
                                                                    $totalTax = 0;
                                                                    $totalInv = 0;
                                                                    ?>
                                                                    @foreach($scheduleDataDP as $data)
                                                                        <tr>
                                                                            <td>{{$i}}</td>
                                                                            <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                                                            <td>{{$data->TRX_CODE}}</td>
                                                                            <td>{{$data->DESC_CHAR}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BASE_AMOUNT_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->DISC_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format(($data->BASE_AMOUNT_NUM - $data->DISC_NUM),0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->PPN_PRICE_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BILL_AMOUNT,0,'','.')}}</td>
                                                                            <td style="text-align: center;">
                                                                                @if($data->SCHEDULE_STATUS_INT == 0)
                                                                                    VOID
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 1)
                                                                                    ACTIVE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 2)
                                                                                    INVOICE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 3)
                                                                                    PAID
                                                                                @else
                                                                                    ''
                                                                                @endif
                                                                            </td>
                                                                            @if($data->SCHEDULE_STATUS_INT == 1)

                                                                            @endif
                                                                        </tr>
                                                                        <?php
                                                                        $i += 1;
                                                                        $totalBase += $data->BASE_AMOUNT_NUM;
                                                                        $totalDisc += $data->DISC_NUM;
                                                                        $totalDPP += ($data->BASE_AMOUNT_NUM - $data->DISC_NUM);
                                                                        $totalTax += $data->PPN_PRICE_NUM;
                                                                        $totalInv += $data->BILL_AMOUNT;
                                                                        ?>
                                                                    @endforeach
                                                                    </tbody>
                                                                    <tfooter>
                                                                        <tr>
                                                                            <td><b>TOTAL</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalBase,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDisc,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDPP,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalTax,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalInv,0,'','.')}}</b></td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tfooter>
                                                                </table>
                                                            </div>
                                                            <div class="tab-pane fade" id="rental" role="tabpanel" aria-labelledby="rental-tab">
                                                                <table class="table-striped table-hover compact" id="vendor_table_rt" cellspacing="0" width="100%">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="text-align: center;">No.</th>
                                                                        <th style="text-align: center;">Date</th>
                                                                        <th style="text-align: center;">Trx. Code</th>
                                                                        <th style="text-align: center;">Description</th>
                                                                        <th style="text-align: center;">Base Amount</th>
                                                                        <th style="text-align: center;">Discount</th>
                                                                        <th style="text-align: center;">DPP</th>
                                                                        <th style="text-align: center;">Tax Amount</th>
                                                                        <th style="text-align: center;">Invoice Amount</th>
                                                                        <th style="text-align: center;">Status</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $i = 1;
                                                                    $totalBase = 0;
                                                                    $totalDisc = 0;
                                                                    $totalDPP = 0;
                                                                    $totalTax = 0;
                                                                    $totalInv = 0;
                                                                    ?>
                                                                    @foreach($scheduleDataRT as $data)
                                                                        <tr>
                                                                            <td>{{$i}}</td>
                                                                            <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                                                            <td>{{$data->TRX_CODE}}</td>
                                                                            <td>{{$data->DESC_CHAR}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BASE_AMOUNT_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->DISC_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format(($data->BASE_AMOUNT_NUM - $data->DISC_NUM),0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->PPN_PRICE_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BILL_AMOUNT,0,'','.')}}</td>
                                                                            <td style="text-align: center;">
                                                                                @if($data->SCHEDULE_STATUS_INT == 0)
                                                                                    VOID
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 1)
                                                                                    ACTIVE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 2)
                                                                                    INVOICE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 3)
                                                                                    PAID
                                                                                @else
                                                                                    ''
                                                                                @endif
                                                                            </td>
                                                                            @if($data->SCHEDULE_STATUS_INT == 1)

                                                                            @endif
                                                                        </tr>
                                                                        <?php
                                                                        $i += 1;
                                                                        $totalBase += $data->BASE_AMOUNT_NUM;
                                                                        $totalDisc += $data->DISC_NUM;
                                                                        $totalDPP += ($data->BASE_AMOUNT_NUM - $data->DISC_NUM);
                                                                        $totalTax += $data->PPN_PRICE_NUM;
                                                                        $totalInv += $data->BILL_AMOUNT;
                                                                        ?>
                                                                    @endforeach
                                                                    </tbody>
                                                                    <tfooter>
                                                                        <tr>
                                                                            <td><b>TOTAL</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalBase,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDisc,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDPP,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalTax,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalInv,0,'','.')}}</b></td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tfooter>
                                                                </table>
                                                            </div>
                                                            <div class="tab-pane fade" id="serviceCharge" role="tabpanel" aria-labelledby="serviceCharge-tab">
                                                                <table class="table-striped table-hover compact" id="vendor_table_sc" cellspacing="0" width="100%">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="text-align: center;">No.</th>
                                                                        <th style="text-align: center;">Date</th>
                                                                        <th style="text-align: center;">Trx. Code</th>
                                                                        <th style="text-align: center;">Description</th>
                                                                        <th style="text-align: center;">Base Amount</th>
                                                                        <th style="text-align: center;">Discount</th>
                                                                        <th style="text-align: center;">DPP</th>
                                                                        <th style="text-align: center;">Tax Amount</th>
                                                                        <th style="text-align: center;">Invoice Amount</th>
                                                                        <th style="text-align: center;">Status</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    $i = 1;
                                                                    $totalBase = 0;
                                                                    $totalDisc = 0;
                                                                    $totalDPP = 0;
                                                                    $totalTax = 0;
                                                                    $totalInv = 0;
                                                                    ?>
                                                                    @foreach($scheduleDataSC as $data)
                                                                        <tr>
                                                                            <td>{{$i}}</td>
                                                                            <td>{{$data->TGL_SCHEDULE_DATE}}</td>
                                                                            <td>{{$data->TRX_CODE}}</td>
                                                                            <td>{{$data->DESC_CHAR}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BASE_AMOUNT_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->DISC_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format(($data->BASE_AMOUNT_NUM - $data->DISC_NUM),0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->PPN_PRICE_NUM,0,'','.')}}</td>
                                                                            <td style="text-align: right;">{{number_format($data->BILL_AMOUNT,0,'','.')}}</td>
                                                                            <td style="text-align: center;">
                                                                                @if($data->SCHEDULE_STATUS_INT == 0)
                                                                                    VOID
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 1)
                                                                                    ACTIVE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 2)
                                                                                    INVOICE
                                                                                @elseif($data->SCHEDULE_STATUS_INT == 3)
                                                                                    PAID
                                                                                @else
                                                                                    ''
                                                                                @endif
                                                                            </td>
                                                                            @if($data->SCHEDULE_STATUS_INT == 1)

                                                                            @endif
                                                                        </tr>
                                                                        <?php
                                                                        $i += 1;
                                                                        $totalBase += $data->BASE_AMOUNT_NUM;
                                                                        $totalDisc += $data->DISC_NUM;
                                                                        $totalDPP += ($data->BASE_AMOUNT_NUM - $data->DISC_NUM);
                                                                        $totalTax += $data->PPN_PRICE_NUM;
                                                                        $totalInv += $data->BILL_AMOUNT;
                                                                        ?>
                                                                    @endforeach
                                                                    </tbody>
                                                                    <tfooter>
                                                                        <tr>
                                                                            <td><b>TOTAL</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td><b>-</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalBase,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDisc,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalDPP,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalTax,0,'','.')}}</b></td>
                                                                            <td style="text-align: right;"><b>{{number_format($totalInv,0,'','.')}}</b></td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tfooter>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                    {{--Data Addendum--}}
                                    <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <a class="btn btn-sm btn-success" href="{{ URL('/marketing/leaseagreement/viewaddaddendum/RNW/' . $dataPSM->PSM_TRANS_ID_INT) }}">
                                                        <i>
                                                            Renewal Agreement
                                                        </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <br>
                                                <table class="table-striped table-hover compact" id="addendum_table" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>Addendum</th>
                                                            <th>Tenant</th>
                                                            <th>Shop Name</th>
                                                            <th>Doc. Type</th>
                                                            <th>Status</th>
                                                            <th>View/Edit</th>
                                                            <th>Cancel</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $no = 1; ?>
                                                    @foreach($dataListAddendum as $addendum)
                                                        <tr>
                                                            <td>{{$no}}</td>
                                                            <td>{{$addendum->PSM_TRANS_ADD_NOCHAR}}</td>
                                                            <td>{{$addendum->MD_TENANT_NOCHAR}}</td>
                                                            <td>{{$addendum->SHOP_NAME_CHAR}}</td>
                                                            <td>{{$addendum->MD_ADD_TYPE}}</td>
                                                            @if($addendum->PSM_TRANS_ADD_STATUS_INT == 1)
                                                                <td>REQUEST</td>
                                                            @elseif($addendum->PSM_TRANS_ADD_STATUS_INT == 2)
                                                                <td>APPROVE</td>
                                                            @else
                                                                <td>NONE</td>
                                                            @endif
                                                            @if($addendum->PSM_TRANS_ADD_STATUS_INT == 1)
                                                                <td class="center">
                                                                    <a class="btn btn-sm btn-warning" href="{{ URL('/marketing/leaseagreement/vieweditaddendum/'.$addendum->PSM_ADD_DOC_TYPE.'/' . $addendum->PSM_TRANS_ADD_ID_INT) }}">
                                                                        <i>
                                                                            View/Edit
                                                                        </i>
                                                                    </a>
                                                                </td>
                                                            @else
                                                                <td class="center">
                                                                    <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                                        <i>
                                                                            View/Edit
                                                                        </i>
                                                                    </a>
                                                                </td>
                                                            @endif
                                                            @if($addendum->PSM_TRANS_ADD_STATUS_INT == 1)
                                                                <td class="center">
                                                                    <a href="#cancelModal{!!$addendum->PSM_TRANS_ADD_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                                        <i>
                                                                            Cancel
                                                                        </i>
                                                                    </a>
                                                                    <div id="cancelModal{!!$addendum->PSM_TRANS_ADD_ID_INT!!}" class="modal fade">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Confirmation</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="form-group">
                                                                                        <p>Are you sure cancel this document ?</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                                    <a href="{{ URL('/marketing/leaseagreement/canceldataAddendum/'. $addendum->PSM_TRANS_ADD_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @else
                                                                <td class="center">
                                                                    <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                                        <i>
                                                                            Cancel
                                                                        </i>
                                                                    </a>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        <?php $no++; ?>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <a class="btn btn-sm btn-danger" href="{{ URL('/marketing/leaseagreement/viewlistdatanew/') }}">
                                                        <i>
                                                            << Back to List
                                                        </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Administration Document --}}
                                    <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                        <form action="{{ URL::route('marketing.leaseagreement.editdataadmindoc') }}" method="post">
                                        @csrf
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Document*</label>
                                                    <input type="text" name="PSM_TRANS_NOCHAR" value="<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>" class="form-control" id="psm_trans_nochar" readonly="yes">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-4">
                                               <div class="form-group">
                                                    <label>BAST Document*</label>
                                                    <input type="text" class="form-control" name="NO_BAST_NOCHAR" value="<?php echo $dataPSM->NO_BAST_NOCHAR; ?>" readonly>
                                                </div>
                                                @if($dataPSM->NO_BAST_NOCHAR == '' || $dataPSM->NO_BAST_DATE == '')
                                                    <div class="form-group">
                                                        <a href="#BASTModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="btn btn-sm btn-info" data-toggle="modal">
                                                            <i>
                                                                Generate BAST
                                                            </i>
                                                        </a>
                                                        <div id="BASTModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="modal fade">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Confirmation</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <p>Are you sure generate this document ?</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                        <a href="{{ URL('/marketing/leaseagreement/getnumberbast/'. $dataPSM->PSM_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-info" href="{{ URL('/marketing/leaseagreement/printbast/' . $dataPSM->PSM_TRANS_ID_INT) }}" onclick="window.open(this.href).print(); return false">
                                                            <i>
                                                                Print BAST
                                                            </i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Lease Agreement*</label>
                                                    <input type="text" class="form-control" name="NO_KONTRAK_NOCHAR" value="<?php echo $dataPSM->NO_KONTRAK_NOCHAR; ?>" readonly="yes">
                                                </div>
                                                @if($dataPSM->NO_KONTRAK_NOCHAR == '' || $dataPSM->NO_KONTRAK_DATE == '')
                                                    <div class="form-group">
                                                        <a href="#PSMModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="btn btn-sm btn-info" data-toggle="modal">
                                                            <i>
                                                                Generate Lease Agreement
                                                            </i>
                                                        </a>
                                                        <div id="PSMModal{!!$dataPSM->PSM_TRANS_ID_INT!!}" class="modal fade">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Confirmation</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <p>Are you sure generate this document ?</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                        <a href="{{ URL('/marketing/leaseagreement/getnumberleaseagreement/'. $dataPSM->PSM_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-info" href="{{ URL('/marketing/confirmationletter/print_sks/' . $dataPSM->PSM_TRANS_ID_INT) }}" onclick="window.open(this.href).print(); return false">
                                                            <i>
                                                                Print Lease Agreement
                                                            </i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Lease Agreement Date*</label>
                                                    <input type="date" value="{{$dataPSM->NO_KONTRAK_DATE}}" class="form-control" id="psmDate" name="NO_KONTRAK_DATE">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-4">

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <br>
                                                <div class="form-group">
                                                    <a class="btn btn-sm btn-danger" href="{{ URL('/marketing/leaseagreement/viewlistdatanew/') }}">
                                                        <i>
                                                            << Back to List
                                                        </i>
                                                    </a>
                                                    <a href="#confModalAdmin" class="btn btn-primary" data-toggle="modal" style="float: right;">
                                                        Save Data
                                                    </a>
                                                    <div id="confModalAdmin" class="modal fade">
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
                                                                    <input type="submit" value="Save Data" class="btn btn-primary">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-desc-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Edit Description</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="{{route('marketing.leaseagreement.editdescschedule')}}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-5">Description *</label>
                    <div class="col-sm-12">
                        <input type="hidden" class="form-control" name="SCHEDULE_ID_EDIT" id="SCHEDULE_ID_EDIT" style="width: 100%;" placeholder="Schedule ID" readonly required />
                        <input type="text" class="form-control" name="DESCRIPTION_EDIT" id="DESCRIPTION_EDIT" style="width: 100%;" placeholder="Description" required />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Edit</button>
            </div>
        </form>
    </div>
  </div>
</div>
<div class="col-md-3 col-sm-offset-10">
    <div id="trxModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transaction Code</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="trx_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Transaction Code</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataInvType as $invType)
                            <tr>
                                <td>{{$invType->INVOICE_TRANS_TYPE_DESC}}</td>
                                <td>{{$invType->INVOICE_TRANS_TYPE}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-offset-10">
    <div id="secureDepModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Security Deposit Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="secure_dep_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($secureType as $data)
                            <tr>
                                <td>{{$data->PSM_SECURE_DEP_TYPE_CODE}}</td>
                                <td>{{$data->PSM_SECURE_DEP_TYPE_DESC}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-offset-10">
    <div id="shopTypeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="shop_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataCategory as $data)
                            <tr>
                                <td>{{$data->PSM_CATEGORY_NAME}}</td>
                                <td>{{$data->PSM_CATEGORY_ID_INT}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-offset-10">
    <div id="addressTaxModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Address Tax</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="address_tax_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Address</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataAddressTax as $dataTax)
                            <tr>
                                <td>{{$dataTax->MD_TENANT_ADDRESS_TAX}}</td>
                                <td>{{$dataTax->MD_TENANT_TAX_ID_INT}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#LOT_STOCK_NO').select2();
    });

    function editDataDesc(PSM_SCHEDULE_ID_INT, DESC_CHAR) {
        $("#SCHEDULE_ID_EDIT").val(PSM_SCHEDULE_ID_INT);
        $("#DESCRIPTION_EDIT").val(DESC_CHAR);
        $('#edit-desc-modal').modal('show');
    }
</script>
@endsection


