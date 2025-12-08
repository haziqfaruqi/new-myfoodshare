<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\FoodMatch;
use App\Models\User;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MatchCreatedNotification;
use App\Notifications\MatchApprovedNotification;
use App\Notifications\MatchScheduledNotification;
use App\Notifications\MatchRejectedNotification;
use App\Notifications\AutoMatchNotification;
use App\Events\MatchStatusUpdated;

class FoodMatchController extends Controller
{
    /**
     * Create a new match (recipient expresses interest)
     */
    public function store(Request $request, FoodListing $foodListing)
    {
        if (!Auth::user()->isRecipient()) {
            abort(403, 'Unauthorized action');
        }

        if (!$foodListing->isVisibleToRecipients()) {
            abort(404, 'Food listing not available');
        }

        // Check if user already has a match for this food listing
        $existingMatch = FoodMatch::where('food_listing_id', $foodListing->id)
            ->where('recipient_id', Auth::id())
            ->whereIn('status', ['pending', 'approved', 'scheduled'])
            ->first();

        if ($existingMatch) {
            return back()->withErrors(['match' => 'You have already expressed interest in this food listing.']);
        }

        // Calculate distance if user has coordinates
        $distance = null;
        if (Auth::user()->latitude && Auth::user()->longitude) {
            // Get food listing coordinates, fallback to restaurant profile
            $listingLat = $foodListing->latitude;
            $listingLon = $foodListing->longitude;

            // If food listing has no coordinates, use restaurant profile coordinates
            if (!$listingLat || !$listingLon) {
                if ($foodListing->restaurantProfile) {
                    $listingLat = $foodListing->restaurantProfile->latitude;
                    $listingLon = $foodListing->restaurantProfile->longitude;
                }
            }

            // Only calculate distance if we have valid coordinates for both
            if ($listingLat && $listingLon) {
                $distance = $this->calculateDistance(
                    $listingLat,
                    $listingLon,
                    Auth::user()->latitude,
                    Auth::user()->longitude
                );
            }
        }

        $match = FoodMatch::create([
            'food_listing_id' => $foodListing->id,
            'recipient_id' => Auth::id(),
            'status' => 'pending',
            'distance' => $distance,
            'notes' => $request->input('notes', ''),
        ]);

        // Notify restaurant owner about the match
        $restaurantOwner = $foodListing->creator;
        Notification::send($restaurantOwner, new MatchCreatedNotification($match));

        // Broadcast real-time event
        event(new MatchStatusUpdated($match));

        return redirect()->route('recipient.matches.index')
            ->with('success', 'Your interest has been registered! The restaurant owner will review your request.');
    }

