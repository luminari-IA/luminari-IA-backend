<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorSession extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'subject_id', 'title', 'status'];

    protected $casts = [
        'title' => \App\Casts\TripleEncryptCast::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function messages()
    {
        return $this->hasMany(TutorMessage::class);
    }
}
