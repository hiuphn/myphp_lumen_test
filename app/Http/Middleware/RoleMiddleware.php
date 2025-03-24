<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, ...$roles) {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();

        if (!array_intersect($roles, $userRoles)) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }

        return $next($request);
    }
}
