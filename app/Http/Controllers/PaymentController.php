<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tampilkan daftar pembayaran.
     */
    public function index()
    {
        // Ambil semua data pembayaran beserta data invoice terkait
        $payments = Payment::with('invoice')->orderBy('paid_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data pembayaran berhasil dimuat.',
            'data' => $payments
        ]);
    }
}
