<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\RestaurantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FoodListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FoodListing::with(['restaurantProfile', 'creator'])
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->where('expiry_date', '>=', now()->toDateString())
            ->orWhere(function ($q) {
                $q->where('expiry_date', '=', now()->toDateString())
                  ->where('expiry_time', '>=', now()->format('H:i'));
            });

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('food_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by distance (if location data is available)
        if ($request->has('distance') && !empty($request->distance) && $request->user()) {
            $lat = $request->user()->latitude;
            $lng = $request->user()->longitude;

            if ($lat && $lng) {
                $distance = (int) $request->distance;
                $query->whereRaw("
                    (6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) + sin(radians(?)) *
                    sin(radians(latitude)))) <= ?
                ", [$lat, $lng, $lat, $distance]);
            }
        }

        // Order by proximity if location data is available
        if ($request->user() && $request->user()->latitude && $request->user()->longitude) {
            $lat = $request->user()->latitude;
            $lng = $request->user()->longitude;
            $query->orderByRaw("
                6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) + sin(radians(?)) *
                sin(radians(latitude)))
            ", [$lat, $lng, $lat]);
        } else {
            $query->latest();
        }

        $foodListings = $query->paginate(12);

        return view('food-listings.index', compact('foodListings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->isRestaurantOwner()) {
            abort(403, 'Unauthorized action');
        }

        return view('food-listings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isRestaurantOwner()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'food_name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:fresh,cooked,bakery,beverages,other',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string|max:50',
            'expiry_date' => 'required|date|after:today',
            'expiry_time' => 'required|date_format:H:i',
            'pickup_location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'pickup_address' => 'required|string|max:255',
            'special_instructions' => 'nullable|string',
            'dietary_info' => 'nullable|array',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('food_images', 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        // Create food listing
        $foodListing = FoodListing::create([
            ...$validated,
            'user_id' => Auth::id(),
            'created_by' => Auth::id(),
            'status' => 'active',
            'approval_status' => 'pending',
        ]);

        // Generate QR code
        $foodListing->generateQrCode();

        return redirect()->route('restaurant.food-listings.show', $foodListing->id)
            ->with('success', 'Food listing created successfully! It is pending approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FoodListing $foodListing)
    {
        // Ensure the food listing is visible
        if (!$foodListing->isVisibleToRecipients()) {
            abort(404, 'Food listing not available');
        }

        $foodListing->load(['restaurantProfile', 'creator', 'approver', 'matches']);

        return view('food-listings.show', compact('foodListing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FoodListing $foodListing)
    {
        if (!Auth::user()->isRestaurantOwner() || $foodListing->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        return view('food-listings.edit', compact('foodListing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodListing $foodListing)
    {
        if (!Auth::user()->isRestaurantOwner() || $foodListing->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'food_name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:fresh,cooked,bakery,beverages,other',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string|max:50',
            'expiry_date' => 'required|date|after:today',
            'expiry_time' => 'required|date_format:H:i',
            'pickup_location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'pickup_address' => 'required|string|max:255',
            'special_instructions' => 'nullable|string',
            'dietary_info' => 'nullable|array',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($foodListing->images) {
                foreach ($foodListing->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('food_images', 'public');
                $imagePaths[] => $path;
            }
            $validated['images'] = $imagePaths;
        }

        $foodListing->update($validated);

        return redirect()->route('restaurant.food-listings.show', $foodListing->id)
            ->with('success', 'Food listing updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodListing $foodListing)
    {
        if (!Auth::user()->isRestaurantOwner() || $foodListing->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Delete images
        if ($foodListing->images) {
            foreach ($foodListing->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $foodListing->delete();

        return redirect()->route('restaurant.food-listings.index')
            ->with('success', 'Food listing deleted successfully!');
    }

    /**
     * Admin approval methods
     */
    public function approve(FoodListing $foodListing)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $foodListing->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'admin_notes' => null,
        ]);

        return redirect()->route('admin.food-listings.index')
            ->with('success', 'Food listing approved successfully!');
    }

    public function reject(Request $request, FoodListing $foodListing)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $foodListing->update([
            'approval_status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.food-listings.index')
            ->with('success', 'Food listing rejected successfully!');
    }

    /**
     * Verify QR code for food listing
     */
    public function verify(FoodListing $foodListing, $code)
    {
        // Generate verification code
        $verificationCode = $foodListing->getVerificationCode();

        if ($code !== $verificationCode) {
            return view('food-listings.verification', [
                'foodListing' => $foodListing,
                'valid' => false,
                'message' => 'Invalid verification code'
            ]);
        }

        return view('food-listings.verification', [
            'foodListing' => $foodListing,
            'valid' => true,
            'message' => 'Verification successful'
        ]);
    }
}