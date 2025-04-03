<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 1;               
    const ROLE_FACILITY = 2;
    const ROLE_AGENCY = 3;
    const ROLE_CANDIDATE = 4;
    const ROLE_APPLICANT = 5;
    const ROLE_OFFICE_ADMIN = 6;                    
    const ROLE_SUB_ADMIN = 7;
    const ROLE_ACCOUNT = 8;                                                                             
    const ROLE_EMPLOYEE = 9;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get User data 
    */
    public static function getUserDetailByID($id) {
        return self::join('user_roles','user_roles.id', 'users.role_id')
                    ->where('users.id',$id)
                    ->select('users.id', 'users.unique_id',
                    'users.name','users.email','users.role_id','users.created_by', 'user_roles.role')
                    ->first();
    }

    /**
     * Get Office staff user ids
     */
    public static function getOfficeStaff($id)
    {
        return self::where('created_by', $id)
            ->where('role_id', self::ROLE_OFFICE_ADMIN)    
            ->pluck('id')->toArray();
    }
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
