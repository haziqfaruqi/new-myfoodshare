<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FoodListing;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard (redirects to role-based dashboard).
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isRestaurantOwner()) {
            return redirect()->route('restaurant.dashboard');
        } elseif ($user->isRecipient()) {
            return redirect()->route('recipient.dashboard');
        }

        return redirect('/');
    }

    /**
     * Admin dashboard.
     */
    public function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_restaurants' => User::where('role', 'restaurant_owner')->where('status', 'active')->count(),
            'total_recipients' => User::where('role', 'recipient')->where('status', 'active')->count(),
            'total_food_listings' => FoodListing::count(),
            'pending_approvals' => FoodListing::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Restaurant owner dashboard.
     */
    public function restaurantDashboard()
    {
        $user = auth()->user();

        $stats = [
            'active_listings' => FoodListing::where('user_id', $user->id)->where('status', 'available')->count(),
            'total_donations' => FoodListing::where('user_id', $user->id)->count(),
            'pending_pickups' => FoodListing::where('user_id', $user->id)->where('status', 'reserved')->count(),
            'total_people_helped' => $user->foodListings()->whereHas('matches')->count(),
        ];

        $recentListings = FoodListing::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('restaurant.dashboard', compact('stats', 'recentListings'));
    }

    /**
     * Recipient dashboard.
     */
    public function recipientDashboard()
    {
        $user = auth()->user();

        $stats = [
            'active_matches' => $user->recipient ? $user->recipient->matches()->where('status', 'confirmed')->count() : 0,
            'completed_pickups' => $user->recipient ? $user->recipient->matches()->where('status', 'completed')->count() : 0,
            'total_food_received' => $user->recipient ? $user->recipient->matches()->where('status', 'completed')->count() : 0,
        ];

        $recentMatches = $user->recipient ? $user->recipient->matches()
            ->with('foodListing')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get() : collect();

        return view('recipient.dashboard', compact('stats', 'recentMatches'));
    }

    /**
     * Admin user management.
     */
    public function manageUsers()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Admin food listing management.
     */
    public function manageFoodListings()
    {
        $listings = FoodListing::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.food-listings', compact('listings'));
    }

    /**
     * Restaurant owner's food listings.
     */
    public function myListings()
    {
        $user = auth()->user();
        $listings = FoodListing::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('restaurant.listings', compact('listings'));
    }

    /**
     * Create new food listing.
     */
    public function createListing()
    {
        return view('restaurant.create-listing');
    }

    /**
     * Restaurant profile.
     */
    public function restaurantProfile()
    {
        $user = auth()->user();
        return view('restaurant.profile', compact('user'));
    }

    /**
     * Available food for recipients.
     */
    public function availableFood()
    {
        $foodListings = FoodListing::where('status', 'available')
            ->orderBy('expiry_datetime', 'asc')
            ->paginate(12);

        return view('recipient.available-food', compact('foodListings'));
    }
}
