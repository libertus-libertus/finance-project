<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Tampilkan daftar invoice.
     */
    public function index()
    {
        $invoices = Invoice::orderBy('invoice_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data invoice berhasil dimuat.',
            'data' => $invoices
        ]);
    }

    /**
     * Tampilkan detail invoice.
     */
    public function show(Invoice $invoice)
    {
        // Ambil data invoice beserta semua data pembayarannya
        $invoice->load('payments');

        return response()->json([
            'success' => true,
            'message' => 'Detail data invoice berhasil dimuat.',
            'data' => $invoice
        ]);
    }
}
