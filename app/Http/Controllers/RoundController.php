<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Round;
use App\Models\Question;
class RoundController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->header('Accept-Language', 'en'); // تحديد اللغة من الهيدر، أو الإنجليزية كافتراضي
        
        if ($language == 'ar') {
            // يمكن إرجاع رسائل أو بيانات باللغة العربية
            $message = 'قائمة الجولات';
        } else {
            // إذا كانت اللغة الإنجليزية
            $message = 'List of rounds';
        }

        $rounds = Round::all();
        return response()->json([
            'message' => $message,
            'rounds' => $rounds
        ]);
    }

    // دالة لحساب النقاط لجولة معينة
    public function calculatePoints(Request $request, $roundId)
    {
        $language = $request->header('Accept-Language', 'en');
        $round = Round::find($roundId);

        if (!$round) {
            return response()->json(['message' => $language == 'ar' ? 'الجولة غير موجودة' : 'Round not found'], 404);
        }

        $questions = Question::where('round_id', $roundId)->get();
        $totalPoints = $questions->count() * 3;

        return response()->json([
            'round_id' => $roundId,
            'total_points' => $totalPoints,
            'message' => $language == 'ar' ? 'تم حساب النقاط بنجاح' : 'Points calculated successfully'
        ]);
    }

    public function show(Request $request, $roundId)
    {
        $language = $request->header('Accept-Language', 'en'); // تحديد اللغة (إنجليزي أو عربي)
    
        $round = Round::find($roundId);
    
        if (!$round) {
            return response()->json([
                'message' => $language == 'ar' ? 'الجولة غير موجودة' : 'Round not found'
            ], 404);
        }
    
        // جلب الأسئلة المرتبطة بالجولة
        $questions = $round->questions;
    
        return response()->json([
            'round_name' => $round->name_rounds,
            'total_points' => $questions->count() * 3, // 3 نقاط لكل سؤال
            'questions_count' => $questions->count(),
            'questions' => $questions->map(function ($question) use ($language) {
                return [
                    'question_id' => $question->id,
                    'question_text' => $language == 'ar' ? $question->title_ar : $question->title_en,
                    'points' => 3,  // 3 نقاط لكل سؤال
                    'duration' => 7, // 7 ثواني لكل سؤال
                    'answers' => $language == 'ar' ? $question->options_ar : $question->options_en
                ];
            })
        ]);
    }
    public function store(Request $request)
{
    // التحقق من أن اسم الجولة موجود
    $request->validate([
        'round_name' => 'required|string|max:255', // التحقق من اسم الجولة
    ]);

    // إنشاء الجولة الجديدة باستخدام الحقل الصحيح
    $round = Round::create([
        'name_rounds' => $request->round_name, // استخدام 'name_rounds' في قاعدة البيانات
    ]);

    // اختيار 15 سؤالًا عشوائيًا
    $questions = Question::inRandomOrder()->limit(15)->get();

    // ربط الأسئلة بالجولة
    foreach ($questions as $question) {
        $question->round_id = $round->id; // ربط السؤال بالجولة عن طريق تعيين round_id
        $question->save();  // حفظ التغيير في قاعدة البيانات
    }

    // حساب النقاط
    $totalPoints = $questions->count() * 3;

    return response()->json([
        'message' => 'Round created successfully',
        'name_rounds' => $round->name_rounds,
        'total_points' => $totalPoints,
        'questions_count' => $questions->count(),
        'questions' => $questions->map(function ($question) use ($request) {
            $language = $request->header('Accept-Language', 'en');
            return [
                'question_id' => $question->id,
                'question_text' => $language == 'ar' ? $question->title_ar : $question->title_en,
                'points' => 3,  // عدد النقاط ثابت
                'duration' => 7, // مدة السؤال ثابتة
                'answers' => $language == 'ar' 
                    ? (is_array($question->options_ar) ? $question->options_ar : json_decode($question->options_ar)) 
                    : (is_array($question->options_en) ? $question->options_en : json_decode($question->options_en))
            ];
        })
    ]);
}


    /*public function store(Request $request)
{
    // التحقق من صحة البيانات المدخلة للجولة
    $request->validate([
       'name_rounds' => 'required|string|max:255',
       
    ]);

    // إنشاء الجولة الجديدة
    $round = Round::create([
        'name_rounds'=> $request->name,
        
    ]);

    // اختيار 15 سؤالًا عشوائيًا من الأسئلة الموجودة في قاعدة البيانات
    $questions = Question::inRandomOrder()->limit(15)->get();

    // إضافة الأسئلة المختارة إلى الجولة مع تحديد مدة كل سؤال (7 ثوانٍ)
    foreach ($questions as $question) {
        $round->questions()->create([
            'question_text' => $question->question_text,
            'choices' => $question->choices,
            'correct_answer' => $question->correct_answer,
            'duration' => 7,  
             
            'points' => 3, 
        ]);
    }

    return response()->json([
        'message' => 'Round created successfully with 15 random questions',
        'round' => $round,
        'questions' => $questions
    ], 201); // حالة الاستجابة 201 تعني تم الإنشاء بنجاح
}*/




}
