<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'latitude',
        'longitude',
        'description',

        // Donor (Restaurant) specific fields
        'restaurant_name',
        'business_license',
        'cuisine_type',
        'restaurant_capacity',

        // Recipient (NGO) specific fields
        'organization_name',
        'contact_person',
        'ngo_registration',
        'dietary_requirements',
        'recipient_capacity',
        'needs_preferences',

        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
        'fcm_token',
        'push_notifications_enabled',
        'notification_preferences',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dietary_requirements' => 'array',
            'approved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'notification_preferences' => 'json',
            'push_notifications_enabled' => 'boolean',
        ];
    }

    public function restaurantProfile()
    {
        return $this->hasOne(RestaurantProfile::class);
    }

    public function recipient()
    {
        return $this->hasOne(Recipient::class);
    }

    public function createdFoodListings()
    {
        return $this->hasMany(FoodListing::class, 'created_by');
    }

    public function foodListings()
    {
        return $this->hasManyThrough(FoodListing::class, RestaurantProfile::class, 'user_id', 'id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDonor()
    {
        return $this->role === 'donor';
    }

    public function isRecipient()
    {
        return $this->role === 'recipient';
    }

    public function isRestaurantOwner()
    {
        return $this->role === 'donor'; // For backward compatibility
    }

    public function admin()
    {
        return $this->role === 'admin';
    }

    public function restaurant_owner()
    {
        return $this->role === 'restaurant_owner' || $this->role === 'donor';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'active';
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function matches()
    {
        return $this->hasMany(FoodMatch::class, 'recipient_id');
    }
}