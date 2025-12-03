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

        // Get pending food listings from restaurant owners
        $pendingFoodListings = FoodListing::with(['restaurantProfile', 'creator'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingFoodListings'));
    }

    /**
     * Restaurant owner dashboard.
     */
    public function restaurantDashboard()
    {
        $user = auth()->user();

        // Get restaurant profile
        $restaurantProfile = $user->restaurantProfile;

        $stats = [
            'active_listings' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->where('status', 'available')->count() : 0,
            'total_donations' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->count() : 0,
            'pending_pickups' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->where('status', 'reserved')->count() : 0,
            'total_people_helped' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->whereHas('matches')->count() : 0,
        ];

        $recentListings = $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get() : collect();

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
