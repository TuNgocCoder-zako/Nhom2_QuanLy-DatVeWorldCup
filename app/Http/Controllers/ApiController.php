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
     * The host application must resolve the authenticated account onto Request.
     * Do not fall back to a user_id supplied by the client: that would allow impersonation.
     */
    protected function authenticatedUser(Request $request): ?User
    {
        $user = $request->user();
        return $user instanceof User ? $user : null;
    }

    protected function requireAuthenticatedUser(Request $request): User|JsonResponse
    {
        return $this->authenticatedUser($request) ?? $this->response(false, 'Bạn cần đăng nhập để sử dụng API này.', null, 401);
    }

    protected function requireAdmin(Request $request): User|JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        return $user->isAdmin() ? $user : $this->response(false, 'Chỉ quản trị viên được phép thực hiện thao tác này.', null, 403);
    }
}
