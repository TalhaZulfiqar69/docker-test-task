<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserPreference extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_user_preferences', 'user_preference_id', 'article_id');
    }
}
