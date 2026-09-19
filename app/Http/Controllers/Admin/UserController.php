<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) =>
                $w->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%")))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'status' => $u->status,
                'source' => $u->source,
                'expires_at' => $u->expires_at?->format('d/m/Y'),
                'is_active_access' => $u->hasActiveAccess(),
            ]);

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => ['q' => $q],
        ]);
    }

    public function block(User $user)
    {
        $user->update(['status' => 'blocked']);

        return back()->with('success', "Đã khóa {$user->email}.");
    }

    public function unblock(User $user)
    {
        $user->update(['status' => 'active', 'violation_count' => 0]);

        return back()->with('success', "Đã mở khóa {$user->email}.");
    }

    public function extend(Request $request, User $user)
    {
        $data = $request->validate(['days' => 'required|integer|min:1|max:3650']);

        $base = ($user->expires_at && $user->expires_at->isFuture()) ? $user->expires_at : now();
        $user->update(['expires_at' => $base->copy()->addDays($data['days']), 'status' => 'active']);

        return back()->with('success', "Đã gia hạn {$data['days']} ngày cho {$user->email} (đến {$user->expires_at->format('d/m/Y')}).");
    }
}
