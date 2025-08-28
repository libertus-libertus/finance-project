@extends('layouts.app')

@section('title', 'Create New Journal')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Create New Journal Entry</h3>
    </div>
    <div class="panel-body">
        <form id="journal-form">
            {{-- Bagian Header Form --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="posting_date">Posting Date</label>
                        <input type="date" id="posting_date" name="posting_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="memo">Memo</label>
                        <input type="text" id="memo" name="memo" class="form-control" placeholder="e.g., Office supplies purchase">
                    </div>
                </div>
            </div>

            <hr>

            {{-- Bagian Detail/Lines Form --}}
            <h4>Journal Lines</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 40%;">Account</th>
                        <th style="width: 25%;">Debit</th>
                        <th style="width: 25%;">Credit</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="journal-lines-body">
                    {{-- Baris akan ditambahkan secara dinamis oleh JavaScript --}}
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right">TOTAL</th>
                        <th class="text-right" id="total-debit">0.00</th>
                        <th class="text-right" id="total-credit">0.00</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <button type="button" class="btn btn-default btn-sm" id="btn-add-line">
                <i class="glyphicon glyphicon-plus"></i> Add Line
            </button>
        </form>
    </div>
    <div class="panel-footer text-right">
        <a href="{{ url('/journals') }}" class="btn btn-default">Cancel</a>
        <button type="button" class="btn btn-primary" id="btn-save-journal">Save Journal</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let accounts = []; // Variabel untuk menyimpan daftar akun dari API

    // 1. Ambil daftar akun (COA) dari API saat halaman dimuat
    $.get('/api/chart-of-accounts', function(response) {
        if (response.success) {
            accounts = response.data;
            // Tambahkan 2 baris awal setelah akun berhasil dimuat
            addNewLine();
            addNewLine();
        }
    });

    // 2. Fungsi untuk membuat dropdown akun
    function createAccountDropdown() {
        let options = '<option value="">Select Account</option>';
        accounts.forEach(function(account) {
            options += `<option value="${account.id}">${account.code} - ${account.name}</option>`;
        });
        return `<select class="form-control account-select">${options}</select>`;
    }

    // 3. Fungsi untuk menambah baris baru ke tabel
    function addNewLine() {
        const newRow = `
            <tr>
                <td>${createAccountDropdown()}</td>
                <td><input type="number" class="form-control debit-input" value="0.00" min="0" step="0.01"></td>
                <td><input type="number" class="form-control credit-input" value="0.00" min="0" step="0.01"></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-remove-line"><i class="glyphicon glyphicon-trash"></i></button></td>
            </tr>
        `;
        $('#journal-lines-body').append(newRow);
    }

    // 4. Event handler untuk tombol "Add Line"
    $('#btn-add-line').click(addNewLine);

    // 5. Event handler untuk tombol hapus baris
    $('#journal-lines-body').on('click', '.btn-remove-line', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // 6. Fungsi untuk menghitung total debit dan kredit
    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        $('#journal-lines-body tr').each(function() {
            totalDebit += parseFloat($(this).find('.debit-input').val()) || 0;
            totalCredit += parseFloat($(this).find('.credit-input').val()) || 0;
        });
        $('#total-debit').text(totalDebit.toFixed(2));
        $('#total-credit').text(totalCredit.toFixed(2));
    }

    // 7. Event handler untuk input debit/kredit agar total otomatis terhitung
    $('#journal-lines-body').on('input', '.debit-input, .credit-input', calculateTotals);

    // 8. Event handler untuk tombol "Save Journal"
    $('#btn-save-journal').click(function() {
        // Kumpulkan data dari form
        const journalData = {
            posting_date: $('#posting_date').val(),
            memo: $('#memo').val(),
            lines: []
        };

        $('#journal-lines-body tr').each(function() {
            const line = {
                account_id: $(this).find('.account-select').val(),
                debit: parseFloat($(this).find('.debit-input').val()) || 0,
                credit: parseFloat($(this).find('.credit-input').val()) || 0,
            };
            // Hanya tambahkan baris yang memiliki account_id
            if (line.account_id) {
                journalData.lines.push(line);
            }
        });

        // Kirim data ke API menggunakan AJAX
        $.ajax({
            url: '/api/journals',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(journalData),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                }).then(() => {
                    window.location.href = '/journals'; // Redirect ke halaman daftar jurnal
                });
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = response.message || 'Terjadi kesalahan.';
                if (response.errors) {
                    errorMessage += '<ul class="text-left">';
                    for (const key in response.errors) {
                        errorMessage += `<li>${response.errors[key][0]}</li>`;
                    }
                    errorMessage += '</ul>';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMessage,
                });
            }
        });
    });
});
</script>
@endpush