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
use Kreait\Firebase\Messaging\WebPushConfig;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Layout('layouts.app')]
class DecisionsBoard extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $title = '';
    public $content = '';
    public $attachment;

    // Filters
    public $search = '';
    public $filterStatus = 'all';
    public $filterMonth = 'all';

    // Inline Editing for Admin
    public $editingDecisionId = null;
    public $editStatus = '';
    public $editComment = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterMonth() { $this->resetPage(); }

    public function postDecision()
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
            'title.required' => 'يرجى كتابة عنوان القرار.'
        ]);

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('announcements', 'public');
        }

        Announcement::create([
            'user_id' => Auth::id(),
            'type' => 'decision', // تحديد النوع كقرار إداري
            'status' => 'pending',
            'title' => $this->title,
            'content' => $this->content,
            'attachment' => $path,
        ]);

        // Send Push Notification
        $this->sendNotification('قرار إداري جديد 📋', "تم إضافة قرار للمتابعة: {$this->title}");

        $this->reset(['title', 'content', 'attachment']);
        session()->flash('message', 'تم نشر القرار بنجاح 📋');
    }

    public function startEdit($id, $status, $comment)
    {
        if (Auth::user()->role !== 'admin') { return; }
        $this->editingDecisionId = $id;
        $this->editStatus = $status;
        $this->editComment = $comment;
    }

    public function cancelEdit()
    {
        $this->reset(['editingDecisionId', 'editStatus', 'editComment']);
    }

    public function updateDecision()
    {
        if (Auth::user()->role !== 'admin') { abort(403); }

        $decision = Announcement::findOrFail($this->editingDecisionId);

        $decision->update([
            'status' => $this->editStatus,
            'admin_comment' => $this->editComment,
        ]);

        $statusAr = match($this->editStatus) {
            'implemented' => 'تم التنفيذ ✅',
            'not_implemented' => 'لم يتم التنفيذ ❌',
            'postponed' => 'مؤجل ⏳',
            default => 'قيد المراجعة 🔄',
        };

        // Notify leaders about the status change
        $this->sendNotification('تحديث حالة قرار 🔄', "تم تغيير حالة القرار '{$decision->title}' إلى: {$statusAr}");

        $this->cancelEdit();
        session()->flash('message', 'تم تحديث حالة القرار والتعقيب بنجاح ✅');
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

    private function sendNotification($title, $body)
    {
        try {
            $tokens = array_unique(User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->pluck('fcm_token')->toArray());

            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
                $messaging = $factory->createMessaging();

                $message = CloudMessage::new()
                    ->withNotification(Notification::create($title, $body))
                    ->withWebPushConfig(WebPushConfig::fromArray([
                        'fcm_options' => ['link' => route('decisions')]
                    ]));

                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Throwable $e) {
            Log::error('Firebase Notification Error (Decisions): ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Announcement::with('user')->where('type', 'decision')->latest();

        // 1. Search Filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Status Filter
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // 3. Month Filter
        if ($this->filterMonth !== 'all') {
            $query->whereMonth('created_at', $this->filterMonth)
                ->whereYear('created_at', Carbon::now()->year);
        }

        return view('livewire.decisions-board', [
            'decisions' => $query->paginate(10)
        ]);
    }
}
