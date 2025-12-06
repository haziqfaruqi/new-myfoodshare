<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_name',
        'contact_person',
        'address',
        'latitude',
        'longitude',
        'location_name',
        'capacity',
        'dietary_requirements',
        'rating',
        'status',
        'needs_preferences',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'dietary_requirements' => 'array',
        'needs_preferences' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the recipient profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the matches for this recipient.
     */
    public function matches()
    {
        return $this->hasMany(FoodMatch::class, 'recipient_id');
    }

    /**
     * Check if the recipient is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the recipient is inactive.
     */
    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    /**
     * Scope a query to only include active recipients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive recipients.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Update the rating based on a new rating value.
     */
    public function updateRating($newRating)
    {
        $currentMatches = $this->matches()->where('status', 'completed')->count();
        if ($currentMatches > 0) {
            $totalRating = $this->matches()
                ->where('status', 'completed')
                ->sum('rating');

            $this->rating = ($totalRating + $newRating) / ($currentMatches + 1);
            $this->save();
        }
    }
}