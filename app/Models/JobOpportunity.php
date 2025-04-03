<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpportunity extends Model
{    
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'profession_id',
        'speciality_id',
        'start_date',
        'shift_id',
        'state_id',
        'city_id',
        'employment_type_id',
        'pay_rate',
        'pay_type',
        'status'
    ];

}
