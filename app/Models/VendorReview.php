<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_agencies_id',
        'user_id',
        'rating',
        'review_text',
        'is_approved',
        'created_at',
        'updated_at',
    ];


    public function vendorAgency()
    {
        return $this->belongsTo(VendorAgency::class, 'vendor_agencies_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
