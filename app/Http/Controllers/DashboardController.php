<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\FoodListing;
use App\Models\PickupVerification;
use App\Models\FoodMatch;

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
            ->paginate(4);

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
            'active_listings' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->where('status', 'active')->where('approval_status', 'approved')->count() : 0,
            'total_donations' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->count() : 0,
            'pending_pickups' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->whereIn('status', ['active', 'reserved'])->count() : 0,
            'total_people_helped' => $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)->whereHas('matches')->count() : 0,
        ];

        $recentListings = $restaurantProfile ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->where(function ($q) {
                $q->where('expiry_date', '>', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->where('expiry_date', '=', now()->toDateString())
                         ->where('expiry_time', '>=', now()->format('H:i'));
                  });
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get() : collect();

        // Get latest 3 notifications for the restaurant user
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $data['message'] ?? 'New notification',
                    'details' => $data['details'] ?? null,
                    'time' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                    'color' => $this->getNotificationColor($notification->type),
                ];
            });

        // Count active recipients within 5km
        $activeRecipientsNearby = 0;
        $restaurantLat = $restaurantProfile->latitude;
        $restaurantLng = $restaurantProfile->longitude;

        if ($restaurantLat && $restaurantLng) {
            // Get all users with recipient profiles that have location data
            $recipientsWithLocation = User::whereHas('recipient')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();

            foreach ($recipientsWithLocation as $recipient) {
                $distance = $this->calculateDistance($restaurantLat, $restaurantLng, $recipient->latitude, $recipient->longitude);
                if ($distance <= 5) {
                    $activeRecipientsNearby++;
                }
            }
        }

        return view('restaurant.dashboard', compact('stats', 'recentListings', 'notifications', 'activeRecipientsNearby'));
    }

    /**
     * Get notification color based on type.
     */
    private function getNotificationColor($type)
    {
        switch ($type) {
            case 'App\Notifications\MatchFound':
                return 'bg-emerald-500';
            case 'App\Notifications\PickupCompleted':
                return 'bg-blue-500';
            case 'App\Notifications\NewRating':
                return 'bg-zinc-300';
            case 'App\Notifications\ListingApproved':
                return 'bg-emerald-500';
            case 'App\Notifications\ListingRejected':
                return 'bg-red-500';
            default:
                return 'bg-zinc-300';
        }
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
    /**
     * Calculate distance between two coordinates.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    public function recipientDashboard()
    {
        $user = auth()->user();

        // Get user's profile for pinned location (since they are the recipient/NGO)
        $pinnedLocation = null;

        if ($user->latitude && $user->longitude) {
            // Use user's pinned location
            $userLat = $user->latitude;
            $userLon = $user->longitude;
            $pinnedLocation = [
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'name' => json_decode($user->profile_data ?: '{}')->location_name ?? 'Organization Location'
            ];
        } else {
            // Fallback to default location
            $userLat = 3.1390; // Kuala Lumpur
            $userLon = 101.6869;
        }

        // Get available food listings within 5km radius
        $nearbyFoodListings = collect();
        if ($userLat && $userLon) {
            $allListings = FoodListing::with(['restaurantProfile', 'creator'])
                ->where('status', 'active')
                ->where('approval_status', 'approved')
                ->where('expiry_date', '>=', now()->toDateString())
                ->orWhere(function ($q) {
                    $q->where('expiry_date', '=', now()->toDateString())
                      ->where('expiry_time', '>=', now()->format('H:i'));
                })
                ->get();

            foreach ($allListings as $listing) {
                // Get coordinates from listing or restaurant profile
                $listingLat = $listing->latitude;
                $listingLon = $listing->longitude;

                if ((!$listingLat || !$listingLon) && $listing->restaurantProfile) {
                    $listingLat = $listing->restaurantProfile->latitude;
                    $listingLon = $listing->restaurantProfile->longitude;
                }

                if ($listingLat && $listingLon) {
                    $distance = $this->calculateDistance($userLat, $userLon, $listingLat, $listingLon);
                    if ($distance <= 5) {
                        $listing->distance = round($distance, 1);
                        $nearbyFoodListings->push($listing);
                    }
                }
            }
        }

        // Get user's existing matches for all food listings
        $userMatches = collect();
        if (auth()->user()->isRecipient()) {
            $userMatches = FoodMatch::where('recipient_id', auth()->id())
                ->whereIn('food_listing_id', FoodListing::pluck('id'))
                ->get()
                ->keyBy('food_listing_id');
        }

        // Get upcoming pickups (matches that are confirmed or scheduled)
        $upcomingPickups = collect();
        if ($user->recipient) {
            $upcomingPickups = $user->recipient->matches()
                ->with(['foodListing.restaurantProfile', 'foodListing.creator'])
                ->whereIn('status', ['approved', 'scheduled'])
                ->orderBy('pickup_scheduled_at', 'asc')
                ->get();
        } else {
            // Fallback: Try to get matches directly from user ID if recipient profile doesn't exist
            $upcomingPickups = \App\Models\FoodMatch::where('recipient_id', $user->id)
                ->with(['foodListing.restaurantProfile', 'foodListing.creator'])
                ->whereIn('status', ['approved', 'scheduled'])
                ->orderBy('pickup_scheduled_at', 'asc')
                ->get();
        }

        // Calculate available food count for sidebar
        $availableFoodCount = $this->getAvailableFoodCount();

        // Get stats
        $stats = [
            'active_matches' => 0,
            'completed_pickups' => 0,
            'total_food_received' => 0,
            'pending_requests' => 0,
            'available_food_count' => $availableFoodCount,
        ];

        if ($user->recipient) {
            $stats['active_matches'] = $user->recipient->matches()->whereIn('status', ['approved', 'scheduled'])->count();
            $stats['completed_pickups'] = $user->recipient->matches()->where('status', 'completed')->count();
            $stats['total_food_received'] = $user->recipient->matches()->where('status', 'completed')->count();
            $stats['pending_requests'] = $user->recipient->matches()->where('status', 'pending')->count();
        } else {
            // Fallback: Calculate directly from FoodMatch table
            $stats['active_matches'] = FoodMatch::where('recipient_id', $user->id)->whereIn('status', ['approved', 'scheduled'])->count();
            $stats['completed_pickups'] = FoodMatch::where('recipient_id', $user->id)->where('status', 'completed')->count();
            $stats['total_food_received'] = FoodMatch::where('recipient_id', $user->id)->where('status', 'completed')->count();
            $stats['pending_requests'] = FoodMatch::where('recipient_id', $user->id)->where('status', 'pending')->count();
        }

        // Share available food count with all recipient views
        view()->share('availableFoodCount', $availableFoodCount);

        return view('recipient.dashboard', compact('stats', 'nearbyFoodListings', 'upcomingPickups', 'userMatches', 'pinnedLocation'));
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

        // If no restaurant profile exists, create one with default values
        if (!$profile) {
            $profile = new \App\Models\RestaurantProfile([
                'user_id' => $user->id,
                'restaurant_name' => $user->name . ' Restaurant',
                'cuisine_type' => 'other',
                'status' => 'pending',
                'email' => $user->email,
            ]);
            $profile->save();
        }

        return view('restaurant.profile.edit', compact('user', 'profile'));
    }

    /**
     * Update restaurant profile.
     */
    public function updateRestaurantProfile(Request $request)
    {
        $user = auth()->user();
        $profile = $user->restaurantProfile;

        // If no restaurant profile exists, create one
        if (!$profile) {
            $profile = new \App\Models\RestaurantProfile([
                'user_id' => $user->id,
                'restaurant_name' => 'New Restaurant',
                'cuisine_type' => 'other',
                'status' => 'pending'
            ]);
            $profile->save();
        }

        // Debug: Log the incoming request data
        \Log::info('Restaurant profile update request:', $request->all());

        $validated = $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'business_hours' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Debug: Log validated data
        \Log::info('Validated restaurant profile data:', $validated);

        $updated = $profile->update($validated);

        // Debug: Log update result
        \Log::info('Restaurant profile update result:', ['updated' => $updated, 'profile_id' => $profile->id]);

        return redirect()->route('restaurant.profile')
            ->with('success', 'Restaurant profile updated successfully!');
    }

    /**
     * Available food for recipients.
     */
    public function availableFood()
    {
        $user = auth()->user();

        // Get recipient profile for pinned location
        $recipientProfile = $user->recipient;

        if ($recipientProfile && $recipientProfile->latitude && $recipientProfile->longitude) {
            // Use NGO's pinned location
            $userLat = $recipientProfile->latitude;
            $userLon = $recipientProfile->longitude;
        } else {
            // Fallback to user's current location
            $userLat = $user->latitude;
            $userLon = $user->longitude;
        }

        // Get available food listings within 5km radius
        if ($userLat && $userLon) {
            $allActiveListings = FoodListing::with(['restaurantProfile', 'creator'])
                ->where('status', 'active')
                ->where('approval_status', 'approved')
                ->where(function ($q) {
                    $q->where('expiry_date', '>=', now()->toDateString())
                      ->orWhere(function ($q2) {
                          $q2->where('expiry_date', '=', now()->toDateString())
                             ->where('expiry_time', '>=', now()->format('H:i'));
                      });
                })
                ->get();

            $nearbyFoodListings = collect();

            foreach ($allActiveListings as $listing) {
                // Get coordinates from listing or restaurant profile
                $listingLat = $listing->latitude;
                $listingLon = $listing->longitude;

                if ((!$listingLat || !$listingLon) && $listing->restaurantProfile) {
                    $listingLat = $listing->restaurantProfile->latitude;
                    $listingLon = $listing->restaurantProfile->longitude;
                }

                if ($listingLat && $listingLon) {
                    $distance = $this->calculateDistance($userLat, $userLon, $listingLat, $listingLon);

                    if ($distance <= 5) {
                        $listing->distance = round($distance, 1);
                        $nearbyFoodListings->push($listing);
                    }
                }
            }
        } else {
            // If user doesn't have coordinates, get all active approved listings
            $nearbyFoodListings = FoodListing::with(['restaurantProfile', 'creator'])
                ->where('status', 'active')
                ->where('approval_status', 'approved')
                ->where(function ($q) {
                    $q->where('expiry_date', '>=', now()->toDateString())
                      ->orWhere(function ($q2) {
                          $q2->where('expiry_date', '=', now()->toDateString())
                             ->where('expiry_time', '>=', now()->format('H:i'));
                      });
                })
                ->get();
        }

        // Share available food count with sidebar
        view()->share('availableFoodCount', $nearbyFoodListings->count());

        // Get user's existing matches for all food listings
        $userMatches = collect();
        if (auth()->user()->isRecipient()) {
            $userMatches = FoodMatch::where('recipient_id', auth()->id())
                ->whereIn('food_listing_id', FoodListing::pluck('id'))
                ->get()
                ->keyBy('food_listing_id');
        }

        // Paginate the nearby food listings
        $page = request()->get('page', 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $paginatedListings = new \Illuminate\Pagination\LengthAwarePaginator(
            $nearbyFoodListings->slice($offset, $perPage),
            $nearbyFoodListings->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('recipient.available-food', compact('paginatedListings', 'userMatches'));
    }

    /**
     * Get available food count within 5km radius.
     */
    private function getAvailableFoodCount()
    {
        $user = auth()->user();

        if (!$user) {
            return 0;
        }

        // If user doesn't have coordinates, return all active approved listings
        if (!$user->latitude || !$user->longitude) {
            return FoodListing::where('status', 'active')
                ->where('approval_status', 'approved')
                ->where(function ($q) {
                    $q->where('expiry_date', '>=', now()->toDateString())
                      ->orWhere(function ($q2) {
                          $q2->where('expiry_date', '=', now()->toDateString())
                             ->where('expiry_time', '>=', now()->format('H:i'));
                      });
                })
                ->count();
        }

        $allListings = FoodListing::with(['restaurantProfile', 'creator'])
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->where('expiry_date', '>=', now()->toDateString())
            ->orWhere(function ($q) {
                $q->where('expiry_date', '=', now()->toDateString())
                  ->where('expiry_time', '>=', now()->format('H:i'));
            })
            ->get();

        $count = 0;
        foreach ($allListings as $listing) {
            if ($listing->latitude && $listing->longitude) {
                $distance = $this->calculateDistance($user->latitude, $user->longitude, $listing->latitude, $listing->longitude);
                if ($distance <= 5) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Map view for recipients.
     */
    public function mapView()
    {
        $user = auth()->user();

        // Get user's profile for pinned location
        $pinnedLocation = null;

        if ($user->latitude && $user->longitude) {
            $pinnedLocation = [
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'name' => json_decode($user->profile_data ?: '{}')->location_name ?? 'Organization Location'
            ];
        }

        $availableFoodCount = $this->getAvailableFoodCount();
        view()->share('availableFoodCount', $availableFoodCount);

        return view('recipient.map-view', compact('pinnedLocation'));
    }

    /**
     * My matches for recipients.
     */
    public function myMatches()
    {
        $user = auth()->user();
        $availableFoodCount = $this->getAvailableFoodCount();

        // Share available food count with sidebar
        view()->share('availableFoodCount', $availableFoodCount);

        // Get user's matches with related data
        $matches = $user->recipient ? $user->recipient->matches()
            ->with(['foodListing.restaurantProfile', 'foodListing.creator'])
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        return view('recipient.my-matches', compact('matches'));
    }

    /**
     * Impact report for recipients.
     */
    public function impactReport()
    {
        $user = auth()->user();

        // Get user's completed matches for impact calculations
        $completedMatches = $user->recipient ? $user->recipient->matches()
            ->with(['foodListing.restaurantProfile'])
            ->where('status', 'completed')
            ->get() : collect();

        // Calculate impact metrics
        $totalCO2Saved = $completedMatches->sum(function($match) {
            return $match->foodListing->estimated_co2_saved ?? 0;
        });

        $totalMealsRecovered = $completedMatches->count();
        $totalMoneySaved = $completedMatches->sum(function($match) {
            return $match->foodListing->estimated_value ?? 0;
        });

        $peopleHelped = $completedMatches->count(); // Simple approximation

        // Get monthly recovery data for charts
        $monthlyRecoveryData = $this->getMonthlyRecoveryData();

        // Get food category distribution
        $foodCategoryData = $this->getFoodCategoryDistribution();

        // Get key statistics
        $keyStats = $this->getKeyImpactStatistics();

        // Get available food count for sidebar
        $availableFoodCount = $this->getAvailableFoodCount();
        view()->share('availableFoodCount', $availableFoodCount);

        return view('recipient.impact-report', compact(
            'totalCO2Saved', 'totalMealsRecovered', 'totalMoneySaved', 'peopleHelped',
            'completedMatches', 'monthlyRecoveryData', 'foodCategoryData', 'keyStats'
        ));
    }

    /**
     * Get monthly recovery data for charts.
     */
    private function getMonthlyRecoveryData()
    {
        $user = auth()->user();

        if (!$user->recipient) {
            return collect();
        }

        // Get completed matches grouped by month
        return $user->recipient->matches()
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->orderBy('completed_at')
            ->get()
            ->groupBy(function($match) {
                return $match->completed_at->format('Y-m');
            })
            ->map(function($matches, $month) {
                return [
                    'month' => $month,
                    'meals' => $matches->count(),
                    'co2_saved' => $matches->sum(function($match) {
                        return $match->foodListing->estimated_co2_saved ?? 0;
                    }),
                    'money_saved' => $matches->sum(function($match) {
                        return $match->foodListing->estimated_value ?? 0;
                    })
                ];
            });
    }

    /**
     * Get food category distribution data.
     */
    private function getFoodCategoryDistribution()
    {
        $user = auth()->user();

        if (!$user->recipient) {
            return collect();
        }

        // Define the specific categories requested by user
        $preferredCategories = ['Prepared Meals', 'Bakery', 'Produce', 'Dairy', 'Canned Goods'];

        // Get completed matches with food listing categories
        $completedMatches = $user->recipient->matches()
            ->where('status', 'completed')
            ->whereHas('foodListing', function($query) {
                $query->whereNotNull('category');
            })
            ->with('foodListing')
            ->get();

        // Group matches by category and count
        $categoryData = $completedMatches->groupBy(function($match) {
            $category = $match->foodListing->category;
            // Normalize category names to match preferred categories
            if (stripos($category, 'meal') !== false || stripos($category, 'food') !== false || stripos($category, 'ready') !== false) {
                return 'Prepared Meals';
            } elseif (stripos($category, 'bakery') !== false || stripos($category, 'bread') !== false || stripos($category, 'pastry') !== false) {
                return 'Bakery';
            } elseif (stripos($category, 'produce') !== false || stripos($category, 'fruit') !== false || stripos($category, 'vegetable') !== false) {
                return 'Produce';
            } elseif (stripos($category, 'dairy') !== false || stripos($category, 'milk') !== false || stripos($category, 'cheese') !== false || stripos($category, 'yogurt') !== false) {
                return 'Dairy';
            } elseif (stripos($category, 'canned') !== false || stripos($category, 'tin') !== false) {
                return 'Canned Goods';
            }
            return $category; // Keep original name if it doesn't match any preferred categories
        });

        // Map to the preferred categories structure
        return collect($preferredCategories)
            ->map(function($preferredCategory) use ($categoryData) {
                $matches = $categoryData->get($preferredCategory, collect());
                return [
                    'category' => $preferredCategory,
                    'count' => $matches->count(),
                    'percentage' => 0 // Will be calculated in view
                ];
            })
            ->filter(function($item) {
                return $item['count'] > 0; // Only include categories with data
            })
            ->sortByDesc('count');
    }

    /**
     * Get key impact statistics.
     */
    private function getKeyImpactStatistics()
    {
        $user = auth()->user();

        if (!$user->recipient) {
            return [
                'averageRating' => 0,
                'successRate' => 0,
                'avgResponseTime' => 0,
                'partnerRestaurants' => 0
            ];
        }

        $completedMatches = $user->recipient->matches()
            ->where('status', 'completed')
            ->count();

        $totalMatches = $user->recipient->matches()->count();

        $successRate = $totalMatches > 0 ? ($completedMatches / $totalMatches) * 100 : 0;

        // Get unique restaurants helped
        $partnerRestaurants = $user->recipient->matches()
            ->where('status', 'completed')
            ->whereHas('foodListing.restaurantProfile')
            ->with('foodListing.restaurantProfile')
            ->get()
            ->unique(function($match) {
                return $match->foodListing->restaurantProfile->id ?? $match->foodListing->creator_id;
            })
            ->count();

        return [
            'averageRating' => 4.8, // Placeholder - would need ratings system
            'successRate' => round($successRate, 1),
            'avgResponseTime' => 2.3, // Placeholder - would need response time tracking
            'partnerRestaurants' => $partnerRestaurants
        ];
    }

    /**
     * NGO profile for recipients.
     */
    public function ngoProfile()
    {
        $user = auth()->user();

        // Get user's profile data (since they are the recipient/NGO)
        $recipientProfile = $user;

        // Get available food count for sidebar
        $availableFoodCount = $this->getAvailableFoodCount();
        view()->share('availableFoodCount', $availableFoodCount);

        return view('recipient.ngo-profile', compact('recipientProfile'));
    }

    /**
     * Generate pickup monitoring report
     */
    public function pickupMonitoringReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Get pickup verifications for the date range
        $pickupVerifications = PickupVerification::with(['match.foodListing', 'match.recipient', 'match.restaurant'])
            ->whereDate('scanned_at', '>=', $startDate)
            ->whereDate('scanned_at', '<=', $endDate)
            ->orderBy('scanned_at', 'desc')
            ->get();

        // Calculate report statistics
        $totalPickups = $pickupVerifications->count();
        $verifiedPickups = $pickupVerifications->where('verification_status', 'verified')->count();
        $failedPickups = $pickupVerifications->where('verification_status', 'failed')->count();
        $pendingPickups = $pickupVerifications->where('verification_status', 'pending')->count();

        $successRate = $totalPickups > 0 ? ($verifiedPickups / $totalPickups) * 100 : 0;

        // Group by date
        $dailyStats = $pickupVerifications->groupBy(function($item) {
            return $item->scanned_at->format('Y-m-d');
        });

        // Group by recipient
        $recipientStats = $pickupVerifications->groupBy(function($item) {
            return $item->match->recipient ? ($item->match->recipient->organization_name ?? $item->match->recipient->name) : 'Unknown Recipient';
        });

        // Generate CSV for download
        if ($request->has('export')) {
            return $this->exportPickupReport($pickupVerifications, $startDate, $endDate);
        }

        return view('admin.pickup-monitoring-report', compact(
            'pickupVerifications',
            'startDate',
            'endDate',
            'totalPickups',
            'verifiedPickups',
            'failedPickups',
            'pendingPickups',
            'successRate',
            'dailyStats',
            'recipientStats'
        ));
    }

    /**
     * Export pickup monitoring report as CSV
     */
    private function exportPickupReport($pickupVerifications, $startDate, $endDate)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pickup-monitoring-report-' . $startDate . '-to-' . $endDate . '.csv"',
        ];

        $callback = function() use ($pickupVerifications) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Pickup ID',
                'Food Item',
                'Restaurant',
                'Recipient',
                'Scheduled Date',
                'Scanned Date',
                'Verification Status',
                'QR Code',
                'Notes'
            ]);

            // Add data rows
            foreach ($pickupVerifications as $verification) {
                fputcsv($file, [
                    $verification->match->id,
                    $verification->match->foodListing->food_name,
                    $verification->match->restaurant->restaurant_name ?? $verification->match->foodListing->creator->name,
                    $verification->match->recipient ? ($verification->match->recipient->organization_name ?? $verification->match->recipient->name) : 'Unknown Recipient',
                    $verification->match->pickup_scheduled_at->format('Y-m-d H:i:s'),
                    $verification->scanned_at->format('Y-m-d H:i:s'),
                    $verification->verification_status,
                    $verification->qr_code,
                    $verification->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Update NGO Profile
     */
    public function updateNgoProfile(Request $request)
    {
        $user = auth()->user();

        // Validate the input
        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'ngo_registration' => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255',
        ]);

        // Update the user's profile
        $user->update([
            'organization_name' => $validated['organization_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => $validated['description'],
            'ngo_registration' => $validated['ngo_registration'],
        ]);

        // Update location name if provided (stored in user profile as extra data)
        if (isset($validated['location_name'])) {
            // You might want to store this in a separate profile table
            // For now, we'll store it in the description or create a JSON field
            $user->profile_data = json_encode([
                'location_name' => $validated['location_name']
            ]);
            $user->save();
        }

        return redirect()->route('recipient.ngo-profile')
            ->with('success', 'NGO profile updated successfully!');
    }

    /**
     * Show logo settings page.
     */
    public function getLogoSettings()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $currentLogo = \App\Models\Setting::get('site_logo');
        return view('admin.logo-settings', compact('currentLogo'));
    }

    /**
     * Upload logo.
     */
    public function uploadLogo(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            $oldLogo = \App\Models\Setting::get('site_logo');
            if ($oldLogo && file_exists(public_path('uploads/logo/' . basename($oldLogo)))) {
                unlink(public_path('uploads/logo/' . basename($oldLogo)));
            }

            // Create uploads directory if it doesn't exist
            $uploadPath = public_path('uploads/logo');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Store the new logo
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($uploadPath, $filename);

            // Save to database
            \App\Models\Setting::set('site_logo', 'uploads/logo/' . $filename);

            return redirect()->route('admin.settings.logo')
                ->with('success', 'Logo uploaded successfully!');
        }

        return redirect()->route('admin.settings.logo')
            ->with('error', 'No file uploaded.');
    }
}
