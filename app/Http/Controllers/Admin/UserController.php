<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->validate(['q' => 'nullable|string|max:100'])['q'] ?? null;

        $users = User::where('role', '!=', 'admin')
            ->when($search, fn ($q) => self::search($q, $search))
            ->withCount(['reports', 'claims'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cabut akses admin dulu sebelum menghapus akun ini.');
        }

        $user->delete(); // laporan, klaim, dan notifikasinya ikut terhapus (cascade)

        return back()->with('success', "Akun {$user->name} dihapus.");
    }

    // Cari berdasarkan nama, NIM, atau email (tidak peka huruf besar/kecil)
    public static function search(Builder $query, string $search): Builder
    {
        $like = '%' . mb_strtolower($search) . '%';

        return $query->where(fn ($q) => $q
            ->whereRaw('LOWER(name) LIKE ?', [$like])
            ->orWhereRaw('LOWER(nim) LIKE ?', [$like])
            ->orWhereRaw('LOWER(email) LIKE ?', [$like]));
    }
}
