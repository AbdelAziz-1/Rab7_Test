<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Statistics;
use App\Models\Question;
use App\Models\RoundResult;
use App\Models\Category;
use App\Models\Statistic;
use App\Models\Redemption;
use Illuminate\Support\Facades\Lang;
class ReportController extends Controller
{
    public function leaderboard()
{
    $leaders = User::orderByDesc('total_points')
        ->select('id', 'name', 'total_points')
        ->take(10)
        ->get();
    return response()->json([
        'message' => [
            'ar' => 'قائمة أفضل اللاعبين',
            'en' => 'Top players leaderboard'
        ],
        'leaders' => $leaders
    ]);
}
public function allUsersReport()
{
    $user = auth('sanctum')->user();
    // جلب المستخدمين مع مجموع النقاط
    $users = User::withSum('roundResults as total_points', 'total_points')
        ->orderByDesc('total_points')
        ->get();

    // حساب الترتيب العالمي
    $globalRank = $users->search(function ($u) use ($user) {
        return $u->id === $user->id;
    }) + 1;

    // حساب الترتيب المحلي (حسب الدولة)
    $localUsers = $users->where('country', $user->country)->values();
    $localRank = $localUsers->search(function ($u) use ($user) {
        return $u->id === $user->id;
    }) + 1;

    return response()->json([
        
        'message_ar' => 'تم جلب التقرير بنجاح',
        'message_en' => 'Report fetched successfully',
        'global_rank' => [
            'ar' => "ترتيبك العالمي هو $globalRank",
            'en' => "Your global rank is $globalRank",
        ],
        'local_rank' => [
            'ar' => "ترتيبك المحلي هو $localRank",
            'en' => "Your local rank is $localRank",
        ],

        'users' => $users->map(function ($u, $index) {
            return [
                'rank' => $index + 1,
                'name' => $u->name,
                'total_points' => $u->total_points ?? 0,
                'country' => $u->country,
            ];
        }),
    ]);
}
private function checkAnswer($answer, $questionId)
{
    // منطق التحقق من الإجابة الصحيحة
    $correctAnswer = Question::find($questionId)->correct_answer;
    return $answer == $correctAnswer;
}
private function calculateScore($isCorrect)
{
    return $isCorrect ? 3 : 0; 
}
/*
public function getUserStatistics($userId, $lang = 'ar')
{
    $categories = Category::all();

    $categoryStats = [];
    foreach ($categories as $category) {

        $totalQuestions = Question::where('category_id', $category->id)->count();

        $correctAnswers = RoundResult::join('rounds', 'round_results.round_id', '=', 'rounds.id')
            ->join('questions', 'round_results.question_id', '=', 'questions.id')
            ->where('rounds.user_id', $userId)
            ->where('questions.category_id', $category->id)
            ->whereColumn(
                'round_results.correct_answers',  // ✅ تم التعديل هنا
                $lang == 'ar' ? 'questions.correct_answer_ar' : 'questions.correct_answer_en'
            )
            ->count();

        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        $categoryStats[] = [
            'category_id' => $category->id,
            'name_ar' => $category->name_ar,
            'name_en' => $category->name_en,
            'correct_percentage' => round($percentage, 2),
        ];
    }

    $totalAnswers = RoundResult::join('rounds', 'round_results.round_id', '=', 'rounds.id')
        ->where('rounds.user_id', $userId)
        ->count();

    $correctAnswers = RoundResult::join('rounds', 'round_results.round_id', '=', 'rounds.id')
        ->join('questions', 'round_results.question_id', '=', 'questions.id')
        ->where('rounds.user_id', $userId)
        ->whereColumn(
            'round_results.correct_answers',  // ✅ تم التعديل هنا
            $lang == 'ar' ? 'questions.correct_answer_ar' : 'questions.correct_answer_en'
        )
        ->count();

    $incorrectAnswers = $totalAnswers - $correctAnswers;

    $score = Statistic::where('user_id', $userId)->sum('score');

    return response()->json([
        'categories' => $categoryStats,

        'total_correct_answers' => [
            'ar' => 'إجابات صحيحة: ' . $correctAnswers,
            'en' => 'Correct Answers: ' . $correctAnswers,
        ],

        'total_incorrect_answers' => [
            'ar' => 'إجابات خاطئة: ' . $incorrectAnswers,
            'en' => 'Incorrect Answers: ' . $incorrectAnswers,
        ],

        'total_questions_answered' => [
            'ar' => 'إجمالي الأسئلة: ' . $totalAnswers,
            'en' => 'Total Questions: ' . $totalAnswers,
        ],

        'score' => [
            'ar' => 'إجمالي النقاط: ' . $score,
            'en' => 'Total Score: ' . $score,
        ],

        'correct_percentage' => [
            'ar' => 'نسبة الإجابات الصحيحة: ' . ($totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0) . '%',
            'en' => 'Correct Percentage: ' . ($totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0) . '%',
        ],

        'incorrect_percentage' => [
            'ar' => 'نسبة الإجابات الخاطئة: ' . ($totalAnswers > 0 ? round(($incorrectAnswers / $totalAnswers) * 100, 2) : 0) . '%',
            'en' => 'Incorrect Percentage: ' . ($totalAnswers > 0 ? round(($incorrectAnswers / $totalAnswers) * 100, 2) : 0) . '%',
        ],
    ]);
}
*/
public function redeem(Request $request)
{
    $request->validate([
        'points' => 'required|integer|min:1',
        'reward' => 'required|string|max:255',
    ]);

    $neededPoints = $request->points;
    $reward = $request->reward;

    // تحديد اللغة المطلوبة
    $locale = $request->header('Accept-Language', 'en'); // افتراضيًا إنجليزي لو لم يتم إرسال الهيدر

    // الرسائل باللغتين
    $messages = [
        'en' => [
            'not_enough_points' => "You don't have enough points to redeem.",
            'points_exchanged' => "Successfully exchanged {$neededPoints} points!",
        ],
        'ar' => [
            'not_enough_points' => "ليس لديك نقاط كافية للاستبدال.",
            'points_exchanged' => "تم استبدال {$neededPoints} نقطة بنجاح!",
        ]
    ];

    return DB::transaction(function () use ($neededPoints, $reward, $messages, $locale) {
        $user = User::where('id', Auth::id())->lockForUpdate()->first();

        if ($user->total_points < $neededPoints) {
            return response()->json([
                'status' => false,
                'message' => $messages[$locale]['not_enough_points'] ?? $messages['en']['not_enough_points'],
                'current_points' => $user->total_points,
            ], 400);
        }

        // تحديث النقاط
        $user->decrement('total_points', $neededPoints);

        // إنشاء سجل الاستبدال
        Redemption::create([
            'user_id' => $user->id,
            'points_used' => $neededPoints,
            'reward' => $reward,
        ]);

        return response()->json([
            'status' => true,
            'message' => $messages[$locale]['points_exchanged'] ?? $messages['en']['points_exchanged'],
            'current_points' => $user->total_points,
            'reward' => $reward,
        ], 200);
    });
}

public function getUserReport($userId){
    $categories = Category::all();
    $report = [];
    $totalQuestionsAnswered = 0;
    $totalCorrectAnswers = 0;

    foreach ($categories as $category) {

        $answers = RoundResult::whereHas('round', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('question', function ($query) use ($category) {
                $query->where('category_id', $category->id);
            })
            ->join('questions', 'round_results.question_id', '=', 'questions.id')
            ->select('round_results.correct_answers', 'questions.correct_answer_ar')
            ->get();

        $totalAnswers = $answers->count();
        $correctAnswers = $answers->filter(function ($answer) {
            return $answer->correct_answers === $answer->correct_answer_ar;
        })->count();
        $incorrectAnswers = $totalAnswers - $correctAnswers;

        $correctPercentage = $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0;
        $incorrectPercentage = $totalAnswers > 0 ? round(($incorrectAnswers / $totalAnswers) * 100, 2) : 0;

        $totalQuestionsAnswered += $totalAnswers;
        $totalCorrectAnswers += $correctAnswers;

        $report[] = [
            'category_ar' => $category->name_ar,
            'category_en' => $category->name_en,
            'total_questions_answered' => $totalAnswers,
            'correct_percentage' => $correctPercentage,
            'incorrect_percentage' => $incorrectPercentage,
        ];
    }

    $totalScore = Statistic::where('user_id', $userId)->sum('score');
    $totalIncorrectAnswers = $totalQuestionsAnswered - $totalCorrectAnswers;

    $overallCorrectPercentage = $totalQuestionsAnswered > 0 ? round(($totalCorrectAnswers / $totalQuestionsAnswered) * 100, 2) : 0;
    $overallIncorrectPercentage = $totalQuestionsAnswered > 0 ? round(($totalIncorrectAnswers / $totalQuestionsAnswered) * 100, 2) : 0;

    return response()->json([
   'categories' => $report,
    'total_questions_answered' => $totalQuestionsAnswered,
    'total_correct_answers' => $totalCorrectAnswers,
    'total_incorrect_answers' => $totalIncorrectAnswers,
    'overall_correct_percentage' => $overallCorrectPercentage,
    'overall_incorrect_percentage' => $overallIncorrectPercentage,
    'total_score' => $totalScore,
    'message' => __('messages.report_fetched'), 
    ]);
}

};
//leader bord بتعرض  قايمه افضل اللاعبين
//redeem بتعرض استبدال النقاط
//get user Reportابعتلها الاidبتاع اليوزر
//all user report بتعرض كل اليوزر