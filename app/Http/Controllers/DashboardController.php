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
            'pending_approvals' => FoodListing::where('approval_status', 'pending')->count(),
            'active_listings' => FoodListing::where('status', 'active')->count(),
            'listings_today' => FoodListing::whereDate('created_at', today())->count(),
            'matches_total' => \App\Models\FoodMatch::count(),
            'pending_users' => User::where('status', 'pending')->count(),
        ];

        // Get pending food listings from restaurant owners
        $pendingFoodListings = FoodListing::with(['restaurantProfile', 'creator'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Get pending user registrations
        $pendingUsers = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent activity
        $recentActivity = collect();

        // Get recently approved listings
        $approvedListings = FoodListing::with(['creator', 'restaurantProfile'])
            ->where('approval_status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        foreach ($approvedListings as $listing) {
            $recentActivity->push([
                'type' => 'listing_approved',
                'title' => 'Donation Approved',
                'description' => 'Verified "' . $listing->food_name . '"',
                'time' => $listing->updated_at->diffForHumans(),
                'icon' => 'check',
                'color' => 'emerald',
            ]);
        }

        // Get recent matches
        $recentMatches = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentMatches as $match) {
            $recipientName = 'Unknown Recipient';
            if ($match->recipient) {
                $recipientName = $match->recipient->organization_name ?? $match->recipient->name ?? 'Unknown Recipient';
            }

            $recentActivity->push([
                'type' => 'match_created',
                'title' => 'Match Found',
                'description' => 'Linked with ' . $recipientName,
                'time' => $match->created_at->diffForHumans(),
                'icon' => 'user-check',
                'color' => 'blue',
            ]);
        }

        // Get recent listings
        $recentListings = FoodListing::with(['creator', 'restaurantProfile'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentListings as $listing) {
            $creatorName = 'Unknown';
            if ($listing->restaurantProfile) {
                $creatorName = $listing->restaurantProfile->restaurant_name ?? 'Unknown Restaurant';
            } elseif ($listing->creator) {
                $creatorName = $listing->creator->name ?? 'Unknown User';
            }

            $recentActivity->push([
                'type' => 'listing_created',
                'title' => 'Listing Created',
                'description' => 'By ' . $creatorName,
                'time' => $listing->created_at->diffForHumans(),
                'icon' => 'upload-cloud',
                'color' => 'zinc',
            ]);
        }

        // Sort by time and take 5
        $recentActivity = $recentActivity->take(5);

        return view('admin.dashboard', compact('stats', 'pendingFoodListings', 'recentActivity', 'pendingUsers'));
    }

    /**
     * Show user approvals page.
     */
    public function userApprovals()
    {
        $pendingUsers = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        // Get users approved today
        $approvedToday = User::where('status', 'active')
            ->whereDate('approved_at', today())
            ->orderBy('approved_at', 'desc')
            ->get();

        // Get recently approved users (last 5, any time)
        $recentlyApproved = User::where('status', 'active')
            ->whereNotNull('approved_at')
            ->orderBy('approved_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.user-approvals', compact('pendingUsers', 'recentlyApproved', 'approvedToday'));
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
            'inactive_users' => User::whereIn('status', ['suspended', 'rejected'])->count(),
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
            'status' => 'required|in:active,suspended,pending,rejected',
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
     * Get user profile data for AJAX request.
     */
    public function getUserProfile(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'created_at' => $user->created_at?->format('M j, Y'),
            'last_login' => $user->last_login?->format('M j, Y g:i A'),
        ];

        // Add restaurant profile if exists
        if ($user->restaurantProfile) {
            $userData['restaurant_profile'] = [
                'restaurant_name' => $user->restaurantProfile->restaurant_name,
                'address' => $user->restaurantProfile->address,
                'phone' => $user->restaurantProfile->phone,
            ];
        }

        // Add recipient profile if exists
        if ($user->recipient) {
            $userData['recipient_profile'] = [
                'organization_name' => $user->recipient->organization_name,
                'address' => $user->recipient->address,
                'phone' => $user->recipient->phone,
            ];
        }

        // Add user's phone if available
        if ($user->phone) {
            $userData['phone'] = $user->phone;
        }

        // Add user's address if available
        if ($user->address) {
            $userData['address'] = $user->address;
        }

        return response()->json([
            'success' => true,
            'user' => $userData
        ]);
    }

    /**
     * Show active listings management page.
     */
    public function activeListings()
    {
        $activeListings = FoodListing::with(['restaurantProfile', 'creator'])
            ->whereIn('status', ['active', 'matched'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_listings' => FoodListing::count(),
            'active_listings' => FoodListing::where('status', 'active')->count(),
            'matched_listings' => FoodListing::where('status', 'matched')->count(),
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
            ->where(function ($query) {
                $query->whereNull('pickup_scheduled_at')
                    ->orWhere('pickup_scheduled_at', '<=', now());
            })
            ->orderBy('created_at', 'asc')
            ->take(15)
            ->get();

        // Get recent pickups with verification records (use scanned_at as verification timestamp)
        $recentPickups = \App\Models\PickupVerification::with(['foodMatch.foodListing.restaurantProfile', 'foodMatch.recipient'])
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

        // Get restaurant profile - create if missing for existing users
        $restaurantProfile = $user->restaurantProfile;

        // If no profile exists but user has restaurant data, create one
        if (!$restaurantProfile && $user->restaurant_name) {
            $restaurantProfile = \App\Models\RestaurantProfile::create([
                'user_id' => $user->id,
                'restaurant_name' => $user->restaurant_name,
                'address' => $user->address ?? '',
                'business_license' => $user->business_license ?? '',
                'cuisine_type' => $user->cuisine_type ?? 'other',
                'status' => $user->status ?? 'pending',
            ]);
        }

        // Determine query method: by restaurant_profile_id or by created_by (fallback)
        $listingsQuery = $restaurantProfile
            ? FoodListing::where('restaurant_profile_id', $restaurantProfile->id)
            : FoodListing::where('created_by', $user->id);

        $stats = [
            'active_listings' => (clone $listingsQuery)->where('status', 'active')->where('approval_status', 'approved')->count(),
            'total_donations' => $listingsQuery->count(),
            'pending_pickups' => (clone $listingsQuery)->whereIn('status', ['active', 'reserved'])->count(),
            'total_people_helped' => (clone $listingsQuery)->whereHas('matches')->count(),
            'pending_approval' => (clone $listingsQuery)->where('approval_status', 'pending')->count(),
        ];

        $recentListings = (clone $listingsQuery)
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->whereDoesntHave('matches', function($q) {
                $q->where('status', 'completed');
            })
            ->where(function ($q) {
                $q->where('expiry_date', '>', now()->toDateString())
                    ->orWhere(function ($q2) {
                        $q2->where('expiry_date', '=', now()->toDateString())
                            ->where('expiry_time', '>=', now()->format('H:i'));
                    });
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get latest 3 notifications for the restaurant user
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($notification) {
                $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
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

        if ($restaurantProfile && $restaurantProfile->latitude && $restaurantProfile->longitude) {
            $restaurantLat = $restaurantProfile->latitude;
            $restaurantLng = $restaurantProfile->longitude;

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
            ->whereHas('foodListing', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->whereDate('pickup_scheduled_at', today())
            ->whereIn('status', ['approved', 'scheduled'])
            ->orderBy('pickup_scheduled_at', 'asc')
            ->get();

        // Get pending pickups (approved but not scheduled)
        $pendingPickups = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->where('status', 'approved')
            ->whereNull('pickup_scheduled_at')
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        // Get completed pickups this week
        $completedPickups = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function ($query) use ($user) {
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
            ->whereHas('foodListing', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->whereIn('status', ['approved', 'scheduled', 'completed'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // Get upcoming pickups (next 5, sorted by pickup date)
        $upcomingPickups = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->whereIn('status', ['approved', 'scheduled'])
            ->whereNotNull('pickup_scheduled_at')
            ->where('pickup_scheduled_at', '>=', now())
            ->orderBy('pickup_scheduled_at', 'asc')
            ->take(5)
            ->get();

        return view('restaurant.schedule.index', compact(
            'todayPickups',
            'pendingPickups',
            'completedPickups',
            'totalDonated',
            'recentActivity',
            'upcomingPickups'
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

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function recipientDashboard()
    {
        $user = auth()->user();

        // Get user's profile for pinned location (since they are the recipient)
        $pinnedLocation = null;

        if ($user->latitude && $user->longitude) {
            // Use user's pinned location
            $userLat = $user->latitude;
            $userLon = $user->longitude;
            $pinnedLocation = [
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'name' => json_decode($user->profile_data ?: '{}', true)['location_name'] ?? 'Organization Location'
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
                ->whereDoesntHave('matches', function($q) {
                    $q->where('status', 'completed');
                })
                ->where(function ($q) {
                    $q->where('expiry_date', '>', now()->toDateString())
                      ->orWhere(function ($q2) {
                          $q2->where('expiry_date', '=', now()->toDateString())
                              ->where('expiry_time', '>=', now()->format('H:i'));
                      });
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
                ->with(['foodListing.restaurantProfile', 'foodListing.creator', 'pickupVerification'])
                ->whereIn('status', ['approved', 'scheduled'])
                ->orderBy('pickup_scheduled_at', 'asc')
                ->get();

            // Generate verification codes for approved pickups that don't have them
            foreach ($upcomingPickups as $pickup) {
                if ($pickup->status == 'approved' && !$pickup->pickupVerification) {
                    \App\Models\PickupVerification::generateForMatch($pickup);
                }
            }

            // Reload with verification data
            $upcomingPickups = $user->recipient->matches()
                ->with(['foodListing.restaurantProfile', 'foodListing.creator', 'pickupVerification'])
                ->whereIn('status', ['approved', 'scheduled'])
                ->orderBy('pickup_scheduled_at', 'asc')
                ->get();
        } else {
            // Fallback: Try to get matches directly from user ID if recipient profile doesn't exist
            $upcomingPickups = \App\Models\FoodMatch::where('recipient_id', $user->id)
                ->with(['foodListing.restaurantProfile', 'foodListing.creator', 'pickupVerification'])
                ->whereIn('status', ['approved', 'scheduled'])
                ->orderBy('pickup_scheduled_at', 'asc')
                ->get();

            // Generate verification codes for approved pickups that don't have them
            foreach ($upcomingPickups as $pickup) {
                if ($pickup->status == 'approved' && !$pickup->pickupVerification) {
                    \App\Models\PickupVerification::generateForMatch($pickup);
                }
            }

            // Reload with verification data
            $upcomingPickups = \App\Models\FoodMatch::where('recipient_id', $user->id)
                ->with(['foodListing.restaurantProfile', 'foodListing.creator', 'pickupVerification'])
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

        // If no restaurant profile exists but user has restaurant data, create one
        if (!$profile && $user->restaurant_name) {
            $profile = \App\Models\RestaurantProfile::create([
                'user_id' => $user->id,
                'restaurant_name' => $user->restaurant_name,
                'address' => $user->address ?? '',
                'business_license' => $user->business_license ?? '',
                'cuisine_type' => $user->cuisine_type ?? 'other',
                'status' => $user->status ?? 'pending',
            ]);
        }

        // If still no profile, create a default one
        if (!$profile) {
            $profile = new \App\Models\RestaurantProfile([
                'user_id' => $user->id,
                'restaurant_name' => $user->name . ' Restaurant',
                'cuisine_type' => 'other',
                'status' => 'pending',
            ]);
        }

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
            // Use recipient's pinned location
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
                ->whereDoesntHave('matches', function($q) {
                    $q->where('status', 'completed');
                })
                ->where(function ($q) {
                    $q->where('expiry_date', '>', now()->toDateString())
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
                ->whereDoesntHave('matches', function($q) {
                    $q->where('status', 'completed');
                })
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
                ->whereDoesntHave('matches', function($q) {
                    $q->where('status', 'completed');
                })
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
            ->whereDoesntHave('matches', function($q) {
                $q->where('status', 'completed');
            })
            ->where(function ($q) {
                $q->where('expiry_date', '>', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->where('expiry_date', '=', now()->toDateString())
                          ->where('expiry_time', '>=', now()->format('H:i'));
                  });
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
                'name' => json_decode($user->profile_data ?: '{}', true)['location_name'] ?? 'Organization Location'
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

        // Get user's matches with related data - use User's matches() relationship directly
        $matches = $user->matches()
            ->with(['foodListing.restaurantProfile', 'foodListing.creator'])
            ->orderBy('created_at', 'desc')
            ->get();

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
        $totalCO2Saved = $completedMatches->sum(function ($match) {
            return $match->foodListing->estimated_co2_saved ?? 0;
        });

        $totalMealsRecovered = $completedMatches->count();
        $totalMoneySaved = $completedMatches->sum(function ($match) {
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
            'totalCO2Saved',
            'totalMealsRecovered',
            'totalMoneySaved',
            'peopleHelped',
            'completedMatches',
            'monthlyRecoveryData',
            'foodCategoryData',
            'keyStats'
        ));
    }

    /**
     * Get monthly recovery data for charts.
     */
    private function getMonthlyRecoveryData()
    {
        $user = auth()->user();

        // Always try to get the recipient profile for the user
        $recipient = $user->recipient;

        if (!$recipient) {
            // No recipient profile found, return empty
            return collect();
        }

        // Get matches from recipient relationship
        $matches = $recipient->matches()
            ->with('foodListing')
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->orderBy('completed_at')
            ->get();

        if ($matches->isEmpty()) {
            return collect();
        }

        // Group by month and calculate stats
        return $matches->groupBy(function ($match) {
            return $match->completed_at->format('Y-m');
        })->map(function ($monthMatches, $month) {
            return [
                'month' => $month,
                'meals' => $monthMatches->count(),
                'co2_saved' => $monthMatches->sum(function ($match) {
                    return $match->foodListing->estimated_co2_saved ?? 0;
                }),
                'money_saved' => $monthMatches->sum(function ($match) {
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

        // Always try to get the recipient profile for the user
        $recipient = $user->recipient;

        if (!$recipient) {
            return collect();
        }

        // Define the specific categories requested by user
        $preferredCategories = ['Prepared Meals', 'Bakery', 'Produce', 'Dairy', 'Canned Goods'];

        // Get completed matches with food listing categories
        $completedMatches = $recipient->matches()
            ->where('status', 'completed')
            ->whereHas('foodListing', function ($query) {
                $query->whereNotNull('category');
            })
            ->with('foodListing')
            ->get();

        if ($completedMatches->isEmpty()) {
            return collect();
        }

        // Group matches by category and count
        $categoryData = $completedMatches->groupBy(function ($match) {
            $category = $match->foodListing->category ?? 'Other';
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
            ->map(function ($preferredCategory) use ($categoryData) {
                $matches = $categoryData->get($preferredCategory, collect());
                return [
                    'category' => $preferredCategory,
                    'count' => $matches->count(),
                    'percentage' => 0 // Will be calculated in view
                ];
            })
            ->filter(function ($item) {
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

        // Always try to get the recipient profile for the user
        $recipient = $user->recipient;

        if (!$recipient) {
            return [
                'averageRating' => 0,
                'successRate' => 0,
                'avgResponseTime' => 0,
                'partnerRestaurants' => 0
            ];
        }

        $completedMatches = $recipient->matches()
            ->where('status', 'completed')
            ->count();

        $totalMatches = $recipient->matches()->count();

        $successRate = $totalMatches > 0 ? ($completedMatches / $totalMatches) * 100 : 0;

        // Get unique restaurants helped
        $partnerRestaurants = $recipient->matches()
            ->where('status', 'completed')
            ->whereHas('foodListing.restaurantProfile')
            ->with('foodListing.restaurantProfile')
            ->get()
            ->unique(function ($match) {
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
     * Profile for recipients.
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
        $pickupVerifications = PickupVerification::with(['foodMatch.foodListing', 'foodMatch.recipient', 'foodListing'])
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
        $dailyStats = $pickupVerifications->groupBy(function ($item) {
            return $item->scanned_at->format('Y-m-d');
        });

        // Group by recipient
        $recipientStats = $pickupVerifications->groupBy(function ($item) {
            return $item->foodMatch?->recipient ? ($item->foodMatch->recipient->organization_name ?? $item->foodMatch->recipient->name) : 'Unknown Recipient';
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

        $callback = function () use ($pickupVerifications) {
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
                    $verification->foodMatch?->id ?? $verification->id,
                    $verification->foodListing?->food_name ?? $verification->foodMatch?->foodListing?->food_name ?? 'N/A',
                    $verification->foodMatch?->foodListing?->restaurantProfile?->restaurant_name ?? $verification->foodListing?->restaurantProfile?->restaurant_name ?? $verification->foodListing?->creator?->name ?? 'N/A',
                    $verification->foodMatch?->recipient?->organization_name ?? $verification->foodMatch?->recipient?->name ?? $verification->recipient?->organization_name ?? $verification->recipient?->name ?? 'Unknown Recipient',
                    $verification->foodMatch?->pickup_scheduled_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $verification->scanned_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $verification->verification_status,
                    $verification->verification_code,
                    $verification->recipient_notes ?? $verification->admin_notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Update Recipient Profile
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
            ->with('success', 'Recipient updated successfully!');
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

    /**
     * Show pickup verification page.
     */
    public function showVerificationPage($code)
    {
        $verification = \App\Models\PickupVerification::with(['foodMatch.foodListing', 'foodMatch.restaurantProfile'])
            ->where('verification_code', $code)
            ->where('verification_status', 'pending')
            ->firstOrFail();

        // Check if user is authenticated
        if (auth()->check()) {
            // Check if this verification belongs to the authenticated user
            if ($verification->recipient_id !== auth()->id()) {
                abort(403, 'Unauthorized access');
            }
        } else {
            // Store the verification code in session for after login
            session(['pending_verification' => $code]);
            // Redirect to login page
            return redirect()->route('login')
                ->with('message', 'Please login to verify your pickup')
                ->with('verification_code', $code);
        }

        return view('recipient.verify-pickup', compact('verification'));
    }

    /**
     * Process pickup verification.
     */
    public function verifyPickup(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
            'pickup_code' => 'required|string',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the verification record - allow pending or verified (scanned) status
        $verification = \App\Models\PickupVerification::where('verification_code', $request->verification_code)
            ->whereIn('verification_status', ['pending', 'verified'])
            ->first();

        // Debug information - remove this in production
        if (app()->environment('local')) {
            \Log::info('Verification Debug', [
                'submitted_verification_code' => $request->verification_code,
                'submitted_pickup_code' => $request->pickup_code,
                'verification_found' => $verification ? true : false,
                'verification_status' => $verification ? $verification->verification_status : 'N/A',
                'verification_id' => $verification ? $verification->id : 'N/A'
            ]);

            // Also check if any verification exists with this code (regardless of status)
            $anyVerification = \App\Models\PickupVerification::where('verification_code', $request->verification_code)->first();
            if ($anyVerification) {
                \Log::info('Verification Found (any status)', [
                    'status' => $anyVerification->verification_status,
                    'id' => $anyVerification->id,
                    'recipient_id' => $anyVerification->recipient_id,
                    'food_match_id' => $anyVerification->food_match_id
                ]);
            }
        }

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Verification not found or already processed.'
            ], 404);
        }

        // Check if the pickup code matches
        if ($verification->verification_code !== $request->pickup_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid pickup code. Please try again.'
            ], 422);
        }

        // Check if this verification belongs to the authenticated user
        if ($verification->recipient_id !== auth()->id()) {
            // Check if user is authenticated and has recipient role
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to verify your pickup. You must be logged in as a recipient user.'
                ], 401);
            }

            if (!auth()->user()->isRecipient()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only recipient users can verify pickups. Please login with a recipient account.'
                ], 403);
            }

            return response()->json([
                'success' => false,
                'message' => 'This verification does not belong to your account.'
            ], 403);
        }

        // Handle photo upload if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoPath = 'pickup-evidence/' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('storage/' . dirname($photoPath)), basename($photoPath));
        }

        // Update verification
        $verification->update([
            'qr_code_scanned' => $request->pickup_code,
            'scanned_at' => now(),
            'verification_status' => 'verified',
            'quality_rating' => $request->quality_rating,
            'recipient_notes' => $request->notes,
            'photo_evidence' => $photoPath ? [$photoPath] : [],
            'quality_confirmed' => $request->quality_rating >= 4,
            'pickup_completed_at' => now(),
        ]);

        // Update the food match status
        $verification->foodMatch->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update the food listing status to picked_up
        $foodListing = $verification->foodMatch->foodListing;
        if ($foodListing) {
            $foodListing->update([
                'status' => 'picked_up'
            ]);
        }

        // Send notification to restaurant
        $restaurant = $verification->foodMatch->foodListing->creator;
        if ($restaurant) {
            $restaurant->notify(new \App\Notifications\PickupVerified($verification));
        }

        return response()->json([
            'success' => true,
            'message' => 'Pickup verified successfully!',
            'redirect_url' => route('recipient.dashboard')
        ]);
    }

    /**
     * Show dedicated verification page for scanning QR codes.
     */
    public function verificationPage()
    {
        $user = auth()->user();

        // Get all pending verifications for this recipient
        $pendingVerifications = \App\Models\PickupVerification::with(['foodMatch.foodListing', 'foodMatch.restaurantProfile'])
            ->where('recipient_id', $user->recipient->id ?? $user->id)
            ->where('verification_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Also get approved pickups that don't have verifications yet
        $approvedPickups = \App\Models\FoodMatch::with(['foodListing'])
            ->where('recipient_id', $user->recipient->id ?? $user->id)
            ->where('status', 'approved')
            ->whereDoesntHave('pickupVerification')
            ->get();

        return view('recipient.verification-page', compact('pendingVerifications', 'approvedPickups'));
    }

    /**
     * Show QR scanner page for recipient
     */
    public function showScannerPage()
    {
        if (!auth()->check() || !auth()->user()->isRecipient()) {
            if (!auth()->check()) {
                return redirect()->route('login')->with('message', 'Please login to access the scanner page.');
            }
            abort(403, 'Unauthorized action');
        }

        $user = auth()->user();

        // Get current pickup (approved match without verification)
        $currentPickup = FoodMatch::with(['foodListing.restaurantProfile'])
            ->where('recipient_id', $user->id)
            ->where('status', 'approved')
            ->whereDoesntHave('pickupVerification')
            ->orderBy('pickup_scheduled_at', 'asc')
            ->first();

        // Get recent verifications
        $recentVerifications = PickupVerification::with(['foodMatch.foodListing'])
            ->where('recipient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('recipient.scanner', compact('currentPickup', 'recentVerifications'));
    }

    /**
     * Cancel a verification (restaurant only)
     */
    public function cancelVerification($verificationId)
    {
        if (!auth()->user()->isRestaurantOwner()) {
            abort(403, 'Unauthorized action');
        }

        $verification = PickupVerification::where('id', $verificationId)
            ->where('donor_id', auth()->id())
            ->where('verification_status', 'pending')
            ->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Verification not found or already processed.'
            ], 404);
        }

        // Cancel the verification and delete the record
        $verification->delete();

        // Also delete the related food match if it exists
        if ($verification->foodMatch) {
            $verification->foodMatch->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification cancelled successfully.'
        ]);
    }

    /**
     * Generate QR code for restaurant pickup verification
     */
    public function generateQr($matchId)
    {
        if (!auth()->user()->isRestaurantOwner()) {
            abort(403, 'Unauthorized action');
        }

        // Find the match owned by this restaurant with relationships
        $match = \App\Models\FoodMatch::with(['foodListing', 'recipient'])
            ->whereHas('foodListing', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->where('id', $matchId)
            ->first();

        if (!$match) {
            abort(404, 'Match not found');
        }

        // Get or create verification record with relationships loaded
        $verification = \App\Models\PickupVerification::with(['foodMatch.foodListing', 'foodMatch.recipient', 'foodListing', 'recipient'])
            ->firstOrCreate(
                ['food_match_id' => $match->id],
                [
                    'food_listing_id' => $match->food_listing_id,
                    'recipient_id' => $match->recipient_id,
                    'donor_id' => auth()->id(),
                    'verification_code' => \App\Models\PickupVerification::generateUniqueCode(),
                    'verification_status' => 'pending',
                ]
            );

        // Reload the verification with relationships to ensure they're loaded
        $verification->load(['foodMatch.foodListing', 'foodMatch.recipient', 'foodListing', 'recipient']);

        // Generate QR code using simple-qrcode (SVG format to avoid imagick dependency)
        $qrCode = \QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verification->generateQrCode());

        $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($qrCode);

        return view('restaurant.qr-code', compact('verification', 'qrCodeImage'));
    }

    /**
     * Get admin notifications (database stored + unread count)
     */
    public function getNotifications(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notifications = $user->unreadNotifications()
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            });

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request, $notificationId)
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}
