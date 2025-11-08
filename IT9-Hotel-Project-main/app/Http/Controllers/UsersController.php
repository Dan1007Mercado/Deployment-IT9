<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();
        return view('employee', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string|max:50',
            'active' => 'nullable',
        ]);

        $user = User::create([
            'name' => $data['firstName'].' '.$data['lastName'],
            'email' => $data['email'],
            'password' => Hash::make('password123'),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? null,
            'is_active' => $request->boolean('active', true),
            'last_login_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created');
    }
}
