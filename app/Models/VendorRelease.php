<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VendorRelease extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "vendor_releases";

    protected $fillable = [
        'vendor_agencies_id',
        'title',
        'desc',
        'content',
    ];
}
