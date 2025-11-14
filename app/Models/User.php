<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'country_id',
        'city_id',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
        'is_active',
        'login_attempts',
        'blocked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
        'blocked_until' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the verification codes for the user.
     */
    public function verification()
    {
        return $this->hasMany(Verification::class);
    }
    
    /**
     * Get the products assigned to the user.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Get the country of the user.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Get the city of the user.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
