<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\User;

class QuestionController extends Controller
{ 
    
    public function index()
{
    return Question::all();
}
public function store(Request $request)
{
    
    if (auth()->user->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'title_ar' => 'required',
        'title_en' => 'required',
        'options_ar' => 'required|array|size:4',
        'options_en' => 'required|array|size:4',
        'correct_answer_ar' => 'required',
        'correct_answer_en' => 'required',
        'round_id' => 'required|exists:rounds,id',
    ]);

    $question = Question::create([
        'category_id' => $request->category_id,
        'title_ar' => $request->title_ar,
        'title_en' => $request->title_en,
        'options_ar' => json_encode($request->options_ar),
        'options_en' => json_encode($request->options_en),
        'correct_answer_ar' => $request->correct_answer_ar,
        'correct_answer_en' => $request->correct_answer_en,
        'round_id' => $request-> round_id,
    ]);

    return response()->json($question, 201);
}

public function destroy($id)
{
    if (auth()->user->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $question = Question::findOrFail($id);
    $question->delete();

    return response()->json(['message' => 'Question deleted']);
}

public function update(Request $request, $id)
{
    $question = Question::find($id); // البحث عن السؤال بناءً على ID

    if (!$question) {
        return response()->json(['message' => 'Question not found'], 404); // إذا لم يتم العثور عليه
    }

    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'title_ar' => 'required',
        'title_en' => 'required',
        'options_ar' => 'required|array|size:4',
         'options_en' => 'required|array|size:4',
        'correct_answer_ar' => 'required',
        'correct_answer_en' => 'required',
    ]);

    // تحديث السؤال
    $question->update([
        'category_id' => $request->category_id,
        'title_ar' => $request->title_ar,
        'title_en' => $request->title_en,
        'options_ar' => json_encode($request->options_ar),
        'options_en' => json_encode($request->options_en),
        'correct_answer_ar' => $request->correct_answer_ar,
        'correct_answer_en' => $request->correct_answer_en,
    ]);

    return response()->json($question);
}

public function show($id)
    {
        $question = Question::findOrFail($id);
        return response()->json($question);
    }
};
