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
  @page {
    size: 38mm 56mm;
    margin: 0;
  }
  /* @page {
    size: 55mm 73mm;
    margin: 0;
  } */
</style>

<title>Print Ticket Water Group</title>

@foreach($dataTicketDetails as $data)
<p style="page-break-before: always;">
  <table border="0" style="width: 100%; border-collapse: collapse;">
    <tbody>
      <tr>
        <td style="text-align: center; font-size: 60%;"><b>{{ strtoupper($dataProject->PROJECT_NAME) }}</b></td>
      </tr>
      <tr>
        <td style="text-align: center; font-size: 60%;"><b>{{ $dataPriceTicket->MD_PRICE_TICKET_DESC }}</b></td>
      </tr>
      <tr>
        <td style="text-align: center;">
          <?php
              QRCode::create(array(
                  "format" => "svg",
                  "value" => $data->NUMBER_TICKET,
                  "size" => 70,
              ));
          ?>
        </td>
      </tr>
      <tr>
        <td style="text-align: center; font-size: 60%;"><b>Rp. {{ number_format($dataTicketTrans->PRICE_AMOUNT_TICKET_NUM, 0, ",", ".") }}</b></td>
      </tr>
      <tr>
        <td style="text-align: center; font-size: 60%;"><b>{{ $data->NUMBER_TICKET }}</b></td>
      </tr>
      <!-- <tr>
        <td style="text-align: center; font-size: 60%;"><b>KARTU TAMU {{ $data->TRANS_TICKET_DETAIL_COUNT_INT }}</b></td>
      </tr> -->
      <tr>
        <td style="text-align: center; font-size: 60%;"><b>{{ date('d/m/Y', strtotime($data->created_at)) }}</b></td>
      </tr>
    </tbody>
  </table>
</p>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    window.print();
  });
</script>
@endforeach