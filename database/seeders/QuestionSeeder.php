<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Category;
class QuestionSeeder extends Seeder
{

        public function run(): void
        {
            $sportsCategory = Category::where('name_en', 'Sports')->first();
            $scienceCategory = Category::where('name_en', 'Science')->first();
            $historyCategory = Category::where('name_en', 'History')->first();
            $techCategory = Category::where('name_en', 'Technology')->first();
    
            // 🏀 Sports
            Question::create([
                'category_id' => $sportsCategory->id,
                'title_ar' => 'كم عدد اللاعبين في فريق كرة القدم؟',
                'title_en' => 'How many players are there in a football team?',
                'options_ar' => json_encode(['9', '10', '11', '12']),
                'options_en' => json_encode(['9', '10', '11', '12']),
                'correct_answer_ar' => '11',
                'correct_answer_en' => '11',
            ]);
    
            Question::create([
                'category_id' => $sportsCategory->id,
                'title_ar' => 'في أي رياضة يستخدم المضرب؟',
                'title_en' => 'In which sport is a racket used?',
                'options_ar' => json_encode(['كرة القدم', 'التنس', 'السباحة', 'الجمباز']),
                'options_en' => json_encode(['Football', 'Tennis', 'Swimming', 'Gymnastics']),
                'correct_answer_ar' => 'التنس',
                'correct_answer_en' => 'Tennis',
            ]);
    
            // 🔬 Science
            Question::create([
                'category_id' => $scienceCategory->id,
                'title_ar' => 'ما هو الكوكب الأحمر؟',
                'title_en' => 'What is the red planet?',
                'options_ar' => json_encode(['المريخ', 'الزهرة', 'الأرض', 'عطارد']),
                'options_en' => json_encode(['Mars', 'Venus', 'Earth', 'Mercury']),
                'correct_answer_ar' => 'المريخ',
                'correct_answer_en' => 'Mars',
            ]);
    
            Question::create([
                'category_id' => $scienceCategory->id,
                'title_ar' => 'ما هو العنصر الكيميائي الذي رمزه O؟',
                'title_en' => 'What is the chemical element with the symbol O?',
                'options_ar' => json_encode(['أكسجين', 'ذهب', 'هيدروجين', 'نيتروجين']),
                'options_en' => json_encode(['Oxygen', 'Gold', 'Hydrogen', 'Nitrogen']),
                'correct_answer_ar' => 'أكسجين',
                'correct_answer_en' => 'Oxygen',
            ]);
    
            // 🏺 History
            Question::create([
                'category_id' => $historyCategory->id,
                'title_ar' => 'من هو أول رئيس للولايات المتحدة؟',
                'title_en' => 'Who was the first President of the United States?',
                'options_ar' => json_encode(['أبراهام لينكولن', 'جورج واشنطن', 'توماس جيفرسون', 'جون آدامز']),
                'options_en' => json_encode(['Abraham Lincoln', 'George Washington', 'Thomas Jefferson', 'John Adams']),
                'correct_answer_ar' => 'جورج واشنطن',
                'correct_answer_en' => 'George Washington',
            ]);
    
            Question::create([
                'category_id' => $historyCategory->id,
                'title_ar' => 'في أي عام انتهت الحرب العالمية الثانية؟',
                'title_en' => 'In which year did World War II end?',
                'options_ar' => json_encode(['1940', '1945', '1950', '1939']),
                'options_en' => json_encode(['1940', '1945', '1950', '1939']),
                'correct_answer_ar' => '1945',
                'correct_answer_en' => '1945',
            ]);
    
            // 💻 Technology
            Question::create([
                'category_id' => $techCategory->id,
                'title_ar' => 'ما هي لغة البرمجة المستخدمة لتطوير تطبيقات الويب؟',
                'title_en' => 'Which programming language is used for web development?',
                'options_ar' => json_encode(['بايثون', 'جافا', 'PHP', 'C++']),
                'options_en' => json_encode(['Python', 'Java', 'PHP', 'C++']),
                'correct_answer_ar' => 'PHP',
                'correct_answer_en' => 'PHP',
            ]);
    
            Question::create([
                'category_id' => $techCategory->id,
                'title_ar' => 'ما هو نظام التشغيل الخاص بشركة آبل؟',
                'title_en' => 'What is Apple\'s operating system?',
                'options_ar' => json_encode(['ويندوز', 'لينكس', 'ماك أو إس', 'أندرويد']),
                'options_en' => json_encode(['Windows', 'Linux', 'macOS', 'Android']),
                'correct_answer_ar' => 'ماك أو إس',
                'correct_answer_en' => 'macOS',
            ]);
        }
    }
   