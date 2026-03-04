<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig; // 👈 السطر الجديد
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class AnnouncementsBoard extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $title = '';
    public $content = '';
    public $attachment;

    public $search = '';

    // Reset pagination when searching
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function postAnnouncement()
    {
        if (Auth::user()->role !== 'admin') { abort(403); }

        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ], [
            'attachment.max' => 'حجم الملف كبير جداً (أقصى حد 10 ميجا)',
            'attachment.mimes' => 'نوع الملف غير مدعوم.',
            'content.required' => 'يرجى كتابة تفاصيل القرار.',
            'title.required' => 'يرجى كتابة عنوان للقرار.'
        ]);

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('announcements', 'public');
        }

        // 1. Save the announcement to the database
        Announcement::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'content' => $this->content,
            'attachment' => $path,
        ]);

        // 2. Send Firebase Push Notification to ALL users
        try {
            $tokens = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->whereNot('id',Auth::id())->pluck('fcm_token')->toArray();

            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
                $messaging = $factory->createMessaging();

                // 👈 توليد رابط القرارات
                $announcementsUrl = route('announcements', [], true);

                $message = CloudMessage::new()
                    ->withNotification(Notification::create(
                        'قرار جديد 📢: ' . $this->title,
                        Str::limit($this->content, 50)
                    ))
                    // 👈 إخبار المتصفح بالرابط عند الضغط
                    ->withWebPushConfig(WebPushConfig::fromArray([
                        'fcm_options' => [
                            'link' => $announcementsUrl
                        ]
                    ]));

                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Throwable $e) { // <-- تم التغيير هنا من Exception إلى Throwable
            // الآن حتى لو كان هناك خطأ قاتل في Firebase لن يظهر خطأ 500 للمستخدم
            Log::error('Firebase Notification Error: ' . $e->getMessage());
        }

        $this->reset(['title', 'content', 'attachment']);
        session()->flash('message', 'تم نشر القرار بنجاح 📢');
    }

    public function deletePost($id)
    {
        if (Auth::user()->role !== 'admin') { return; }

        $post = Announcement::find($id);
        if ($post) {
            if ($post->attachment) {
                Storage::disk('public')->delete($post->attachment);
            }
            $post->delete();
        }
    }

    public function render()
    {
        $query = Announcement::with('user')->latest();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }
        return view('livewire.announcements-board', [
        'posts' => $query->paginate(10)
    ]);
    }
}
