<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{   public function index()
    {
        $user = Profile::all();
        return response()->json($user, 200);
    }
    
     /////create/////
     public function store(Request $request)
     {
         $request->validate([
             'address' => 'nullable|string|max:255',
             'phone' => 'nullable|string|max:20',
             'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
         ]);
 
         $user = Auth::user();
 
         $data = $request->only(['address', 'phone']);
 
         if ($request->hasFile('profile_image')) {
             $imagePath = $request->file('profile_image')->store('profile_images', 'public');
             $data['profile_image'] = $imagePath;
         }
 
         $profile = Profile::updateOrCreate(
             ['user_id' => $user->id],
             $data
         );
 
         return response()->json($profile, 201);
     }

/* public function update(Request $request)
{
    $request->validate([
        'address' => 'sometimes|required|string|max:255',
        'phone' => 'sometimes|nullable|string|max:20',
        'profile_image' => 'sometimes|nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    $user = Auth::user();
    $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

    $data = $request->only(['address', 'phone']);


    if ($request->hasFile('profile_image')) {
        $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        $data['profile_image'] = $imagePath;
    }

    $profile->fill($data)->save();

    return response()->json($profile, 200);
}*/
     
/*public function update(Request $request)
{
    $request->validate([
        'address' => 'sometimes|required|string|max:255',
        'phone' => 'sometimes|nullable|string|max:20',
        'profile_image' => 'sometimes|nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    $user = Auth::user();
    
    // جلب أو إنشاء البروفايل للمستخدم
    $profile = Profile::firstOrCreate(
        ['user_id' => $user->id],
        ['address' => '', 'phone' => '', 'profile_image' => null]
    );

    // تحديث البيانات
    if ($request->has('address')) {
        $profile->address = $request->address;
    }
    if ($request->has('phone')) {
        $profile->phone = $request->phone;
    }

    if ($request->hasFile('profile_image')) {
        $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        $profile->profile_image = $imagePath;
    }

    $profile->save(); // حفظ التعديلات

    return response()->json($profile, 200);
}*/
public function update(Request $request, $id)
{
    // جلب البروفايل المراد تحديثه
    $user_profile = Profile::findOrFail($id);

    // التحقق من صحة البيانات قبل التحديث
    $validatedData = $request->validate([
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    // معالجة الصورة إذا تم إرسالها
    if ($request->hasFile('profile_image')) {
        $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        $validatedData['profile_image'] = $imagePath;
    }

    // تحديث البيانات
    $user_profile->update($validatedData);

    
    return response()->json($user_profile, 200);
}

};




 

