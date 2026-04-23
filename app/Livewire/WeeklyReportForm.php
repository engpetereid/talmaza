<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;

#[Layout('layouts.app')]
class WeeklyReportForm extends Component
{
    public $reportId;
    public $family;
    public $isReadOnly = false;

    public $timeline = [];
    public $weekly_achievements = [];

    // --- Visitation Data ---
    public $visitation_hours;
    public $visitation_replies = [];
    public $visitation_new_reply = '';

    // --- Session Time Data (معاد الجلسة) ---
    public $session_time;
    public $session_replies = [];
    public $session_new_reply = '';

    public $priest_message = [];

    public function mount(Report $report = null)
    {
        if ($report && $report->id) {
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->family_id !== $report->family_id) {
                abort(403);
            }

            $this->reportId = $report->id;
            $this->family = $report->family;
            $this->isReadOnly = true;

            $this->timeline = $this->normalizeSection($report->timeline ?? [['time' => '', 'activity' => '']]);
            $this->weekly_achievements = $this->normalizeSection($report->weekly_achievements ?? [['text' => '']]);
            $this->priest_message = $this->normalizeSection($report->priest_message ?? [['text' => '']]);

            $this->visitation_hours = $report->visitation_hours;
            $this->visitation_replies = is_array($report->visitation_replies) ? $report->visitation_replies : [];

            $this->session_time = $report->session_time;
            $this->session_replies = is_array($report->session_replies) ? $report->session_replies : [];

        } else {
            $this->family = Auth::user()->family;
            $this->timeline = $this->normalizeSection([['time' => '', 'activity' => '']]);
            $this->weekly_achievements = $this->normalizeSection([['text' => '']]);
            $this->priest_message = $this->normalizeSection([['text' => '']]);
        }
    }

    private function normalizeSection($section)
    {
        if (!is_array($section)) return [];
        foreach ($section as &$item) {
            if (!isset($item['replies'])) {
                $item['replies'] = [];
                if (isset($item['reply']) && $item['reply'] !== '') {
                    $item['replies'][] = ['role' => 'admin', 'name' => 'الإدارة', 'text' => $item['reply'], 'date' => now()->toDateTimeString()];
                }
            }
            unset($item['reply']);
            $item['new_reply'] = '';
        }
        return $section;
    }

    public function addItem($listName)
    {
        if (in_array($listName, ['weekly_achievements', 'priest_message'])) {
            $this->$listName[] = ['text' => '', 'replies' => [], 'new_reply' => ''];
        }
    }

    public function removeItem($listName, $index)
    {
        if (in_array($listName, ['weekly_achievements', 'priest_message'])) {
            unset($this->$listName[$index]);
            $this->$listName = array_values($this->$listName);
        }
    }

    public function addTimelineRow()
    {
        $this->timeline[] = ['time' => '', 'activity' => '', 'replies' => [], 'new_reply' => ''];
    }

    public function removeTimelineRow($index)
    {
        unset($this->timeline[$index]);
        $this->timeline = array_values($this->timeline);
    }

    public function save()
    {
        $this->weekly_achievements = array_values(array_filter($this->weekly_achievements, fn($i) => !empty($i['text'])));
        $this->priest_message = array_values(array_filter($this->priest_message, fn($i) => !empty($i['text'])));

        $this->validate([
            'visitation_hours' => 'nullable|numeric|min:0',
            'session_time' => 'nullable|string|max:255',
             ]);

        $newReport = Report::create([
            'family_id' => $this->family->id,
            'type' => 'weekly',
            'report_date' => now(),
            'timeline' => $this->timeline,
            'weekly_achievements' => $this->weekly_achievements,
            'visitation_hours' => $this->visitation_hours,
            'visitation_replies' => [],
            'session_time' => $this->session_time,
            'session_replies' => [],
            'priest_message' => $this->priest_message,
        ]);

        $this->notifyUsers($newReport, 'تقرير جديد 📝', "تم ارسال التقرير الأسبوعي بواسطة " . Auth::user()->name);

        return redirect()->route('leader.reports')->with('message', 'تم إرسال التقرير بنجاح ✅');
    }

    public function saveReplies()
    {
        $report = Report::find($this->reportId);
        if (!$report) return;

        $hasNewReplies = false;
        $userRole = Auth::user()->role;
        $userName = Auth::user()->name;

        $sections = ['timeline', 'weekly_achievements', 'priest_message'];
        foreach ($sections as $section) {
            if (is_array($this->$section)) {
                foreach ($this->$section as &$item) {
                    if (!empty($item['new_reply'])) {
                        $item['replies'][] = ['role' => $userRole, 'name' => $userName, 'text' => $item['new_reply'], 'date' => now()->toDateTimeString()];
                        $item['new_reply'] = '';
                        $hasNewReplies = true;
                    }
                }
            }
        }

        // Visitation Replies
        if (!empty($this->visitation_new_reply)) {
            $this->visitation_replies[] = ['role' => $userRole, 'name' => $userName, 'text' => $this->visitation_new_reply, 'date' => now()->toDateTimeString()];
            $this->visitation_new_reply = '';
            $hasNewReplies = true;
        }

        // Session Time Replies
        if (!empty($this->session_new_reply)) {
            $this->session_replies[] = ['role' => $userRole, 'name' => $userName, 'text' => $this->session_new_reply, 'date' => now()->toDateTimeString()];
            $this->session_new_reply = '';
            $hasNewReplies = true;
        }

        if ($hasNewReplies) {
            $report->update([
                'timeline' => $this->timeline,
                'weekly_achievements' => $this->weekly_achievements,
                'visitation_replies' => $this->visitation_replies,
                'session_replies' => $this->session_replies,
                'priest_message' => $this->priest_message,
                'admin_reply_at' => $userRole === 'admin' ? now() : $report->admin_reply_at,
            ]);

            $title = $userRole === 'admin' ? 'رد جديد على التقرير 📝' : 'تعقيب جديد من القائد 📝';
            $body = $userRole === 'admin' ? "تم الرد على تقريرك بواسطة {$userName}." : "قام {$userName} بالرد على ملاحظات التقرير.";
            $this->notifyUsers($report, $title, $body, true);
        }

        session()->flash('message', 'تم حفظ الردود بنجاح ✅');
    }

    public function closeReplies()
    {
        $report = Report::find($this->reportId);
        $report->update(['admin_reply_at' => Auth::user()->role === 'admin' ? now() : $report->admin_reply_at]);
        session()->flash('message', 'تم الاغلاق بنجاح ✅');
    }

    private function notifyUsers($report, $title, $body, $isReply = false)
    {
        try {
            $targetRole = (Auth::user()->role === 'admin' && $isReply) ? 'leader' : 'admin';

            $query = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '');
            if ($targetRole === 'admin') {
                $query->where('role', 'admin');
            } else {
                $query->where('family_id', $report->family_id);
            }

            $tokens = array_unique($query->pluck('fcm_token')->toArray());

            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
                $messaging = $factory->createMessaging();
                $message = CloudMessage::new()
                    ->withNotification(Notification::create($title, $body))
                    ->withWebPushConfig(WebPushConfig::fromArray(['fcm_options' => ['link' => route('report.weekly.view', $report->id)]]));
                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Throwable $e) {
            Log::error('Firebase Notification Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.weekly-report-form');
    }
}
