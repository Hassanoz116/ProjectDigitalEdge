<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'country_id',
    ];
    
    /**
     * Get name based on current locale
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }
    
    /**
     * Get the country that owns the city.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Get the users for the city.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Get the governorates for the city.
     */
    public function governorates()
    {
        return $this->hasMany(Governorate::class);
    }
}
