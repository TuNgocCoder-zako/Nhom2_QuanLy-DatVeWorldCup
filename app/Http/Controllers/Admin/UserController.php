<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends ApiController
{
    /**
     * GET /admin/users
     * Query: ?role=&search=
     */
    public function index(Request $request): View
    {
        $query = User::query()->orderBy('id', 'DESC');

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * PUT /admin/users/{id}
     * Phân quyền (USER / ADMIN) hoặc cập nhật trạng thái
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role'  => 'required|in:USER,ADMIN',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'role'  => $request->input('role'),
            'phone' => $request->input('phone'),
        ]);

        return redirect('/admin/users')->with('success', "Đã cập nhật thông tin và vai trò cho người dùng {$user->name}.");
    }
}
