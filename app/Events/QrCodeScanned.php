<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\PickupVerification;
use App\Models\User;

class QrCodeScanned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $verification;
    public $recipient;

    public function __construct(PickupVerification $verification, User $recipient)
    {
        $this->verification = $verification->load(['foodMatch.foodListing', 'foodListing']);
        $this->recipient = $recipient;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('private-restaurant.' . $this->verification->donor_id),
            new PrivateChannel('private-admin.matches'),
        ];
    }

    public function broadcastAs()
    {
        return 'qr.code.scanned';
    }

    public function broadcastWith()
    {
        return [
            'verification_id' => $this->verification->id,
            'verification_code' => $this->verification->verification_code,
            'food_name' => $this->verification->foodListing?->food_name ?? $this->verification->foodMatch?->foodListing?->food_name ?? 'N/A',
            'recipient_name' => $this->recipient->name,
            'scanned_at' => now()->toISOString(),
            'message' => 'QR code has been scanned - pickup verification initiated',
        ];
    }
}
