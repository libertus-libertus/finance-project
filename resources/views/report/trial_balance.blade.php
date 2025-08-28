@extends('layouts.app')

@section('title', 'Trial Balance Report')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Trial Balance Report</h3>
    </div>
    <div class="panel-body">
        <form class="form-inline" id="filter-form">
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" value="2025-07-01">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" value="2025-07-31">
            </div>
            <button type="button" class="btn btn-primary" id="btn-filter">View Report</button>
            <button type="button" class="btn btn-success" id="btn-download">Download PDF</button>
        </form>
        <hr>
        <table id="trial-balance-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Account Code</th>
                    <th>Account Name</th>
                    <th>Opening Balance</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Closing Balance</th>
                </tr>
            </thead>
            <tbody id="report-body">
                <!-- Data Laporan akan dimuat di sini oleh jQuery -->
            </tbody>
            <tfoot id="report-footer">
                <!-- Total akan dimuat di sini -->
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
        }

        // Fungsi untuk memuat laporan
        function loadReport(startDate, endDate) {
            $('#report-body').html('<tr><td colspan="6" class="text-center">Loading report...</td></tr>');
            $('#report-footer').html(''); // Kosongkan footer saat loading

            $.get('/api/reports/trial-balance', { start_date: startDate, end_date: endDate }, function(response) {
                var data = response.data;
                var reportBodyHtml = '';
                var totalOpening = 0, totalDebit = 0, totalCredit = 0, totalClosing = 0;

                data.forEach(function(row) {
                    reportBodyHtml += `
                        <tr>
                            <td>${row.account_code}</td>
                            <td>${row.account_name}</td>
                            <td class="text-right">${formatRupiah(row.opening_balance)}</td>
                            <td class="text-right">${formatRupiah(row.debit)}</td>
                            <td class="text-right">${formatRupiah(row.credit)}</td>
                            <td class="text-right">${formatRupiah(row.closing_balance)}</td>
                        </tr>
                    `;
                    totalOpening += row.opening_balance;
                    totalDebit += row.debit;
                    totalCredit += row.credit;
                    totalClosing += row.closing_balance;
                });

                $('#report-body').html(reportBodyHtml);

                var reportFooterHtml = `
                    <tr>
                        <th colspan="2" class="text-right">TOTAL</th>
                        <th class="text-right">${formatRupiah(totalOpening)}</th>
                        <th class="text-right">${formatRupiah(totalDebit)}</th>
                        <th class="text-right">${formatRupiah(totalCredit)}</th>
                        <th class="text-right">${formatRupiah(totalClosing)}</th>
                    </tr>
                `;
                $('#report-footer').html(reportFooterHtml);
            });
        }

        // Event handler for filter button
        $('#btn-filter').click(function() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            loadReport(startDate, endDate);
        });

        $('#btn-download').click(function() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            window.location.href = `/reports/trial-balance/pdf?start_date=${startDate}&end_date=${endDate}`;
        });

        // Langsung muat laporan saat halaman pertama kali dibuka
        $('#btn-filter').click();
    });
</script>
@endpush