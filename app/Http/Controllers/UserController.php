<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'unique:users,phone', 'regex:/^01[0-9]{9}$/'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'],
            'country' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'role' => 'required|string',
        ]);
    
        // إذا تم رفع صورة، قم بحفظها
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        }
    
        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'role' => $request->role,
            'address' => $request->address,
            'profile_image' => $imagePath, // حفظ مسار الصورة
        ]);
        // $user->assignRole('user'); 
        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
        ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);
        if (!Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid phone number or password'], 401);
        }

        $user = User::where('phone', $request->phone)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully.',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully.']);
        }

        return response()->json(['message' => 'No user logged in.'], 400);
    }

   
}

