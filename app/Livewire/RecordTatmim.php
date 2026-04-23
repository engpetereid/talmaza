<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\WeeklyMeeting;
use App\Models\Stage;
use App\Models\Lesson;
use App\Models\TatmimRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url; // Enable URL Query Parameter
use Carbon\Carbon;

#[Layout('layouts.app')]
class RecordTatmim extends Component
{
    public WeeklyMeeting $meeting;
    public $step = 1;

    // Read Only Mode
    #[Url]
    public $readonly = false;

    // بيانات الخطوة 1
    public $status = 'completed';
    public $selected_stage_id = '';
    public $selected_lesson_id = '';
    public $custom_topic = '';
    public $max_note_score = 28;

    public $suggestedLessonText = '';

    // بيانات الخطوة 2
    public $records = [];

    public function mount(WeeklyMeeting $meeting)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->family_id !== $meeting->family_id) {
            abort(403, 'غير مصرح لك بالدخول لاجتماعات عائلة أخرى.');
        }
        // ----------------------------------
        $this->meeting = $meeting;

        if (
            ($meeting->status !== 'pending' && $meeting->created_at < Carbon::now()->subWeeks(4))
            || Auth::user()->role === 'admin'
        ) {
            $this->readonly = true;
        }
        // --------------------------------------------------

        $this->status = $meeting->status == 'pending' ? 'completed' : $meeting->status;
        $this->max_note_score = $meeting->max_note_score;

        if (!$meeting->lesson_id && $meeting->status == 'pending') {
            $this->suggestNextLesson();
        } else {
            if ($meeting->lesson_id) {
                $this->selected_lesson_id = $meeting->lesson_id;
                $this->selected_stage_id = $meeting->lesson->stage_id;
            }
            $this->custom_topic = $meeting->custom_topic;
        }

        // تهيئة السجلات عند الفتح لأول مرة
        $this->ensureRecordsExist();
    }

    public function suggestNextLesson()
    {
        $lastMeeting = WeeklyMeeting::where('family_id', $this->meeting->family_id)
            ->where('status', 'completed')
            ->where('id', '!=', $this->meeting->id)
            ->whereNotNull('lesson_id')
            ->latest('week_date')
            ->first();

        if ($lastMeeting && $lastMeeting->lesson) {
            $nextLesson = Lesson::where('stage_id', $lastMeeting->lesson->stage_id)
                ->where('order_number', '>', $lastMeeting->lesson->order_number)
                ->orderBy('order_number')
                ->first();

            if (!$nextLesson) {
                $nextStage = Stage::where('order_number', '>', $lastMeeting->lesson->stage->order_number)
                    ->orderBy('order_number')
                    ->first();

                if ($nextStage) {
                    $nextLesson = $nextStage->lessons()->orderBy('order_number')->first();
                }
            }

            if ($nextLesson) {
                $this->selected_stage_id = $nextLesson->stage_id;
                $this->selected_lesson_id = $nextLesson->id;
            }
        }
    }

    public function ensureRecordsExist()
    {
        $members = $this->meeting->family->members()->where('is_active', true)->get();

        foreach ($members as $member) {
            if (!isset($this->records[$member->id])) {
                $record = TatmimRecord::firstOrCreate(
                    [
                        'weekly_meeting_id' => $this->meeting->id,
                        'member_id' => $member->id
                    ],
                    [
                        'is_present' => false,
                        'note_score' => null,
                        'talmaza_training_count' => null,
                        'kholwa_count' => null,
                        'has_weekly_kholwa' => false,
                        'has_mass' => false,
                        'has_tasbeha' => false,
                        'has_servants_meeting' => false,
                        'has_reading' => false,
                        'has_family_altar' => false,
                        'has_sermon' => false,
                        'has_vespers' => false,
                    ]
                );

                // Convert to array for Livewire state
                $data = $record->toArray();


                $this->records[$member->id] = $data;
            }
        }

        return $members;
    }

    // الحفظ التلقائي (Livewire Auto-save)
    public function updated($property, $value)
    {
        // Block updates if readonly
        if ($this->readonly) return;

        $parts = explode('.', $property);

        if (count($parts) === 3 && $parts[0] === 'records') {
            $memberId = $parts[1];
            $field = $parts[2];

            // If input is empty, save as 0 in DB (since DB doesn't allow null)
            if ($value === '' && in_array($field, ['note_score', 'kholwa_count', 'talmaza_training_count'])) {
                $value = 0;
            }

            TatmimRecord::where('weekly_meeting_id', $this->meeting->id)
                ->where('member_id', $memberId)
                ->update([$field => $value]);
        }
    }

    public function saveMeetingDetails()
    {
        // Block saves if readonly
        if ($this->readonly) {
            $this->step = 2; // Allow navigation but no save
            return;
        }

        $this->validate([
            'status' => 'required',
            'max_note_score' => 'required|numeric|min:0',
        ]);

        $this->meeting->update([
            'status' => $this->status,
            'lesson_id' => $this->selected_lesson_id ?: null,
            'custom_topic' => $this->custom_topic,
            'max_note_score' => $this->max_note_score,
        ]);

        if ($this->status == 'cancelled') {
            return redirect()->route('dashboard')->with('status', 'تم تسجيل إلغاء الاجتماع لهذا الأسبوع.');
        }

        $this->step = 2;
    }

    public function finish()
    {
        if ($this->readonly) {
            // If readonly, just go back
            return redirect()->route('dashboard');
        }

        try {
            // جلب الادمن
            $tokens = array_unique(User::where('role', 'admin')
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->toArray());

            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
                $messaging = $factory->createMessaging();

                $userName = Auth::user()->name;

                // تم التعديل هنا: استخدام $this->meeting->id بدلاً من $newMeeting->id
                $reportUrl = route('meeting.record', $this->meeting->id, true);

                $message = CloudMessage::new()
                    ->withNotification(Notification::create(
                        'تتميم جديد 📝',
                        "تم ارسال تتميم بواسطة {$userName}."
                    ))
                    // 👈 إخبار المتصفح بالرابط عند الضغط
                    ->withWebPushConfig(WebPushConfig::fromArray([
                        'fcm_options' => [
                            'link' => $reportUrl
                        ]
                    ]));

                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Throwable $e) {
            // تجاهل الخطأ حتى لا يتم إيقاف عمل السيرفر إذا فشل الإرسال
            Log::error('Firebase Notification Error (New Record): ' . $e->getMessage());
        }

        // تم إضافة الفاصلة المنقوطة هنا
        return redirect()->route('dashboard')->with('status', 'تم تسجيل التتميم بنجاح!');
    }

    public function updateRecord($memberId, $field, $value)
    {
        // Block updates if readonly
        if ($this->readonly) return;

        // Force integer (0 or 1) for boolean fields
        $val = (int) $value;

        // Update Local State for UI
        if (isset($this->records[$memberId])) {
            $this->records[$memberId][$field] = $val;
        }

        // Update Database
        TatmimRecord::where('weekly_meeting_id', $this->meeting->id)
            ->where('member_id', $memberId)
            ->update([$field => $val]);
    }

    public function render()
    {
        $members = $this->ensureRecordsExist();

        return view('livewire.record-tatmim', [
            'stages' => Stage::with('lessons')->orderBy('order_number')->get(),
            'members' => $members,
        ]);
    }
}
