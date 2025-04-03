<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marketplace extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "marketplaces";

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
        return $this->hasMany(Post::class,'marketplace_id');
    }
}
