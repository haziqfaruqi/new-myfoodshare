<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MatchScheduledNotification extends Notification
{
    use Queueable;

    protected $match;

    public function __construct($match)
    {
        $this->match = $match;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Pickup has been Scheduled! - MyFoodshare')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your pickup time has been scheduled by the restaurant.')
            ->line('**Food Item:** ' . $this->match->foodListing->food_name)
            ->line('**Quantity:** ' . $this->match->foodListing->quantity . ' ' . $this->match->foodListing->unit)
            ->line('**Restaurant:** ' . $this->match->foodListing->restaurantProfile->restaurant_name)
            ->line('**Scheduled Pickup:** ' . $this->match->pickup_scheduled_at->format('F j, Y, g:i A'))
            ->line('**Pickup Location:** ' . $this->match->foodListing->pickup_location)
            ->line('**Verification Code:** ' . $this->match->qr_code)
            ->action('View Pickup Details', route('recipient.matches.show', $this->match->id))
            ->line('Please arrive at the scheduled time and show the verification code or QR code to restaurant staff.');
    }

    public function toArray($notifiable)
    {
        return [
            'match_id' => $this->match->id,
            'food_name' => $this->match->foodListing->food_name,
            'quantity' => $this->match->foodListing->quantity,
            'restaurant_name' => $this->match->foodListing->restaurantProfile->restaurant_name,
            'scheduled_pickup' => $this->match->pickup_scheduled_at->format('Y-m-d H:i:s'),
            'pickup_location' => $this->match->foodListing->pickup_location,
            'verification_code' => $this->match->qr_code,
            'message' => 'Your pickup has been scheduled for ' . $this->match->foodListing->quantity . ' ' .
                        $this->match->foodListing->unit . ' of ' . $this->match->foodListing->food_name .
                        ' on ' . $this->match->pickup_scheduled_at->format('F j, Y, g:i A'),
            'url' => route('recipient.matches.show', $this->match->id),
            'type' => 'match_scheduled',
        ];
    }
}