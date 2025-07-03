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
      <td style="text-align: center; font-size: 5%;"><b>POS</b></td>
    </tr>
  </tbody>
</table>
<hr />
<table style="width: 100%; border-collapse: collapse;" border="0">
   <tbody>
      <tr>
        <td style="width: 25%; font-size: 5%;">Date</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ date('d/m/Y H:i:s', strtotime($dataTransPOS->created_at)) }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Cashier</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTransPOS->CASHIER_NAME_CHAR }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Customer</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTransPOS->CUSTOMER_NAME_CHAR }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Trx Num</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTransPOS->TRANS_POS_NO_CHAR }}</td>
      </tr>
   </tbody>
</table>
<hr />
<table style="border-collapse: collapse; width: 100%;" border="0">
   <tbody>
      <tr style="border-style: solid; border-width: thin;">
         <th style="text-align: left; font-size: 5%;"><strong>ITEM</strong></td>
         <th style="text-align: left; font-size: 5%;"><strong>QTY</strong></td>
         <th style="text-align: right; font-size: 5%;"><strong>AMT</strong></td>
      </tr>
      <?php $totalPriceReal = 0; ?>
      @foreach($dataTransPOSDetails as $data)
      <tr>
         <td style="text-align: left; font-size: 5%;">{{ $data->MD_PRODUCT_POS_DESC_CHAR }}</td>
         <td style="text-align: right; font-size: 5%;">{{ $data->QTY_INT }}</td>
         <td style="text-align: right; font-size: 5%;">{{ number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".") }}</td>
      </tr>
      <?php $totalPriceReal += $data->TOTAL_HARGA_FLOAT; ?>
      @endforeach
   </tbody>
</table>
<hr />
<table style="width: 100%; border-collapse: collapse;" border="0">
   <tbody>
      <tr>
        <td style="width: 40%; font-size: 5%;">Item Count</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransPOS->QTY_INT, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Item Count (Free)</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <?php
          $countTransPOSDetails = $dataTransPOS->QTY_INT - count($dataTransPOSDetails);
          $countTransPOSDetails = $countTransPOSDetails < 0 ? $countTransPOSDetails * -1 : $countTransPOSDetails;
        ?>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($countTransPOSDetails, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Total Bill</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransPOS->TOTAL_HARGA_FLOAT, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Discount</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($totalPriceReal - $dataTransPOS->TOTAL_HARGA_FLOAT, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Total Paid</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransPOS->TOTAL_PAID_FLOAT, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Change</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTransPOS->TOTAL_CHANGE_FLOAT, 0, ",", ".") }}</td>
      </tr>
   </tbody>
</table>
<hr />
<p style="text-align: center; font-size: 5%;">THANK YOU</p>
<p style="text-align: center; font-size: 5%;">{{ strtoupper($dataProject->PROJECT_NAME) }}</p>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    window.print();
  });
</script>