    /**
     * Recipient view their matches
     */
    public function index()
    {
        if (!Auth::user()->isRecipient()) {
            abort(403, 'Unauthorized action');
        }

        $matches = FoodMatch::with(['foodListing.restaurantProfile'])
            ->where('recipient_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('recipient.matches.index', compact('matches'));
    }

    /**
     * Recipient show individual match
     */
    public function show(FoodMatch $match)
    {
        if (!Auth::user()->isRecipient() || Auth::user()->id !== $match->recipient_id) {
            abort(403, 'Unauthorized action');
        }

        $match->load(['foodListing.restaurantProfile', 'foodListing.images']);

        return view('recipient.matches.show', compact('match'));
    }

    /**
     * Restaurant owner view their matches
     */
    public function restaurantIndex()
    {
        if (!Auth::user()->isRestaurantOwner()) {
            abort(403, 'Unauthorized action');
        }

        // Get statistics
        $pendingRequests = FoodMatch::whereHas('foodListing', function($query) {
                $query->where('created_by', Auth::id());
            })
            ->where('status', 'pending')
            ->count();

        $approvedRequests = FoodMatch::whereHas('foodListing', function($query) {
                $query->where('created_by', Auth::id());
            })
            ->where('status', 'approved')
            ->count();

        $rejectedRequests = FoodMatch::whereHas('foodListing', function($query) {
                $query->where('created_by', Auth::id());
            })
            ->where('status', 'rejected')
            ->count();

        $completedRequests = FoodMatch::whereHas('foodListing', function($query) {
                $query->where('created_by', Auth::id());
            })
            ->where('status', 'completed')
            ->count();

        $scheduledRequests = FoodMatch::whereHas('foodListing', function($query) {
                $query->where('created_by', Auth::id());
            })
            ->where('status', 'scheduled')
            ->count();

        // Get paginated matches for the list
        $status = request('status');
        $requestsQuery = FoodMatch::with(['recipient', 'foodListing'])
            ->whereHas('foodListing', function($query) {
                $query->where('created_by', Auth::id());
            });

        if ($status) {
            $requestsQuery->where('status', $status);
        }

        $requests = $requestsQuery->latest()->paginate(10);

        return view('restaurant.requests.index', compact('pendingRequests', 'approvedRequests', 'rejectedRequests', 'completedRequests', 'scheduledRequests', 'requests'));
    }

    /**
     * Restaurant owner show individual match
     */
    public function restaurantShow(FoodMatch $match)
    {
        if (!Auth::user()->isRestaurantOwner() ||
            Auth::user()->id !== $match->foodListing->created_by) {
            abort(403, 'Unauthorized action');
        }

        $match->load(['recipient', 'foodListing']);

        return view('restaurant.matches.show', compact('match'));
    }

    /**
     * Restaurant owner approve a match
     */
    public function approve(Request $request, FoodMatch $match)
    {
        if (!Auth::user()->isRestaurantOwner() ||
            Auth::user()->id !== $match->foodListing->created_by) {
            abort(403, 'Unauthorized action');
        }

        if ($match->status !== 'pending') {
            abort(400, 'Only pending matches can be approved');
        }

        DB::transaction(function() use ($match, $request) {
            // Update match status
            $match->update([
                'status' => 'approved',
                'approved_at' => now(),
                'notes' => $request->input('notes', ''),
            ]);

            // Generate QR code
            $match->generateQrCode();

            // Notify recipient if they exist
            if ($match->recipient) {
                $match->recipient->notify(new MatchApprovedNotification($match));
            }

            // Broadcast real-time update
            event(new MatchStatusUpdated($match));
        });

        return redirect()->route('restaurant.requests')
            ->with('success', 'Match approved successfully! QR code generated for pickup verification.');
    }

    /**
     * Restaurant owner reject a match
     */
    public function reject(Request $request, FoodMatch $match)
    {
        if (!Auth::user()->isRestaurantOwner() ||
            Auth::user()->id !== $match->foodListing->created_by) {
            abort(403, 'Unauthorized action');
        }

        if ($match->status !== 'pending') {
            abort(400, 'Only pending matches can be rejected');
        }

        $match->update([
            'status' => 'rejected',
            'notes' => $request->input('notes', ''),
        ]);

        // Notify recipient if they exist
        if ($match->recipient) {
            $match->recipient->notify(new MatchRejectedNotification($match));
        }

        // Broadcast real-time update
        event(new MatchStatusUpdated($match));

        return redirect()->route('restaurant.requests')
            ->with('success', 'Match rejected successfully.');
    }

    /**
     * Restaurant owner schedule pickup for a match
     */
    public function schedule(Request $request, FoodMatch $match)
    {
        if (!Auth::user()->isRestaurantOwner() ||
            Auth::user()->id !== $match->foodListing->created_by) {
            abort(403, 'Unauthorized action');
        }

        if ($match->status !== 'approved') {
            abort(400, 'Only approved matches can be scheduled');
        }

        $validated = $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $pickupDateTime = \Carbon\Carbon::parse(
            $validated['pickup_date'] . ' ' . $validated['pickup_time']
        );

        $match->update([
            'status' => 'scheduled',
            'pickup_scheduled_at' => $pickupDateTime,
            'notes' => $validated['notes'],
        ]);

        // Notify recipient about scheduled pickup if they exist
        if ($match->recipient) {
            $match->recipient->notify(new MatchScheduledNotification($match));
        }

        // Broadcast real-time update
        event(new MatchStatusUpdated($match));

        return redirect()->route('restaurant.requests')
            ->with('success', 'Pickup scheduled successfully!');
    }

    /**
     * Recipient confirm pickup
     */
    public function confirmPickup(FoodMatch $match)
    {
        if (!Auth::user()->isRecipient() || Auth::user()->id !== $match->recipient_id) {
            abort(403, 'Unauthorized action');
        }

        if ($match->status !== 'scheduled') {
            abort(400, 'Only scheduled matches can be confirmed');
        }

        $match->confirmPickup();

        return redirect()->route('recipient.matches.show', $match->id)
            ->with('success', 'Pickup confirmed! Please show the QR code to the restaurant staff.');
    }

    /**
     * Recipient complete pickup with rating
     */
    public function complete(Request $request, FoodMatch $match)
    {
        if (!Auth::user()->isRecipient() || Auth::user()->id !== $match->recipient_id) {
            abort(403, 'Unauthorized action');
        }

        if ($match->status !== 'in_progress') {
            abort(400, 'Only in-progress matches can be completed');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'feedback' => 'nullable|string|max:500',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function() use ($match, $validated) {
            $match->completePickup();

            // Store rating and feedback
            $match->update([
                'rating' => $validated['rating'],
                'feedback' => $validated['feedback'],
            ]);

            // Handle photo uploads if any
            if ($request->hasFile('photos')) {
                $photoPaths = [];
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('pickup_photos', 'public');
                    $photoPaths[] = $path;
                }
                $match->update(['pickup_photos' => $photoPaths]);
            }
        });

        return redirect()->route('recipient.matches.index')
            ->with('success', 'Pickup completed successfully! Thank you for using MyFoodshare.');
    }

