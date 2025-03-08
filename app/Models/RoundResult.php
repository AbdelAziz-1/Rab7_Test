<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoundResult extends Model
{
   
    protected $fillable = [
        'user_id',
        'round_id',
        'total_points',
        'correct_answers',
        'wrong_answers',
        'total_questions',
        'details',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}

public function round()
{
    return $this->belongsTo(Round::class, 'round_id');
}

public function question()
{
    return $this->belongsTo(Question::class, 'question_id');
}
    
}
