<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    /** Return only the documented API envelope; the service's internal code is mapped to HTTP. */
    protected function serviceResponse(array $result): JsonResponse
    {
        $status = (int) ($result['code'] ?? ($result['success'] ?? false ? 200 : 500));
        return new JsonResponse([
            'success' => (bool) ($result['success'] ?? false),
            'message' => (string) ($result['message'] ?? ''),
            'data' => $result['data'] ?? null,
        ], $status);
    }

    protected function response(bool $success, string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return new JsonResponse(compact('success', 'message', 'data'), $status);
    }

    /**
     * Xác thực tài khoản người dùng:
     * 1. Ưu tiên session đã đăng nhập ($request->user()).
     * 2. Nếu không có session, hỗ trợ user_id từ request body, query hoặc headers (tiện lợi cho kiểm thử API REST / Postman).
     */
    protected function authenticatedUser(Request $request): ?User
    {
        $user = $request->user();
        if ($user instanceof User) {
            return $user;
        }

        $sessionUserId = session('user_id') ?? ($_SESSION['user_id'] ?? null);
        if ($sessionUserId && is_numeric($sessionUserId)) {
            $user = User::query()->find((int) $sessionUserId);
            if ($user instanceof User) {
                return $user;
            }
        }

        $userId = $request->input('user_id') ?? $request->query('user_id') ?? $request->header('X-User-Id') ?? ($_GET['user_id'] ?? null);
        if (!$userId) {
            $userId = $request->route('userId') ?? $request->route('id');
        }
        if (!$userId && preg_match('#/users/(\d+)#', $request->getPathInfo(), $matches)) {
            $userId = (int) $matches[1];
        }

        if ($userId && is_numeric($userId)) {
            $user = User::query()->find((int) $userId);
            if ($user instanceof User) {
                return $user;
            }
        }

        return null;
    }

    protected function requireAuthenticatedUser(Request $request): User|JsonResponse
    {
        $user = $this->authenticatedUser($request);
        if ($user instanceof User) {
            return $user;
        }

        $userId = $request->input('user_id') ?? $request->query('user_id') ?? $request->header('X-User-Id');
        if ($userId && is_numeric($userId)) {
            return $this->response(false, "Không tìm thấy người dùng với user_id = {$userId}.", null, 404);
        }

        return $this->response(false, 'Bạn cần đăng nhập hoặc cung cấp user_id hợp lệ để sử dụng API này.', null, 401);
    }

    protected function requireAdmin(Request $request): User|JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        return $user->isAdmin() ? $user : $this->response(false, 'Chỉ quản trị viên được phép thực hiện thao tác này.', null, 403);
    }
}
