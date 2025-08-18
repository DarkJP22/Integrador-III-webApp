<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        try {
            $notification = auth()->user()->notifications()->findOrFail($notificationId);
            $notification->delete();
            
            return response()->json(['message' => 'Notification deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'notification_id' => $notificationId
            ]);
            
            return response()->json(['error' => 'Notification not found'], 404);
        }
    }

    public function destroyByType(User $user, $type)
    {
        try {
            $deleted = auth()->user()->notifications()->where('type', 'App\\Notifications\\'.$type)->delete();
            
            return response()->json([
                'message' => 'Notifications deleted successfully',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting notifications by type: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'type' => $type
            ]);
            
            return response()->json(['error' => 'Error deleting notifications'], 500);
        }
    }

    public function destroyAll(User $user)
    {
        try {
            $deleted = auth()->user()->notifications()->delete();
            
            return response()->json([
                'message' => 'All notifications deleted successfully',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting all notifications: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);
            
            return response()->json(['error' => 'Error deleting notifications'], 500);
        }
    }
}
