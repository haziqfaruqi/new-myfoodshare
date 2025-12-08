<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodListingController;
use App\Http\Controllers\FoodMatchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('public.home');
})->name('home');


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
Route::get('/register/restaurant', [RegisteredUserController::class, 'showRestaurantForm'])->name('register.restaurant');
Route::get('/register/recipient', [RegisteredUserController::class, 'showRecipientForm'])->name('register.recipient');
Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/register/restaurant', [RegisteredUserController::class, 'registerRestaurant'])->name('register.restaurant.store');
Route::post('/register/recipient', [RegisteredUserController::class, 'registerRecipient'])->name('register.recipient.store');


// Dashboard routes with role-based middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/users', [DashboardController::class, 'manageUsers'])->name('admin.users');
        Route::get('/food-listings', [DashboardController::class, 'manageFoodListings'])->name('admin.food-listings');
    });

    // Restaurant owner routes
    Route::middleware(['role:restaurant_owner'])->prefix('restaurant')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'restaurantDashboard'])->name('restaurant.dashboard');

        // Manage Listings
        Route::get('/listings', [FoodListingController::class, 'index'])->name('restaurant.listings');
        Route::get('/listings/{listing}', [FoodListingController::class, 'show'])->name('restaurant.listings.show');
        Route::get('/listings/create', [FoodListingController::class, 'create'])->name('restaurant.listings.create');
        Route::post('/listings', [FoodListingController::class, 'storeRestaurantListing'])->name('restaurant.listings.store');
        Route::get('/listings/{listing}/edit', [FoodListingController::class, 'editRestaurantListing'])->name('restaurant.listings.edit');
        Route::put('/listings/{listing}', [FoodListingController::class, 'updateRestaurantListing'])->name('restaurant.listings.update');
        Route::delete('/listings/{listing}', [FoodListingController::class, 'deleteRestaurantListing'])->name('restaurant.listings.destroy');

        // Manage Requests (Food Matches)
        Route::get('/requests', [FoodMatchController::class, 'restaurantIndex'])->name('restaurant.requests');
        Route::get('/requests/{match}', [FoodMatchController::class, 'restaurantShow'])->name('restaurant.requests.show');
        Route::post('/requests/{match}/approve', [FoodMatchController::class, 'approve'])->name('restaurant.requests.approve');
        Route::post('/requests/{match}/reject', [FoodMatchController::class, 'reject'])->name('restaurant.requests.reject');
        Route::post('/requests/{match}/schedule', [FoodMatchController::class, 'schedule'])->name('restaurant.requests.schedule');

        // Manage Schedule (Pickup Monitoring)
        Route::get('/schedule', [DashboardController::class, 'pickupSchedule'])->name('restaurant.schedule');

        // Profile
        Route::get('/profile', [DashboardController::class, 'restaurantProfile'])->name('restaurant.profile');
        Route::get('/profile/edit', [DashboardController::class, 'editRestaurantProfile'])->name('restaurant.profile.edit');
        Route::put('/profile', [DashboardController::class, 'updateRestaurantProfile'])->name('restaurant.profile.update');
    });

    // Recipient routes
    Route::middleware(['role:recipient'])->prefix('recipient')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'recipientDashboard'])->name('recipient.dashboard');
        Route::get('/available-food', [DashboardController::class, 'availableFood'])->name('recipient.available-food');
        Route::get('/map-view', [DashboardController::class, 'mapView'])->name('recipient.map-view');
        Route::get('/my-matches', [DashboardController::class, 'myMatches'])->name('recipient.my-matches');
        Route::get('/impact-report', [DashboardController::class, 'impactReport'])->name('recipient.impact-report');
        Route::get('/ngo-profile', [DashboardController::class, 'ngoProfile'])->name('recipient.ngo-profile');

        // Food matching routes
        Route::get('/matches', [FoodMatchController::class, 'index'])->name('recipient.matches.index');
        Route::get('/matches/{match}', [FoodMatchController::class, 'show'])->name('recipient.matches.show');
        Route::post('/food-listings/{foodListing}/match', [FoodMatchController::class, 'store'])->name('recipient.matches.store');
        Route::post('/matches/{match}/cancel', [FoodMatchController::class, 'cancel'])->name('recipient.matches.cancel');
        Route::post('/matches/{match}/confirm', [FoodMatchController::class, 'confirmPickup'])->name('recipient.matches.confirmPickup');
        Route::get('/matches/{match}/complete', [FoodMatchController::class, 'complete'])->name('recipient.matches.complete');

        // NGO Profile routes
        Route::get('/ngo-profile', [DashboardController::class, 'ngoProfile'])->name('recipient.ngo-profile');
        Route::put('/ngo-profile', [DashboardController::class, 'updateNgoProfile'])->name('recipient.ngo-profile.update');
    });

    // Restaurant owner matching routes are already defined above

    // Admin matching routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/matches', [FoodMatchController::class, 'adminIndex'])->name('admin.matches.index');
        Route::post('/food-listings/{foodListing}/automatch', [FoodMatchController::class, 'autoMatch'])->name('admin.automatch');
    });

    // Food listing routes
    Route::get('/food-listings', [FoodListingController::class, 'index'])->name('food-listings.index');
    Route::get('/food-listings/{foodListing}', [FoodListingController::class, 'show'])->name('food-listings.show');
    Route::get('/food-listings/{foodListing}/verify', [FoodListingController::class, 'verify'])->name('food-listing.verify');

    
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/user-approvals', [DashboardController::class, 'userApprovals'])->name('admin.user-approvals');
        Route::post('/users/{user}/approve', [DashboardController::class, 'approveUser'])->name('admin.users.approve');
        Route::post('/users/{user}/reject', [DashboardController::class, 'rejectUser'])->name('admin.users.reject');
        Route::get('/user-management', [DashboardController::class, 'userManagement'])->name('admin.user-management');
        Route::put('/users/{user}', [DashboardController::class, 'updateUserStatus'])->name('admin.users.update');
        Route::delete('/users/{user}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');
        Route::get('/active-listings', [DashboardController::class, 'activeListings'])->name('admin.active-listings');
        Route::get('/pickup-monitoring', [DashboardController::class, 'pickupMonitoring'])->name('admin.pickup-monitoring');
        Route::get('/pickup-monitoring/report', [DashboardController::class, 'pickupMonitoringReport'])->name('admin.pickup-monitoring.report');

        Route::get('/food-listings', [FoodListingController::class, 'adminIndex'])->name('admin.food-listings');
        Route::get('/food-listings/{foodListing}', [FoodListingController::class, 'adminShow'])->name('admin.food-listings.show');
        Route::post('/food-listings/{foodListing}/approve', [FoodListingController::class, 'approve'])->name('admin.food-listings.approve');
        Route::post('/food-listings/{foodListing}/reject', [FoodListingController::class, 'reject'])->name('admin.food-listings.reject');

        // Logo upload routes
        Route::get('/settings/logo', [DashboardController::class, 'getLogoSettings'])->name('admin.settings.logo');
        Route::post('/settings/logo', [DashboardController::class, 'uploadLogo'])->name('admin.settings.logo.upload');
    });
});
