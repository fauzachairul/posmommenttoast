<!DOCTYPE html>
<html>

<head>
    <title>Struk #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 58mm;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="text-center">
        <strong>Moment Toast</strong><br>
        buat hari anda kenyang dengan toast yang yummy
    </div>
    <div class="line"></div>
    <div class="text-center" style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">
        {{ $order->transaction_code }} </div>
    <div class="line"></div>
    <div>
        Tgl: {{ $order->transaction_date }}<br>
        Cust: {{ $order->customer_name }}
    </div>

    <div class="line"></div>

    @foreach ($order->items as $item)
        <div>
            {{ $item->product->name }}<br>
            <div class="flex">
                <span>{{ $item->quantity }} x {{ number_format($item->price_at_purchase) }}</span>
                <span>{{ number_format($item->subtotal) }}</span>
            </div>
        </div>
    @endforeach

    <div class="line"></div>
    <div class="flex" style="font-weight: bold; font-size: 14px;">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total_amount) }}</span>
    </div>
    <div class="line"></div>
    <div class="text-center" style="margin-top: 10px;">
        Terima Kasih!
    </div>
</body>

</html>
