<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "news_categories";

    protected $fillable = [
        "title",
        "slug",
        "description",
        "image",
        "status",
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function posts() {
        return $this->hasMany(Post::class,'news_category_id');
    }
}
