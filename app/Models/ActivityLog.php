<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'causer_type',
        'causer_id',
        'properties',
        'old_values',
        'new_values',
        'batch_uuid',
    ];

    protected $casts = [
        'properties' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    public static function logActivity($logName, $description, $subject = null, $causer = null, $properties = [])
    {
        return static::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer ? $causer->id : null,
            'properties' => $properties,
            'batch_uuid' => Str::uuid(),
        ]);
    }

    public static function logFoodDonation($event, $foodListing, $user, $properties = [])
    {
        $descriptions = [
            'created' => "{$user->name} created a new food listing: {$foodListing->food_name}",
            'updated' => "{$user->name} updated food listing: {$foodListing->food_name}",
            'deleted' => "{$user->name} deleted food listing: {$foodListing->food_name}",
            'approved' => "Admin approved food listing: {$foodListing->food_name}",
            'expired' => "Food listing expired: {$foodListing->food_name}",
        ];

        return static::logActivity(
            'donation',
            $descriptions[$event] ?? "Food listing {$event}: {$foodListing->food_name}",
            $foodListing,
            $user,
            array_merge(['event' => $event], $properties)
        );
    }

    public static function logPickupActivity($event, $match, $user, $properties = [])
    {
        $foodName = $match->foodListing->food_name;
        $descriptions = [
            'interest_expressed' => "{$user->name} expressed interest in {$foodName}",
            'pickup_confirmed' => "Pickup confirmed for {$foodName}",
            'pickup_verified' => "{$user->name} verified pickup of {$foodName}",
            'pickup_completed' => "{$user->name} completed pickup of {$foodName}",
            'pickup_cancelled' => "Pickup cancelled for {$foodName}",
        ];

        return static::logActivity(
            'pickup',
            $descriptions[$event] ?? "Pickup {$event}: {$foodName}",
            $match,
            $user,
            array_merge(['event' => $event], $properties)
        );
    }

    public static function logAdminAction($event, $subject, $admin, $properties = [])
    {
        $subjectName = method_exists($subject, 'getName') ? $subject->getName() : 
            (isset($subject->name) ? $subject->name : 
            (isset($subject->food_name) ? $subject->food_name : 'Unknown'));

        $descriptions = [
            'user_approved' => "Admin approved user: {$subjectName}",
            'user_rejected' => "Admin rejected user: {$subjectName}",
            'listing_approved' => "Admin approved listing: {$subjectName}",
            'listing_rejected' => "Admin rejected listing: {$subjectName}",
            'dispute_resolved' => "Admin resolved dispute for: {$subjectName}",
        ];

        return static::logActivity(
            'admin',
            $descriptions[$event] ?? "Admin {$event}: {$subjectName}",
            $subject,
            $admin,
            array_merge(['event' => $event], $properties)
        );
    }

    public static function getImpactStats($userId = null, $userType = null)
    {
        $baseQuery = static::query();

        if ($userId) {
            $baseQuery->where('causer_id', $userId);
        }

        if ($userType) {
            $baseQuery->whereHas('causer', function($q) use ($userType) {
                $q->where('role', $userType);
            });
        }

        // Clone query for each stat to avoid conflicts
        $donationQuery = clone $baseQuery;
        $pickupQuery = clone $baseQuery;

        return [
            'total_donations' => $donationQuery->where('log_name', 'donation')
                ->where('description', 'like', '%created a new food listing%')->count(),
            'completed_pickups' => $pickupQuery->where('log_name', 'pickup')
                ->where('description', 'like', '%completed pickup%')->count(),
            'meals_provided' => static::calculateMealsProvided($userId),
            'food_waste_reduced' => static::calculateFoodWasteReduced($userId),
        ];
    }

    private static function calculateMealsProvided($userId = null)
    {
        // Calculate based on completed pickups or created donations
        $query = static::where('log_name', 'donation')
            ->where('description', 'like', '%created a new food listing%');

        if ($userId) {
            $query->where('causer_id', $userId);
        }

        return $query->get()->sum(function ($log) {
            $properties = $log->properties ?? [];
            return $properties['estimated_meals'] ?? 1; // Default 1 meal per donation
        });
    }

    private static function calculateFoodWasteReduced($userId = null)
    {
        // Calculate based on food listings created
        $query = static::where('log_name', 'donation')
            ->where('description', 'like', '%created a new food listing%');

        if ($userId) {
            $query->where('causer_id', $userId);
        }

        return round($query->get()->sum(function ($log) {
            $properties = $log->properties ?? [];
            return $properties['estimated_weight_kg'] ?? 0.5; // Default 0.5kg per listing
        }), 1);
    }
}