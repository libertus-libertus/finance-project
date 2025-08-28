<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invoice::create([
            'invoice_no' => 'INV-2025-0005',
            'invoice_date' => '2025-07-10',
            'customer' => 'PT Maju Jaya',
            'amount' => 1800000.00,
            'tax_amount' => 198000.00,
            'status' => 'partial',
        ]);

        Invoice::create([
            'invoice_no' => 'INV-2025-0006',
            'invoice_date' => '2025-07-18',
            'customer' => 'CV Cemerlang',
            'amount' => 900000.00,
            'tax_amount' => 99000.00,
            'status' => 'open',
        ]);

        Invoice::create([
            'invoice_no' => 'INV-2025-0007',
            'invoice_date' => '2025-07-25',
            'customer' => 'PT Sejahtera',
            'amount' => 500000.00,
            'tax_amount' => 55000.00,
            'status' => 'paid',
        ]);
    }
}
