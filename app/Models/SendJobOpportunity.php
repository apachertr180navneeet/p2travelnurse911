<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendJobOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_opportunity_id',
        'job_request_id',
        'job_id',
        'response',
        'user_id',
        'is_delete'
    ];
}
