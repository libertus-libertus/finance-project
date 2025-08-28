@extends('layouts.app')

@section('title', 'Journals')

@push('styles')
<style>
    .panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Journals</h3>
        <button class="btn btn-primary btn-sm" id="btn-add-journal">
            <i class="glyphicon glyphicon-plus"></i> Tambah Jurnal Baru
        </button>
    </div>
    <div class="panel-body">
        <table id="journal-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th><th>Ref No</th><th>Memo</th><th>Debit Total</th>
                    <th>Credit Total</th><th width="100px">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal untuk Detail Jurnal --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="detail-modal-title">Detail Jurnal</h4>
            </div>
            <div class="modal-body">
                <p><strong>Ref No:</strong> <span id="detail-ref-no"></span></p>
                <p><strong>Date:</strong> <span id="detail-date"></span></p>
                <p><strong>Memo:</strong> <span id="detail-memo"></span></p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                    </thead>
                    <tbody id="detail-lines-table">
                        <!-- Muat detail jurnal -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Form Tambah Jurnal --}}
<div class="modal fade" id="journalFormModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Create New Journal Entry</h4>
            </div>
            <div class="modal-body">
                <form id="journal-form">
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
                    <h4>Journal Lines</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Account</th><th style="width: 25%;">Debit</th>
                                <th style="width: 25%;">Credit</th><th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="journal-lines-body"></tbody>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-journal">Save Journal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let accounts = [];

    // Fungsi format rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number).replace('IDR', '').trim();
    }

    var table = $('#journal-table').DataTable({
        processing: true,
        ajax: { url: '/api/journals', dataSrc: 'data' },
        columns: [
            { data: 'posting_date' }, { data: 'ref_no' }, { data: 'memo' },
            { data: 'journal_lines_sum_debit', render: formatRupiah, className: 'text-right' },
            { data: 'journal_lines_sum_credit', render: formatRupiah, className: 'text-right' },
            {
                data: null, orderable: false, searchable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-xs btn-info btn-detail" data-id="${data.id}"><i class="glyphicon glyphicon-eye-open"></i></button>
                        <button class="btn btn-xs btn-danger btn-delete" data-id="${data.id}"><i class="glyphicon glyphicon-trash"></i></button>
                    `;
                }
            }
        ]
    });

    // Event handler untuk tombol Detail
    $('#journal-table').on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        $.get('/api/journals/' + id, function(response) {
            var journal = response.data;
            $('#detail-ref-no').text(journal.ref_no);
            $('#detail-date').text(journal.posting_date);
            $('#detail-memo').text(journal.memo);

            var linesHtml = '';
            journal.journal_lines.forEach(function(line) {
                linesHtml += `
                    <tr>
                        <td>${line.account.code} - ${line.account.name}</td>
                        <td class="text-right">${formatRupiah(line.debit)}</td>
                        <td class="text-right">${formatRupiah(line.credit)}</td>
                    </tr>
                `;
            });

            $('#detail-lines-table').html(linesHtml);
            $('#detailModal').modal('show');
        });
    });

    // Event handler untuk tombol Delete
    $('#journal-table').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?', text: "Data jurnal ini akan dihapus permanen!",
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/journals/' + id, method: 'DELETE',
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Dihapus!', response.message, 'success');
                    }
                });
            }
        });
    });

    // Bagian Form Tambah Jurnal (Modal)
    $('#btn-add-journal').click(function() {
        $('#journal-form')[0].reset();
        $('#journal-lines-body').empty();
        addNewLine();
        addNewLine();
        calculateTotals();
        $('#journalFormModal').modal('show');
    });

    $.get('/api/chart-of-accounts', function(response) {
        if (response.success) accounts = response.data;
    });

    function createAccountDropdown() {
        let options = '<option value="">Select Account</option>';
        accounts.forEach(account => options += `<option value="${account.id}">${account.code} - ${account.name}</option>`);
        return `<select class="form-control account-select">${options}</select>`;
    }

    function addNewLine() {
        const newRow = `<tr><td>${createAccountDropdown()}</td><td><input type="number" class="form-control debit-input" value="0.00" min="0" step="0.01"></td><td><input type="number" class="form-control credit-input" value="0.00" min="0" step="0.01"></td><td><button type="button" class="btn btn-danger btn-sm btn-remove-line"><i class="glyphicon glyphicon-trash"></i></button></td></tr>`;
        $('#journal-lines-body').append(newRow);
    }

    $('#btn-add-line').click(addNewLine);
    $('#journal-lines-body').on('click', '.btn-remove-line', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    function calculateTotals() {
        let totalDebit = 0, totalCredit = 0;
        $('#journal-lines-body tr').each(function() {
            totalDebit += parseFloat($(this).find('.debit-input').val()) || 0;
            totalCredit += parseFloat($(this).find('.credit-input').val()) || 0;
        });
        $('#total-debit').text(totalDebit.toFixed(2));
        $('#total-credit').text(totalCredit.toFixed(2));
    }

    $('#journal-lines-body').on('input', '.debit-input, .credit-input', calculateTotals);

    $('#btn-save-journal').click(function() {
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
            if (line.account_id) journalData.lines.push(line);
        });

        $.ajax({
            url: '/api/journals', method: 'POST',
            contentType: 'application/json', data: JSON.stringify(journalData),
            success: function(response) {
                $('#journalFormModal').modal('hide');
                table.ajax.reload();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = response.message || 'Terjadi kesalahan.';
                if (response.errors) {
                    errorMessage += '<ul class="text-left">';
                    for (const key in response.errors) errorMessage += `<li>${response.errors[key][0]}</li>`;
                    errorMessage += '</ul>';
                }
                Swal.fire({ icon: 'error', title: 'Oops...', html: errorMessage });
            }
        });
    });
});
</script>
@endpush
