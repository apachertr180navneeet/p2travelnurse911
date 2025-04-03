<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorAgency extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "vendor_agencies";

    protected $fillable = [
        'company_name',
        'tagline',
        'website',
        'phone_number',
        'email',
        'logo',
        'desc',
        'youtube',
        'linkedin',
        'instagram',
        'twitter',
        'facebook'
    ];

    protected $casts = [
        'vendor_subcategories_ids' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_categories_id');
    }

    public function products()
    {
        return $this->hasMany(VendorProduct::class, 'vendor_agencies_id');
    }

    public function blogs()
    {
        return $this->hasMany(VendorBlog::class, 'vendor_agencies_id');
    }

    public function releases()
    {
        return $this->hasMany(VendorRelease::class, 'vendor_agencies_id');
    }

    public function reviews()
    {
        return $this->hasMany(VendorReview::class, 'vendor_agencies_id');
    }

    public function categories() {
        return $this->hasMany(VendorAgencyCategory::class, 'vendor_agencies_id');
    }
}
