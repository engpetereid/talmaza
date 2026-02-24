<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Stage;
use App\Models\Lesson;
use App\Models\LessonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Added Storage facade

#[Layout('layouts.app')]
class LessonLibrary extends Component
{
    use WithFileUploads;

    public $activeStage = null;
    public $activeLesson = null;

    // Upload variables
    public $newFile;
    public $newDescription;

    public function toggleStage($stageId)
    {
        $this->activeStage = $this->activeStage === $stageId ? null : $stageId;
    }

    public function selectLesson($lessonId)
    {
        $this->activeLesson = Lesson::with(['resources.user'])->find($lessonId);
        $this->reset(['newFile', 'newDescription']);
    }

    public function closeLesson()
    {
        $this->activeLesson = null;
    }

    // Real-time validation for file
    public function updatedNewFile()
    {
        $this->validate([
            'newFile' => 'file|max:10240|mimes:pdf,doc,docx,jpg,png,jpeg', // Increased to 10MB
        ]);
    }

    public function saveResource()
    {
        $this->validate([
            'newDescription' => 'required|string|max:9000',
            'newFile' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,png,jpeg',
        ], [
            'newDescription.required' => 'يرجى كتابة وصف أو ملخص للملف.',
            'newFile.max' => 'حجم الملف كبير جداً (أقصى حد 10 ميجا).',
            'newFile.mimes' => 'نوع الملف غير مدعوم.',
        ]);

        $path = null;
        if ($this->newFile) {
            $path = $this->newFile->store('lesson_files', 'public');
        }

        LessonResource::create([
            'lesson_id' => $this->activeLesson->id,
            'user_id' => Auth::id(),
            'description' => $this->newDescription,
            'file_path' => $path,
            'type' => $path ? 'file' : 'text',
        ]);

        $this->reset(['newFile', 'newDescription']);

        // Refresh the lesson data to show the new resource
        $this->activeLesson = Lesson::with(['resources.user'])->find($this->activeLesson->id);

        session()->flash('message', 'تمت المشاركة بنجاح! شكراً لخدمتك 🌹');
    }

    public function deleteResource($resourceId)
    {
        $resource = LessonResource::find($resourceId);

        if (!$resource) {
            return;
        }

        // Authorization Check: Only Owner or Admin
        if (Auth::id() !== $resource->user_id && Auth::user()->role !== 'admin') {
            return;
        }

        // Delete file from storage if it exists
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        // Refresh data
        $this->activeLesson = Lesson::with(['resources.user'])->find($this->activeLesson->id);

        session()->flash('message', 'تم حذف المشاركة بنجاح 🗑️');
    }

    public function render()
    {
        return view('livewire.lesson-library', [
            'stages' => Stage::with('lessons')->orderBy('order_number')->get()
        ]);
    }
}
