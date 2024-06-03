<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request): Response
    {
        try {
            $user = $request->user();

            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
                // Add other fields as needed
            ]);

            $user->update($validatedData);

            return response()->json(['message' => 'Profile updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update profile', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): Response
    {
        try {
            $user = $request->user();

            $request->validate([
                'password' => ['required', 'current_password'],
            ]);

            Auth::logout();

            $user->delete();

            return response()->json(['message' => 'Account deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete account', 'error' => $e->getMessage()], 500);
        }
    }
}
