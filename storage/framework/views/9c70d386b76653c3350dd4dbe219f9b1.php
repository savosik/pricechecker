<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение цены</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #28a745;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .button:hover {
            background: #764ba2;
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
            <h1 style="margin: 0; font-size: 24px;">🔔 Уведомление об изменении цены</h1>
        </div>

        <div class="product-info">
            <h2 style="margin-top: 0; color: #212529;"><?php echo e($product->name); ?></h2>
            
            <div class="price-row">
                <span class="label">Маркетплейс:</span>
                <span class="value"><?php echo e($marketplace->name); ?></span>
            </div>
            
            <?php if($product->sku): ?>
            <div class="price-row">
                <span class="label">SKU:</span>
                <span class="value"><?php echo e($product->sku); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if($product->brand): ?>
            <div class="price-row">
                <span class="label">Бренд:</span>
                <span class="value"><?php echo e($product->brand->name); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <div class="price-row">
                <span class="label">Базовая цена:</span>
                <span class="price"><?php echo e(number_format($basePrice, 2, '.', ' ')); ?> ₽</span>
            </div>
            
            <div class="price-row">
                <span class="label">Пользовательская цена:</span>
                <span class="price"><?php echo e(number_format($userPrice, 2, '.', ' ')); ?> ₽</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="<?php echo e(config('app.url')); ?>/admin/resource/product-resource/<?php echo e($product->id); ?>" class="button">
                Посмотреть в админ-панели
            </a>
        </div>

        <div class="footer">
            <p>Это автоматическое уведомление от системы мониторинга цен.</p>
            <p style="margin: 5px 0;">
                <a href="<?php echo e($url); ?>" style="color: #667eea;">Посмотреть на маркетплейсе</a>
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/emails/price-changed.blade.php ENDPATH**/ ?>