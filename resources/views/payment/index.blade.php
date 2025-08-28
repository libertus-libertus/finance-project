@extends('layouts.app')

@section('title', 'Payments')

@push('styles')
<style>
    .panel-heading { display: flex; justify-content: space-between; align-items: center; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Payments</h3>
            </div>
            <div class="panel-body">
                <table id="payment-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Payment Ref</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount Paid</th>
                            <th>Invoice No</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Load data by Ajax Datatables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Fungsi format rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number).replace('IDR', '').trim();
        }

        // Inisialisasi Datatables
        $('#payment-table').DataTable({
            processing: true,
            ajax: {
                url: '/api/payments',
                dataSrc: 'data'
            },
            columns: [
                { data: 'payment_ref' },
                { data: 'paid_at' },
                { data: 'method' },
                {
                    data: 'amount_paid',
                    render: function(data) { return formatRupiah(data); },
                    className: 'text-right'
                },
                { data: 'invoice.invoice_no' }, // Ambil invoice_no dari objek invoice
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<button class="btn btn-xs btn-info" disabled>Detail</button>`;
                    }
                }
            ]
        });
    });
</script>
@endpush
