<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAgencyCategory extends Model
{
    use HasFactory;

    protected $table = 'vendor_agency_category';
    
    protected $fillable = ['vendor_agencies_id', 'vendor_categories_id', 'vendor_subcategories_ids'];

    protected $casts = [
        'vendor_subcategories_ids' => 'array', // Auto-convert JSON to array
    ];

    public function products()
    {
        return $this->belongsToMany(VendorProduct::class, 'vendor_agencies_id');
    }

    public function agency()
    {
        return $this->belongsTo(VendorAgency::class, 'vendor_agencies_id');
    }



}
