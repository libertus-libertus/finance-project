<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JournalController extends Controller
{
    /**
     * Tampilkan daftar jurnal.
     */
    public function index()
    {
        // Ambil semua jurnal, hitung total debit & kredit dari relasinya
        $journals = Journal::withSum('journalLines', 'debit')
                            ->withSum('journalLines', 'credit')
                            ->orderBy('posting_date', 'desc')
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data jurnal berhasil dimuat.',
            'data' => $journals
        ]);
    }

    /**
     * Tampilkan detail jurnal.
     */
    public function show(Journal $journal)
    {
        // Ambil satu jurnal beserta semua baris/detailnya
        $journal->load('journalLines.account');

        return response()->json([
            'success' => true,
            'message' => 'Detail data jurnal berhasil dimuat.',
            'data' => $journal
        ]);
    }

    /**
     * Buat & simpan jurnal baru.
     */
    public function store(Request $request)
    {
        // Validasi data header dan array dari lines
        $validator = Validator::make($request->all(), [
            'posting_date' => 'required|date',
            'memo' => 'nullable|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Hitung total debit dan kredit
        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return response()->json(['success' => false, 'message' => 'Total Debit dan Credit tidak seimbang.'], 422);
        }

        // Simpan data menggunakan Transaction
        try {
            DB::transaction(function () use ($request) {
                // Buat nomor referensi otomatis (contoh sederhana)
                $latestJournal = Journal::orderBy('id', 'desc')->first();
                $nextId = $latestJournal ? $latestJournal->id + 1 : 1;
                $refNo = 'JV-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                // Simpan header jurnal
                $journal = Journal::create([
                    'posting_date' => $request->posting_date,
                    'memo' => $request->memo,
                    'ref_no' => $refNo,
                ]);

                // Simpan setiap baris detail
                foreach ($request->lines as $line) {
                    // Hanya simpan jika ada nilai debit atau kredit
                    if ($line['debit'] > 0 || $line['credit'] > 0) {
                        $journal->journalLines()->create([
                            'account_id' => $line['account_id'],
                            'debit' => $line['debit'],
                            'credit' => $line['credit'],
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Jurnal berhasil disimpan.'], 201);

        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan pesan error
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus jurnal yang ditentukan.
     */
    public function destroy(Journal $journal)
    {
        $journal->delete();
        return response()->json([
            'success' => true, 
            'message' => 'Data jurnal berhasil dihapus.'
        ]);
    }
}
