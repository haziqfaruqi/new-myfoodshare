<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_name',
        'cuisine_type',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'email',
        'website',
        'business_hours',
        'description',
        'latitude',
        'longitude',
        'business_license',
        'restaurant_capacity',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the restaurant profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the food listings for this restaurant.
     */
    public function foodListings()
    {
        return $this->hasMany(FoodListing::class);
    }

    /**
     * Check if the restaurant profile is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the restaurant profile is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Scope a query to only include approved restaurants.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending restaurants.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}