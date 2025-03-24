<?php
namespace App\Http\Middleware;

use Closure;

class CorsMiddleware {
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Thêm các header CORS vào response
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        // Xử lý preflight request (OPTIONS)
        if ($request->getMethod() === "OPTIONS") {
            return response()->json('', 204)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }

        return $response;
    }
}
