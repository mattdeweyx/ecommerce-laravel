<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        // Logic to retrieve all users
        return User::all();
    }
    public function indexRole($role)
    {
        // Logic to retrieve all users with a specific role
        return User::where('role', $role)->get();
    }

    public function Adminsignup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return response()->json($user, 201);
    }

    public function store(Request $request)
    {
        // Logic to create a new user
    }

    public function show($id)
    {
        // Logic to retrieve a specific user by ID
        return User::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        // Logic to update a user
        $user = User::findOrFail($id);
        // Update user details based on request data
        $user->update($request->all());
        return $user;
    }

    public function destroy($id)
    {
        // Logic to delete a user
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }


}
