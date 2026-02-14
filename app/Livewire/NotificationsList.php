<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;

#[Layout('layouts.app')]
class NotificationsList extends Component
{
    /**
     * Mark notification as read and redirect if link exists
     */
    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();

            if (isset($notification->data['link'])) {
                return redirect($notification->data['link']);
            }
        }
    }

    /**
     * Mark all as read
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        session()->flash('message', 'تم تحديد جميع الإشعارات كمقروءة ✅');
    }

    public function render()
    {
        $user = Auth::user();
        $birthdays = collect();

        // Birthday Logic
        if ($user->role === 'admin') {
            $birthdays = Member::where('is_active', true)
                ->whereMonth('birth_date', now()->month)
                ->whereDay('birth_date', now()->day)
                ->with('family')
                ->get();
        } elseif ($user->family_id) {
            $birthdays = Member::where('family_id', $user->family_id)
                ->where('is_active', true)
                ->whereMonth('birth_date', now()->month)
                ->whereDay('birth_date', now()->day)
                ->get();
        }

        return view('livewire.notifications-list', [
            'notifications' => $user->notifications()->latest()->paginate(10),
            'birthdays' => $birthdays
        ]);
    }
}
