<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Нарушение РРЦ</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 30px;
        }
        .header {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .product-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .price-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #6c757d;
        }
        .value {
            color: #212529;
            font-weight: 500;
        }
        .price {
            font-size: 24px;
            font-weight: bold;
        }
        .price-violation {
            color: #e53e3e;
        }
        .price-rrp {
            color: #28a745;
        }
        .button {
            display: inline-block;
            background: #e53e3e;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">Уведомление о нарушении РРЦ</h1>
        </div>

        <div class="product-info">
            <h2 style="margin-top: 0; color: #212529;">{{ $product->name }}</h2>

            <div class="price-row">
                <span class="label">Маркетплейс:</span>
                <span class="value">{{ $marketplace->name }}</span>
            </div>

            @if($product->sku)
            <div class="price-row">
                <span class="label">SKU:</span>
                <span class="value">{{ $product->sku }}</span>
            </div>
            @endif

            <div class="price-row">
                <span class="label">Продавец:</span>
                <span class="value">{{ $seller->name }}</span>
            </div>
        </div>

        <div class="product-info">
            <div class="price-row">
                <span class="label">Текущая цена:</span>
                <span class="price price-violation">{{ number_format($currentPrice, 2, '.', ' ') }} ₽</span>
            </div>

            <div class="price-row">
                <span class="label">РРЦ:</span>
                <span class="price price-rrp">{{ number_format($recommendedPrice, 2, '.', ' ') }} ₽</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $url }}" class="button">Посмотреть товар</a>
        </div>

        <div class="footer">
            <p>Это автоматическое уведомление системы мониторинга РРЦ.</p>
            <p>Пожалуйста, приведите цену в соответствие с РРЦ.</p>
        </div>
    </div>
</body>
</html>
