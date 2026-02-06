<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PriceChecker - Продвинутая аналитика маркетплейсов</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-bg {
            background: radial-gradient(circle at top right, #e0e7ff 0%, #ffffff 40%, #f3f4f6 100%);
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-white">

    <!-- Hero Section -->
    <div class="relative overflow-hidden hero-bg min-h-screen flex flex-col">
        <nav class="container mx-auto px-6 py-6 flex justify-between items-center">
            <div class="text-2xl font-bold tracking-tight text-slate-900">
                Price<span class="text-indigo-600">Checker</span>
            </div>
            <a href="/admin" class="hidden md:inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900">
                Войти в админку
            </a>
        </nav>

        <main class="flex-grow container mx-auto px-6 flex flex-col justify-center items-center text-center relative z-10">
            <div class="max-w-4xl mx-auto space-y-8">
                <div class="inline-flex items-center space-x-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-slate-200 shadow-sm mb-4">
                    <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-sm font-medium text-slate-600">Система работает v2.0</span>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-tight text-slate-900">
                    Мониторьте цены на <br/>
                    <span class="gradient-text">маркетплейсах</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-slate-600 max-w-2xl mx-auto font-light leading-relaxed">
                    Автоматический мониторинг цен и аналитика для Ozon и Wildberries. Будьте на шаг впереди конкурентов благодаря данным в реальном времени и умным уведомлениям.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
                    <a href="/admin" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white transition-all duration-200 bg-indigo-600 rounded-full hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-lg hover:shadow-indigo-500/30">
                        <span>Перейти в панель</span>
                        <svg class="w-5 h-5 ml-2 -mr-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-200">
                        Узнать больше
                    </a>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-1/2 left-0 -translate-y-1/2 -translate-x-1/2 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        </main>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Мощные функции для роста</h2>
                <p class="text-lg text-slate-600">Всё необходимое для мониторинга конкурентов и оптимизации вашей ценовой стратегии.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300">
                    <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6 text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Парсинг в реальном времени</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Мгновенное получение актуальных цен с Ozon и Wildberries. Наши продвинутые парсеры легко справляются со сложными страницами товаров.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-purple-100 hover:shadow-xl hover:shadow-purple-500/10 transition-all duration-300">
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-6 text-purple-600 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">История цен</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Отслеживайте колебания цен во времени. Визуализируйте тренды и принимайте решения на основе данных для максимизации маржи.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-blue-100 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Умные уведомления</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Получайте мгновенные уведомления при изменении цен конкурентов. Настройте оповещения, чтобы быть в курсе событий 24/7.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <span class="text-2xl font-bold text-white">Price<span class="text-indigo-500">Checker</span></span>
                <p class="text-sm mt-2 text-slate-400">© <?php echo e(date('Y')); ?> PriceChecker. Все права защищены.</p>
            </div>
            <div class="flex space-x-6">
                <a href="/admin" class="hover:text-white transition-colors">Панель администратора</a>
                <a href="<?php echo e(route('documentation')); ?>" class="hover:text-white transition-colors">Документация</a>
                <a href="mailto:<?php echo e(config('mail.from.address')); ?>" class="hover:text-white transition-colors">Поддержка</a>
            </div>
        </div>
    </footer>

</body>
</html>
<?php /**PATH /var/www/html/resources/views/landing.blade.php ENDPATH**/ ?>