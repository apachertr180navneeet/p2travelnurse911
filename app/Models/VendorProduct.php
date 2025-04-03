<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VendorProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "vendor_products";

    protected $fillable = [
        'vendor_agencies_id',
        'product_title',
        'desc',
        'logo',
        'content',
    ];
}
