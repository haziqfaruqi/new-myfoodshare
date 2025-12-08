<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MatchCreatedNotification extends Notification
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
            ->subject('New Food Match Request - MyFoodshare')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a new food match request.')
            ->line('**Food Item:** ' . $this->match->foodListing->food_name)
            ->line('**Quantity:** ' . $this->match->foodListing->quantity . ' ' . $this->match->foodListing->unit)
            ->line('**Recipient:** ' . ($this->match->recipient ? ($this->match->recipient->organization_name ?? $this->match->recipient->name) : 'Unknown Recipient'))
            ->line('**Distance:** ' . number_format($this->match->distance, 1) . ' km away')
            ->line('**Expiry:** ' . $this->match->foodListing->expiry_date->format('F j, Y') . ' at ' . $this->match->foodListing->expiry_time->format('H:i'))
            ->action('View Match', route('restaurant.matches.show', $this->match->id))
            ->line('Please review and approve or reject this match request.');
    }

    public function toArray($notifiable)
    {
        return [
            'match_id' => $this->match->id,
            'food_name' => $this->match->foodListing->food_name,
            'quantity' => $this->match->foodListing->quantity,
            'recipient_name' => $this->match->recipient ? ($this->match->recipient->organization_name ?? $this->match->recipient->name) : 'Unknown Recipient',
            'distance' => $this->match->distance,
            'expiry_date' => $this->match->foodListing->expiry_date->format('Y-m-d'),
            'message' => 'New match request for ' . $this->match->foodListing->quantity . ' ' .
                        $this->match->foodListing->unit . ' of ' . $this->match->foodListing->food_name .
                        ' from ' . ($this->match->recipient ? ($this->match->recipient->organization_name ?? $this->match->recipient->name) : 'Unknown Recipient'),
            'url' => route('restaurant.matches.show', $this->match->id),
            'type' => 'match_created',
        ];
    }
}