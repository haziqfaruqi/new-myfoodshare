<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PickupVerified extends Notification
{
    use Queueable;

    protected $verification;

    /**
     * Create a new notification instance.
     */
    public function __construct($verification)
    {
        $this->verification = $verification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pickup Verified',
            'message' => 'Your food pickup has been successfully verified by the recipient.',
            'food_match_id' => $this->verification->food_match_id,
            'verification_code' => $this->verification->verification_code,
            'pickup_time' => $this->verification->scanned_at?->format('M j, Y g:i A'),
            'quality_rating' => $this->verification->quality_rating,
            'link' => route('restaurant.requests.show', $this->verification->food_match_id),
        ];
    }
}