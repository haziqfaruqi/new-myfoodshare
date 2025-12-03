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
            ->where('created_by', auth()->id())
            ->where(function ($q) {
                $q->where('expiry_date', '>', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->where('expiry_date', '=', now()->toDateString())
                         ->where('expiry_time', '>=', now()->format('H:i'));
                  });
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

        return view('restaurant.listings.index', compact('foodListings'));
    }

    /**
     * Display admin listing of pending food listings.
     */
    public function adminIndex(Request $request)
    {
        $query = FoodListing::with(['restaurantProfile', 'creator'])
            ->where('approval_status', 'pending')
            ->where('status', 'active')
            ->latest();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('food_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        $foodListings = $query->paginate(20);

        return view('admin.food-listings', compact('foodListings'));
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
     * Display the specified resource for admin (shows all listings).
     */
    public function adminShow(FoodListing $foodListing)
    {
        // Admin can see all listings regardless of status
        $foodListing->load(['restaurantProfile', 'creator', 'approver', 'matches']);

        return view('admin.food-listings.show', compact('foodListing'));
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
                $imagePaths[] = $path;
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

    /**
     * Store food listing from restaurant dashboard
     */
    public function storeRestaurantListing(Request $request)
    {
        if (!Auth::user()->isRestaurantOwner()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'food_category' => 'required|string|max:50',
            'quantity' => 'required|string|max:100',
            'expiry_date' => 'required|date',
            'expiry_time' => 'required',
            'pickup_instructions' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $restaurantProfile = Auth::user()->restaurantProfile;

        if (!$restaurantProfile) {
            return back()->withErrors(['error' => 'Restaurant profile not found.']);
        }

        $foodListing = new FoodListing();
        $foodListing->restaurant_profile_id = $restaurantProfile->id;
        $foodListing->created_by = Auth::id();
        $foodListing->food_name = $validated['title'];
        $foodListing->description = $validated['description'];
        $foodListing->category = $validated['food_category'];
        $foodListing->quantity = $validated['quantity'];
        $foodListing->unit = 'kg'; // Default unit
        $foodListing->expiry_date = $validated['expiry_date'];
        $foodListing->expiry_time = $validated['expiry_time'];
        $foodListing->pickup_location = $restaurantProfile->address;
        $foodListing->pickup_address = $restaurantProfile->address;
        $foodListing->special_instructions = $validated['pickup_instructions'];
        $foodListing->status = 'active';
        $foodListing->approval_status = 'pending';

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('food_images', $imageName, 'public');
            $foodListing->images = ['food_images/' . $imageName];
        }

        $foodListing->save();

        // Generate QR code
        $foodListing->generateQrCode();

        return redirect()->route('restaurant.dashboard')
            ->with('success', 'Food donation posted successfully! It is pending approval.');
    }

    /**
     * Update food listing from restaurant dashboard
     */
    public function updateRestaurantListing(Request $request, FoodListing $listing)
    {
        if (!Auth::user()->isRestaurantOwner() || $listing->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'food_category' => 'required|string|max:50',
            'quantity' => 'required|string|max:100',
            'expiry_date' => 'required|date',
            'expiry_time' => 'required',
            'pickup_instructions' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $listing->food_name = $validated['title'];
        $listing->description = $validated['description'];
        $listing->category = $validated['food_category'];
        $listing->quantity = $validated['quantity'];
        $listing->expiry_date = $validated['expiry_date'];
        $listing->expiry_time = $validated['expiry_time'];
        $listing->special_instructions = $validated['pickup_instructions'];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old images if exist
            if ($listing->images) {
                foreach ($listing->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('food_images', $imageName, 'public');
            $listing->images = ['food_images/' . $imageName];
        }

        $listing->save();

        return redirect()->route('restaurant.dashboard')
            ->with('success', 'Food donation updated successfully!');
    }

    /**
     * Show the form for editing restaurant food listing.
     */
    public function editRestaurantListing(FoodListing $listing)
    {
        if (!Auth::user()->isRestaurantOwner() || $listing->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        return view('restaurant.listings.edit', compact('listing'));
    }

    /**
     * Delete food listing from restaurant dashboard
     */
    public function deleteRestaurantListing(FoodListing $listing)
    {
        if (!Auth::user()->isRestaurantOwner() || $listing->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Delete images if exist
        if ($listing->images) {
            foreach ($listing->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $listing->delete();

        return redirect()->route('restaurant.dashboard')
            ->with('success', 'Food donation removed successfully.');
    }
}