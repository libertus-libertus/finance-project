<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TrialBalanceController extends Controller
{
    /**
     * Dapatkan data Neraca Saldo
     */
    private function getTrialBalanceData($startDate, $endDate)
    {
        $openingBalances = [
            '1101' => 5000000.00, '1201' => 2000000.00, '2101' => 1500000.00,
            '4101' => 0.00, '5101' => 0.00, '6101' => 100000.00,
        ];

        $movements = ChartOfAccount::where('is_active', true)
            ->leftJoin('journal_lines', function($join) use ($startDate, $endDate) {
                $join->on('chart_of_accounts.id', '=', 'journal_lines.account_id')
                     ->join('journals', 'journal_lines.journal_id', '=', 'journals.id')
                     ->whereBetween('journals.posting_date', [$startDate, $endDate]);
            })
            ->select(
                'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.normal_balance',
                DB::raw('COALESCE(SUM(journal_lines.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_lines.credit), 0) as total_credit')
            )
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.normal_balance')
            ->orderBy('chart_of_accounts.code', 'asc')
            ->get();

        return $movements->map(function ($account) use ($openingBalances) {
            $openingBalance = $openingBalances[$account->code] ?? 0;
            $totalDebit = (float) $account->total_debit;
            $totalCredit = (float) $account->total_credit;
            $closingBalance = 0;

            if ($account->normal_balance == 'DR') {
                $closingBalance = $openingBalance + $totalDebit - $totalCredit;
            } else {
                $closingBalance = $openingBalance - $totalDebit + $totalCredit;
            }

            return [
                'account_code' => $account->code, 'account_name' => $account->name,
                'opening_balance' => $openingBalance, 'debit' => $totalDebit,
                'credit' => $totalCredit, 'closing_balance' => $closingBalance,
            ];
        });
    }

    /**
     * Tampilkan laporan Neraca Saldo.
     */
    public function view(Request $request)
    {
        $startDate = $request->input('start_date', '2025-07-01');
        $endDate = $request->input('end_date', '2025-07-31');
        $reportData = $this->getTrialBalanceData($startDate, $endDate);

        return response()->json([
            'success' => true, 'message' => 'Laporan Trial Balance berhasil dibuat.',
            'data' => $reportData
        ]);
    }

    /**
     * Unduh / Print laporan Neraca Saldo dalam format PDF.
     */
    public function downloadPdf(Request $request)
    {
        $startDate = $request->input('start_date', '2025-07-01');
        $endDate = $request->input('end_date', '2025-07-31');
        $data['reportData'] = $this->getTrialBalanceData($startDate, $endDate);
        $data['period'] = "Period: $startDate to $endDate";

        $pdf = Pdf::loadView('report.trial_balance_pdf', $data);
        return $pdf->download('trial-balance-'.$startDate.'-'.$endDate.'.pdf');
    }
}
