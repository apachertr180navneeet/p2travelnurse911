<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRequest extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;

    protected $fillable = [
        'user_id','profession_id','speciality_id',
        'flexible',
        'start_date',
        'end_date',
        'shift_id',
        'pay_rate',
        'pay_type',
        'employment_type_id',
        'user_document_id',
        'file',
        'status',
    ];
}
