<!DOCTYPE html>
<html>
<head>
    <title>Изменение цены</title>
</head>
<body>
    <h2>Изменение цены на товар</h2>
    <p><strong>Товар:</strong> {{ $product->name }}</p>
    <p><strong>Маркетплейс:</strong> {{ $marketplace->name }}</p>
    <p><strong>Цена (по карте/скидке):</strong> {{ $userPrice }}</p>
    <p><strong>Базовая цена:</strong> {{ $basePrice }}</p>
    <p>
        <a href="{{ $url }}">Перейти в магазин</a>
    </p>
</body>
</html>
