<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;
class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
        public function run(): void
{
    $questions = Question::all();

    foreach ($questions as $question) {
        $options_ar = json_decode($question->options_ar);
        $options_en = json_decode($question->options_en);

        foreach ($options_ar as $index => $option_ar) {
            Answer::create([
                'question_id' => $question->id,
                'answer_ar' => $option_ar,
                'answer_en' => $options_en[$index],
                'is_correct' => $option_ar === $question->correct_answer_ar,
            ]);
        }
    }
}

  
};