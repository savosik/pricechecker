<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Документация - PriceChecker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .prose h3 { margin-top: 1.5rem; margin-bottom: 0.75rem; }
        .prose ul { margin-bottom: 1rem; }
    </style>
</head>
<body class="antialiased text-slate-800 bg-slate-50">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold tracking-tight text-slate-900">
                Price<span class="text-indigo-600">Checker</span> <span class="text-slate-400 font-light ml-2">Docs</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="/admin" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">
                    Панель управления
                </a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 flex flex-col md:flex-row gap-12">
        <!-- Sidebar Navigation -->
        <aside class="md:w-64 flex-shrink-0 hidden md:block">
            <nav class="sticky top-24 space-y-1">
                <p class="px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Навигация</p>
                <a href="#intro" class="block px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg">Введение</a>
                <a href="#catalog" class="block px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Каталог товаров</a>
                <a href="#parsing" class="block px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Мониторинг цен</a>
                <a href="#notifications" class="block px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Уведомления</a>
                <a href="#history" class="block px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">История цен</a>
                <a href="#export" class="block px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Экспорт</a>
                <a href="#support" class="block px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Поддержка</a>
            </nav>
        </aside>

        <!-- Content -->
        <div class="flex-grow max-w-4xl space-y-20">
            
            <section id="intro" class="scroll-mt-28">
                <h1 class="text-4xl font-extrabold text-slate-900 mb-6">Руководство пользователя</h1>
                <p class="text-lg text-slate-600 leading-relaxed mb-4">
                    PriceChecker — это инструмент для автоматического мониторинга цен конкурентов на маркетплейсах Ozon и Wildberries. Система позволяет собирать данные о ценах, сохранять историю изменений и уведомлять вас о важных событиях.
                </p>
            </section>

            <section id="catalog" class="scroll-mt-28">
                <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 text-sm">01</span>
                    Каталог товаров
                </h2>
                <div class="prose prose-slate max-w-none text-slate-600">
                    <p>
                        Работа с сервисом начинается с добавления товаров в раздел <strong class="text-slate-900">Товары</strong>.
                    </p>
                    
                    <h3 class="text-lg font-semibold text-slate-900">1. Создание товара</h3>
                    <p>Нажмите кнопку "Создать" и заполните обязательные поля:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li><strong class="text-slate-800">Название</strong>: внутреннее наименование товара.</li>
                        <li><strong class="text-slate-800">SKU</strong>: уникальный артикул товара.</li>
                    </ul>

                    <h3 class="text-lg font-semibold text-slate-900 mt-6">2. Добавление ссылок для отслеживания</h3>
                    <p>В карточке товара найдите блок <strong class="text-slate-800">Ссылки</strong>. Это ключевой этап настройки:</p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong class="font-medium text-yellow-800">Важно:</strong> Вы должны вручную выбрать <span class="font-bold">Маркетплейс</span> (Ozon или Wildberries) и вставить полную ссылку на товар в поле <span class="font-bold">URL</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li>Нажмите кнопку <strong>+</strong> в блоке Ссылки.</li>
                        <li>Выберите <strong>Маркетплейс</strong> из выпадающего списка.</li>
                        <li>Вставьте прямую ссылку на товар в поле <strong>URL</strong>.</li>
                    </ul>
                </div>
            </section>

            <section id="parsing" class="scroll-mt-28">
                <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 text-sm">02</span>
                    Мониторинг цен (Парсинг)
                </h2>
                <div class="prose prose-slate max-w-none text-slate-600">
                    <p>
                        Система поддерживает как автоматический, так и ручной запуск проверки цен.
                    </p>
                    <h3 class="text-lg font-semibold text-slate-900">Ручной запуск</h3>
                    <p>Вы можете принудительно обновить цены в любой момент:</p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li><strong>Для одного товара:</strong> В списке товаров или в карточке товара нажмите кнопку <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Парсить</span>.</li>
                        <li><strong>Для конкретной ссылки:</strong> В блоке "Ссылки" внутри карточки товара также есть кнопка запуска парсинга для отдельного URL.</li>
                    </ul>
                    <p class="mt-4 text-sm text-slate-500">
                        Задача на парсинг добавляется в очередь и будет выполнена в фоновом режиме в ближайшее время.
                    </p>
                </div>
            </section>

            <section id="notifications" class="scroll-mt-28">
                <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 text-sm">03</span>
                    Условия и Уведомления
                </h2>
                <div class="prose prose-slate max-w-none text-slate-600">
                    <p>
                        В карточке товара есть два важных блока настроек логики:
                    </p>

                    <div class="grid md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                            <h4 class="font-bold text-slate-900 mb-2">Условия записи цены</h4>
                            <p class="text-sm mb-4">Определяет, в каких случаях сохранять новую цену в историю.</p>
                            <div class="text-xs bg-slate-50 p-3 rounded text-slate-500">
                                Пример: Маркетплейс = Ozon, Тип = Цена пользователя, Условие = Меньше 1000.
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                            <h4 class="font-bold text-slate-900 mb-2">Условия уведомления</h4>
                            <p class="text-sm mb-4">Определяет, когда отправлять уведомление администратору (Email/Система).</p>
                             <div class="text-xs bg-slate-50 p-3 rounded text-slate-500">
                                Пример: Если Базовая цена Ozon стала Меньше, чем 500₽ -> Отправить алерт.
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-900 mt-6">Поля условий</h3>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li><strong>Тип цены</strong>: <em>Базовая цена</em> (цена до скидок) или <em>Цена пользователя</em> (конечная цена).</li>
                        <li><strong>Условие</strong>: Больше, Меньше, Равно и др.</li>
                        <li><strong>Значение</strong>: Пороговая сумма.</li>
                    </ul>
                </div>
            </section>

            <section id="history" class="scroll-mt-28">
                <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 text-sm">04</span>
                    История цен
                </h2>
                <div class="prose prose-slate max-w-none text-slate-600">
                    <p>
                        Вся собранная информация доступна в блоке <strong>История цен</strong> внутри карточки товара.
                    </p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li><strong>График</strong>: Визуализация динамики цен по времени.</li>
                        <li><strong>Таблица</strong>: Детальный список проверок с датами, ценами и статусом.</li>
                    </ul>
                </div>
            </section>

            <section id="export" class="scroll-mt-28">
                <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 text-sm">05</span>
                    Экспорт
                </h2>
                <div class="prose prose-slate max-w-none text-slate-600">
                    <p>
                        Вы можете выгрузить данные в формате Excel или CSV. Кнопка <strong>Экспорт</strong> доступна в правом верхнем углу в разделе "Товары" и "История цен".
                    </p>
                </div>
            </section>

            <section id="support" class="scroll-mt-28 border-t border-slate-200 pt-10 mt-10">
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-8 md:p-12 text-center relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-2xl md:text-3xl font-bold mb-4">Остались вопросы?</h3>
                        <p class="text-slate-300 mb-8 max-w-xl mx-auto text-lg">
                            Если что-то работает не так, как ожидалось, или вам нужна помощь с настройкой парсеров.
                        </p>
                        <a href="mailto:{{ config('mail.from.address') }}" class="inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-slate-900 bg-white rounded-full hover:bg-indigo-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Написать в поддержку
                        </a>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 mt-24 py-12">
        <div class="container mx-auto px-6 text-center text-slate-500">
            <p>&copy; {{ date('Y') }} PriceChecker. Сделано для эффективного бизнеса.</p>
        </div>
    </footer>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
