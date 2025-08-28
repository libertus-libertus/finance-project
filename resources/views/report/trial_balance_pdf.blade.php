<!DOCTYPE html>
<html>
<head>
    <title>Trial Balance Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        h2, h4 { text-align: center; }
    </style>
</head>
<body>
    <h2>Trial Balance Report</h2>
    <h4>{{ $period }}</h4>
    <table>
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th class="text-right">Opening Balance</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Closing Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOpening = 0; $totalDebit = 0; $totalCredit = 0; $totalClosing = 0;
            @endphp
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['account_code'] }}</td>
                    <td>{{ $row['account_name'] }}</td>
                    <td class="text-right">{{ number_format($row['opening_balance'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['debit'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['credit'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['closing_balance'], 2) }}</td>
                </tr>
                @php
                    $totalOpening += $row['opening_balance'];
                    $totalDebit += $row['debit'];
                    $totalCredit += $row['credit'];
                    $totalClosing += $row['closing_balance'];
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th class="text-right">{{ number_format($totalOpening, 2) }}</th>
                <th class="text-right">{{ number_format($totalDebit, 2) }}</th>
                <th class="text-right">{{ number_format($totalCredit, 2) }}</th>
                <th class="text-right">{{ number_format($totalClosing, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>