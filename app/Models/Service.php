<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'services';

    protected $dates = ['deleted_at'];



    protected $fillable = [
        'marketplace','name', 'status'
    ];


    public function marketplacedata()
    {
        return $this->belongsTo(Marketplace::class, 'marketplace', 'id');
    }

}
