<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = session('user_id') ?? ($_SESSION['user_id'] ?? null) ?? $request->input('user_id') ?? $request->header('X-User-Id');

        if (!$userId) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để truy cập tài nguyên này.',
                ], 401);
            }
            return redirect('/login')->with('warning', 'Vui lòng đăng nhập với tài khoản quản trị viên.');
        }

        $user = User::find($userId);
        if (!$user || !$user->isAdmin()) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền quản trị viên.',
                ], 403);
            }
            return redirect('/')->with('error', 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        return $next($request);
    }
}