    /**
     * Recipient cancel match
     */
    public function cancel(FoodMatch $match)
    {
        if (!Auth::user()->isRecipient() && Auth::user()->id !== $match->recipient_id) {
            abort(403, 'Unauthorized action');
        }

        if (in_array($match->status, ['completed', 'rejected', 'cancelled'])) {
            abort(400, 'Cannot cancel this match');
        }

        $match->cancelMatch('Cancelled by recipient');

        return redirect()->route('recipient.matches.index')
            ->with('success', 'Match cancelled successfully.');
    }

    /**
     * Auto-match food listings to nearby recipients
     */
    public function autoMatch(FoodListing $foodListing)
    {
        if (!Auth::user()->isRestaurantOwner() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        // Verify this restaurant owns the food listing
        if ($foodListing->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Find nearby recipients within 10km
        $recipients = User::where('role', 'recipient')
            ->where('latitude', '!=', null)
            ->where('longitude', '!=', null)
            ->where('status', 'active')
            ->get();

        $newMatches = [];

        foreach ($recipients as $recipient) {
            // Get food listing coordinates, fallback to restaurant profile
            $listingLat = $foodListing->latitude;
            $listingLon = $foodListing->longitude;

            // If food listing has no coordinates, use restaurant profile coordinates
            if (!$listingLat || !$listingLon) {
                if ($foodListing->restaurantProfile) {
                    $listingLat = $foodListing->restaurantProfile->latitude;
                    $listingLon = $foodListing->restaurantProfile->longitude;
                }
            }

            // Only create match if we have valid coordinates for both
            if (!$listingLat || !$listingLon || !$recipient->latitude || !$recipient->longitude) {
                continue;
            }

            $distance = $this->calculateDistance(
                $listingLat,
                $listingLon,
                $recipient->latitude,
                $recipient->longitude
            );

            if ($distance <= 10) { // Within 10km
                // Check if match already exists
                $existingMatch = FoodMatch::where('food_listing_id', $foodListing->id)
                    ->where('recipient_id', $recipient->id)
                    ->first();

                if (!$existingMatch) {
                    $match = FoodMatch::create([
                        'food_listing_id' => $foodListing->id,
                        'recipient_id' => $recipient->id,
                        'status' => 'pending',
                        'distance' => $distance,
                        'notes' => 'Auto-matched based on proximity',
                    ]);

                    $newMatches[] = $match;

                    // Notify recipient about auto-match if they exist
                    if ($recipient) {
                        $recipient->notify(new AutoMatchNotification($match));
                    }

                    // Broadcast real-time event
                    event(new MatchStatusUpdated($match));
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Created {$newMatches} auto-matches",
            'matches_count' => count($newMatches),
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $earthRadius * $angle;
    }
}