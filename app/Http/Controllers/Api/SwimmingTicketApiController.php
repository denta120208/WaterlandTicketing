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
        $scan_by = $request->input('scan_by');

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
            // Update IS_SCAN menjadi 1 jika tiket ditemukan
            DB::table('TRANS_TICKET_PURCHASE_DETAILS')
                ->where('NUMBER_TICKET', $number_ticket)
                ->update([
                    'IS_SCAN' => 1,
                    'SCAN_BY' => $scan_by,
                    'SCAN_AT' => now(),
                ]);

            // Ambil data terbaru setelah update
            $updatedData = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
                ->where('NUMBER_TICKET', $number_ticket)
                ->first();

            return response()->json([
                'status' => true,
                'message' => 'Tiket ditemukan dan berhasil di scan',
                'scan_result' => $updatedData->scan_result ?? null,
                'data' => $updatedData
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tiket tidak ditemukan'
            ]);
        }
    }

    // 2. API cek in
    public function checkIn(Request $request)
    {
        $number_ticket = $request->input('number_ticket');
        $scan_by = $request->input('scan_by');
        $scan_at = now();

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
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Cek in berhasil'
        ]);
    }

    // 3. API cek out
    public function checkOut(Request $request)
    {
        $number_ticket = $request->input('number_ticket');
        
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

        $update = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
            ->where('NUMBER_TICKET', $number_ticket)
            ->update([
                'CekOut' => now(),
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Cek out berhasil'
        ]);
    }

    // 4. API gate check (cek is_synced)
    public function gateCheck(Request $request)
    {
        $number_ticket = $request->input('number_ticket');

        // Jika nomor tiket diberikan, cek tiket spesifik
        if ($number_ticket) {
            // Validasi format nomor tiket
            if (!$this->isValidTicketFormat($number_ticket)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Format nomor tiket tidak valid. Nomor tiket harus 4 atau 10 digit'
                ]);
            }

            // Cek tiket spesifik
            $data = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
                ->where('NUMBER_TICKET', $number_ticket)
                ->where('is_synced', 0)
                ->first();

           // berikan respon gagal
            if ($number_ticket === '8051344597') {
                return response()->json([
                    'status' => false,
                    'message' => 'Tiket ini tidak dapat diproses'
                ]);
            }
        } else {
            // Jika tidak ada nomor tiket, ambil tiket pertama yang belum di-sync
            $data = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
                ->where('is_synced', 0)
                ->first();
        }

        if ($data) {
            // Update is_synced menjadi 1
            $update = DB::table('TRANS_TICKET_PURCHASE_DETAILS')
                ->where('TRANS_TICKET_DETAIL_ID_INT', $data->TRANS_TICKET_DETAIL_ID_INT)
                ->update([
                    'is_synced' => 1,
                    'sync_at' => now()
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Gate check berhasil',
                'data' => [
                    'ticket_id' => $data->TRANS_TICKET_DETAIL_ID_INT,
                    'number_ticket' => $data->NUMBER_TICKET,
                    'is_synced' => 1,
                    'sync_at' => now()
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada tiket yang perlu di-sync'
            ]);
        }
    }
} 