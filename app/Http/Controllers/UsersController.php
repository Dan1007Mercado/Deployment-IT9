<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index(Request $request) // Add Request parameter
    {
        $search = $request->get('search', ''); // Get search parameter
        
        $users = User::when($search, function($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('created_at')
        ->get();
        
        return view('employee', compact('users', 'search')); // Pass search to view
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => ['required', 'string', Rule::in(['admin', 'receptionist', 'staff'])],
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => trim($data['firstName'] . ' ' . $data['lastName']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('users.index', ['search' => $request->get('search')])->with('success', 'User created successfully. They can now log in with their email and password.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => ['required', 'string', Rule::in(['admin', 'receptionist', 'staff'])],
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => trim($data['firstName'] . ' ' . $data['lastName']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active', true),
        ];

        // Update password only if provided
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index', ['search' => $request->get('search')])->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, Request $request)
    {
        // Prevent deleting your own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index', ['search' => $request->get('search')])->with('error', 'You cannot delete your own account.');
        }

        // Delete the user
        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index', ['search' => $request->get('search')])->with('success', "User \"{$userName}\" has been deleted successfully.");
    }
}