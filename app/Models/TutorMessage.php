<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorMessage extends Model
{
    use HasFactory;

    protected $fillable = ['tutor_session_id', 'role', 'content'];

    protected $casts = [
        'content' => \App\Casts\TripleEncryptCast::class,
    ];

    public function session()
    {
        return $this->belongsTo(TutorSession::class, 'tutor_session_id');
    }
}
