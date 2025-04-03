<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorDirectoryCompanyDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_categories_id',
        'company_name',
        'email',
        'phone',
        'address',
        'about',
    ];

}
