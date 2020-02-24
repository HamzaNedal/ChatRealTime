<?php

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

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

use App\Message;
use App\User;

Broadcast::channel('online', function ($user) {

    return $user;
});
// Broadcast::channel('chat-real.{receiver_id}', function ($user,$receiver_id) {
//     return "5";
// });
// Broadcast::channel('chat-real.{receiver_id}', function ($user, $receiver_id) {
//     return $user->id === Message::findOrNew($receiver_id)->sender_id;
// });
Broadcast::channel('typing-{roomShared}', function () {
    return true;
});
