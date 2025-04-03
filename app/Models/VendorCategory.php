<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "vendor_categories";

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

    public function vendorsubcategories() {
        return $this->hasMany(VendorSubCategory::class, 'vendor_category_id');
    }

    public function agencies() {
        return $this->hasMany(VendorAgencyCategory::class, 'vendor_categories_id');
    }
}
