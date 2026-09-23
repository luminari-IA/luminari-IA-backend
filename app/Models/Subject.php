<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'cover_image', 'is_active'];

    protected $casts = [
        'description' => \App\Casts\TripleEncryptCast::class,
    ];

    public function liveClasses()
    {
        return $this->hasMany(LiveClass::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('level')->withTimestamps();
    }
}
