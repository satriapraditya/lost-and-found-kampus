<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/*
| Kelola akun admin. Admin = user dengan kolom role 'admin', jadi
| "menambah admin" bisa membuat akun baru atau menaikkan user yang sudah ada.
*/
class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->validate(['q' => 'nullable|string|max:100'])['q'] ?? null;

        $admins = User::where('role', 'admin')
            ->when($search, fn ($q) => UserController::search($q, $search))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.admins.index', compact('admins', 'search'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'nim' => 'required|string|max:30|unique:users,nim',
            'study_program' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nim.unique' => 'NIM/NIP ini sudah dipakai akun lain.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Ulangi password tidak sama.',
        ]);

        $admin = User::create([
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ] + $validated);

        return redirect()->route('admin.admins.index')->with('success', "Akun admin {$admin->name} dibuat.");
    }

    public function promote(User $user)
    {
        $user->update(['role' => 'admin']);

        return back()->with('success', "{$user->name} sekarang admin.");
    }

    public function demote(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kamu tidak bisa mencabut akses admin milikmu sendiri.');
        }

        if (User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu admin.');
        }

        $user->update(['role' => 'user']);

        return back()->with('success', "Akses admin {$user->name} dicabut. Akunnya kini user biasa.");
    }
}
