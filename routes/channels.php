<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Restaurant channel - only restaurant owners can listen to their own channel
Broadcast::channel('restaurant.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->isRestaurantOwner();
});

// Private restaurant channel (for consistency with frontend)
Broadcast::channel('private-restaurant.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->isRestaurantOwner();
});

// Recipient matches channel
Broadcast::channel('private-matches.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->isRecipient();
});

// Admin matches channel
Broadcast::channel('private-admin.matches', function ($user) {
    \Log::info('Admin channel auth check', [
        'user_id' => $user->id ?? 'null',
        'user_name' => $user->name ?? 'null',
        'user_role' => $user->role ?? 'null',
        'is_admin' => isset($user) ? $user->isAdmin() : 'no user',
        'auth_check' => auth()->check(),
        'auth_id' => auth()->id(),
    ]);
    return $user && $user->isAdmin();
});
