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

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// OTP Status Channel (Public - uses hashed identifier for security)
Broadcast::channel('otp.{hashedIdentifier}', function ($user, $hashedIdentifier) {
    // This is a public channel for OTP status updates
    // The identifier is hashed for security
    return true;
});
