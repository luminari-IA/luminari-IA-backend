<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'tokens_limit', 'live_classes_limit'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
