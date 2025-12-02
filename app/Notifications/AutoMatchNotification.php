<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AutoMatchNotification extends Notification
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
            ->subject('New Food Match Nearby - MyFoodshare')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! We\'ve found a food match near your location.')
            ->line('**Food Item:** ' . $this->match->foodListing->food_name)
            ->line('**Quantity:** ' . $this->match->foodListing->quantity . ' ' . $this->match->foodListing->unit)
            ->line('**Restaurant:** ' . $this->match->foodListing->restaurantProfile->restaurant_name)
            ->line('**Distance:** ' . number_format($this->match->distance, 1) . ' km away')
            ->line('**Expiry:** ' . $this->match->foodListing->expiry_date->format('F j, Y') . ' at ' . $this->match->foodListing->expiry_time->format('H:i'))
            ->line('**Pickup Location:** ' . $this->match->foodListing->pickup_location)
            ->action('View Match Details', route('recipient.matches.show', $this->match->id))
            ->line('This match was automatically created based on your proximity to the restaurant.');
    }

    public function toArray($notifiable)
    {
        return [
            'match_id' => $this->match->id,
            'food_name' => $this->match->foodListing->food_name,
            'quantity' => $this->match->foodListing->quantity,
            'restaurant_name' => $this->match->foodListing->restaurantProfile->restaurant_name,
            'distance' => $this->match->distance,
            'expiry_date' => $this->match->foodListing->expiry_date->format('Y-m-d'),
            'pickup_location' => $this->match->foodListing->pickup_location,
            'message' => 'New auto-matched food: ' . $this->match->foodListing->quantity . ' ' .
                        $this->match->foodListing->unit . ' of ' . $this->match->foodListing->food_name .
                        ' (' . number_format($this->match->distance, 1) . 'km away)',
            'url' => route('recipient.matches.show', $this->match->id),
            'type' => 'auto_match',
        ];
    }
}