<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Session authentication for the manual XAMPP front controller. */
class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            return $this->response(false, 'email và password là bắt buộc.', null, 422);
        }
        $user = User::query()->where('email', $email)->first();
        if (!$user || !password_verify($password, (string) $user->password)) {
            return $this->response(false, 'Email hoặc mật khẩu không đúng.', null, 401);
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user->id;
        return $this->response(true, 'Đăng nhập thành công.', $user);
    }

    public function logout(Request $request): JsonResponse
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        return $this->response(true, 'Đã đăng xuất.', null);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        return $user instanceof JsonResponse ? $user : $this->response(true, 'Thông tin người dùng.', $user);
    }
}
