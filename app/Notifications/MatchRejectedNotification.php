<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MatchRejectedNotification extends Notification
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
            ->subject('Your Food Match Request was Rejected - MyFoodshare')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We\'re sorry, but your food match request has been rejected by the restaurant.')
            ->line('**Food Item:** ' . $this->match->foodListing->food_name)
            ->line('**Restaurant:** ' . $this->match->foodListing->restaurantProfile->restaurant_name)
            ->line('**Reason:** ' . ($this->match->notes ?: 'No specific reason provided'))
            ->action('Browse Other Food', route('food-listings.index'))
            ->line('Thank you for using MyFoodshare. Please feel free to browse other available food listings.');
    }

    public function toArray($notifiable)
    {
        return [
            'match_id' => $this->match->id,
            'food_name' => $this->match->foodListing->food_name,
            'restaurant_name' => $this->match->foodListing->restaurantProfile->restaurant_name,
            'rejection_reason' => $this->match->notes,
            'message' => 'Your match request for ' . $this->match->foodListing->food_name .
                        ' has been rejected by ' . $this->match->foodListing->restaurantProfile->restaurant_name,
            'url' => route('food-listings.index'),
            'type' => 'match_rejected',
        ];
    }
}