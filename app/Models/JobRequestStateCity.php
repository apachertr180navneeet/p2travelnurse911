<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRequestStateCity extends Model
{
    use HasFactory;
    // Specify the fillable attributes for mass assignment
    protected $fillable = [
        'job_request_id',
        'state_id',
        'city_id',
    ];

}
