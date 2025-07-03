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
      <td style="text-align: center; font-size: 5%;"><b>TICKET PURCHASE</b></td>
    </tr>
  </tbody>
</table>
<hr />
<table style="width: 100%; border-collapse: collapse;" border="0">
   <tbody>
      <tr>
        <td style="width: 25%; font-size: 5%;">Date</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ date('d/m/Y H:i:s', strtotime($dataTicketTrans[0]->created_at)) }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Cashier</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTicketTrans[0]->CASHIER_NAME_CHAR }}</td>
      </tr>
      <tr>
        <td style="width: 25%; font-size: 5%;">Trx Num</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ $dataTicketTrans[0]->TRANS_TICKET_NOCHAR }}</td>
      </tr>
   </tbody>
</table>
<hr />
<table style="border-collapse: collapse; width: 100%;" border="0">
   <tbody>
      <tr style="border-style: solid; border-width: thin;">
         <th style="text-align: left; font-size: 5%;"><strong>ITEM</strong></td>
         <th style="text-align: right; font-size: 5%;"><strong>QTY</strong></td>
         <th style="text-align: right; font-size: 5%;"><strong>AMT</strong></td>
      </tr>
      <tr>
         <td style="text-align: left; font-size: 5%;">Ticket ({{ $dataTicketTrans[0]->MD_PRICE_TICKET_DESC }})</td>
         <td style="text-align: right; font-size: 5%;">{{ $dataTicketTrans[0]->QTY_TICKET_INT }}</td>
         <td style="text-align: right; font-size: 5%;">{{ number_format($dataTicketTrans[0]->PRICE_REAL_NUM, 0, ",", ".") }}</td>
      </tr>
      @if($dataTicketTrans[0]->TICKET_FREE_INT > 0)
      <tr>
         <td style="text-align: left; font-size: 5%;">Free Ticket ({{ $dataTicketTrans[0]->MD_PRICE_TICKET_DESC }})</td>
         <td style="text-align: right; font-size: 5%;">{{ $dataTicketTrans[0]->TICKET_FREE_INT }}</td>
         <td style="text-align: right; font-size: 5%;">0</td>
      </tr>
      @endif
   </tbody>
</table>
<hr />
<table style="width: 100%; border-collapse: collapse;" border="0">
   <tbody>
      <tr>
        <td style="width: 40%; font-size: 5%;">Total Bill</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTicketTrans[0]->PRICE_REAL_NUM, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Discount</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTicketTrans[0]->PRICE_REAL_NUM - $dataTicketTrans[0]->TOTAL_PRICE_NUM, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Total Paid</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTicketTrans[0]->TOTAL_PAID_NUM, 0, ",", ".") }}</td>
      </tr>
      <tr>
        <td style="width: 40%; font-size: 5%;">Change</td>
        <td style="width: 0.1%; font-size: 5%;">:</td>
        <td style="width: 100%; font-size: 5%; text-align: right;">{{ number_format($dataTicketTrans[0]->TOTAL_CHANGE_NUM, 0, ",", ".") }}</td>
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