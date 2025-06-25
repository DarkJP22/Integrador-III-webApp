<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserNotificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
     

    }
    public function index()
    {
        return auth()->user()->unreadNotifications;
    }

    public function destroy(User $user, $notificationId)
    {
        auth()->user()->notifications()->findOrFail($notificationId)->delete();//markAsRead();
    }

    public function destroyByType(User $user, $type)
    {
        auth()->user()->notifications()->where('type', 'App\\Notifications\\'.$type)->delete();//markAsRead();
    }

    public function destroyAll(User $user)
    {
        auth()->user()->notifications()->delete();//markAsRead();
    }
}
