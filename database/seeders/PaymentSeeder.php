<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mencari invoice yang akan dibayar
        $invoice1 = Invoice::where('invoice_no', 'INV-2025-0005')->first();
        $invoice3 = Invoice::where('invoice_no', 'INV-2025-0007')->first();

        // Membuat data pembayaran untuk invoice pertama (2 kali pembayaran)
        if ($invoice1) {
            Payment::create([
                'invoice_id' => $invoice1->id,
                'payment_ref' => 'PAY-2025-001',
                'paid_at' => '2025-07-12',
                'amount_paid' => 1000000.00,
                'method' => 'Bank Transfer',
            ]);

            Payment::create([
                'invoice_id' => $invoice1->id,
                'payment_ref' => 'PAY-2025-002',
                'paid_at' => '2025-07-22',
                'amount_paid' => 800000.00,
                'method' => 'Cash',
            ]);
        }

        // Membuat data pembayaran untuk invoice ketiga
        if ($invoice3) {
            Payment::create([
                'invoice_id' => $invoice3->id,
                'payment_ref' => 'PAY-2025-003',
                'paid_at' => '2025-07-28',
                'amount_paid' => 555000.00,
                'method' => 'Bank Transfer',
            ]);
        }
    }
}
