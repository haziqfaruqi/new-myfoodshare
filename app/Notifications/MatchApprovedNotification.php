<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MatchApprovedNotification extends Notification
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
            ->subject('Your Food Match has been Approved! - MyFoodshare')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your food match request has been approved by the restaurant.')
            ->line('**Food Item:** ' . $this->match->foodListing->food_name)
            ->line('**Quantity:** ' . $this->match->foodListing->quantity . ' ' . $this->match->foodListing->unit)
            ->line('**Restaurant:** ' . $this->match->foodListing->restaurantProfile->restaurant_name)
            ->line('**Pickup Location:** ' . $this->match->foodListing->pickup_location)
            ->line('**Expiry Date:** ' . $this->match->foodListing->expiry_date->format('F j, Y'))
            ->action('View Match Details', route('recipient.matches.show', $this->match->id))
            ->line('Please contact the restaurant to schedule your pickup time. You will need to show the QR code for verification.');
    }

    public function toArray($notifiable)
    {
        return [
            'match_id' => $this->match->id,
            'food_name' => $this->match->foodListing->food_name,
            'quantity' => $this->match->foodListing->quantity,
            'restaurant_name' => $this->match->foodListing->restaurantProfile->restaurant_name,
            'pickup_location' => $this->match->foodListing->pickup_location,
            'expiry_date' => $this->match->foodListing->expiry_date->format('Y-m-d'),
            'qr_code' => $this->match->qr_code,
            'message' => 'Your match for ' . $this->match->foodListing->quantity . ' ' .
                        $this->match->foodListing->unit . ' of ' . $this->match->foodListing->food_name .
                        ' has been approved by ' . $this->match->foodListing->restaurantProfile->restaurant_name,
            'url' => route('recipient.matches.show', $this->match->id),
            'type' => 'match_approved',
        ];
    }
}