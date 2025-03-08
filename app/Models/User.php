<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'country',
        'birth_date',
        'gender',
        'role', 
        'total_points',
        'address', 'profile_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function profile()
{
    return $this->hasOne(Profile::class);
}
// RoundResult Model
public function user()
{
    return $this->belongsTo(User::class);
}

public function question()
{
    return $this->belongsTo(Question::class);
}

public function round()
{
    return $this->belongsTo(Round::class);
}

public function roundResults()
{
    return $this->hasMany(RoundResult::class);
}

}
