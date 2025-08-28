@extends('layouts.app')

@section('title', 'Chart of Accounts')

@push('styles')
<style>
    .panel-heading { display: flex; justify-content: space-between; align-items: center; }
    .help-block { min-height: 15px; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Chart of Accounts (COA)</h3>
                <button class="btn btn-primary btn-sm" id="btn-add">
                    <i class="glyphicon glyphicon-plus"></i> Tambah Akun
                </button>
            </div>
            <div class="panel-body">
                <table id="coa-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Normal Balance</th>
                            <th>Status</th>
                            <th width="100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat oleh Datatables melalui AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form (untuk Tambah dan Edit) -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-title">Tambah Akun</h4>
            </div>
            <div class="modal-body">
                <form id="coaForm">
                    <input type="hidden" id="account_id" name="account_id">
                    <div class="form-group">
                        <label for="code">Code</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                        <span class="help-block text-danger" id="code-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <span class="help-block text-danger" id="name-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="normal_balance">Normal Balance</label>
                        <select class="form-control" id="normal_balance" name="normal_balance" required>
                            <option value="DR">DR (Debit)</option>
                            <option value="CR">CR (Credit)</option>
                        </select>
                        <span class="help-block text-danger" id="normal_balance-error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        var table = $('#coa-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/api/chart-of-accounts',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id' },
                { data: 'code' },
                { data: 'name' },
                { data: 'normal_balance' },
                {
                    data: 'is_active',
                    render: function(data) {
                        return data ? '<span class="label label-success">Aktif</span>' : '<span class="label label-danger">Non-Aktif</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-xs btn-warning btn-edit" data-id="${data.id}"><i class="glyphicon glyphicon-edit"></i></button>
                            <button class="btn btn-xs btn-danger btn-delete" data-id="${data.id}"><i class="glyphicon glyphicon-trash"></i></button>
                        `;
                    }
                }
            ]
        });

        function clearValidationErrors() {
            $('.help-block').text('');
        }

        // Event handler for add button
        $('#btn-add').click(function() {
            $('#coaForm')[0].reset();
            $('#modal-title').text('Tambah Akun');
            $('#account_id').val('');
            clearValidationErrors();
            $('#formModal').modal('show');
        });

        // Event handler for edit button
        $('#coa-table').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            clearValidationErrors();
            $.get('/api/chart-of-accounts/' + id, function(response) {
                var data = response.data;
                $('#modal-title').text('Edit Akun');
                $('#account_id').val(data.id);
                $('#code').val(data.code);
                $('#name').val(data.name);
                $('#normal_balance').val(data.normal_balance);
                $('#formModal').modal('show');
            });
        });

        // Event handler for save button
        $('#btn-save').click(function() {
            clearValidationErrors();
            var id = $('#account_id').val();
            var url = id ? '/api/chart-of-accounts/' + id : '/api/chart-of-accounts';
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $('#coaForm').serialize(),
                success: function(response) {
                    $('#formModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        if (errors.code) $('#code-error').text(errors.code[0]);
                        if (errors.name) $('#name-error').text(errors.name[0]);
                        if (errors.normal_balance) $('#normal_balance-error').text(errors.normal_balance[0]);
                    }
                }
            });
        });

        // Event handler for delete button
        $('#coa-table').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/chart-of-accounts/' + id,
                        method: 'DELETE',
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire('Dihapus!', response.message, 'success');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush