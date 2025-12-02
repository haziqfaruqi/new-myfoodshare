<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PickupVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_match_id',
        'food_listing_id',
        'recipient_id',
        'donor_id',
        'verification_code',
        'qr_code_scanned',
        'scanned_at',
        'created_at',
        'updated_at',
        'pickup_details',
        'verification_status',
        'recipient_notes',
        'donor_notes',
        'admin_notes',
        'location_data',
        'photo_evidence',
        'quality_confirmed',
        'quality_rating',
        'quality_issues',
        'pickup_completed_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'pickup_details' => 'array',
        'location_data' => 'array',
        'photo_evidence' => 'array',
        'scanned_at' => 'datetime',
        'pickup_completed_at' => 'datetime',
        'quality_confirmed' => 'boolean',
    ];

    public function foodMatch()
    {
        return $this->belongsTo(FoodMatch::class);
    }

    public function foodListing()
    {
        return $this->belongsTo(FoodListing::class);
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    public function donor()
    {
        // Get donor information through the food listing
        $foodListing = $this->foodListing;
        if ($foodListing && $foodListing->restaurantProfile) {
            return $foodListing->restaurantProfile;
        }
        return $foodListing ? $foodListing->creator : null;
    }

    public static function generateForMatch(FoodMatch $match)
    {
        // Determine donor_id based on whether the food listing has a restaurant profile
        $foodListing = $match->foodListing;
        $donorId = $foodListing->restaurantProfile ? $foodListing->restaurantProfile->user_id : $foodListing->created_by;

        return self::create([
            'food_match_id' => $match->id,
            'food_listing_id' => $match->food_listing_id,
            'recipient_id' => $match->recipient_id,
            'donor_id' => $donorId,
            'verification_code' => self::generateUniqueCode(),
            'verification_status' => 'pending',
        ]);
    }

    protected static function generateUniqueCode()
    {
        do {
            $code = 'VRF-' . strtoupper(Str::random(8));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    public function generateQrCode()
    {
        // Generate QR code URL for mobile scanning
        return route('pickup.verify', ['code' => $this->verification_code]);
    }

    public function scanCode($scannedCode, $locationData = null)
    {
        if ($this->verification_code === $scannedCode) {
            $this->update([
                'qr_code_scanned' => $scannedCode,
                'scanned_at' => now(),
                'verification_status' => 'verified',
                'location_data' => $locationData,
            ]);

            // Broadcast QR code scanned event
            broadcast(new \App\Events\QrCodeScanned($this, $this->recipient));

            return true;
        }
        return false;
    }

    public function completePickup($details = [], $photos = [])
    {
        $this->update([
            'verification_status' => 'completed',
            'pickup_details' => $details,
            'photo_evidence' => $photos,
            'pickup_completed_at' => now(),
        ]);

        // Broadcast pickup completed event
        broadcast(new \App\Events\PickupCompleted($this, $this->recipient));
    }

    public function reportQualityIssue($rating, $issues, $notes = null)
    {
        $this->update([
            'quality_confirmed' => false,
            'quality_rating' => $rating,
            'quality_issues' => $issues,
            'recipient_notes' => $notes,
            'verification_status' => 'disputed',
        ]);
    }

    public function confirmQuality($rating = 5, $notes = null)
    {
        $this->update([
            'quality_confirmed' => true,
            'quality_rating' => $rating,
            'recipient_notes' => $notes,
        ]);
    }
}
