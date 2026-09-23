<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plan_id',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'role', // 'admin', 'student'
        'profile_photo_path',
        'theme_color',
        'font_family',
        'border_style',
        'reduced_animations',
        'tts_speed',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;
    }

    protected function casts(): array
    {
        return [
            'email' => \App\Casts\TripleEncryptCast::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class)->withPivot('level')->withTimestamps();
    }

    public function tutorSessions()
    {
        return $this->hasMany(TutorSession::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
