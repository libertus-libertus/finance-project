<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\JournalLine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jurnal 1: Opening accrual
        $journal1 = Journal::create([
            'ref_no' => 'JV-2025-0001',
            'posting_date' => '2025-07-01',
            'memo' => 'Opening accrual',
        ]);

        JournalLine::create([
            'journal_id' => $journal1->id,
            'account_id' => ChartOfAccount::where('code', '6101')->first()->id, // Accrued Expense
            'debit' => 100000.00,
            'credit' => 0,
        ]);

        JournalLine::create([
            'journal_id' => $journal1->id,
            'account_id' => ChartOfAccount::where('code', '2101')->first()->id, // Accounts Payable
            'debit' => 0,
            'credit' => 100000.00,
        ]);


        // Jurnal 2: Sales cash
        $journal2 = Journal::create([
            'ref_no' => 'JV-2025-0002',
            'posting_date' => '2025-07-15',
            'memo' => 'Sales cash',
        ]);

        JournalLine::create([
            'journal_id' => $journal2->id,
            'account_id' => ChartOfAccount::where('code', '1101')->first()->id, // Cash
            'debit' => 2800000.00,
            'credit' => 0,
        ]);

        JournalLine::create([
            'journal_id' => $journal2->id,
            'account_id' => ChartOfAccount::where('code', '4101')->first()->id, // Revenue
            'debit' => 0,
            'credit' => 2800000.00,
        ]);


        // Jurnal 3: Utilities expense
        $journal3 = Journal::create([
            'ref_no' => 'JV-2025-0003',
            'posting_date' => '2025-07-20',
            'memo' => 'Utilities expense',
        ]);

        JournalLine::create([
            'journal_id' => $journal3->id,
            'account_id' => ChartOfAccount::where('code', '5101')->first()->id, // Expense
            'debit' => 1200000.00,
            'credit' => 0,
        ]);

        JournalLine::create([
            'journal_id' => $journal3->id,
            'account_id' => ChartOfAccount::where('code', '1101')->first()->id, // Cash
            'debit' => 0,
            'credit' => 1200000.00,
        ]);
    }
}
