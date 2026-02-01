<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FoodListingCreatedNotification extends Notification
{
    use Queueable;

    protected $foodListing;

    public function __construct($foodListing)
    {
        $this->foodListing = $foodListing;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toMail($notifiable)
    {
        $restaurantName = $this->foodListing->restaurantProfile->restaurant_name ?? 'Unknown Restaurant';

        return (new MailMessage)
            ->subject('New Food Listing Pending Approval - MyFoodshare')
            ->greeting('Hello Admin,')
            ->line('A new food listing has been created and is pending approval.')
            ->line('**Restaurant:** ' . $restaurantName)
            ->line('**Food Item:** ' . $this->foodListing->food_name)
            ->line('**Quantity:** ' . $this->foodListing->quantity . ' ' . $this->foodListing->unit)
            ->line('**Category:** ' . $this->foodListing->category)
            ->line('**Expiry:** ' . $this->foodListing->expiry_date->format('F j, Y') . ' at ' . $this->foodListing->expiry_time)
            ->action('Review Listing', url('/admin/approvals'))
            ->line('Please review and approve or reject this listing.');
    }

    public function toArray($notifiable)
    {
        $restaurantName = $this->foodListing->restaurantProfile->restaurant_name ?? 'Unknown Restaurant';

        return [
            'title' => 'New Food Listing Pending Approval',
            'message' => $restaurantName . ' posted a new listing: ' . $this->foodListing->food_name,
            'listing_id' => $this->foodListing->id,
            'food_name' => $this->foodListing->food_name,
            'quantity' => $this->foodListing->quantity,
            'unit' => $this->foodListing->unit,
            'category' => $this->foodListing->category,
            'restaurant_name' => $restaurantName,
            'expiry_date' => $this->foodListing->expiry_date->format('Y-m-d'),
            'expiry_time' => $this->foodListing->expiry_time,
            'approval_status' => $this->foodListing->approval_status,
            'url' => url('/admin/approvals'),
            'type' => 'food_listing_created',
        ];
    }
}
