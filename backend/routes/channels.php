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

Broadcast::channel('Chat.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chatify.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('privatechat.{receiverid}', function ($user,$receiverid) {
    return auth()->check();
});


// Broadcast::channel('chatify.{receiverid}', function ($user,$receiverid) {
//    return (int) $user->id === (int) $id;
// });

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
