<?php
    use \koolreport\barcode\QRCode;
?>
<style>
  table {
    font-family: Arial, Helvetica, sans-serif;
  }
  tr {
    font-family: Arial, Helvetica, sans-serif;
  }
  td {
    font-family: Arial, Helvetica, sans-serif;
  }
  /* @page {
    size: 38mm 56mm;
    margin: 0;
  } */
  @page {
    size: 38mm 73mm;
    margin: 0;
  }
  /* @page {
    size: 55mm 73mm;
    margin: 0;
  } */
</style>

<title>Print Receipt Water Group</title>

<table border="0" style="width: 100%; border-collapse: collapse;">
  <tbody>
    <tr>
      <td style="text-align: center; font-size: 60%;"><b>RECEIPT</b></td>
    </tr>
    <tr>
      <td style="text-align: center; font-size: 5%;"><b>SWIMMING EQUIPMENT</b></td>
    </tr>
  </tbody>
</table>
<hr />
<table style="width: 100%; border-collapse: collapse;" border="0">
   <tbody>
      <tr>
        <td style="width: 25%; font-size: 5%;">Date</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ date('d/m/Y H:i:s', strtotime($dataTransSwimmingEquipment->created_at)) }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Cashier</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTransSwimmingEquipment->CASHIER_NAME_CHAR }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Customer</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTransSwimmingEquipment->CUSTOMER_NAME_CHAR }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Trx Num</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTransSwimmingEquipment->TRANS_PERLENGKAPAN_RENANG_NO_CHAR }}</td>
      </tr>
   </tbody>
</table>
<hr />
<table style="border-collapse: collapse; width: 100%;" border="0">
   <tbody>
      <tr style="border-style: solid; border-width: thin;">
         <th style="text-align: left; font-size: 5%;"><strong>ITEM</strong></td>
         <th style="text-align: center; font-size: 5%;"><strong>QTY</strong></td>
         <th style="text-align: right; font-size: 5%;"><strong>AMT</strong></td>
      </tr>
      @foreach($dataTransSwimmingEquipmentDetails as $data)
      <tr>
         <td style="text-align: left; font-size: 5%;">{{ $data->DESC_CHAR }}</td>
         <td style="text-align: center; font-size: 5%;">{{ number_format($data->QTY_INT, 0, ",", ".") }}</td>
         <td style="text-align: right; font-size: 5%;">{{ number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".") }}</td>
      </tr>
      @endforeach
   </tbody>
</table>
<hr />
<table style="width: 100%; border-collapse: collapse;" border="0">
   <tbody>
      <tr>
        <td style="width: 40%; font-size: 5%;">Item Count</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransSwimmingEquipment->QTY_INT, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Total Bill</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransSwimmingEquipment->TOTAL_HARGA_FLOAT, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Total Paid</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransSwimmingEquipment->TOTAL_PAID_FLOAT, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Change</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransSwimmingEquipment->TOTAL_CHANGE_FLOAT, 0, ",", ".") }}</td>
      </tr>
   </tbody>
</table>
<hr />
<p style="text-align: center; font-size: 5%;">THANK YOU</p>
<p style="text-align: center; font-size: 5%;">{{ strtoupper($dataProject->PROJECT_NAME) }}</p>