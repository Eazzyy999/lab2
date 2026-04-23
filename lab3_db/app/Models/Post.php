<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'content', 'is_published'];
    public function user() {
    return $this->belongsTo(User::class); // Зворотний зв'язок
}
public function comments() {
    return $this->hasMany(Comment::class);
}
public function likes() {
    return $this->hasMany(Like::class);
}
}
