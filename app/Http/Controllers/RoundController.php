<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Round;
use App\Models\Question;
class RoundController extends Controller
{
    public function getAllRound(Request $request)
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

    $messages = [
        'round_not_found' => [
            'en' => 'Round not found',
            'ar' => 'الجولة غير موجودة',
        ],
        'no_answers' => [
            'en' => 'No answers provided',
            'ar' => 'لا توجد إجابات مرسلة',
        ],
        'points_calculated' => [
            'en' => 'Points calculated successfully',
            'ar' => 'تم احتساب النقاط بنجاح',
        ],
    ];
    $round = Round::find($roundId);
    if (!$round) {
        return response()->json([
            'message' => $messages['round_not_found'][$language],
        ], 404);
    }
    $answers = $request->input('answers', []);
    if (empty($answers)) {
        return response()->json([
            'message' => $messages['no_answers'][$language],
        ], 400);
    }

    $questions = Question::where('round_id', $roundId)->get();

    $earnedPoints = 0;
    $pointsPerQuestion = 3;

    foreach ($questions as $question) {
        $userAnswer = $answers[$question->id] ?? null;

        if ($userAnswer) {
            if ($userAnswer == $question->correct_answer) {
                $earnedPoints += $pointsPerQuestion;
            }
        }
    }
    return response()->json([
        'round_id' => $roundId,
        'earned_points' => $earnedPoints,
        'total_possible_points' => $questions->count() * $pointsPerQuestion,
        'message' => $messages['points_calculated'][$language],
    ]);
}

    public function getRound(Request $request, $roundId)
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
    //store
    public function createRound(Request $request)
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
//عشان اعرف رقم السؤال واتاكد من الاجابة
public function getRoundQuestions($roundId)
{
    $questions = Question::where('round_id', $roundId)->get();
    if ($questions->isEmpty()) {
        return response()->json([
            'message' => 'لا توجد أسئلة لهذه الجولة'
        ], 404);
    }
    return response()->json([
        'questions' => $questions
    ]);
}
public function submitAnswers(Request $request, $roundId)
{
    $language = $request->header('Accept-Language', 'ar');

    // جلب الجولة
    $round = Round::find($roundId);
    if (!$round) {
        return response()->json([
            'message' => $language == 'ar' ? 'الجولة غير موجودة' : 'Round not found',
        ], 404);
    }

    // استخراج الإجابات من الطلب
    $answers = $request->input('answers', []); // يجب أن يكون كود الإرسال بالشكل: {"answers": {"1": "إجابة المستخدم", "2": "إجابة أخرى"}}

    if (empty($answers)) {
        return response()->json([
            'message' => $language == 'ar' ? 'لا توجد إجابات مرسلة' : 'No answers provided',
        ], 400);
    }

    // جلب جميع الأسئلة الخاصة بالجولة دفعة واحدة
    $questions = Question::whereIn('id', array_keys($answers))
                         ->where('round_id', $roundId)
                         ->get()
                         ->keyBy('id'); // تخزينها كمصفوفة مفهرسة بمعرف السؤال

    $earnedPoints = 0;
    $pointsPerQuestion = 3;
    $details = [];

    foreach ($answers as $questionId => $userAnswer) {
        $question = $questions[$questionId] ?? null;

        if ($question) {
            $isCorrect = $userAnswer == $question->correct_answer;

            if ($isCorrect) {
                $earnedPoints += $pointsPerQuestion;
            }

            $details[] = [
                'question_id' => $question->id,
                'your_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'status' => $isCorrect
                    ? ($language == 'ar' ? '✅ صحيحة' : '✅ Correct')
                    : ($language == 'ar' ? '❌ خاطئة' : '❌ Wrong'),
            ];
        } else {
            $details[] = [
                'question_id' => $questionId,
                'your_answer' => $userAnswer,
                'correct_answer' => null,
                'status' => $language == 'ar' ? '⚠️ السؤال غير موجود' : '⚠️ Question not found',
            ];
        }
    }

    return response()->json([
        'round_id' => $roundId,
        'earned_points' => $earnedPoints,
        'total_possible_points' => count($questions) * $pointsPerQuestion,
        'details' => $details,
        'message' => $language == 'ar' ? 'تم احتساب النقاط بنجاح' : 'Points calculated successfully',
    ]);
}

   /* public function submitAnswers(Request $request, $roundId)
    {
        $language = $request->header('Accept-Language', 'ar');
        $round = Round::find($roundId);
        if (!$round) {
            return response()->json([
                'message' => $language == 'ar' ? 'الجولة غير موجودة' : 'Round not found',
            ], 404);
        }
        $answers = [];
        foreach ($request->all() as $key => $value) {
            if (preg_match('/answers\[(\d+)\]/', $key, $matches)) {
                $questionId = $matches[1];
                $answers[$questionId] = $value;
            }
        }
        if (empty($answers)) {
            return response()->json([
                'message' => $language == 'ar' ? 'لا توجد إجابات مرسلة' : 'No answers provided',
            ], 400);
        }

        $questions = Question::where('round_id', $roundId)->get();
        $earnedPoints = 0;
        $pointsPerQuestion = 3;
        $details = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
    
            if ($userAnswer) {
                $isCorrect = $userAnswer == $question->correct_answer;
                if ($isCorrect) {
                    $earnedPoints += $pointsPerQuestion;
                }
    
                $details[] = [
                    'question_id' => $question->id,
                    'your_answer' => $userAnswer,
                    'correct_answer' => $question->correct_answer,
                    'status' => $isCorrect
                        ? ($language == 'ar' ? '✅ صحيحة' : '✅ Correct')
                        : ($language == 'ar' ? '❌ خاطئة' : '❌ Wrong'),
                ];
            } else {
                $details[] = [
                    'question_id' => $question->id,
                    'your_answer' => null,
                    'correct_answer' => $question->correct_answer,
                    'status' => $language == 'ar' ? '⚠️ لم يتم الإجابة' : '⚠️ Not answered',
                ];
            }
        }
    
        return response()->json([
            'round_id' => $roundId,
            'earned_points' => $earnedPoints,
            'total_possible_points' => $questions->count() * $pointsPerQuestion,
            'details' => $details,
            'message' => $language == 'ar' ? 'تم احتساب النقاط بنجاح' : 'Points calculated successfully',
        ]);
    }*/

};
//بجيب كل الراوند
//create round  عشوائي وبياخد اسئلة من داتابيز 
//get round question بتعرض الاسئلة اللي ف راند بالid بتاع السؤال عشان احطه ف فانشكن submit answer
//submit answer بحطلها idسؤال,اجابته     وهكذا  كل سؤال وبعدين يعرض لي النقاط الصح والغلط   فtotal points 
