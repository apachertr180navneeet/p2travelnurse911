<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorSubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "vendor_subcategories";

    protected $fillable = [
        "title",
        "slug",
        "vendor_category_id", // Parent category ID
        "description",
        "image",
        "status",
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function vendorcategory() {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id'); // FIXED RELATIONSHIP
    }
}
