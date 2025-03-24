<?php

require_once __DIR__.'/../vendor/autoload.php';

// Tải biến môi trường từ file .env
(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

// Cài đặt múi giờ (giờ Việt Nam)
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Khởi tạo ứng dụng
$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

// Singleton bindings
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Routing\ResponseFactory::class,
    function ($app) {
        return $app->make(Laravel\Lumen\Http\ResponseFactory::class);
    }
);

// Cấu hình ứng dụng
$app->configure('app');
$app->configure('auth');
$app->configure('session');

// Đăng ký Service Providers
$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

// Kích hoạt Facades và Eloquent
$app->withFacades();
$app->withEloquent();

// Middleware
$app->middleware([
    App\Http\Middleware\CorsMiddleware::class,
    \Illuminate\Session\Middleware\StartSession::class, // Middleware hỗ trợ session
]);

// Route Middleware
$app->routeMiddleware([
    'role' => App\Http\Middleware\RoleMiddleware::class,
    'auth' => App\Http\Middleware\Authenticate::class,
]);

// Khởi tạo routes
$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

// Trả về ứng dụng
return $app;