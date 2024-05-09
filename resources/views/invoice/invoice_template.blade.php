<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .invoice-table th {
            background-color: #f2f2f2;
        }

        .invoice-footer {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="invoice-header">
        <h2>Invoice</h2>
    </div>
    <div class="invoice-details">
        <p><strong>Invoice Number:</strong> {{ $invoice->invoice_no }}</p>
        <p><strong>Date:</strong> {{ $invoice->date }}</p>
        <p><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
    </div>
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->total_price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="invoice-footer">
        <p><strong>Subtotal:</strong> {{ $invoice->subtotal }}</p>
    </div>
</body>

</html>
