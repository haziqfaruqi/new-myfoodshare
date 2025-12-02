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
        Route::get('/listings', [DashboardController::class, 'myListings'])->name('restaurant.listings');
        Route::get('/listings/create', [DashboardController::class, 'createListing'])->name('restaurant.listings.create');
        Route::get('/profile', [DashboardController::class, 'restaurantProfile'])->name('restaurant.profile');
    });

    // Recipient routes
    Route::middleware(['role:recipient'])->prefix('recipient')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'recipientDashboard'])->name('recipient.dashboard');
        Route::get('/available-food', [DashboardController::class, 'availableFood'])->name('recipient.available-food');

        // Food matching routes
        Route::get('/matches', [FoodMatchController::class, 'index'])->name('recipient.matches.index');
        Route::get('/matches/{match}', [FoodMatchController::class, 'show'])->name('recipient.matches.show');
        Route::post('/food-listings/{foodListing}/match', [FoodMatchController::class, 'store'])->name('recipient.matches.store');
        Route::post('/matches/{match}/cancel', [FoodMatchController::class, 'cancel'])->name('recipient.matches.cancel');
        Route::post('/matches/{match}/confirm', [FoodMatchController::class, 'confirmPickup'])->name('recipient.matches.confirmPickup');
        Route::get('/matches/{match}/complete', [FoodMatchController::class, 'complete'])->name('recipient.matches.complete');
    });

    // Restaurant owner matching routes
    Route::middleware(['role:restaurant_owner'])->prefix('restaurant')->group(function () {
        Route::get('/matches', [FoodMatchController::class, 'restaurantIndex'])->name('restaurant.matches.index');
        Route::get('/matches/{match}', [FoodMatchController::class, 'restaurantShow'])->name('restaurant.matches.show');
        Route::post('/matches/{match}/approve', [FoodMatchController::class, 'approve'])->name('restaurant.matches.approve');
        Route::post('/matches/{match}/reject', [FoodMatchController::class, 'reject'])->name('restaurant.matches.reject');
        Route::post('/matches/{match}/schedule', [FoodMatchController::class, 'schedule'])->name('restaurant.matches.schedule');
    });

    // Admin matching routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/matches', [FoodMatchController::class, 'adminIndex'])->name('admin.matches.index');
        Route::post('/food-listings/{foodListing}/automatch', [FoodMatchController::class, 'autoMatch'])->name('admin.automatch');
    });

    // Food listing routes
    Route::get('/food-listings', [FoodListingController::class, 'index'])->name('food-listings.index');
    Route::get('/food-listings/{foodListing}', [FoodListingController::class, 'show'])->name('food-listings.show');
    Route::get('/food-listings/{foodListing}/verify', [FoodListingController::class, 'verify'])->name('food-listing.verify');

    // Restaurant owner food listing routes
    Route::middleware(['role:restaurant_owner'])->prefix('restaurant')->group(function () {
        Route::get('/listings', [FoodListingController::class, 'index'])->name('restaurant.food-listings.index');
        Route::get('/listings/create', [FoodListingController::class, 'create'])->name('restaurant.food-listings.create');
        Route::post('/listings', [FoodListingController::class, 'store'])->name('restaurant.food-listings.store');
        Route::get('/listings/{foodListing}', [FoodListingController::class, 'show'])->name('restaurant.food-listings.show');
        Route::get('/listings/{foodListing}/edit', [FoodListingController::class, 'edit'])->name('restaurant.food-listings.edit');
        Route::put('/listings/{foodListing}', [FoodListingController::class, 'update'])->name('restaurant.food-listings.update');
        Route::delete('/listings/{foodListing}', [FoodListingController::class, 'destroy'])->name('restaurant.food-listings.destroy');
    });

    // Admin food listing management routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/food-listings', [FoodListingController::class, 'index'])->name('admin.food-listings.index');
        Route::post('/food-listings/{foodListing}/approve', [FoodListingController::class, 'approve'])->name('admin.food-listings.approve');
        Route::post('/food-listings/{foodListing}/reject', [FoodListingController::class, 'reject'])->name('admin.food-listings.reject');
    });
});
