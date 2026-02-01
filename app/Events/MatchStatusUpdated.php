<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\FoodMatch;

class MatchStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $match;

    public function __construct(FoodMatch $match)
    {
        $this->match = $match->load(['foodListing.restaurantProfile', 'recipient']);
    }

    public function broadcastOn()
    {
        // Broadcast to channels
        return [
            new PrivateChannel('private-matches.' . $this->match->recipient_id),
            new PrivateChannel('private-restaurant.' . $this->match->foodListing->created_by),
            new PrivateChannel('private-admin.matches'),
            // Also use public channels for testing (avoid auth issues)
            new Channel('restaurant-notifications'),
            new Channel('recipient-notifications'),
        ];
    }

    public function broadcastAs()
    {
        return 'match.status.updated';
    }

    public function broadcastWith()
    {
        return [
            'match_id' => $this->match->id,
            'food_name' => $this->match->foodListing->food_name,
            'quantity' => $this->match->foodListing->quantity,
            'restaurant_name' => $this->match->foodListing->restaurantProfile->restaurant_name,
            'recipient_name' => \App\Models\User::find($this->match->recipient_id)?->name ?? 'Unknown Recipient',
            'status' => $this->match->status,
            'qr_code' => $this->match->qr_code,
            'pickup_scheduled_at' => $this->match->pickup_scheduled_at?->format('Y-m-d H:i:s'),
            'message' => $this->getStatusMessage(),
            'timestamp' => now()->toISOString(),
        ];
    }

    private function getStatusMessage()
    {
        switch ($this->match->status) {
            case 'pending':
                return 'New match request received';
            case 'approved':
                return 'Match approved by restaurant';
            case 'rejected':
                return 'Match rejected by restaurant';
            case 'scheduled':
                return 'Pickup time scheduled';
            case 'in_progress':
                return 'Pickup in progress';
            case 'completed':
                return 'Pickup completed successfully';
            case 'cancelled':
                return 'Match cancelled';
            default:
                return 'Match status updated';
        }
    }
}