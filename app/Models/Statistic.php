<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    
 protected $fillable = ['user_id', 'correct_answers', 'incorrect_answers', 'score'];

public function user()
{
    return $this->belongsTo(User::class);
}
public function category()
{
    return $this->belongsTo(Category::class);
}
}