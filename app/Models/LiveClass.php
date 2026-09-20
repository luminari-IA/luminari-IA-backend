<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    use HasFactory;

    protected $fillable = ['subject_id', 'title', 'scheduled_at', 'status', 'recording_url'];

    protected $casts = [
        'title' => \App\Casts\TripleEncryptCast::class,
        'recording_url' => \App\Casts\TripleEncryptCast::class,
        'scheduled_at' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
