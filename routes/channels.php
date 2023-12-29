<?php

use App\Models\Room;
use App\Models\User;
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

// // ++> private
// Broadcast::channel('messages.{room}', function (User $user, Room $room) {
//     // return false;
//     return $room->participants->first(fn ($participant) => $participant->id === $user->id);
// });

// +++> presence
Broadcast::channel('messages.{room}', function (User $user, Room $room) {
    $canJoinRoom = $room->participants->first(fn ($participant) => $participant->id === $user->id);

    if ($canJoinRoom) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});
