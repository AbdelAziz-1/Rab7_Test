<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validatedData = $request->validate([
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'country'       => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $validatedData['profile_image'] = $imagePath;
        }
        $user->update($validatedData);
        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => $user
        ], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

};
//بعرض البروفايل 
//بعدل علي البروفايل
//بعرضه بالid





 

