<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'category_id', 
        'title_ar', 
        'title_en', 
        'options_ar', 
        'options_en', 
        'correct_answer_ar', 
        'correct_answer_en', 
        'points', 
        'round_id',
        'duration'
    ];
    protected $casts = [
       'options_ar' => 'array',   
        'options_en' => 'array',
    ];

    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }
}

