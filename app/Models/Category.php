<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    
    protected $fillable = ['name', 'description'];

   
    
    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
// في Category.php
public function questions()
{
    return $this->hasMany(Question::class, 'category_id');
}

}
