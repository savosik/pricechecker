<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Уведомление о цене товара</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .product-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #4CAF50;
        }
        .price-info {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 3px;
        }
        .price-label {
            font-weight: bold;
            color: #2e7d32;
        }
        .price-value {
            font-size: 1.2em;
            font-weight: bold;
            color: #1b5e20;
        }
        .link {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .link:hover {
            background-color: #45a049;
        }
        .footer {
            text-align: center;
            padding: 15px;
            color: #666;
            font-size: 0.9em;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 Уведомление о цене товара</h1>
    </div>
    
    <div class="content">
        <p>Здравствуйте!</p>
        
        <p>Условия уведомления для товара выполнены:</p>
        
        <div class="product-info">
            <h2 style="margin-top: 0; color: #2e7d32;">{{ $product->name }}</h2>
            <p><strong>SKU:</strong> {{ $product->sku }}</p>
            <p><strong>Маркетплейс:</strong> {{ $marketplace->name }}</p>
            
            <div class="price-info">
                <span class="price-label">Цена пользователя:</span>
                <span class="price-value">{{ number_format($userPrice, 2, ',', ' ') }} ₽</span>
            </div>
            
            <div class="price-info">
                <span class="price-label">Базовая цена:</span>
                <span class="price-value">{{ number_format($basePrice, 2, ',', ' ') }} ₽</span>
            </div>
            
            <a href="{{ $url }}" class="link" target="_blank">Перейти на маркетплейс →</a>
        </div>
        
        <p style="color: #666; font-size: 0.9em;">
            <strong>Время проверки:</strong> {{ now()->format('d.m.Y H:i:s') }}
        </p>
    </div>
    
    <div class="footer">
        <p>Это автоматическое уведомление из системы мониторинга цен</p>
    </div>
</body>
</html>
