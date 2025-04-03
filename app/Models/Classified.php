<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classified extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];



    protected $fillable = [
        'marketplace_id','state_id', 'city_id', 'title','slug','pets_allowed',
        'price','price_type', 'bedrooms', 'certification_type', 'service_type',
        'description', 'name', 'phone', 'email', 'website','thumbnail','status'
    ];

    protected $attributes = [
        'description' => '',
    ];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
