<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthController extends ApiController
{
    /** Hiển thị form đăng nhập (GET /login) */
    public function showLoginForm(): View
    {
        return view('user.auth.login');
    }

    /** Xử lý đăng nhập (POST /login & POST /api/auth/login) */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $isApi = $request->is('api/*') || $request->wantsJson();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            if ($isApi) {
                return $this->response(false, 'email và password là bắt buộc.', null, 422);
            }
            return back()->withInput()->withErrors(['email' => 'Vui lòng nhập email và mật khẩu hợp lệ.']);
        }

        $user = User::query()->where('email', $email)->first();
        if (!$user || !Hash::check($password, (string) $user->password)) {
            if ($isApi) {
                return $this->response(false, 'Email hoặc mật khẩu không đúng.', null, 401);
            }
            return back()->withInput()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }

        // Lưu session cho cả web và API simulation
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['user_id'] = (int) $user->id;
        session(['user_id' => (int) $user->id, 'user_role' => (string) $user->role, 'user_name' => $user->name]);

        if ($isApi) {
            return $this->response(true, 'Đăng nhập thành công.', $user);
        }

        if ($user->isAdmin()) {
            return redirect('/admin/dashboard')->with('success', 'Đăng nhập quyền Quản trị viên thành công!');
        }

        return redirect('/')->with('success', 'Đăng nhập thành công!');
    }

    /** Hiển thị form đăng ký (GET /register) */
    public function showRegisterForm(): View
    {
        return view('user.auth.register');
    }

    /** Xử lý đăng ký tài khoản (POST /register & POST /api/auth/register) */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $isApi = $request->is('api/*') || $request->wantsJson();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Họ tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            if ($isApi) {
                return $this->response(false, $validator->errors()->first(), $validator->errors(), 422);
            }
            return back()->withInput()->withErrors($validator);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'full_name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'role' => 'USER',
        ]);

        if ($isApi) {
            return $this->response(true, 'Đăng ký tài khoản thành công.', $user, 201);
        }

        return redirect('/login')->with('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
    }

    /** Đăng xuất tài khoản (POST /logout & POST /api/auth/logout) */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $isApi = $request->is('api/*') || $request->wantsJson();

        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        session()->flush();

        if ($isApi) {
            return $this->response(true, 'Đã đăng xuất.', null);
        }

        return redirect('/')->with('success', 'Đã đăng xuất tài khoản.');
    }

    /** Lấy thông tin user hiện tại (GET /api/auth/me) */
    public function me(Request $request): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        return $user instanceof JsonResponse ? $user : $this->response(true, 'Thông tin người dùng.', $user);
    }
}
