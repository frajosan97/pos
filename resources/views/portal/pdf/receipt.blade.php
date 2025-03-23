<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sales Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .receipt-heading {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .receipt-heading h1 {
            font-size: 14px;
            margin: 5px 0;
            color: #333;
        }

        .receipt-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            padding: 2px;
            border: 1px solid #333;
        }

        .receipt-info {
            margin-bottom: 15px;
        }

        .receipt-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-info td {
            padding: 3px 0;
            font-size: 12px;
            color: #000;
        }

        .receipt-items {
            margin-bottom: 15px;
        }

        .receipt-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-items th,
        .receipt-items td {
            padding: 5px;
            text-align: left;
            font-size: 12px;
            color: #333;
            border-bottom: 1px solid #ddd;
        }

        .receipt-items th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .hr {
            border-top: 1px dashed #ddd;
            margin: 5px 0;
        }

        .total {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }

        .receipt-footer {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Receipt Heading -->
        <div class="receipt-heading">
            <img src="{{ public_path('assets/images/logo/'.$company_info->logo ?? 'logo.png') }}" alt="" srcset="">
            <h1>{{ strtoupper($company_info->name) }}</h1>
            <h1>{{ strtoupper('P.O BOX ' . $company_info->address) }}</h1>
            <h1>{{ strtoupper($sale->user->branch?->name) }}</h1>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">
            Sales Receipt
        </div>

        <!-- Receipt Info -->
        <div class="receipt-info">
            <table>
                <tr>
                    <td>Transaction #:</td>
                    <td>{{ $sale->id }}</td>
                </tr>
                <tr>
                    <td>Date: {{ $sale->created_at->format('d/m/Y') }}</td>
                    <td>Time: {{ $sale->created_at->format('H:i') }}</td>
                </tr>
                <tr>
                    <td>Cashier:</td>
                    <td>{{ ucwords($sale->user->name ?? 'No Cashier') }}</td>
                </tr>
            </table>
        </div>

        <!-- Receipt Items -->
        <div class="receipt-items">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->saleItems as $saleItem)
                    <tr>
                        <td>{{ $saleItem->product->barcode ?? 'N/A' }}</td>
                        <td>{{ ucfirst($saleItem->product->name ?? 'N/A') }}</td>
                        <td style="text-align: right;">{{ number_format($saleItem->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align: right;" colspan="2">Sub Total:</td>
                        <td style="text-align: right;">Ksh. {{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right;" colspan="2">Total:</td>
                        <td style="text-align: right;">Ksh. {{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                    <!-- Payments -->
                    @foreach($sale->payments as $payment)
                    <tr>
                        <td style="text-align: right;" colspan="2">{{ ucwords($payment->paymentMethod->name) }}:</td>
                        <td style="text-align: right;">Ksh. {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <!-- Change -->
                    @php
                    $balance = $sale->total_amount - $sale->payments->sum('amount'); $isChange = $balance < 0;
                        @endphp

                        <tr>
                        <td style="text-align: right;" colspan="2">{{ $isChange ? 'Change' : 'Balance' }}:</td>
                        <td style="text-align: right;">Ksh. {{ $isChange ? number_format(abs($balance), 2) : number_format($balance, 2) }}</td>
                        </tr>
                </tfoot>
            </table>
        </div>

        <div class="receipt-footer">
            <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
            <p>{{ $sale->id }}</p>
            <p>+254785933333 | {{ $company_info->phone }}</p>
            <p>Thank you for shopping with Atricare!</p>
            <p>All goods are sold subject to manufacturer's warranty. The warranty does not cover faults arising from misuse of damage to the goods caused by the user.</p>
        </div>
    </div>
</body>

</html>