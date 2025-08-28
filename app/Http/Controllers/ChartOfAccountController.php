<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ChartOfAccount;

class ChartOfAccountController extends Controller
{
    /**
     * Tampilkan daftar akun.
     */
    public function index()
    {
        $accounts = ChartOfAccount::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar data akun berhasil dimuat.',
            'data' => $accounts
        ]);
    }

    /**
     * Buat & simpan akun baru.
     */
    public function store(Request $request)
    {
        // Validasi inputan
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:10|unique:chart_of_accounts,code',
            'name' => 'required|string|max:100',
            'normal_balance' => 'required|in:DR,CR',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buat data baru
        $account = ChartOfAccount::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Akun baru berhasil ditambahkan.',
            'data' => $account
        ], 201);
    }

    /**
     * Tampilkan detail akun.
     */
    public function show(ChartOfAccount $chartOfAccount)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data akun berhasil dimuat.',
            'data' => $chartOfAccount
        ]);
    }

    /**
     * Ubah data akun yang ditentukan.
     */
    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:10|unique:chart_of_accounts,code,' . $chartOfAccount->id,
            'name' => 'required|string|max:100',
            'normal_balance' => 'required|in:DR,CR',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update data
        $chartOfAccount->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data akun berhasil diperbarui.',
            'data' => $chartOfAccount
        ]);
    }

    /**
     * Hapus akun yang ditentukan.
     */
    public function destroy(ChartOfAccount $chartOfAccount)
    {
        $chartOfAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data akun berhasil dihapus.'
        ], 200);
    }
}
