<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'user_id',
        'food_name',
        'description',
        'category',
        'quantity',
        'unit',
        'expiry_date',
        'expiry_time',
        'pickup_location',
        'latitude',
        'longitude',
        'pickup_address',
        'special_instructions',
        'dietary_info',
        'images',
        'status',
        'approval_status',
        'approved_at',
        'approved_by',
        'admin_notes',
        'qr_code_data',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'expiry_time' => 'datetime:H:i',
        'dietary_info' => 'json',
        'images' => 'json',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'approved_at' => 'datetime',
        'qr_code_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function restaurantProfile()
    {
        return $this->belongsTo(RestaurantProfile::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function matches()
    {
        return $this->hasMany(\App\Models\FoodMatch::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isExpired()
    {
        return $this->expiry_date < now()->toDateString() || 
               ($this->expiry_date == now()->toDateString() && $this->expiry_time < now()->format('H:i'));
    }

    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    public function isPending()
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected()
    {
        return $this->approval_status === 'rejected';
    }

    public function isVisibleToRecipients()
    {
        return $this->isActive() && $this->isApproved() && !$this->isExpired();
    }

    public function generateQrCode()
    {
        $data = [
            'listing_id' => $this->id,
            'food_name' => $this->food_name,
            'quantity' => $this->quantity . ' ' . $this->unit,
            'donor' => $this->restaurantProfile->restaurant_name,
            'pickup_location' => $this->pickup_location,
            'pickup_address' => $this->pickup_address,
            'expiry_date' => $this->expiry_date->format('Y-m-d'),
            'expiry_time' => $this->expiry_time ? $this->expiry_time->format('H:i') : null,
            'special_instructions' => $this->special_instructions,
            'dietary_info' => $this->dietary_info,
            'verification_code' => strtoupper(substr(md5($this->id . $this->created_at), 0, 8)),
        ];

        $this->qr_code_data = json_encode($data);
        $this->save();

        return $data;
    }

    public function getQrCodeUrl()
    {
        if (!$this->qr_code_data) {
            $this->generateQrCode();
        }
        
        return route('food-listing.verify', ['id' => $this->id, 'code' => $this->getVerificationCode()]);
    }

    public function getVerificationCode()
    {
        return strtoupper(substr(md5($this->id . $this->created_at), 0, 8));
    }
}