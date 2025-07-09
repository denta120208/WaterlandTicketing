<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwimmingTicketApiController extends Controller
{
   
    private function isValidTicketFormat($number_ticket) 
    {
       
        return preg_match('/^\d{4}$/', $number_ticket) || preg_match('/^\d{10}$/', $number_ticket);
    }

    // 1. API cek data tiket
   public function checkTicket(Request $request)
{
    $number_ticket = $request->input('number_ticket');

    // Validasi format nomor tiket
    if (!$this->isValidTicketFormat($number_ticket)) {
        return response()->json([
            'status' => false,
            'message' => 'Format nomor tiket tidak valid. Nomor tiket harus 4 atau 10 digit'
        ]);
    }

    $data = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
        ->where('NUMBER_TICKET', $number_ticket)
        ->first();

    if ($data) {
        return response()->json([
             'message'       => 'Tiket ditemukan',
            'number_ticket' => $data->NUMBER_TICKET,
            'gate_in'       => $data->gate_in,
            'gate_out'      => $data->gate_out,
            'is_synced'     => $data->is_synced,
            'sync_at'       => $data->sync_at,
             'cekout_at'     => $data->CekOut,
            'scan_at'       => $data->SCAN_AT,
            'scan_by'       => $data->SCAN_BY,
            'is_scan'       => $data->IS_SCAN,
           
        ]);
    } else {
        return response()->json([
            'status'  => false,
            'message' => 'Tiket tidak ditemukan'
        ]);
    }
}
    // 2. API cek in
    public function checkIn(Request $request)
    {
        $number_ticket = $request->input('number_ticket');
        $scan_by = $request->input('scan_by');
        $scan_at = $request->input('scan_at');
        $gate_in = $request->input('gate_in');
        if (!$scan_at) {
            $scan_at = now();
        }

        // Validasi format nomor tiket
        if (!$this->isValidTicketFormat($number_ticket)) {
            return response()->json([
                'status' => false,
                'message' => 'Format nomor tiket tidak valid. Nomor tiket harus 4 atau 10 digit'
            ]);
        }

        // Cek dulu apakah tiket ada
        $ticket = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('NUMBER_TICKET', $number_ticket)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Tiket tidak ditemukan'
            ]);
        }

        // Cek apakah tiket sudah di scan
        if ($ticket->IS_SCAN == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Tiket sudah di scan sebelumnya'
            ]);
        }

        // Jika tiket ada dan belum di scan, lakukan update
        $update = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('NUMBER_TICKET', $number_ticket)
            ->update([
                'IS_SCAN' => 1,
                'SCAN_BY' => $scan_by,
                'SCAN_AT' => $scan_at,
                'gate_in' => $gate_in,
            ]);

        // Ambil data terbaru setelah update
        $updatedTicket = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('NUMBER_TICKET', $number_ticket)
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Cek in berhasil',
            'scan_at' => $updatedTicket->SCAN_AT,
            'gate_in' => $updatedTicket->gate_in ?? null,
            'data' => $updatedTicket
        ]);
    }

    // 3. API cek out
    public function checkOut(Request $request)
    {
        $number_ticket = $request->input('number_ticket');
        $cekout_at = $request->input('scan_at');
        $gate_out = $request->input('gate_out');
        if (!$cekout_at) {
            $cekout_at = now();
        }
        // Validasi format nomor tiket
        if (!$this->isValidTicketFormat($number_ticket)) {
            return response()->json([
                'status' => false,
                'message' => 'Format nomor tiket tidak valid. Nomor tiket harus 4 atau 10 digit'
            ]);
        }

        // Cek dulu apakah tiket ada
        $ticket = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('NUMBER_TICKET', $number_ticket)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Tiket tidak ditemukan'
            ]);
        }

        // Cek apakah tiket sudah di scan masuk
        if ($ticket->IS_SCAN != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Tiket belum di scan masuk'
            ]);
        }

        // Cek apakah sudah checkout sebelumnya
        if ($ticket->CekOut != null) {
            return response()->json([
                'status' => false,
                'message' => 'Tiket sudah di checkout sebelumnya'
            ]);
        }

        // Update kolom CekOut dengan waktu dari request dan gate_out
        $update = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('NUMBER_TICKET', $number_ticket)
            ->update([
                'CekOut' => $cekout_at,
                'gate_out' => $gate_out,
            ]);

        // Ambil data terbaru setelah update
        $updatedTicket = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('NUMBER_TICKET', $number_ticket)
            ->first();
        return response()->json([
            'status' => true,
            'message' => 'Cek out berhasil',
            'cekout_at' => $updatedTicket->CekOut,
            'gate_out' => $updatedTicket->gate_out ?? null,
            'data' => $updatedTicket
        ]);
    }

    // 4. API gate check (GET, hanya tampilkan array number_ticket saja, limit 100)
public function gateCheck(Request $request)
{
    
    $numberTickets = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
        ->where('is_synced', 0)
        ->limit(500)
        ->pluck('NUMBER_TICKET')
        ->toArray();

    if (!empty($numberTickets)) {
        // Update semua tiket yang diambil menjadi is_synced = 1 dan sync_at = now()
        DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->whereIn('NUMBER_TICKET', $numberTickets)
            ->update([
                'is_synced' => 1,
                'sync_at' => now()
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Gate check berhasil',
            'data' => $numberTickets
        ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Tidak ada tiket yang perlu di-sync',
            'data' => []
        ]);
    }
}
}