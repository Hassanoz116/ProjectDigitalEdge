<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:success,warning,error,info'
        ]);

        $users = User::where('is_active', 1)->get();
        
        foreach ($users as $user) {
            $user->notify(new CustomNotification(
                $request->title,
                $request->message,
                $request->type,
                ['admin_broadcast' => true, 'sent_by' => auth()->id()]
            ));
        }

        return back()->with('success', "تم إرسال الإشعار لـ {$users->count()} مستخدم / Notification sent to {$users->count()} users");
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser(Request $request, User $user)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:success,warning,error,info'
        ]);

        $user->notify(new CustomNotification(
            $request->title,
            $request->message,
            $request->type,
            ['admin_message' => true, 'sent_by' => auth()->id()]
        ));

        return back()->with('success', "تم إرسال الإشعار للمستخدم {$user->name} / Notification sent to user {$user->name}");
    }

    /**
     * Send system notification
     */
    public function sendSystemNotification($title, $message, $type = 'info', $data = [])
    {
        $adminUsers = User::role('admin')->get();
        
        foreach ($adminUsers as $admin) {
            $admin->notify(new CustomNotification(
                $title,
                $message,
                $type,
                array_merge($data, ['system_notification' => true])
            ));
        }
    }
}
