<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\FoodListing;

class FoodListingCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $foodListing;

    public function __construct(FoodListing $foodListing)
    {
        $this->foodListing = $foodListing->load(['restaurantProfile', 'creator']);
    }

    public function broadcastOn()
    {
        // Temporarily use public channel for testing
        return [
            new Channel('admin-listings'),
        ];
    }

    public function broadcastAs()
    {
        return 'food.listing.created';
    }

    public function broadcastWith()
    {
        return [
            'listing_id' => $this->foodListing->id,
            'food_name' => $this->foodListing->food_name,
            'quantity' => $this->foodListing->quantity,
            'unit' => $this->foodListing->unit,
            'category' => $this->foodListing->category,
            'expiry_date' => $this->foodListing->expiry_date->format('Y-m-d'),
            'expiry_time' => $this->foodListing->expiry_time,
            'restaurant_name' => $this->foodListing->restaurantProfile->restaurant_name ?? 'Unknown Restaurant',
            'restaurant_id' => $this->foodListing->restaurantProfile->id,
            'created_by' => $this->foodListing->creator->name,
            'approval_status' => $this->foodListing->approval_status,
            'message' => 'New food listing created: ' . $this->foodListing->food_name,
            'timestamp' => now()->toISOString(),
        ];
    }
}
