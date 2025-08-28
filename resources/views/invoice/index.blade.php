@extends('layouts.app')

@section('title', 'Invoices')

@push('styles')
<style>
    .status-open { background-color: #f0ad4e; color: white; }
    .status-partial { background-color: #337ab7; color: white; }
    .status-paid { background-color: #5cb85c; color: white; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Invoices (AR)</h3>
            </div>
            <div class="panel-body">
                <table id="invoice-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Tax</th>
                            <th>Status</th>
                            <th>Aksi</th>
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

<!-- Modal Detail Invoice -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detail Invoice</h4>
            </div>
            <div class="modal-body">
                <h4>Invoice Info</h4>
                <p><strong>Invoice No:</strong> <span id="detail-invoice-no"></span></p>
                <p><strong>Customer:</strong> <span id="detail-customer"></span></p>
                <p><strong>Date:</strong> <span id="detail-date"></span></p>
                <p><strong>Amount:</strong> <span id="detail-amount"></span></p>
                <hr>
                <h4>Payment History</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Payment Ref</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody id="payment-history-table">
                        <!-- memuat daftar Riwayat pembayaran -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
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

        var table = $('#invoice-table').DataTable({
            processing: true,
            ajax: { url: '/api/invoices', dataSrc: 'data' },
            columns: [
                { data: 'invoice_no' },
                { data: 'invoice_date' },
                { data: 'customer' },
                { data: 'amount', render: function(data) { return formatRupiah(data); }, className: 'text-right' },
                { data: 'tax_amount', render: function(data) { return formatRupiah(data); }, className: 'text-right' },
                {
                    data: 'status',
                    render: function(data) {
                        let badgeClass = '';
                        if (data === 'open') badgeClass = 'status-open';
                        if (data === 'partial') badgeClass = 'status-partial';
                        if (data === 'paid') badgeClass = 'status-paid';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                {
                    data: null, orderable: false, searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-xs btn-info btn-detail" data-id="${data.id}"><i class="glyphicon glyphicon-eye-open"></i> Detail</button>
                            <button class="btn btn-xs btn-success" disabled>Payment</button>
                        `;
                    }
                }
            ]
        });

        // Event handler untuk tombol Detail
        $('#invoice-table').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            $.get('/api/invoices/' + id, function(response) {
                var invoice = response.data;
                $('#detail-invoice-no').text(invoice.invoice_no);
                $('#detail-customer').text(invoice.customer);
                $('#detail-date').text(invoice.invoice_date);
                $('#detail-amount').text(formatRupiah(invoice.amount));

                var paymentHtml = '';
                if (invoice.payments.length > 0) {
                    invoice.payments.forEach(function(payment) {
                        paymentHtml += `
                            <tr>
                                <td>${payment.payment_ref}</td>
                                <td>${payment.paid_at}</td>
                                <td>${payment.method}</td>
                                <td class="text-right">${formatRupiah(payment.amount_paid)}</td>
                            </tr>
                        `;
                    });
                } else {
                    paymentHtml = '<tr><td colspan="4" class="text-center">No payment history found.</td></tr>';
                }

                $('#payment-history-table').html(paymentHtml);
                $('#detailModal').modal('show');
            });
        });
    });
</script>
@endpush