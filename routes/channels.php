<?php

use App\Orders;
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

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*Broadcast::private('order.{orderId}.{userId}', function ($user, $orderId, $userId) {
    // El usuario que creó la orden o usuarios de la farmacia pueden escuchar
    $order = Orders::find($orderId);

    return (int) $user->id === (int) $userId || // Verifica si el usuario es el propietario de la orden
        ($order && $user->pharmacies && $user->pharmacies->contains('id', $order->pharmacy_id)); // Verifica si el usuario pertenece a la farmacia de la orden
});**/