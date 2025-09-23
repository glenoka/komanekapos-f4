<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            max-width: 300px; 
            margin: 0 auto;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            text-align: center;
        }
        h2, h3, p { margin: 4px 0; }
        .left { width: 60%; float: left; text-align: left; margin-bottom: 3px; }
        .right { width: 40%; float: right; text-align: right; margin-bottom: 3px; }
        .clearfix { clear: both; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th { text-align: left; border-bottom: 1px solid #000; font-weight: normal; }
        td { padding: 2px 0; }
        .totals { margin-top: 10px; }
        .totals .left { width: 50%; }
        .totals .right { width: 50%; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div id="wrapper">
        <h2>Seneng Kitchen</h2>
        <p>Komaneka at Bisma</p>

        <div class="left" style="font-size:9px;">GUEST: {{ $guest }}</div>
        <div class="right" style="font-size:9px;">ACTIVITY: {{ $activity }}</div>
        <div class="clearfix" style="font-size:9px;"></div>

        <div class="left" style="font-size:9px;">SALE NO.: {{ $sale_no }}</div>
        <div class="right" style="font-size:9px;">DATE: {{ $date }}</div>
        <div class="clearfix"></div>

        <table style="font-size:9px;">
            <thead>
                <tr>
                    <th style="width:5%; font-weight:bold; ">#</th>
                    <th style="width:55%; font-weight:bold; ">Description</th>
                    <th style="width:15%; font-weight:bold;  text-align:right;">Qty</th>
                    <th style="width:25%; font-weight:bold; text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $item['desc'] }}</td>
                        <td style="text-align:right;">{{ $item['qty'] }}</td>
                        <td style="text-align:right;">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width:100%; border-top:1px solid  #000 border-bottom:1px solid #000; margin-top:8px; border-collapse:collapse; font-size:11px;">
            <tr>
                <td style="text-align:left; padding:4px;">Total Items</td>
                <td style="text-align:right; padding:4px; border-right:1px solid #000; "><strong>{{ count($items) }}</strong></td>
                <td style="text-align:left; padding:4px;">Total</td>
                <td style="text-align:right; padding:4px;"><strong>{{ number_format($total, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td style="text-align:left; padding:4px; border-bottom:1px solid #000;">Tax</td>
                <td style="text-align:right; padding:4px; border-right:1px solid #000; "><strong>{{ number_format($tax, 0, ',', '.') }}</strong></td>
             
             
            </tr>
            <tr>
                <td style="text-align:left; padding:4px; font-weight:bold;">Grand Total</td>
                <td colspan="3" style="border-top:1px solid #000; text-align:right; padding:6px; font-weight:bold;">
                    <strong> {{ number_format($grand_total, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align:left; padding:4px; font-weight:bold;">Signature</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="4" style="height:50px;"></td> {{-- space untuk tanda tangan --}}
            </tr>
        </table>
        
        <p style="margin-top:10px;">All transactions are in Indonesian Rupiah</p>
        <p>Thank You</p>
        
    </div>
</body>
</html>
