<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Member;

class MemberAbsentAlert extends Notification
{
    use Queueable;

    public $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'تنبيه غياب ⚠️',
            'message' => "المخدوم {$this->member->name} تغيب عن الاجتماع 3 مرات متتالية.",
            'link' => route('member.stats', $this->member->id),
            'type' => 'warning', 
        ];
    }
}
