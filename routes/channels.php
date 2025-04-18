<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

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
Broadcast::channel('private-conversation.{conversation_id}', function ($user, $conversation_id) {
    $conversation = Conversation::findOrFail($conversation_id);
    return $conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id;
});
