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
            $distance = $this->calculateDistance(
                $foodListing->latitude,
                $foodListing->longitude,
                Auth::user()->latitude,
                Auth::user()->longitude
            );
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

        $matches = FoodMatch::with(['recipient', 'foodListing'])
            ->whereHas('foodListing', function($query) {
                $query->where('created_by', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('restaurant.matches.index', compact('matches'));
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

            // Notify recipient
            $match->recipient->notify(new MatchApprovedNotification($match));

            // Broadcast real-time update
            event(new MatchStatusUpdated($match));
        });

        return redirect()->route('restaurant.matches.index')
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

        // Notify recipient
        $match->recipient->notify(new \App\Notifications\MatchRejectedNotification($match));

        // Broadcast real-time update
        event(new MatchStatusUpdated($match));

        return redirect()->route('restaurant.matches.index')
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

        // Notify recipient about scheduled pickup
        $match->recipient->notify(new MatchScheduledNotification($match));

        // Broadcast real-time update
        event(new MatchStatusUpdated($match));

        return redirect()->route('restaurant.matches.index')
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

        // Find nearby recipients within 10km
        $recipients = User::where('role', 'recipient')
            ->where('latitude', '!=', null)
            ->where('longitude', '!=', null)
            ->where('status', 'active')
            ->get();

        $newMatches = [];

        foreach ($recipients as $recipient) {
            $distance = $this->calculateDistance(
                $foodListing->latitude,
                $foodListing->longitude,
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

                    // Notify recipient about auto-match
                    $recipient->notify(new \App\Notifications\AutoMatchNotification($match));

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