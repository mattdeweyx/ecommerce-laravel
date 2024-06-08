<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        // Log the incoming path
        \Log::info('Request path:', ['path' => $request->path()]);
    
        // Extract the last segment of the path
        $segments = explode('/', $request->path());
        $model = end($segments);
    
        \Log::info('Determined model:', ['model' => $model]);
    
        if ($model == 'admin') {
            return response()->json(Admin::all(), 200);
        } else if ($model == 'user') {
            return response()->json(User::all(), 200);
        } else {
            return response()->json(['message' => 'Invalid path'], 404);
        }
    }
    

    public function adminSignup(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins', // Note the table name here
            'password' => 'required|string|min:8',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => $e->errors()
        ], 401);
    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'message' => 'Database error',
            'errors' => ['email' => ['Email already exists']]
        ], 500);
    }
}public function userSignup(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Note the table name here
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => $e->errors()
        ], 401);
    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'message' => 'Database error',
            'errors' => ['email' => ['Email already exists']]
        ], 500);
    }
}


    public function signup(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'user',
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token' => $token
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->errors()
            ], 401);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'errors' => ['email' => ['Email already exists']]
            ], 500);
        }
    }
    public function destroyAdmin($id)
    {
        $Admin = Admin::where('id', $id)->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }
    public function destroyUser($id)
    {
        $User = User::findOrFail($id);
        $User->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'role' => 'user',
                'token' => $token
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'role' => 'admin',
                'token' => $token
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id, 
            'name' => $user->name, 
            'email' => $user->email, 
            'role' => $user->role,
        ], 200);
    }

    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|string|min:8|confirmed',
            ]);

            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }

            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }

            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json(['message' => 'User information updated successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating user information',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
