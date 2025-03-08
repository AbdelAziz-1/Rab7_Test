<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['question_id',  'answer_ar', 'answer_en', 'is_correct'];

    public function user()
{
    return $this->belongsTo(User::class);
    
}
public function question()
{
    return $this->belongsTo(Question::class, 'question_id');
}




}
