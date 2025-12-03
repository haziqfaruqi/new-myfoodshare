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
     * Show user approvals page.
     */
    public function userApprovals()
    {
        $pendingUsers = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $recentlyApproved = User::where('status', 'active')
            ->whereNotNull('approved_at')
            ->orderBy('approved_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.user-approvals', compact('pendingUsers', 'recentlyApproved'));
    }

    /**
     * Approve user registration.
     */
    public function approveUser(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $user->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.user-approvals')
            ->with('success', 'User approved successfully!');
    }

    /**
     * Reject user registration.
     */
    public function rejectUser(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.user-approvals')
            ->with('success', 'User rejected successfully!');
    }

    /**
     * Show user management page.
     */
    public function userManagement()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'restaurant_owners' => User::where('role', 'restaurant_owner')->where('status', 'active')->count(),
            'recipients' => User::where('role', 'recipient')->where('status', 'active')->count(),
        ];

        return view('admin.user-management', compact('users', 'stats'));
    }

    /**
     * Update user status.
     */
    public function updateUserStatus(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,pending,rejected',
            'role' => 'required|in:admin,restaurant_owner,recipient',
        ]);

        $user->update($validated);

        return redirect()->route('admin.user-management')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        if (!auth()->user()->isAdmin() || auth()->user()->id === $user->id) {
            abort(403, 'Unauthorized action');
        }

        $user->delete();

        return redirect()->route('admin.user-management')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Show active listings management page.
     */
    public function activeListings()
    {
        $activeListings = FoodListing::with(['restaurantProfile', 'creator'])
            ->whereIn('status', ['active', 'reserved'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_listings' => FoodListing::count(),
            'active_listings' => FoodListing::where('status', 'active')->count(),
            'reserved_listings' => FoodListing::where('status', 'reserved')->count(),
            'expired_listings' => FoodListing::where('expiry_date', '<', now()->toDateString())->count(),
            'pending_approvals' => FoodListing::where('approval_status', 'pending')->count(),
        ];

        return view('admin.active-listings', compact('activeListings', 'stats'));
    }

    /**
     * Show pickup verification monitoring page.
     */
    public function pickupMonitoring()
    {
        // Get active matches that need verification (approved or scheduled but not yet verified)
        $activePickups = \App\Models\FoodMatch::with(['foodListing.restaurantProfile', 'recipient', 'pickupVerification'])
            ->whereIn('status', ['approved', 'scheduled'])
            ->whereDoesntHave('pickupVerification') // Only matches without verification records
            ->where(function($query) {
                $query->whereNull('pickup_scheduled_at')
                      ->orWhere('pickup_scheduled_at', '<=', now());
            })
            ->orderBy('created_at', 'asc')
            ->take(15)
            ->get();

        // Get recent pickups with verification records (use scanned_at as verification timestamp)
        $recentPickups = \App\Models\PickupVerification::with(['match.foodListing.restaurantProfile', 'match.recipient'])
            ->whereNotNull('scanned_at')
            ->where('scanned_at', '>=', now()->subHours(24))
            ->orderBy('scanned_at', 'desc')
            ->take(10)
            ->get();

        // Get verification stats
        $pendingVerifications = \App\Models\FoodMatch::with(['foodListing.restaurantProfile', 'recipient'])
            ->whereIn('status', ['approved', 'scheduled'])
            ->whereDoesntHave('pickupVerification')
            ->count();

        $completedToday = \App\Models\PickupVerification::whereDate('scanned_at', today())->count();

        $totalVerified = \App\Models\PickupVerification::whereNotNull('scanned_at')->count();

        // Calculate verification rate (mock calculation)
        $totalRecentMatches = \App\Models\FoodMatch::where('status', 'approved')->where('created_at', '>=', now()->subDays(7))->count();
        $verificationRate = $totalRecentMatches > 0 ? round(($totalVerified / max($totalRecentMatches, 1)) * 100) : 85;

        $stats = [
            'active_pickups' => $pendingVerifications,
            'completed_today' => $completedToday,
            'verification_rate' => $verificationRate,
            'total_verified' => $totalVerified,
        ];

        return view('admin.pickup-monitoring', compact('activePickups', 'recentPickups', 'stats'));
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
     * Restaurant owner schedule management.
     */
    public function pickupSchedule()
    {
        $user = auth()->user();

        // Get today's pickups (scheduled for today)
        $todayPickups = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->whereDate('pickup_scheduled_at', today())
            ->whereIn('status', ['approved', 'scheduled'])
            ->orderBy('pickup_scheduled_at', 'asc')
            ->get();

        // Get pending pickups (approved but not scheduled)
        $pendingPickups = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->where('status', 'approved')
            ->whereNull('pickup_scheduled_at')
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        // Get completed pickups this week
        $completedPickups = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->startOfWeek())
            ->count();

        // Get total donated this month
        $totalDonated = \App\Models\FoodListing::where('created_by', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('quantity');

        
        // Get recent activity
        $recentActivity = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->whereIn('status', ['approved', 'scheduled', 'completed'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('restaurant.schedule.index', compact(
            'todayPickups',
            'pendingPickups',
            'completedPickups',
            'totalDonated',
            'recentActivity'
        ));
    }

    /**
     * Generate calendar days for current month (optimized).
     */
    private function generateCalendarDaysOptimized()
    {
        $days = [];
        $currentDate = now();
        $startOfMonth = $currentDate->startOfMonth()->startOfWeek();
        $endOfMonth = $currentDate->endOfMonth()->endOfWeek();

        $userId = auth()->user()->id;

        // Simplified calendar generation without complex queries
        $date = $startOfMonth;
        while ($date <= $endOfMonth) {
            $dateStr = $date->toDateString();
            $day = [
                'day' => $date->day,
                'is_current_month' => $date->month === $currentDate->month,
                'is_today' => $date->isToday(),
                'pickup_count' => 0, // Simplified - no complex queries
                'has_completed' => false // Simplified - no complex queries
            ];

            $days[] = $day;
            $date->addDay();
        }

        return $days;
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
        $profile = $user->restaurantProfile;
        return view('restaurant.profile.index', compact('user', 'profile'));
    }

    /**
     * Edit restaurant profile.
     */
    public function editRestaurantProfile()
    {
        $user = auth()->user();
        $profile = $user->restaurantProfile;
        return view('restaurant.profile.edit', compact('user', 'profile'));
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
