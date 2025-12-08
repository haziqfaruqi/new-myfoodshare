<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\PickupConfirmedNotification;
use App\Notifications\PickupCompletedNotification;
use App\Notifications\PickupScheduledNotification;
use App\Events\MatchStatusUpdated;

class FoodMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'food_listing_id',
        'recipient_id',
        'status',
        'distance',
        'created_at',
        'updated_at',
        'matched_at',
        'approved_at',
        'pickup_scheduled_at',
        'completed_at',
        'qr_code',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'matched_at' => 'datetime',
        'approved_at' => 'datetime',
        'pickup_scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'distance' => 'decimal:2',
    ];

    public function foodListing()
    {
        return $this->belongsTo(FoodListing::class);
    }

    public function listing()
    {
        return $this->belongsTo(FoodListing::class, 'food_listing_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    public function restaurantProfile()
    {
        return $this->hasOneThrough(RestaurantProfile::class, FoodListing::class, 'id', 'user_id', 'food_listing_id');
    }

    public function tracking()
    {
        return $this->hasMany(Tracking::class);
    }

    public function pickupVerification()
    {
        return $this->hasOne(PickupVerification::class);
    }

    public function generateQrCode()
    {
        $this->qr_code = 'QR-' . strtoupper(substr(md5($this->id . time()), 0, 8));
        $this->save();
        return $this->qr_code;
    }

    public function confirmPickup()
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Generate pickup verification record
        $verification = PickupVerification::generateForMatch($this);

        // Update QR code to point to verification
        $this->qr_code = $verification->verification_code;
        $this->save();

        // Notify recipient about pickup confirmation if they exist
        if ($this->recipient) {
            $this->recipient->notify(new PickupConfirmedNotification($this));
        }

        // Broadcast real-time update
        event(new MatchStatusUpdated($this));

        return $this;
    }

    public function schedulePickup(\DateTime $scheduledAt)
    {
        $this->update([
            'status' => 'scheduled',
            'pickup_scheduled_at' => $scheduledAt,
        ]);

        // Notify recipient about the scheduled pickup if they exist
        if ($this->recipient) {
            $this->recipient->notify(new PickupScheduledNotification($this));
        }

        // Broadcast real-time update
        event(new MatchStatusUpdated($this));

        return $this;
    }

    public function completePickup()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Notify donor about completed pickup if they exist
        if ($this->foodListing->creator) {
            $this->foodListing->creator->notify(new PickupCompletedNotification($this));
        }

        // Update food listing status if this was the last active match
        $activeMaches = FoodMatch::where('food_listing_id', $this->food_listing_id)
            ->whereIn('status', ['pending', 'approved', 'scheduled', 'in_progress'])
            ->count();

        if ($activeMaches === 0) {
            $this->foodListing->update(['status' => 'picked_up']);
        }

        return $this;
    }

    public function cancelMatch($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);

        return $this;
    }

    /**
     * Get CSS class for status badge
     */
    public function getStatusColorClass()
    {
        return match($this->status) {
            'pending' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-red-100 text-red-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get user-friendly status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }
}